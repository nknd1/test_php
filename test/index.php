<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест</title>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Тестовое задание</h1>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="login.php">Войти</a></li>
                    <li><a href="registration.php">Зарегистрироваться</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <h2>Это тестовое задание</h2>
    </main>

    <style>
        * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
}

header {
    background-color: #007bff; 
    color: white; 
    padding: 10px 0; 
}

.container {
    display: flex;
    justify-content: space-between; 
    align-items: center; 
    max-width: 1200px; 
    margin: 0 auto; 
    padding: 0 20px; 
}

.logo h1 {
    font-size: 24px; 
}

nav ul {
    list-style-type: none; 
}

.nav-links {
    display: flex; 
}

.nav-links li {
    margin-left: 20px; 
}

.nav-links a {
    text-decoration: none; 
    color: white; 
    padding: 8px 16px; 
    transition: background-color 0.3s; 
}

.nav-links a:hover {
    background-color: rgba(255, 255, 255, 0.2); 
    border-radius: 4px; 
}

    </style>
</body>
</html>
