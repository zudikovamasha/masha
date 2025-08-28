<?php
require_once 'config/db.php';

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<div class="mb-4">
    <a href="?page=program&add=1" class="btn btn-success btn-sm">Добавить программу обучения</a>
</div>

<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'успешно') !== false ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header bg-success text-light">Добавить программу обучения</div>
    <div class="card-body">
        <?php $data = ['name' => '']; $errors = []; ?>
        <form method="post">
            <input type="hidden" name="create_program" value="1">
            <?php include __DIR__ . '/forms/program_form.php'; ?>
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="?page=program" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Название программы обучения</th>
        </tr>
    </thead>
    <tbody>
        <?php $program = $pdo->query("SELECT * FROM study_program ORDER BY name")->fetchAll(); ?>
        <?php if (empty($program)): ?>
            <tr><td class="text-center text-muted">Нет данных</td></tr>
        <?php else: ?>
            <?php foreach ($program as $p): ?>
                <tr>
                    <td>
                        <a href="?page=program&edit_id=<?= $p['id'] ?>" class="text-decoration-none text-success fw-bold">
                            <?= htmlspecialchars($p['name']) ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit_id'])):
    $id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM study_program WHERE id = ?");
    $stmt->execute([$id]);
    $program = $stmt->fetch();

    if ($program): ?>
        <div class="card mt-4">
            <div class="card-header bg-success text-light">Редактировать или удалить программу обучения</div>
            <div class="card-body">
                <form method="post" onsubmit="return confirm('Сохранить изменения?')">
                    <input type="hidden" name="update_program" value="1">
                    <input type="hidden" name="id" value="<?= $program['id'] ?>">
                    <?php $data = $program; $errors = []; include __DIR__ . '/forms/program_form.php'; ?>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="?page=program" class="btn btn-secondary">Отмена</a>
                </form>

                <form method="post" onsubmit="return confirm('Удалить группу? Это действие нельзя отменить.')">
                    <input type="hidden" name="delete_program" value="1">
                    <input type="hidden" name="id" value="<?= $program['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
if (isset($_POST['create_program'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: введите название программы обучения.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM study_program WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: программа обучения с таким названием уже существует.';
        } else {
            $pdo->prepare("INSERT INTO study_program (name) VALUES (?)")->execute([$name]);
            $_SESSION['message'] = 'Программа обучения успешно добавлена.';
        }
    }
    header("Location: ?page=program");
    exit;
}

if (isset($_POST['update_program'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: введите название программы обучения.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM study_program WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: программа обучения с таким названием уже существует.';
        } else {
            $pdo->prepare("UPDATE study_program SET name = ? WHERE id = ?")->execute([$name, $id]);
            $_SESSION['message'] = 'Программа обучения успешно обновлена.';
        }
    }
    header("Location: ?page=program");
    exit;
}

if (isset($_POST['delete_program'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE study_program_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Нельзя удалить программу обучения: она используется в анкетах у $count учеников.";
    } else {
        try {
            $pdo->prepare("DELETE FROM study_program WHERE id = ?")->execute([$id]);
            $_SESSION['message'] = "Программу обучения успешно удалена.";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ошибка удаления: " . $e->getMessage();
        }
    }
    header("Location: ?page=program");
    exit;
}
?>