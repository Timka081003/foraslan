<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем данные из формы
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверяем, есть ли пользователь с таким именем
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Если пароль верный, начинаем сессию
        $_SESSION['user_id'] = $user['id'];
        header('Location: fileslist.php');
    } else {
        echo "Неверный логин или пароль!";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Имя пользователя" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <button type="submit">Войти</button>
</form>
