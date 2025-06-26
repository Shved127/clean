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
$db   = 'sportgo_db';
$user = 'shved';
$pass = 'DeadDemon6:6';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Получение всех типов инвентаря для быстрого доступа
$equipments = [];
$result_equipments = $conn->query("SELECT equipment_id, name FROM equipment");
while ($row = $result_equipments->fetch_assoc()) {
    $equipments[$row['equipment_id']] = $row['name'];
}

// Получение истории заявок пользователя
$result_orders = [];
$stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
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
h1, h2 {
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
       cursor:pointer; 
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
    padding-right:10px;
    white-space: nowrap;
    font-weight:bold;
    content: attr(data-label);
  }
}
</style>
</head>
<body>

<h1>Личный кабинет</h1>

<section style="text-align:center;">
<a href="order.php" class="btn btn-primary">Создать заявку</a>
<a href="logout.php" class="btn btn-primary">Выйти</a>
</section>

<h2>История ваших заявок</h2>

<?php if (count($result_orders) > 0): ?>
<table style="text-align:center;">
<tr>
  <th>Дата создания</th>
  <th>Тип инвентаря</th>
  <th>начало аренды</th>
  <th>конец аренды</th>
  <th>способ оплаты</th>
  <th>Статус</th>
</tr>
<?php foreach ($result_orders as $order): ?>
<tr>
  <td data-label="Дата создания"><?= htmlspecialchars($order['created_at']) ?></td>
  

<td data-label="Тип инвентаря"><?= htmlspecialchars($equipments[$order['equipment_id']] ?? 'Неизвестно') ?></td>

<td data-label="начало аренды"><?= htmlspecialchars($order['start_time']) ?></td>
<td data-label="конец аренды"><?= htmlspecialchars($order['end_time']) ?></td>
<td data-label="способ оплаты"><?= htmlspecialchars($order['payment_method']) ?></td>
<td data-label="Статус"><?= htmlspecialchars($order['status']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php else : ?>
<p style='text-align:center;'>У вас пока нет заявок.</p>
<?php endif; ?>

</body>
</html>