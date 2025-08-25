<?php
require_once 'config/db.php';

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<div class="mb-4">
    <a href="?page=positions&add=1" class="btn btn-success btn-sm">+ Добавить должность</a>
</div>

<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'успешно') !== false ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить должность</div>
    <div class="card-body">
        <?php $data = ['name' => '']; $errors = []; ?>
        <form method="post">
            <input type="hidden" name="create_position" value="1">
            <?php include __DIR__ . '/partials/position_form.php'; ?>
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="?page=positions" class="btn btn-secondary">Отмена</a>
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
        <?php $positions = $pdo->query("SELECT * FROM positions ORDER BY name")->fetchAll(); ?>
        <?php if (empty($positions)): ?>
            <tr><td class="text-center text-muted">Нет данных</td></tr>
        <?php else: ?>
            <?php foreach ($positions as $p): ?>
                <tr>
                    <td>
                        <a href="?page=positions&edit_id=<?= $p['id'] ?>" class="text-decoration-none text-primary fw-bold">
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
    $stmt = $pdo->prepare("SELECT * FROM positions WHERE id = ?");
    $stmt->execute([$id]);
    $position = $stmt->fetch();

    if ($position): ?>
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">Редактировать или удалить должность</div>
            <div class="card-body">
                <form method="post" onsubmit="return confirm('Сохранить изменения?')">
                    <input type="hidden" name="update_position" value="1">
                    <input type="hidden" name="id" value="<?= $position['id'] ?>">
                    <?php $data = $position; $errors = []; include __DIR__ . '/partials/position_form.php'; ?>
                    <button type="submit" class="btn btn-primary">Редактировать</button>
                    <a href="?page=positions" class="btn btn-secondary">Отмена</a>
                </form>

                <form method="post" onsubmit="return confirm('Удалить должность? Это действие нельзя отменить.')">
                    <input type="hidden" name="delete_position" value="1">
                    <input type="hidden" name="id" value="<?= $position['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
if (isset($_POST['create_position'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM positions WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: должность с таким названием уже существует.';
        } else {
            $pdo->prepare("INSERT INTO positions (name) VALUES (?)")->execute([$name]);
            $_SESSION['message'] = 'Должность успешно добавлена.';
        }
    }
    header("Location: ?page=positions");
    exit;
}

if (isset($_POST['update_position'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM positions WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: должность с таким названием уже существует.';
        } else {
            $pdo->prepare("UPDATE positions SET name = ? WHERE id = ?")->execute([$name, $id]);
            $_SESSION['message'] = 'Должность успешно обновлена.';
        }
    }
    header("Location: ?page=positions");
    exit;
}

if (isset($_POST['delete_position'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE position_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Нельзя удалить должность: она используется в анкетах у $count сотрудников.";
    } else {
        try {
            $pdo->prepare("DELETE FROM positions WHERE id = ?")->execute([$id]);
            $_SESSION['message'] = "Должность успешно удалена.";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ошибка удаления: " . $e->getMessage();
        }
    }
    header("Location: ?page=positions");
    exit;
}
?>