<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
// Подключение к базе данных
$host = 'localhost';
$db   = 'db_cllean';
$user = 'shved';
$pass = 'DeadDemon6:6';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Ошибка подключения (' . $conn->connect_errno . '): ' . $conn->connect_error);
}

// Проверка авторизации и роли
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Обработка изменения статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'], $_POST['status'])) {
        $order_id = intval($_POST['order_id']);
        $status = $_POST['status'];

        // Обработка отмены с комментариями
        if ($status === 'Отменено' && isset($_POST['cancel_comment'])) {
            $cancel_comment = trim($_POST['cancel_comment']);
            // Обновляем статус и комментарий
            $stmt = $conn->prepare("UPDATE orders SET status = ?, cancel_comment = ? WHERE id = ?");
            $stmt->bind_param("ssi", $status, $cancel_comment, $order_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Просто обновление статуса без комментария
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $order_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Обработка удаления заказа
    if (isset($_POST['delete_order_id'])) {
        $delete_order_id = intval($_POST['delete_order_id']);
        // Удаление заказа
        $stmt_del = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt_del->bind_param("i", $delete_order_id);
        $stmt_del->execute();
        $stmt_del->close();
    }
}

// Получение всех заказов с данными пользователей
$sql = "SELECT o.id AS order_id, o.address, o.name AS order_name, o.phone,
               o.date, o.time, o.service_type, o.payment_type,
               o.created_at, o.status, o.cancel_comment,
               u.id AS user_id, u.login, u.full_name, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Панель администратора - Заказы</title>
<style>
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ccc; padding: 8px; }
  th { background-color:rgba(153, 153, 153, 0.6); }
  
  .btn {
    padding: 5px 20px;
    font-size: 1.2em;
    border-radius: 8px; 
    border:none; 
    cursor:pointer; 
    transition: background-color 0.3s ease, transform 0.2s ease;
}
/* Стиль для основной кнопки */
.btn-primary {
   text-decoration:none;
   background-color:#007bbb; 
   color:white; 
}
.btn-primary:hover{
   background-color:#0056b3; 
   transform:scale(1.05);
}

@media(max-width: 768px) {
  body {
    font-size: 14px;
  }

  table {
    font-size: 0.9em;
  }

  /* Сделать кнопки более компактными */
  .btn {
    padding: 4px 10px;
    font-size: 1em;
  }

  /* Сделать формы и поля ввода более компактными */
  input[type="text"], select {
    width: auto;
    max-width: 150px;
    font-size: 0.9em;
    padding: 4px;
    margin-top: 4px;
    display:inline-block;
  }

  /* Убрать лишние отступы или изменить расположение элементов */
}

@media(max-width:768px){
   form select,
   form input[type="text"] {
       width: auto;
       max-width: 150px;
       font-size:0.9em;
   }
}
</style>
</head>
<body>
<h1 style="text-align:center;">Добро пожаловать в панель администратора!</h1>
<h1 style="text-align:center;">Все заказы</h1>

<table>
  <thead>
    <tr>
      <th>ID заказа</th>
      <th>Адрес</th>
      <th>Имя клиента</th>
      <th>Телефон</th>
      <th>Дата</th>
      <th>Время</th>
      <th>Тип услуги</th>
      <th>Тип оплаты</th>
      <th>Создано</th>
      <th>Статус</th>
      <th>Комментарий при отмене</th>
      <th>Пользователь (логин)</th>
      <th>Email</th>
      <th>Действия</th> <!-- Колонка для действий -->
    </tr>
  </thead>
  <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>

  <td data-label="Дата создания"><?= htmlspecialchars($order['order_id']) ?></td>
  <td data-label="Адрес"><?= htmlspecialchars($order['address']) ?></td>
  <td data-label="Имя"><?= htmlspecialchars($order['order_name']) ?></td>
  <td data-label="Телефон"><?= htmlspecialchars($order['phone']) ?></td>
  <td data-label="Дата услуги"><?= htmlspecialchars($order['date']) ?></td>
  <td data-label="Время услуги"><?= htmlspecialchars($order['time']) ?></td>
  <td data-label="Тип услуги"><?= htmlspecialchars($order['service_type']) ?></td>
  <td data-label="Способ оплаты"><?= htmlspecialchars($order['payment_type']) ?></td>
  <td data-label="Статус"><?= htmlspecialchars($order['status']) ?></td>
  <td data-label="Причина отмены"><?= htmlspecialchars($order['cancel_comment'] ?? '') ?></td>


          <td><?= htmlspecialchars($row['order_id']) ?></td>
          <td><?= htmlspecialchars($row['address']) ?></td>
          <td><?= htmlspecialchars($row['order_name']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= htmlspecialchars($row['time']) ?></td>
          <td><?= htmlspecialchars($row['service_type']) ?></td>
          <td><?= htmlspecialchars($row['payment_type']) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>

          <!-- Комментарий при отмене -->
         <td><?= htmlspecialchars($row['cancel_comment'] ?? '') ?></td>

          <!-- Данные пользователя -->
          <td><?= htmlspecialchars($row['login']) ?> (ID: <?= htmlspecialchars($row['user_id']) ?>)</td>
          <td><?= htmlspecialchars($row['email']) ?></td>

          <!-- Форма для изменения статуса -->
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['order_id']) ?>">
              <select name="status" onchange="toggleCancelComment(this)">
<?php
$statuses = ['Новая заявка', 'Принято', 'Выполнено', 'Отменено'];
foreach ($statuses as $st) {
    $safe_st = htmlspecialchars($st);
    $selected = ($st == $row['status']) ? 'selected' : '';
    $is_cancel = ($st === 'Отменено') ? 'true' : 'false';
    echo "<option value=\"$safe_st\" data-cancel=\"$is_cancel\" $selected>$safe_st</option>";
}
?>
              </select>

              <!-- Поле для комментария при отмене -->
              <?php if ($row['status'] === 'Отменено'): ?>
                <?php
                  // Получить текущий комментарий или оставить пустым
                  $comment_value = isset($row['cancel_comment']) ? htmlspecialchars($row['cancel_comment']) : '';
                ?>
                <br/>
                Комментарий:
                <input type="text" name="cancel_comment" value="<?= isset($_POST['cancel_comment'][$row['order_id']]) ? htmlspecialchars($_POST['cancel_comment'][$row['order_id']]) : '' ?>" placeholder="Причина отмены"/>
              <?php else: ?>
                <!-- Для других статусов скрываем или не показываем -->
                <!-- Можно оставить пустым или скрытым -->
              <?php endif; ?>

              <!-- Кнопка обновления -->
              <?php if ($row['status'] === 'Отменено'): ?>
                <!-- Можно оставить кнопку или убрать -->
                <!-- В данном случае форма отправляется при изменении селекта -->
              <?php endif; ?>
              <!-- Можно добавить отдельную кнопку для подтверждения изменений -->

            </form>

            <!-- Скрипт для отображения поля комментария только при выборе "Отменено" -->
            <script>
              function toggleCancelComment(selectElem) {
                const selectedOption = selectElem.options[selectElem.selectedIndex];
                const isCancel = selectedOption.getAttribute('data-cancel') === 'true';
                const form = selectElem.closest('form');

                let cancelInput = form.querySelector('input[name="cancel_comment"]');
                if (isCancel) {
                  if (!cancelInput) {
                    cancelInput = document.createElement('input');
                    cancelInput.type='text';
                    cancelInput.name='cancel_comment';
                    cancelInput.placeholder='Причина отмены';
                    form.appendChild(document.createElement('br'));
                    form.appendChild(document.createTextNode('Комментарий:'));
                    form.appendChild(cancelInput);
                  } else {
                    cancelInput.style.display='inline-block';
                  }
                } else {
                  if (cancelInput) {
                    cancelInput.style.display='none';
                  }
                }
              }

              // Инициализация при загрузке страницы
              document.querySelectorAll('select[name="status"]').forEach(function(sel){
                toggleCancelComment(sel);
              });
            </script>

          </td>

          <!-- Кнопка удаления -->
          <td style="text-align:center;">
            <form method="post" action="" onsubmit="return confirm('Вы уверены, что хотите удалить эту заявку?');">
              <!-- Передаем id заявки для удаления -->
              <input type="hidden" name="delete_order_id" value="<?= htmlspecialchars($row['order_id']) ?>">
              <button type="submit">Удалить</button>
            </form>
          </td>

        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="14">Нет заказов.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<section style="text-align: center; padding-top: 20px;">
<a href="../clean/logout.php" class="btn btn-primary">Выйти из панели администратора</a></section>

<?php
$conn->close();
?>