<?php
// Функция для генерации CSRF токена
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Функция для проверки CSRF токена
function validateCsrfToken($token) {
    return isset($token) && $token === $_SESSION['csrf_token'];
}
?>
