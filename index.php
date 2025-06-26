<?php
// Настройки подключения к базе данных (замените на свои параметры)
$host = 'localhost';
$db   = 'sportgo_db';
$user = 'shved';
$pass = 'DeadDemon6:6';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Инициализация массива ошибок
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Валидация логина (уникальность и формат)
    if (empty($login)) {
        $errors['login'] = 'Пожалуйста, введите логин.';
    } else {
        // Проверка уникальности логина
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetchColumn() > 0) {
            $errors['login'] = 'Этот логин уже занят.';
        }
    }

    // Валидация пароля
    if (empty($password)) {
        $errors['password'] = 'Пожалуйста, введите пароль.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Пароль должен быть не менее 6 символов.';
    }

    // Валидация ФИО (кириллица и пробелы)
    if (empty($full_name)) {
        $errors['full_name'] = 'Пожалуйста, введите ФИО.';
    } elseif (!preg_match('/^[А-Яа-яЁё\s]+$/u', $full_name)) {
        $errors['full_name'] = 'ФИО должно содержать только кириллические символы и пробелы.';
    }

    // Валидация телефона
    if (!validatePhone($phone)) {
        $errors['phone'] = 'Телефон должен содержать 11 цифр, начинающихся с 7 или 8.';
    }

    // Валидация email
    if (empty($email)) {
        $errors['email'] = 'Пожалуйста, введите email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный формат email.';
    } else {
        // Проверка уникальности email
        $stmt_email = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt_email->execute([$email]);
        if ($stmt_email->fetchColumn() > 0) {
            $errors['email'] = 'Этот email уже зарегистрирован.';
        }
    }

    // Если ошибок нет — сохраняем пользователя
    if (empty($errors)) {
        // Хешируем пароль
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Вставляем данные в базу
        try {
            $stmt_insert = $pdo->prepare("INSERT INTO users (login, password, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->execute([$login, $hashed_password, $full_name, $phone, $email]);
            $success_message = 'Пользователь успешно зарегистрирован!';
            $_POST = []; // очищаем форму после успешной регистрации
        } catch (PDOException $e) {
            die("Ошибка при сохранении пользователя: " . htmlspecialchars($e->getMessage()));
        }
    }
}

// Функция проверки номера телефона
function validatePhone($phone) {
    $digits = preg_replace('/\D/', '', $phone);
    if (strlen($digits) !== 11) {
        return false;
    }
    if ($digits[0] !== '7' && $digits[0] !== '8') {
        return false;
    }
    return true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Регистрация пользователя</title>
<style>
  body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
  }
  .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
      box-sizing: border-box;
      text-align: center;
  }
  h2 { margin-top: 0; }
  form { width: 100%; }
  input[type=text], input[type=password] {
      width: calc(100% - 20px);
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-sizing: border-box;
  }
  button { padding: 10px 20px; border:none; background-color:#007bff; color:#fff; border-radius:10px; cursor:pointer; }
  button:hover { background-color:#0056b3; }
  .error { color:red; font-size:14px; margin-bottom:10px; text-align:left; }
  .success { color:green; font-size:16px; margin-bottom:15px; }
  
  /* Ссылки */
  .links { margin-top:15px; font-size:14px; }
</style>
</head>
<body>

<div class="container">
<h2>Регистрация нового пользователя</h2>

<?php if ($success_message): ?>
<p class="success"><?php echo htmlspecialchars($success_message); ?></p>
<?php endif; ?>

<form method="post" action="">

<label for="full_name">ФИО:</label><br>
<input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"><br>
<?php if(isset($errors['full_name'])) echo '<div class="error">'.htmlspecialchars($errors['full_name']).'</div>'; ?>

<label for="phone">Телефон:</label><br>
<input type="text" id="phone" name="phone" placeholder="+7(XXX)-XXX-XX-XX" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"><br>
<?php if(isset($errors['phone'])) echo '<div class="error">'.htmlspecialchars($errors['phone']).'</div>'; ?>

<label for="email">Email:</label><br>
<input type="text" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br>
<?php if(isset($errors['email'])) echo '<div class="error">'.htmlspecialchars($errors['email']).'</div>'; ?>

<label for="login">Логин:</label><br>
<input type="text" id="login" name="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"><br>
<?php if(isset($errors['login'])) echo '<div class="error">'.htmlspecialchars($errors['login']).'</div>'; ?>

<label for="password">Пароль:</label><br>
<input type="password" id="password" name="password"><br>
<?php if(isset($errors['password'])) echo '<div class="error">'.htmlspecialchars($errors['password']).'</div>'; ?>

<button type="submit">Зарегистрироваться</button>

</form>

<div class="links">
<p>Уже есть аккаунт? <a href="login.php">Войти</a></p>

</div>

</div>

</body>
</html>