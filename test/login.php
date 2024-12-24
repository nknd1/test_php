<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Определите ключ сервера для SmartCaptcha
define('SMARTCAPTCHA_SERVER_KEY', 'ysc2_IWUNu7tp01NUhrndvHm3EaBKTq7trkgDnDreXJWl6f8d90c0');

function check_captcha($token) {
    $ch = curl_init("https://smartcaptcha.yandexcloud.net/validate");
    $args = [
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'], // Получение IP-адреса пользователя
    ];
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch); 
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Ошибка сервера CAPTCHA: code=$httpcode; message=$server_output\n";
        return false;
    }

    $resp = json_decode($server_output);
    return $resp->status === "ok";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $captcha_token = $_POST['smart-token']; // Токен из SmartCaptcha

    // Проверка CAPTCHA
    if (!check_captcha($captcha_token)) {
        echo "Ошибка CAPTCHA!";
        exit;

    }

    // Проверка данных в базе
    $stmt = $pdo->prepare('SELECT * FROM users WHERE phone = ? OR email = ?');
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        header('Location: profile.php');
        exit;
    } else {
        echo "<p>Неверный телефон/почта или пароль.</p>";
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
    <input type="text" name="login" placeholder="Телефон или почта" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>

    <!-- SmartCaptcha Widget -->
    <div id="captcha-container" class="smart-captcha" data-sitekey="ysc1_IWUNu7tp01NUhrndvHm3iyxMnDTWEhtMwPhN3LLv87e60d70" style="height: 100px"></div>
    <br>
    <button type="submit">Войти</button>
    <br>
    <div class="container">
    <p><a href="index.php">На главную</a></p>
    </div>
</form>

<!-- Подключение JS для SmartCaptcha -->
<script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</body>
</html>