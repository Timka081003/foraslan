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

// Папка для загрузки файлов
$uploadDir = 'uploads/';

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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список файлов</title>
    <link rel="stylesheet" href="style.css"> <!-- Подключаем внешний CSS -->
</head>
<body>
    <div class="container">
        <h1>Загрузить файл</h1>

        <!-- Форма для загрузки файла -->
        <form method="POST" enctype="multipart/form-data" class="upload-form">
            <input type="file" name="file" required><br>
            <button type="submit" class="upload-btn">Загрузить файл</button>
        </form>

        <h2>Ваши файлы</h2> <!-- Заголовок для списка файлов -->

        <?php if (empty($files)): ?>
            <p>У вас нет загруженных файлов.</p>
        <?php else: ?>
            <ul class="file-list">
                <?php foreach ($files as $file): ?>
                    <li>
                        <a href="<?php echo $file['file_path']; ?>" download><?php echo htmlspecialchars($file['file_name']); ?></a>
                        <!-- Кнопка для удаления файла -->
                        <a href="?delete=<?php echo $file['id']; ?>" class="delete-btn" onclick="return confirm('Вы уверены, что хотите удалить этот файл?')">Удалить</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
