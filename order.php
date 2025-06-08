<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Стартуем сессию для получения user_id
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Пользователь не авторизован");
}
$user_id = $_SESSION['user_id'];

// Подключение к базе данных
$host = 'localhost';
$db   = 'db_cllean';
$user = 'shved';
$pass = 'DeadDemon6:6';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка обязательных полей
    if (empty($_POST['address']) || empty($_POST['name']) || empty($_POST['phone']) || 
        empty($_POST['date']) || empty($_POST['time']) || empty($_POST['service_type']) || 
        empty($_POST['payment_type'])) {
        $message = "Пожалуйста, заполните все обязательные поля.";
    } else {
        $phone = $_POST['phone'];
        // Проверка номера телефона (начинается с 7 или 8 и далее 10 цифр)
        if (!preg_match('/^[78][0-9]{10}$/', $phone)) {
            $message = "Некорректный номер телефона. Формат: 7XXXXXXXXXX или 8XXXXXXXXXX";
        } else {
            $date = $_POST['date'];
            // Проверка формата даты
            $d = DateTime::createFromFormat('Y-m-d', $date);
            if (!$d || $d->format('Y-m-d') !== $date) {
                $message = "Некорректная дата.";
            } else {
                // Получение данных из формы
                $address = $_POST['address'];
                $name = $_POST['name'];
                $time = $_POST['time'];
                $service_type = $_POST['service_type'];
                $payment_type = $_POST['payment_type'];

                // Подготовка запроса на вставку
                $stmt = $conn->prepare("INSERT INTO orders (address, name, phone, date, time, service_type, payment_type, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    die("Ошибка подготовки запроса: " . $conn->error);
                }

                $stmt->bind_param("sssssssi", 
                    $address, 
                    $name, 
                    $phone, 
                    $date, 
                    $time, 
                    $service_type, 
                    $payment_type,
                    $user_id
                );

                if ($stmt->execute()) {
                    // После успешной вставки делаем редирект
                    header("Location: zov.php");
                    exit();
                } else {
                    $message = "Ошибка при сохранении: " . $stmt->error;
                }

                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Заказ</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-image: url('images/bg.jpg');
    background-size: cover;
    background-position: center;
    margin: 0;
    padding: 0;
    color: #fff;
}

.center-container {
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.order-form {
    max-width: 600px;
    width: 100%;
    padding: 30px;
    background-color: rgba(5, 107, 218, 0.87);
    border-radius: 30px;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
}

.order-form h2 {
    margin-bottom: 20px;
    color: #fff;
}

.order-form label {
    text-align: left;
    margin-bottom: 5px;
    display: block;
    font-weight: bold;
}

.order-form input[type=text],
.order-form input[type=email],
.order-form input[type=date],
.order-form input[type=time],
.order-form select,
.order-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-radius: 5px;
    box-sizing: border-box;
}

.order-form button {
    padding: 10px 20px;
    font-size: 20px;
    background-color: #fff;
    color: #007bff;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.order-form button:hover {
    background-color: #e0e0e0;
}

.message {
    margin-bottom: 15px;
    font-weight: bold;
    color: #ffdddd;
}
a {
    color: white;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="center-container">
    <form class="order-form" action="" method="post">
        <h2>Оформление заказа</h2>

        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <label for="address">Адрес:</label>
        <input type="text" id="address" name="address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" />

        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" required placeholder="Например, 79991234567" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" />

        <label for="date">Дата:</label>
        <input type="date" id="date" name="date" required value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>" />

        <label for="time">Время:</label>
        <select id="time" name="time" required>
          <option value="">Выберите время</option>
          <option value="08:00" <?php if(isset($_POST['time']) && $_POST['time'] == '08:00') echo 'selected'; ?>>8:00</option>
          <option value="09:00" <?php if(isset($_POST['time']) && $_POST['time'] == '09:00') echo 'selected'; ?>>9:00</option>
          <option value="10:00" <?php if(isset($_POST['time']) && $_POST['time'] == '10:00') echo 'selected'; ?>>10:00</option>
          <option value="11:00" <?php if(isset($_POST['time']) && $_POST['time'] == '11:00') echo 'selected'; ?>>11:00</option>
          <option value="12:00" <?php if(isset($_POST['time']) && $_POST['time'] == '12:00') echo 'selected'; ?>>12:00</option>
          <option value="13:00" <?php if(isset($_POST['time']) && $_POST['time'] == '13:00') echo 'selected'; ?>>13:00</option>
          <option value="14:00" <?php if(isset($_POST['time']) && $_POST['time'] == '14:00') echo 'selected'; ?>>14:00</option>
          <option value="15:00" <?php if(isset($_POST['time']) && $_POST['time'] == '15:00') echo 'selected'; ?>>15:00</option>
          <option value="16:00" <?php if(isset($_POST['time']) && $_POST['time'] == '16:00') echo 'selected'; ?>>16:00</option>
          <option value="17:00" <?php if(isset($_POST['time']) && $_POST['time'] == '17:00') echo 'selected'; ?>>17:00</option>
          <option value="18:00" <?php if(isset($_POST['time']) && $_POST['time'] == '18:00') echo 'selected'; ?>>18:00</option>
        </select>

        <label for="service_type">Тип услуги:</label>
        <select id="service_type" name="service_type" required>
            <option value="">Выберите услугу</option>
            <option value="Общий клининг" <?php if(isset($_POST['service_type']) && $_POST['service_type'] == 'Общий клининг') echo 'selected'; ?>>Общий клининг</option>
            <option value="Генеральная уборка" <?php if(isset($_POST['service_type']) && $_POST['service_type'] == 'Генеральная уборка') echo 'selected'; ?>>Генеральная уборка</option>
            <option value="Послестроительная уборка" <?php if(isset($_POST['service_type']) && $_POST['service_type'] == 'Послестроительная уборка') echo 'selected'; ?>>Послестроительная уборка</option>
            <option value="Химчистка ковров и мебели" <?php if(isset($_POST['service_type']) && $_POST['service_type'] == 'Химчистка ковров и мебели') echo 'selected'; ?>>Химчистка ковров и мебели</option>
        </select>

        <label for="payment_type">Тип оплаты:</label>
        <select id="payment_type" name="payment_type" required>
            <option value="">Выберите способ оплаты</option>
            <option value="Карта" <?php if(isset($_POST['payment_type']) && $_POST['payment_type'] == 'Карта') echo 'selected'; ?>>Карта</option>
            <option value="Наличные" <?php if(isset($_POST['payment_type']) && $_POST['payment_type'] == 'Наличные') echo 'selected'; ?>>Наличные</option>
        </select>

        <button type="submit">Отправить заказ</button>
        <p><a href="../clean/index.php">Вернуться на главную</a></p>
    </form>
</div>

</body>
</html>

Найти еще