<?php
session_start();

// Проверка авторизации пользователя
if (isset($_SESSION['user_id'])) {
    // Если пользователь авторизован, перенаправляем на страницу загрузки файлов
    header("Location: fileslist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
</head>
<body>
    <h1>Добро пожаловать!</h1>
    <p>Пожалуйста, войдите или зарегистрируйтесь, чтобы продолжить.</p>
    
    <button onclick="window.location.href='login.php'">Вход</button>
    <button onclick="window.location.href='register.php'">Регистрация</button>
</body>
</html>
