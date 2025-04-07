<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка файлов</title>
</head>
<body>
    <h1>Загрузите файл</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Выберите файл для загрузки:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Загрузить</button>
    </form>
    <br><br>
    <button onclick="window.location.href='fileslist.php'">Перейти к списку файлов</button>
</body>
</html>
