<?php
require_once 'config/db.php';

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<div class="mb-4">
    <a href="?page=group&add=1" class="btn btn-success btn-sm">Добавить группу</a>
</div>

<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'успешно') !== false ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить группу</div>
    <div class="card-body">
        <?php $data = ['name' => '']; $errors = []; ?>
        <form method="post">
            <input type="hidden" name="create_group" value="1">
            <?php include __DIR__ . '/forms/group_form.php'; ?>
            <button type="submit" class="btn btn-success">Сохранить</button>
            <a href="?page=group" class="btn btn-secondary">Отмена</a>
        </form>
    </div>
</div>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Название группы</th>
        </tr>
    </thead>
    <tbody>
        <?php $group = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll(); ?>
        <?php if (empty($group)): ?>
            <tr><td class="text-center text-muted">Нет данных</td></tr>
        <?php else: ?>
            <?php foreach ($group as $g): ?>
                <tr>
                    <td>
                        <a href="?page=group$group&edit_id=<?= $g['id'] ?>" class="text-decoration-none text-primary fw-bold">
                            <?= htmlspecialchars($g['name']) ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit_id'])):
    $id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    $group = $stmt->fetch();

    if ($group): ?>
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">Редактировать или удалить группу</div>
            <div class="card-body">
                <form method="post" onsubmit="return confirm('Сохранить изменения?')">
                    <input type="hidden" name="update_group" value="1">
                    <input type="hidden" name="id" value="<?= $group['id'] ?>">
                    <?php $data = $group; $errors = []; include __DIR__ . '/forms/group_form.php'; ?>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="?page=group$group" class="btn btn-secondary">Отмена</a>
                </form>

                <form method="post" onsubmit="return confirm('Удалить группу? Это действие нельзя отменить.')">
                    <input type="hidden" name="delete_group" value="1">
                    <input type="hidden" name="id" value="<?= $group['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
if (isset($_POST['create_group'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: введите название группы.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: группа с таким названием уже существует.';
        } else {
            $pdo->prepare("INSERT INTO classes (name) VALUES (?)")->execute([$name]);
            $_SESSION['message'] = 'Группа успешно добавлена.';
        }
    }
    header("Location: ?page=group$group");
    exit;
}

if (isset($_POST['update_group'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    if (empty($name)) {
        $_SESSION['message'] = 'Ошибка: введите название группы.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: группа с таким названием уже существует.';
        } else {
            $pdo->prepare("UPDATE classes SET name = ? WHERE id = ?")->execute([$name, $id]);
            $_SESSION['message'] = 'Группа успешно обновлена.';
        }
    }
    header("Location: ?page=group$group");
    exit;
}

if (isset($_POST['delete_group'])) {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE position_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $_SESSION['message'] = "Нельзя удалить группу: она используется в анкетах у $count учеников.";
    } else {
        try {
            $pdo->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
            $_SESSION['message'] = "Группа успешно удалена.";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Ошибка удаления: " . $e->getMessage();
        }
    }
    header("Location: ?page=group$group");
    exit;
}
?>