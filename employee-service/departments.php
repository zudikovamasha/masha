<?php require_once 'config/db.php'; ?>

<div class="mb-4">
    <a href="?page=departments&add=1" class="btn btn-success btn-sm">+ Добавить отдел</a>
</div>

<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить отдел</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="create_department" value="1">
            <?php $data = ['name' => '']; $errors = []; include 'partials/department_form.php'; ?>
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
                    <?php $data = $department; $errors = []; include 'partials/department_form.php'; ?>
                    <button type="submit" class="btn btn-primary">Редактировать</button>
                    <a href="?page=departments" class="btn btn-secondary">Отмена</a>
                </form>

                <form method="post" onsubmit="return confirm('Удалить отдел?')">
                    <input type="hidden" name="delete_department" value="1">
                    <input type="hidden" name="id" value="<?= $department['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php endif;
endif; ?>

<?php
if (isset($_POST['create_department'])) {
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $errors['name'] = 'Отдел с таким названием уже существует.';
        } else {
            $pdo->prepare("INSERT INTO departments (name) VALUES (?)")->execute([$name]);
            header("Location: ?page=departments");
            exit;
        }
    }
}

if (isset($_POST['update_department'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    if (empty($name)) {
        $errors['name'] = 'Название обязательно.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $errors['name'] = 'Отдел с таким названием уже существует.';
        } else {
            $pdo->prepare("UPDATE departments SET name = ? WHERE id = ?")->execute([$name, $id]);
            header("Location: ?page=departments");
            exit;
        }
    }
}

if (isset($_POST['delete_department'])) {
    $id = (int)$_POST['id'];
    try {
        $pdo->prepare("DELETE FROM departments WHERE id = ?")->execute([$id]);
    } catch (PDOException $e) {}
    header("Location: ?page=departments");
    exit;
}
?>