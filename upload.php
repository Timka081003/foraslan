<?php
session_start();
include 'db.php'; // Подключение к базе данных

// Проверка авторизации (если это необходимо)
if (!isset($_SESSION['user_id'])) {
    die("Вы не авторизованы! Пожалуйста, войдите в систему.");
}

// Папка для загрузки файлов
//$uploadDir = 'uploads/';

// Разрешенные типы файлов (например, изображения, PDF, текст)
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];

// Проверка, был ли файл загружен
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Проверка на ошибки при загрузке
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Проверка на максимальный размер файла (например, 20 MB)
        if ($file['size'] > 20971520) { // 20 MB
            echo "Файл слишком большой! Максимальный размер 20 MB.";
            exit;
        }

        // Проверка типа файла
        if (!in_array($file['type'], $allowedTypes)) {
            echo "Неподдерживаемый формат файла. Разрешены только изображения и PDF.";
            exit;
        }

        // Генерация уникального имени файла, чтобы избежать конфликтов
        $fileName = uniqid() . '-' . basename($file['name']);
        $uploadPath = $uploadDir . $fileName;

        // Перемещение файла в целевую папку
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Сохранение метаданных файла в базе данных
            $stmt = $pdo->prepare("INSERT INTO files (user_id, file_name, file_path, file_size, file_type, uploaded_at) 
                                   VALUES (:user_id, :file_name, :file_path, :file_size, :file_type, NOW())");
            $stmt->bindParam(':user_id', $_SESSION['user_id']); // привязываем пользователя
            $stmt->bindParam(':file_name', $file['name']);
            $stmt->bindParam(':file_path', $uploadPath);
            $stmt->bindParam(':file_size', $file['size']);
            $stmt->bindParam(':file_type', $file['type']);
            $stmt->execute();

            echo "Файл успешно загружен!";
        } else {
            echo "Ошибка при загрузке файла. Попробуйте еще раз.";
        }
    } else {
        // Обработка ошибок загрузки файлов
        echo "Ошибка при загрузке файла. Код ошибки: " . $file['error'];
    }
}
?>

<!-- HTML форма для загрузки файла -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required><br>
    <button type="submit">Загрузить файл</button>
</form>
