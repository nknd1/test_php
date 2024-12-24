<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $csrf_token = $_POST['csrf_token'];

    $errors = [];

    // Проверка CSRF токена
    if (!validateCsrfToken($csrf_token)) {
        die("Ошибка CSRF.");
    }

    // Проверка на пустые поля
    if (empty($name) || empty($phone) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Все поля обязательны для заполнения.";
    }

    // Проверка формата почты
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный формат почты.";
    }

    // Проверка формата телефона
    if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $errors[] = "Некорректный номер телефона.";
    }

    // Проверка на совпадение паролей
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают.";
    }

    // Проверка минимальной длины пароля
    if (strlen($password) < 8) {
        $errors[] = "Пароль должен содержать хотя бы 8 символов.";
    }

    // Проверка уникальности телефона и почты
    $stmt = $pdo->prepare('SELECT * FROM users WHERE phone = ? OR email = ?');
    $stmt->execute([$phone, $email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Телефон или почта уже зарегистрированы.";
    }

    // Хеширование пароля для безопасного хранения в базе 
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $phone, $email, $hashed_password]);

        echo "Регистрация успешна!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<form method="POST">
    <input type="text" name="name" placeholder="Имя" required><br>
    <input type="text" name="phone" placeholder="Телефон" required><br>
    <input type="email" name="email" placeholder="Почта" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <input type="password" name="confirm_password" placeholder="Повторите пароль" required><br>
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>"><br>
    <button type="submit">Зарегистрироваться</button>
    <br>
    <div class="container">
    <p><a href="index.php">На главную</a></p>
    </div>
</form>

<?php
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}
?>
</body>
</html>