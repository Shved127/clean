<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

// Проверка роли
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Доступ запрещен');
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

// Проверка наличия ID заявки
if (!isset($_POST['order_id'])) {
    die('Некорректный запрос');
}

$order_id = intval($_POST['order_id']);

// Удаление заявки
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Успешно удалено
    header('Location: your_orders_page.php'); // замените на название вашей страницы с заявками
} else {
    die('Ошибка при удалении заявки');
}

$stmt->close();
$conn->close();
?>