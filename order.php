<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Стартуем сессию для получения user_id
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Подключение к базе данных
$host = 'localhost';
$db   = 'sportgo_db';
$user = 'shved';
$pass = 'DeadDemon6:6';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получение данных для селектов
// Инвентарь
$equipments = [];
$result = $conn->query("SELECT equipment_id, name FROM equipment");
while ($row = $result->fetch_assoc()) {
    $equipments[] = $row;
}

// Пункты выдачи
$pickup_points = [];
$result = $conn->query("SELECT point_id, address FROM pickup_points");
while ($row = $result->fetch_assoc()) {
    $pickup_points[] = $row;
}

// Обработка формы при отправке
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка наличия всех обязательных полей
    if (
        isset($_POST['equipment'], $_POST['pickup_point'], $_POST['start_time'], $_POST['end_time'], $_POST['payment_method'])
        && !empty($_POST['equipment']) && !empty($_POST['pickup_point']) && !empty($_POST['start_time']) && !empty($_POST['end_time']) && !empty($_POST['payment_method'])
    ) {
        // Получение и приведение данных
        $equipment_id = intval($_POST['equipment']);
        $point_id = intval($_POST['pickup_point']);
        $start_time_input = $_POST['start_time'];
        $end_time_input = $_POST['end_time'];
        $payment_method = $_POST['payment_method'];

        // Проверка существования оборудования
        $stmt_check_eq = $conn->prepare("SELECT price_per_hour, available_quantity FROM equipment WHERE equipment_id = ?");
        $stmt_check_eq->bind_param("i", $equipment_id);
        $stmt_check_eq->execute();
        $res_eq = $stmt_check_eq->get_result();
        if ($res_eq->num_rows === 0) {
            die('Инвентарь не найден');
        }
        $equipment_data = $res_eq->fetch_assoc();
        $price_per_hour = floatval($equipment_data['price_per_hour']);

        // Обработка дат и времени
        try {
            $start_dt = new DateTime($start_time_input);
            $end_dt = new DateTime($end_time_input);
        } catch (Exception $e) {
            die('Некорректная дата или время.');
        }

        if ($end_dt <= $start_dt) {
            die('Дата окончания должна быть позже начала.');
        }

        // Расчет продолжительности в часах (округление вверх)
        $interval = $start_dt->diff($end_dt);
        // Время в часах (учитываем дни)
        $hours = max(1, (int)ceil($interval->h + ($interval->d * 24)));

        // Общая цена
        $total_price = round($hours * $price_per_hour, 2);

        // Вставляем заказ
        $stmt_insert = $conn->prepare(
            "INSERT INTO orders (user_id, equipment_id, point_id, start_time, end_time, total_price, payment_method) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt_insert) {
            die('Ошибка подготовки запроса: ' . htmlspecialchars($conn->error));
        }

        // Связываем параметры
        // Обратите внимание на типы: i - integer, d - double/float, s - string
        $stmt_insert->bind_param(
            "iiissds",
            $_SESSION['user_id'],
            $equipment_id,
            $point_id,
            $start_time_input,
            $end_time_input,
            number_format($total_price, 2),
            htmlspecialchars($payment_method)
        );

        if ($stmt_insert->execute()) {
            echo "<p>Заказ успешно оформлен!</p>";
            header('Location: zov   .php');
            exit;
        } else {
            echo "<p>Ошибка при сохранении заказа: " . htmlspecialchars($conn->error) . "</p>";
        }
    } else {
        echo "<p>Пожалуйста, заполните все поля формы.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Оформление заказа</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
  }

  h1 {
    text-align: center;
    color: #333;
  }

  form {
    max-width: 600px;
    margin: 0 auto;
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: #555;
  }

  input[type="datetime-local"],
  select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;цц
    border-radius: 4px;
    border: 1px solid #ccc;
    box-sizing: border-box;
    font-size: 16px;
  }

  input[type="radio"] {
    margin-right: 8px;
  }

  .payment-options {
    margin-bottom: 20px;
  }

  button {
    width: 100%;
    padding: 12px;
    background-color: #007BFF; /* синий цвет */
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #0056b3; /* темнее при наведении */
  }

  p {
    max-width: 600px;
    margin:20px auto;
    padding:15px;
    background-color:#e0ffe0; /* светло-зеленый фон для сообщений */
    border-left:5px solid #00cc00; 
    border-radius:4px; 
}
</style>
</head>
<body>
<h1>Оформление заказа</h1>
<form method="post" action="">
  <label for="equipment">Тип инвентаря:</label><br/>
  <select name="equipment" id="equipment" required>
      <option value="">Выберите инвентарь</option>
      <?php foreach ($equipments as $eq): ?>
          <option value="<?= htmlspecialchars($eq['equipment_id']) ?>"><?= htmlspecialchars($eq['name']) ?></option>
      <?php endforeach; ?>
  </select><br/><br/>

  <label for="start_time">Дата и время начала аренды:</label><br/>
  <input type="datetime-local" id="start_time" name="start_time" required><br/><br/>

  <label for="end_time">Дата и время окончания аренды:</label><br/>
  <input type="datetime-local" id="end_time" name="end_time" required><br/><br/>

  <label for="pickup_point">Пункт выдачи:</label><br/>
  <select name="pickup_point" id="pickup_point" required>
      <option value="">Выберите пункт выдачи</option>
      <?php foreach ($pickup_points as $point): ?>
          <option value="<?= htmlspecialchars($point['point_id']) ?>"><?= htmlspecialchars($point['address']) ?></option>
      <?php endforeach; ?>
  </select><br/><br/>

  <label>Способ оплаты:</label><br/>
  <input type="radio" name="payment_method" value="cash" id="cash" required>
  <label for="cash">Наличные</label><br/>
  <input type="radio" name="payment_method" value="card" id="card">
  <label for="card">Карта</label><br/><br/>

  <button type="submit">Подтвердить заказ</button>
</form>
</body>
</html>