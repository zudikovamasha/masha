<?php
$host = 'localhost';
$port = '5432';
$dbname = 'masha';
$user = 'postgres';
$password = 'masha';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Подключено к PostgreSQL через PDO!";
} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
?>