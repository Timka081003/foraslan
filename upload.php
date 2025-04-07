<?php
include 'db.php'; // Подключение к базе данных

// Папка для загрузки файлов
$uploadDir = 'uploads/';

// Проверка, был ли файл загружен
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Проверка на ошибки при загрузке
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Проверка на максимальный размер файла (например, 20 MB)
        if ($file['size'] > 20971520) {
            echo "Файл слишком большой! Максимальный размер 20 MB.";
            exit;
        }

        // Генерация уникального имени файла
        $fileName = uniqid() . '-' . basename($file['name']);
        $uploadPath = $uploadDir . $fileName;

        // Перемещение файла в целевую папку
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Сохранение метаданных файла в базе данных
            $stmt = $pdo->prepare("INSERT INTO files (file_name, file_path, file_size, file_type) 
                                   VALUES (:file_name, :file_path, :file_size, :file_type)");
            $stmt->bindParam(':file_name', $file['name']);
            $stmt->bindParam(':file_path', $uploadPath);
            $stmt->bindParam(':file_size', $file['size']);
            $stmt->bindParam(':file_type', $file['type']);
            $stmt->execute();

            echo "Файл успешно загружен!";
        } else {
            echo "Ошибка загрузки файла.";
        }
    } else {
        echo "Ошибка при загрузке файла. Код ошибки: " . $file['error'];
    }
}
?>
