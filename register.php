<?php
// Подключение к базе данных
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем данные из формы
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Хешируем пароль
    $email = $_POST['email'];

    // Проверяем, существует ли уже пользователь с таким именем
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        echo "Пользователь с таким именем уже существует!";
    } else {
        // Добавляем нового пользователя
        $stmt = $pdo->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)');
        $stmt->execute([$username, $password, $email]);
        echo "Регистрация прошла успешно!";
        header('Location: fileslist.php');
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Имя пользователя" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <button type="submit">Зарегистрироваться</button>
</form>
