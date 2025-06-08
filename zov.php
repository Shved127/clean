<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    // Пользователь не авторизован — перенаправляем на страницу входа
    header('Location: login.php');
    exit();
}

// Подключение к базе данных
$host = 'localhost';
$db   = 'db_cllean';
$user = 'shved';
$pass = 'DeadDemon6:6';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die("Пожалуйста, войдите в систему для просмотра этой страницы.");
}

$user_id = $_SESSION['user_id'];

// Получение истории заявок пользователя
$result_orders = [];
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY date DESC");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$result_orders_obj = $stmt_orders->get_result();
while ($row = $result_orders_obj->fetch_assoc()) {
    $result_orders[] = $row;
}
$stmt_orders->close();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Мои заявки</title>
<style>
body {    
    font-family: Arial, sans-serif;
    background-image: url('images/bg.jpg');
    background-size: cover;
    background-position: center;
    margin: 0;
    padding: 20px;
}
h1 {
    text-align: center;
}
.message {
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
}
.container {
    max-width: 900px;
    margin: 0 auto;
}
h2 {
    margin-top: 40px;
}
table {
    width:100%;
    border-collapse: collapse;
    margin-top:30px;
}
th, td {
    border:1px solid #fff;
    padding:8px;
}
th {
   background-color:#007bff; 
   color:#fff; 
}

.btn {
    padding: 10px 20px;
    font-size: 1.2em;
    border-radius: 8px; 
    border:none; 
    cursor:pointer; 
    transition: background-color 0.3s ease, transform 0.2s ease;
}
/* Стиль для основной кнопки */
.btn-primary {
   text-decoration:none;
   background-color:#007bff; 
   color:white; 
}
.btn-primary:hover{
   background-color:#0056b3; 
   transform:scale(1.05);
}
</style>
</head>
<body>

<h1>История ваших заявок</h1>

<section style=text-align:center;>
<a href="../clean/order.php" type="submit" class="btn btn-primary">Оставить новую заявку</a>
<a href="../clean/index.php" type="submit" class="btn btn-primary">Вернуться на главную</a>
<a href="../clean/logout.php" type="submit" class="btn btn-primary">Выйти</a>
</section>

<?php if (count($result_orders) > 0): ?>
<table>
<tr>
  <th>Дата создания</th>
  <th>Адрес</th>
  <th>Имя</th>
  <th>Телефон</th>
  <th>Дата услуги</th>
  <th>Время услуги</th>
  <th>Тип услуги</th>
  <th>Способ оплаты</th>
  <th>Статус</th>
  <th>Причина отмены</th> <!-- добавляем колонку -->
</tr>
<?php foreach ($result_orders as $order): ?>
<tr>
  <td><?= htmlspecialchars($order['created_at']) ?></td>
  <td><?= htmlspecialchars($order['address']) ?></td>
  <td><?= htmlspecialchars($order['name']) ?></td>
  <td><?= htmlspecialchars($order['phone']) ?></td>
  <td><?= htmlspecialchars($order['date']) ?></td>
  <td><?= htmlspecialchars($order['time']) ?></td>
  <td><?= htmlspecialchars($order['service_type']) ?></td>
  <td><?= htmlspecialchars($order['payment_type']) ?></td>
  <td><?= htmlspecialchars($order['status']) ?></td>
  <!-- вывод причины отмены -->
  <td><?= htmlspecialchars($order['cancel_comment'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php else: ?>
<p style='text-align:center;'>У вас пока нет заявок.</p>
<?php endif; ?>

</body>
</html>