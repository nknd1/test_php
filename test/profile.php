<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Генерация CSRF токена
$csrf_token = generateCsrfToken();
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
        <h2>Профиль пользователя</h2>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        <input type="password" name="password" placeholder="Новый пароль (если нужно)">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <button type="submit">Сохранить изменения</button>
        <br>
         <p><a href="logout.php">Выйти</a></p>
    </form>
   

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $csrf_token_post = $_POST['csrf_token'];

    if (!validateCsrfToken($csrf_token_post)) {
        echo '<p class="error-message">Ошибка CSRF.</p>';
        exit;
    }

    $errors = [];
    
    // Получаем текущий пароль пользователя из базы данных
    $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo '<p class="error-message">Пользователь не найден.</p>';
        exit;
    }

    $current_password_hash = $user['password'];

    // Проверяем, используется ли телефон или почта
    $stmt = $pdo->prepare('SELECT * FROM users WHERE (phone = ? OR email = ?) AND id != ?');
    $stmt->execute([$phone, $email, $user_id]);

    if ($stmt->rowCount() > 0) {
        $errors[] = "Телефон или почта уже используются.";
    }

    // Проверяем, совпадает ли новый пароль с существующим
    if (!empty($password) && password_verify($password, $current_password_hash)) {
        $errors[] = "Новый пароль не может совпадать с текущим паролем.";
    }

    if (empty($errors)) {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ?, email = ?, password = ? WHERE id = ?');
            $stmt->execute([$name, $phone, $email, $hashed_password, $user_id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $phone, $email, $user_id]);
        }

        echo '<div class="container"><p>Данные успешно обновлены!</p></div>';
    } else {
        foreach ($errors as $error) {
            echo '<p class="error-message">' . htmlspecialchars($error) . '</p>';
        }
    }
}
?>

</body>
</html>
