<?php
require_once 'config/db.php';

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<div class="mb-4">
    <a href="?page=departments&add=1" class="btn btn-success btn-sm">+ Добавить отдел</a>
</div>

<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'успешно') !== false ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить отдел</div>
    <div class="card-body">
        <?php $data = ['name' => '']; $errors = []; ?>
        <form method="post">
            <input type="hidden" name="create_department" value="1">
            <?php include __DIR__ . '/partials/department_form.php'; ?>
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="?page=departments" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Название</th>
        </tr>
    </thead>
    <tbody>
        <?php $departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll(); ?>
        <?php if (empty($departments)): ?>
            <tr><td class="text-center text-muted">Нет данных</td></tr>
        <?php else: ?>
            <?php foreach ($departments as $d): ?>
                <tr>
                    <td>
                        <a href="?page=departments&edit_id=<?= $d['id'] ?>" class="text-decoration-none text-primary fw-bold">
                            <?= htmlspecialchars($d['name']) ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit_id'])):
    $id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->execute([$id]);
    $department = $stmt->fetch();

    if ($department): ?>
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">Редактировать или удалить отдел</div>
            <div class="card-body">
                <form method="post" onsubmit="return confirm('Сохранить изменения?')">
                    <input type="hidden" name="update_department" value="1">
                    <input type="hidden" name="id" value="<?= $department['id'] ?>">
                    <?php $data = $department; $errors = []; include __DIR__ . '/partials/department_form.php'; ?>
                    <button type="submit" class="btn btn-primary">Редактировать</button>
                    <a href="?page=departments" class="btn btn-secondary">Отмена</a>
                </form>

                <form method="post" onsubmit="return confirm('Удалить отдел? Это действие нельзя отменить.')">
                    <input type="hidden" name="delete_department" value="1">
                    <input type="hidden" name="id" value="<?= $department['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
if (isset($_POST['create_department'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: отдел с таким названием уже существует.';
        } else {
            $pdo->prepare("INSERT INTO departments (name) VALUES (?)")->execute([$name]);
            $_SESSION['message'] = 'Отдел успешно добавлен.';
        }
    }
    header("Location: ?page=departments");
    exit;
}

if (isset($_POST['update_department'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: отдел с таким названием уже существует.';
        } else {
            $pdo->prepare("UPDATE departments SET name = ? WHERE id = ?")->execute([$name, $id]);
            $_SESSION['message'] = 'Отдел успешно обновлён.';
        }
    }
    header("Location: ?page=departments");
    exit;
}

if (isset($_POST['delete_department'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE department_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Нельзя удалить отдел: он используется в анкетах у $count сотрудников.";
    } else {
        try {
            $pdo->prepare("DELETE FROM departments WHERE id = ?")->execute([$id]);
            $_SESSION['message'] = "Отдел успешно удалён.";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ошибка удаления: " . $e->getMessage();
        }
    }
    header("Location: ?page=departments");
    exit;
}
?>