<?php require_once 'config/db.php'; ?>

<div class="mb-4">
    <a href="?page=positions&add=1" class="btn btn-success btn-sm">+ Добавить должность</a>
</div>

<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить должность</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="create_position" value="1">
            <?php $data = ['name' => '']; $errors = []; include 'partials/position_form.php'; ?>
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
                    <?php $data = $position; $errors = []; include 'partials/position_form.php'; ?>
                    <button type="submit" class="btn btn-primary">Редактировать</button>
                    <a href="?page=positions" class="btn btn-secondary">Отмена</a>
                </form>

                <form method="post" onsubmit="return confirm('Удалить должность?')">
                    <input type="hidden" name="delete_position" value="1">
                    <input type="hidden" name="id" value="<?= $position['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php endif;
endif; ?>

<?php
if (isset($_POST['create_position'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM positions WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $errors['name'] = 'Должность с таким названием уже существует.';
        } else {
            $pdo->prepare("INSERT INTO positions (name) VALUES (?)")->execute([$name]);
            header("Location: ?page=positions");
            exit;
        }
    }
}

if (isset($_POST['update_position'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM positions WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $errors['name'] = 'Должность с таким названием уже существует.';
        } else {
            $pdo->prepare("UPDATE positions SET name = ? WHERE id = ?")->execute([$name, $id]);
            header("Location: ?page=positions");
            exit;
        }
    }
}

if (isset($_POST['delete_position'])) {
    $id = (int)$_POST['id'];
    try {
        $pdo->prepare("DELETE FROM positions WHERE id = ?")->execute([$id]);
    } catch (PDOException $e) {}
    header("Location: ?page=positions");
    exit;
}
?>