<?php
require "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Запуск сессии
session_start(); 

// Удаление всех переменных сессии
session_unset();

// Уничтожение сессии
session_destroy();

// Удаление куков
setcookie('acclogin', '', time() - 3600, '/'); // Устанавливаем время действия в прошлом
setcookie(session_name(), '', time() - 3600, '/'); // Удаление PHPSESSID

// Редирект на index.html после выхода
header("Location: index.html");
exit();
?>