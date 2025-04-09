<?php
session_start();
include 'db.php'; // Подключение к базе данных

// Проверка авторизации пользователя
if (!isset($_SESSION['user_id'])) {
    die("Вы не авторизованы! Пожалуйста, войдите в систему.");
}

// Извлечение файлов, загруженных текущим пользователем
$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка запроса на удаление файла
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $fileId = $_GET['delete'];

    // Получаем информацию о файле
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $fileId);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        // Удаляем файл с сервера
        if (unlink($file['file_path'])) {
            // Удаляем информацию о файле из базы данных
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = :id");
            $stmt->bindParam(':id', $fileId);
            $stmt->execute();
            echo "Файл успешно удален!";
        } else {
            echo "Ошибка при удалении файла с сервера.";
        }
    } else {
        echo "Файл не найден или у вас нет доступа к этому файлу.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список файлов</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #0066cc;
        }
        .delete-btn {
            color: red;
            margin-left: 10px;
        }
        .upload-btn {
            background-color: #4CAF50; /* Зеленый цвет */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            display: inline-block;
        }
        .upload-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Загруженные файлы</h1>
    
    <!-- Кнопка для загрузки файлов -->
    <a href="upload.php" class="upload-btn">Загрузить файл</a>
    
    <?php if (empty($files)): ?>
        <p>У вас нет загруженных файлов.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($files as $file): ?>
                <li>
                    <a href="<?php echo $file['file_path']; ?>" download><?php echo htmlspecialchars($file['file_name']); ?></a>
                    <!-- Кнопка для удаления файла -->
                    <a href="?delete=<?php echo $file['id']; ?>" class="delete-btn" onclick="return confirm('Вы уверены, что хотите удалить этот файл?')">Удалить</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
