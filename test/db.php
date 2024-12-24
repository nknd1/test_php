<?php
$host = 'db'; // Название сервиса MySQL из docker-compose.yml
$db = 'app_db';
$user = 'app_user';
$pass = 'app_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
