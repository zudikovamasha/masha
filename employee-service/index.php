<?php
session_start(); // ВСЕГДА первая строка
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анкеты сотрудников</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="?page=employees">Анкеты сотрудников</a>
            <div class="navbar-nav">
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'employees' ? 'active' : '' ?>" href="?page=employees">Сотрудники</a>
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'positions' ? 'active' : '' ?>" href="?page=positions">Должности</a>
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'departments' ? 'active' : '' ?>" href="?page=departments">Отделы</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php
        $page = $_GET['page'] ?? 'employees';

        if ($page === 'employees') {
            include 'employees.php';
        } elseif ($page === 'positions') {
            include 'positions.php';
        } elseif ($page === 'departments') {
            include 'departments.php';
        } else {
            echo '<div class="alert alert-danger">Страница не найдена.</div>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>