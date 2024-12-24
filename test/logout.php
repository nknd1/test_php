<?php
session_start();

// Удаляем сессию пользователя и перенаправляем на страницу логина
session_destroy();
header('Location: login.php');
exit;
?>
