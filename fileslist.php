<?php
include 'db.php'; // Подключение к базе данных

// Извлечение всех файлов из базы данных
$stmt = $pdo->query("SELECT * FROM files");
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список файлов</title>
</head>
<body>
    <h1>Загруженные файлы</h1>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="<?php echo $file['file_path']; ?>" download><?php echo $file['file_name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
