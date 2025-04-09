<?php
$servername = "localhost";
$username = "timka";  // Имя пользователя базы данных
$password = "timka007";   // Пароль
$dbname = "uploads"; // Имя базы данных

// Создание подключения
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit();
}
?>
