<?php
session_start(); // ВСЕГДА первая строка
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анкеты учеников</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand" href="?page=students">Анкеты учеников</a>
            <div class="navbar-nav">
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'students' ? 'active' : '' ?>" href="?page=students">Ученики</a>
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'group' ? 'active' : '' ?>" href="?page=group">Группы</a>
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'program' ? 'active' : '' ?>" href="?page=program">Программы обучения</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php
        $page = $_GET['page'] ?? 'students';

        if ($page === 'students') {
            include 'students.php';
        } elseif ($page === 'group') {
            include 'group.php';
        } elseif ($page === 'program') {
            include 'program.php';
        } else {
             echo '
             <div id="error-alert" class="alert alert-danger alert-dismissible fade show" style="cursor: pointer;">Страница не найдена.</div>

            <script>
            // Дожидаемся, пока страница загрузится
            document.addEventListener("DOMContentLoaded", function() {
            const alertEl = document.getElementById("error-alert");
            if (alertEl) {
                alertEl.addEventListener("click", function() {
                    // Используем Bootstrap API для плавного закрытия
                    bootstrap.Alert.getOrCreateInstance(this).close();
                });
            }
            });
            </script>
        ';}
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>