<?php
session_start();

// Подключение к базе данных
$host = 'localhost';
$db   = 'db_cllean';
$user = 'shved';
$pass = 'DeadDemon6:6';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . htmlspecialchars($e->getMessage()));
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error_message = 'Пожалуйста, введите логин и пароль';
    } else {
        // Поиск пользователя по логину
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            $error_message = 'Пользователь с таким логином не найден.';
        } else {
            // Проверка пароля
            if (password_verify($password, $user_data['password'])) {
                // Успешный вход — установить сессию
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['login'] = $user_data['login'];
                $_SESSION['full_name'] = $user_data['full_name'] ?? '';

                // Установка роли
                if (isset($user_data['role']) && $user_data['role'] === 'admin') {
                    $_SESSION['role'] = 'admin';
                    header('Location: admin.php');  // Перенаправляем в админ-панель
                    exit;
                } else {
                    $_SESSION['role'] = 'user';
                    header('Location: index.php');  // Перенаправляем на главную для обычных пользователей
                    exit;
                }
            } else {
                $error_message = 'Неверный пароль.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta charset="UTF-8" />
<title>Вход в систему</title>
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
  button { padding: 10px 20px; border:none; background-color:#007bff; color:#fff; border-radius:5px; cursor:pointer; width:100%; }
  button:hover { background-color:#0056b3; }
  
  .error { color:red; font-size:14px; margin-bottom:10px; text-align:left; }
  
  /* Ссылки */
  .links { margin-top:15px; font-size:14px; }
</style>
</head>
<body>

<div class="container">
<h2>Авторизация</h2>

<?php if ($error_message): ?>
<p class="error"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<form method="post" action="">
<label for="login">Логин:</label><br>
<input type="text" id="login" name="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"><br>

<label for="password">Пароль:</label><br>
<input type="password" id="password" name="password"><br>

<button type="submit">Войти</button>
</form>

<div class="links">
<p>Нет аккаунта? <a href="../clean/register.php">Зарегистрироваться</a></p>
<p><a href="../clean/index.php">Вернуться на главную</a></p>
</div>

</div>

</body>
</html>