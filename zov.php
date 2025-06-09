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

section a {
    margin: 5px;
    display: inline-block; /* чтобы margin работал корректно */
}

@media(max-width:768px){
   .btn {
       padding: 5px 10px;
       font-size: 1em;
       border-radius: 10px; 
       border: none; 
       cursor: pointer; 
       transition: background-color 0.3s ease, transform 0.2s ease;
       margin: 5px; /* добавляем отступы вокруг кнопок */
   }
}

@media(max-width: 768px){
  table {
    display: block;
    overflow-x: auto;
    width: 100%;
    -webkit-overflow-scrolling: touch; /* плавный скролл на iOS */
  }
  thead, tbody, tr, th, td {
    display: block;
  }
  thead {
    display: none; /* скрываем заголовки для мобильных */
  }
  tr {
    margin-bottom: 15px;
    border-bottom: 2px solid #ccc;
    padding: 10px;
  }
  td {
    position: relative;
    padding-left: 50%;
    border: none;
    border-bottom: 1px solid #ddd;
  }
  td::before {
    position: absolute;
    top: 0;
    left: 10px;
    width: calc(50% - 20px);
    padding-right: 10px;
    white-space: nowrap;
    font-weight: bold;
    content: attr(data-label);
  }
}
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
  <td data-label="Дата создания"><?= htmlspecialchars($order['created_at']) ?></td>
  <td data-label="Адрес"><?= htmlspecialchars($order['address']) ?></td>
  <td data-label="Имя"><?= htmlspecialchars($order['name']) ?></td>
  <td data-label="Телефон"><?= htmlspecialchars($order['phone']) ?></td>
  <td data-label="Дата услуги"><?= htmlspecialchars($order['date']) ?></td>
  <td data-label="Время услуги"><?= htmlspecialchars($order['time']) ?></td>
  <td data-label="Тип услуги"><?= htmlspecialchars($order['service_type']) ?></td>
  <td data-label="Способ оплаты"><?= htmlspecialchars($order['payment_type']) ?></td>
  <td data-label="Статус"><?= htmlspecialchars($order['status']) ?></td>
  <td data-label="Причина отмены"><?= htmlspecialchars($order['cancel_comment'] ?? '') ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php else: ?>
<p style='text-align:center;'>У вас пока нет заявок.</p>
<?php endif; ?>

</body>
</html>