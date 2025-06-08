<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Мой Не Сам - Клининговые услуги</title>
<link rel="stylesheet" href="../clean/css/style.css"/>
</head>
<body>
<div class="container">
    
<header>
<h1>Мой Не Сам</h1>
<nav>
    <ul>
        <li><a href="#">О нас</a></li>
        <li><a href="../clean/zov.php" class="salka">Мои заявки</a></li> 
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="../clean/logout.php">Выход</a></li>
        <?php else: ?>
            <li><a href="../clean/login.php">Войти</a></li>
            <li><a href="../clean/register.php">Регистрация</a></li>
        <?php endif; ?>
    </ul>
</nav>
</header>

<main>
<h2 style="text-align:center;">
<?php
if (isset($_SESSION['login'])) {
    echo  htmlspecialchars($_SESSION['login']) . ", ";
}
?>
Добро пожаловать в портал клининговых услуг «Мой Не Сам»!
</h2>

<section class="intro" id="about">
<p>Мы предлагаем профессиональную уборку жилых и производственных помещений. Быстро, качественно и по доступным ценам.</p>
</section>

<section id="services">
<h2 style="text-align:center;">Наши услуги</h2>
<ul style="list-style:none;padding-left:none;text-align:center;">
<li>Ежедневная уборка квартир и домов</li>
<li>Генеральная уборка офисов и предприятий</li>
<li>Уборка после ремонта</li>
<li>Уборка промышленных помещений</li>
</ul>
</section>

<section id="order" style="text-align:center;margin-top: 40px;    ">
<a href="order.php" type="submit" class="btn btn-primary">Отправить заявку</a>
</section>

</main>

<footer id='contacts'>
&copy; 2025 Мой Не Сам. Все права защищены.
</footer>
</div>
</body>
</html>