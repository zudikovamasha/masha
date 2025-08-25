<?php
// Подключаем БД
require_once 'config/db.php';

// === ОБРАБОТКА ФОРМ — ДО ЛЮБОГО ВЫВОДА ===

// Добавление сотрудника
if (isset($_POST['create_employee'])) {
    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'birth_date' => $_POST['birth_date'] ?? '',
        'position_id' => $_POST['position_id'] ?? '',
        'department_id' => $_POST['department_id'] ?? ''
    ];
    $errors = [];

    if (empty($data['first_name'])) $errors['first_name'] = 'Имя обязательно.';
    if (empty($data['last_name'])) $errors['last_name'] = 'Фамилия обязательна.';
    if (empty($data['email'])) {
        $errors['email'] = 'Email обязателен.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM employees WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) $errors['email'] = 'Email уже используется.';
    }
    if (empty($data['birth_date'])) {
        $errors['birth_date'] = 'Дата рождения обязательна.';
    } else {
        $birth_date = new DateTime($data['birth_date']);
        $today = new DateTime();
        if ($birth_date > $today) $errors['birth_date'] = 'Дата рождения не может быть в будущем.';
    }
    if (empty($data['position_id']) || !is_numeric($data['position_id'])) {
        $errors['position_id'] = 'Выберите корректную должность.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM positions WHERE id = ?");
        $stmt->execute([(int)$data['position_id']]);
        if (!$stmt->fetch()) $errors['position_id'] = 'Указанная должность не существует.';
    }
    if (empty($data['department_id']) || !is_numeric($data['department_id'])) {
        $errors['department_id'] = 'Выберите корректный отдел.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE id = ?");
        $stmt->execute([(int)$data['department_id']]);
        if (!$stmt->fetch()) $errors['department_id'] = 'Указанный отдел не существует.';
    }

    if (empty($errors)) {
        $pdo->prepare("INSERT INTO employees (first_name, last_name, email, birth_date, position_id, department_id) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$data['first_name'], $data['last_name'], $data['email'], $data['birth_date'], $data['position_id'], $data['department_id']]);
        $_SESSION['message'] = 'Сотрудник успешно добавлен.';
        header("Location: ?page=employees");
        exit;
    } else {
        $_SESSION['message'] = 'Исправьте ошибки в форме.';
        $_SESSION['form_data'] = $data;
        $_SESSION['form_errors'] = $errors;
        header("Location: ?page=employees&add=1");
        exit;
    }
}

// Обновление сотрудника
if (isset($_POST['update_employee'])) {
    $id = (int)$_POST['id'];
    $data = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'birth_date' => $_POST['birth_date'] ?? '',
        'position_id' => $_POST['position_id'] ?? '',
        'department_id' => $_POST['department_id'] ?? ''
    ];
    $errors = [];

    if (empty($data['first_name'])) $errors['first_name'] = 'Имя обязательно.';
    if (empty($data['last_name'])) $errors['last_name'] = 'Фамилия обязательна.';
    if (empty($data['email'])) {
        $errors['email'] = 'Email обязателен.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM employees WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $id]);
        if ($stmt->fetch()) $errors['email'] = 'Email уже используется.';
    }
    if (empty($data['birth_date'])) {
        $errors['birth_date'] = 'Дата рождения обязательна.';
    } else {
        $birth_date = new DateTime($data['birth_date']);
        $today = new DateTime();
        if ($birth_date > $today) $errors['birth_date'] = 'Дата рождения не может быть в будущем.';
    }
    if (empty($data['position_id']) || !is_numeric($data['position_id'])) {
        $errors['position_id'] = 'Выберите корректную должность.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM positions WHERE id = ?");
        $stmt->execute([(int)$data['position_id']]);
        if (!$stmt->fetch()) $errors['position_id'] = 'Указанная должность не существует.';
    }
    if (empty($data['department_id']) || !is_numeric($data['department_id'])) {
        $errors['department_id'] = 'Выберите корректный отдел.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM departments WHERE id = ?");
        $stmt->execute([(int)$data['department_id']]);
        if (!$stmt->fetch()) $errors['department_id'] = 'Указанный отдел не существует.';
    }

    if (empty($errors)) {
        $pdo->prepare("UPDATE employees SET first_name=?, last_name=?, email=?, birth_date=?, position_id=?, department_id=? WHERE id=?")
            ->execute([$data['first_name'], $data['last_name'], $data['email'], $data['birth_date'], $data['position_id'], $data['department_id'], $id]);
        $_SESSION['message'] = 'Сотрудник успешно обновлён.';
        header("Location: ?page=employees");
        exit;
    } else {
        $_SESSION['message'] = 'Исправьте ошибки в форме.';
        $_SESSION['form_data'] = $data;
        $_SESSION['form_errors'] = $errors;
        header("Location: ?page=employees&edit_id=$id");
        exit;
    }
}

// Удаление сотрудника
if (isset($_POST['delete_employee'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Сотрудник успешно удалён.';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Ошибка удаления: ' . $e->getMessage();
    }
    header("Location: ?page=employees");
    exit;
}

// === КОНЕЦ ОБРАБОТКИ ФОРМ ===

// Получаем сообщение из сессии
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Восстанавливаем данные формы при ошибке
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);

// Инициализируем $data и $errors
$data = $form_data;
$errors = $form_errors;
?>

<!-- Кнопки действий -->
<div class="mb-4 d-flex gap-2">
    <a href="?page=employees&add=1" class="btn btn-success btn-sm">+ Добавить сотрудника</a>
    <a href="?page=positions&add=1" class="btn btn-outline-primary btn-sm">+ Добавить должность</a>
    <a href="?page=departments&add=1" class="btn btn-outline-primary btn-sm">+ Добавить отдел</a>
</div>

<!-- Вывод уведомления -->
<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'успешно') !== false || strpos($message, 'удалён') !== false ? 'alert-success' : 'alert-danger' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Форма добавления -->
<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить сотрудника</div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="create_employee" value="1">
            <?php include __DIR__ . '/partials/employee_form.php'; ?>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="?page=employees" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Фильтрация -->
<form method="GET" class="mb-4">
    <input type="hidden" name="page" value="employees">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Поиск по ФИО или email" 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="department_id" class="form-select">
                <option value="">Все отделы</option>
                <?php
                $departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
                foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= ($d['id'] == ($_GET['department_id'] ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="position_id" class="form-select">
                <option value="">Все должности</option>
                <?php
                $positions = $pdo->query("SELECT * FROM positions ORDER BY name")->fetchAll();
                foreach ($positions as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= ($p['id'] == ($_GET['position_id'] ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Фильтр</button>
        </div>
    </div>
</form>

<!-- Сортировка -->
<div class="mb-3">
    <div class="btn-group btn-group-sm">
        <a href="?page=employees&sort=last_name&order=<?= ($_GET['sort'] ?? '') === 'last_name' && ($_GET['order'] ?? '') === 'ASC' ? 'DESC' : 'ASC' ?>"
           class="btn btn-outline-secondary">Фамилия</a>
        <a href="?page=employees&sort=birth_date&order=<?= ($_GET['sort'] ?? '') === 'birth_date' && ($_GET['order'] ?? '') === 'ASC' ? 'DESC' : 'ASC' ?>"
           class="btn btn-outline-secondary">Дата рождения</a>
        <a href="?page=employees&sort=created_at&order=<?= ($_GET['sort'] ?? '') === 'created_at' && ($_GET['order'] ?? '') === 'ASC' ? 'DESC' : 'ASC' ?>"
           class="btn btn-outline-secondary">Дата создания</a>
    </div>
</div>

<!-- Таблица сотрудников -->
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ФИО</th>
            <th>Email</th>
            <th>Дата рождения</th>
            <th>Должность</th>
            <th>Отдел</th>
            <th>Дата создания</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $search = $_GET['search'] ?? '';
        $dept_filter = $_GET['department_id'] ?? '';
        $pos_filter = $_GET['position_id'] ?? '';
        $sort = in_array($_GET['sort'] ?? '', ['last_name', 'birth_date', 'created_at']) ? $_GET['sort'] : 'last_name';
        $order = strtoupper($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT e.*, d.name as department_name, p.name as position_name 
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                LEFT JOIN positions p ON e.position_id = p.id
                WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND (e.first_name ILIKE :search OR e.last_name ILIKE :search OR e.email ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($dept_filter) {
            $sql .= " AND e.department_id = :department_id";
            $params[':department_id'] = $dept_filter;
        }
        if ($pos_filter) {
            $sql .= " AND e.position_id = :position_id";
            $params[':position_id'] = $pos_filter;
        }

        $sql .= " ORDER BY $sort $order";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $employees = $stmt->fetchAll();

        if (empty($employees)): ?>
            <tr><td colspan="6" class="text-center text-muted">Нет данных</td></tr>
        <?php else:
            foreach ($employees as $e): ?>
                <tr>
                    <td>
                        <a href="?page=employees&edit_id=<?= $e['id'] ?>" class="text-decoration-none text-primary fw-bold">
                            <?= htmlspecialchars($e['last_name'] . ' ' . $e['first_name']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($e['email']) ?></td>
                    <td><?= $e['birth_date'] ?></td>
                    <td><?= htmlspecialchars($e['position_name']) ?></td>
                    <td><?= htmlspecialchars($e['department_name']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($e['created_at'])) ?></td>
                </tr>
            <?php endforeach;
        endif; ?>
    </tbody>
</table>

<!-- Форма редактирования/удаления -->
<?php if (isset($_GET['edit_id'])):
    $id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $employee = $stmt->fetch();

    if ($employee):
        // При редактировании используем данные из БД, но если были ошибки — из сессии
        $data = $_SESSION['form_data'] ?? $employee;
        $errors = $_SESSION['form_errors'] ?? [];
    ?>
        <div class="card mt-4 border-warning">
            <div class="card-header bg-warning text-dark">Редактировать или удалить сотрудника</div>
            <div class="card-body">
                <form method="post" onsubmit="return confirm('Сохранить изменения?')">
                    <input type="hidden" name="update_employee" value="1">
                    <input type="hidden" name="id" value="<?= $employee['id'] ?>">
                    <?php include __DIR__ . '/partials/employee_form.php'; ?>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Редактировать</button>
                        <a href="?page=employees" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>

                <form method="post" onsubmit="return confirm('Удалить этого сотрудника? Это действие нельзя отменить.')">
                    <input type="hidden" name="delete_employee" value="1">
                    <input type="hidden" name="id" value="<?= $employee['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">Сотрудник не найден.</div>
    <?php endif; ?>
<?php endif; ?>