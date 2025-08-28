<?php
// Настройки подключения
$host = 'localhost';
$db   = 'students';  // имя БД
$user = 'postgres';          // замените при необходимости
$pass = 'masha';          // замените на ваш пароль
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db;user=$user;password=$pass");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>