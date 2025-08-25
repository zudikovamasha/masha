<?php
$dsn = "pgsql:host=localhost;dbname=masha;user=postgres;password=masha";

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Запись
    //$stmt = $pdo->prepare("INSERT INTO test1 (fio) VALUES (?)");
    //$stmt->execute(['ОД']);

    // Чтение
    $stmt = $pdo->query("SELECT * FROM test1");
    while ($row = $stmt->fetch()) {
        //print_r($row);
		print_r($row[1].'<br>');
    }

} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}