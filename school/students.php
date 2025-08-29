<?php
require_once 'config/db.php';

// === ОБРАБОТКА ФОРМ — ДО ЛЮБОГО ВЫВОДА ===

// Добавление сотрудника
if (isset($_POST['create_students'])) {
    $data = [
        'fio_kids' => trim($_POST['fio_kids'] ?? ''),
        'years' => $_POST['years'] ?? '',
        'classes_id' => $_POST['classes_id'] ?? '',
        'study_program_id' => $_POST['study_program_id'] ?? '',
        'fio_parent' => trim($_POST['fio_parent'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'waitings' => trim($_POST['waitings'] ?? ''),
    ];
    $errors = [];

    if (empty($data['fio_kids'])) $errors['fio_kids'] = 'ФИО ученика обязательно.';
    if (empty($data['years'])) $errors['years'] = 'Возраст обязателен.';
    if (empty($data['classes_id']) || !is_numeric($data['classes_id'])) {
        $errors['classes_id'] = 'Выберите корректную группу.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ?");
        $stmt->execute([(int)$data['classes_id']]);
        if (!$stmt->fetch()) $errors['classes_id'] = 'Указанная группа не существует.';
    }
    if (empty($data['study_program_id']) || !is_numeric($data['study_program_id'])) {
        $errors['study_program_id'] = 'Выберите корректную программу обучения.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM study_program WHERE id = ?");
        $stmt->execute([(int)$data['study_program_id']]);
        if (!$stmt->fetch()) $errors['study_program_id'] = 'Указанная программа обучения не существует.';
    }
    if (empty($data['fio_parent'])) $errors['fio_parent'] = 'ФИО родителя обязательно.';
    if (empty($data['phone'])) $errors['phone'] = 'Телефон обязателен.';
    if (empty($data['email'])) {
        $errors['email'] = 'Email обязателен.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) $errors['email'] = 'Email уже используется.';
    }
    if (empty($data['waitings'])) $errors['waitings'] = 'Цель обучения обязательна.';

    if (empty($errors)) {
        $pdo->prepare("INSERT INTO students (fio_kids, years, classes_id, study_program_id, fio_parent, phone, email, waitings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$data['fio_kids'], $data['years'], $data['classes_id'], $data['study_program_id'], $data['fio_parent'], $data['phone'], $data['email'], $data['waitings']]);
        $_SESSION['message'] = 'Ученик успешно добавлен.';
        header("Location: ?page=students");
        exit;
    } else {
        $_SESSION['message'] = 'Исправьте ошибки в форме.';
        $_SESSION['form_data'] = $data;
        $_SESSION['form_errors'] = $errors;
        header("Location: ?page=students&add=1");
        exit;
    }
}

// Обновление сотрудника
if (isset($_POST['update_students'])) {
    $id = (int)$_POST['id'];
    $data = [
        'fio_kids' => trim($_POST['fio_kids'] ?? ''),
        'years' => $_POST['years'] ?? '',
        'classes_id' => $_POST['classes_id'] ?? '',
        'study_program_id' => $_POST['study_program_id'] ?? '',
        'fio_parent' => trim($_POST['fio_parent'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'waitings' => trim($_POST['waitings'] ?? ''),
    ];
    $errors = [];

    if (empty($data['fio_kids'])) $errors['fio_kids'] = 'ФИО ученика обязательно.';
    if (empty($data['years'])) $errors['years'] = 'Возраст обязателен.';
    if (empty($data['classes_id']) || !is_numeric($data['classes_id'])) {
        $errors['classes_id'] = 'Выберите корректную группу.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ?");
        $stmt->execute([(int)$data['classes_id']]);
        if (!$stmt->fetch()) $errors['classes_id'] = 'Указанная группа не существует.';
    }
    if (empty($data['study_program_id']) || !is_numeric($data['study_program_id'])) {
        $errors['study_program_id'] = 'Выберите корректную программу обучения.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM study_program WHERE id = ?");
        $stmt->execute([(int)$data['study_program_id']]);
        if (!$stmt->fetch()) $errors['study_program_id'] = 'Указанная программа обучения не существует.';
    }
    if (empty($data['fio_parent'])) $errors['fio_parent'] = 'ФИО родителя обязательно.';
    if (empty($data['phone'])) $errors['phone'] = 'Телефон обязателен.';
    if (empty($data['email'])) {
        $errors['email'] = 'Email обязателен.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Некорректный email.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) $errors['email'] = 'Email уже используется.';
    }
    if (empty($data['waitings'])) $errors['waitings'] = 'Цель обучения обязательна.';

    if (empty($errors)) {
        $pdo->prepare("UPDATE students SET fio_kids=?, years=?, classes_id=?, study_program_id=?, fio_parent=?, phone=?, email=?, waitings=? WHERE id=?")
            ->execute([$data['fio_kids'], $data['years'], $data['classes_id'], $data['study_program_id'], $data['fio_parent'], $data['phone'], $data['email'], $data['waitings'], $id]);
        $_SESSION['message'] = 'Ученик успешно обновлён.';
        header("Location: ?page=students");
        exit;
    } else {
        $_SESSION['message'] = 'Исправьте ошибки в форме.';
        $_SESSION['form_data'] = $data;
        $_SESSION['form_errors'] = $errors;
        header("Location: ?page=students&edit_id=$id");
        exit;
    }
}

// Удаление сотрудника
if (isset($_POST['delete_students'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = 'Ученик успешно удалён.';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Ошибка удаления: ' . $e->getMessage();
    }
    header("Location: ?page=students");
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
?>

<!-- Кнопки действий -->
<div class="mb-4 d-flex gap-2">
    <a href="?page=students&add=1" class="btn btn-success btn-m">Добавить ученика</a>
    <a href="?page=group&add=1" class="btn btn-outline-primary btn-m">Добавить группу</a>
    <a href="?page=program&add=1" class="btn btn-outline-primary btn-m">Добавить программу обучения</a>
</div>

<!-- Вывод уведомления -->
<?php if ($message): ?>
    <div class="alert <?= strpos($message, 'успешно') !== false || strpos($message, 'удалён') !== false ? 'alert-success' : 'alert-danger' ?>"
        onclick="this.remove()"
        style="cursor: pointer;"
        role="alert">
    </div>>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Форма добавления -->
<?php if (isset($_GET['add'])): ?>
<div class="card mb-4">
    <div class="card-header">Добавить ученика</div>
    <div class="card-body">
        <?php 
        $data = [
            'fio_kids' => $form_data['fio_kids'] ?? '',
            'years' => $form_data['years'] ?? '',
            'classes_id' => $form_data['classes_id'] ?? '',
            'study_program_id' => $form_data['study_program_id'] ?? '',
            'fio_parent' => $form_data['fio_parent'] ?? '',
            'phone' => $form_data['phone'] ?? '',
            'email' => $form_data['email'] ?? '',
            'waitings' => $form_data['waitings'] ?? '',
        ];
        $errors = $form_errors;
        ?>
        <form method="post">
            <input type="hidden" name="create_students" value="1">
            <?php include __DIR__ . '/forms/students_form.php'; ?>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">Сохранить</button>
                <a href="?page=students" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Фильтрация -->
<form method="GET" class="mb-4">
    <input type="hidden" name="page" value="students">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Поиск по ФИО ученика или ФИО родителя" 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <select name="group_id" class="form-select">
                <option value="">Все группы</option>
                <?php
                $group = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
                foreach ($group as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= ($g['id'] == ($_GET['classes_id'] ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="study_program_id" class="form-select">
                <option value="">Все программы обучения</option>
                <?php
                $study_program = $pdo->query("SELECT * FROM study_program ORDER BY name")->fetchAll();
                foreach ($study_program as $sp): ?>
                    <option value="<?= $sp['id'] ?>" <?= ($sp['id'] == ($_GET['study_program_id'] ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sp['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Фильтр</button>
        </div>
    </div>
</form>

<!-- Сортировка -->
<div class="mb-3">
    <div class="btn-group btn-group-m">
        <?php
        // Функция для генерации ссылки сортировки
        function sortLink($field, $label, $current_order) {
            $params = $_GET;
            $params['page'] = 'students';
            $current_sort = $_GET['sort'] ?? 'fio_kids';
            $current_order = strtoupper($_GET['order'] ?? 'ASC');

            if ($current_sort === $field) {
                // Переключаем направление
                $new_order = $current_order === 'ASC' ? 'DESC' : 'ASC';
            } else {
                // Новое поле — сортируем по возрастанию
                $new_order = 'ASC';
            }

            $params['sort'] = $field;
            $params['order'] = $new_order;

            $icon = '';
            if ($current_sort === $field) {
                $icon = $new_order === 'ASC' ? ' ↑' : ' ↓';
            }

            $url = '?' . http_build_query($params);
            return "<a href='$url' class='btn btn-outline-secondary'>$label$icon</a>";
        }
        ?>
        <?= sortLink('fio_kids', 'ФИО ученика', 'ASC') ?>
        <?= sortLink('years', 'Возраст', 'ASC') ?>
        <?= sortLink('created_at', 'Дата создания', 'ASC') ?>
    </div>
</div>


<!-- Таблица сотрудников -->
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ФИО ученика</th>
            <th>Возраст</th>
            <th>Группа</th>
            <th>Программа обучения</th>
            <th>ФИО родителя</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Цель обучения</th>
            <th>Дата создания</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $search = $_GET['search'] ?? '';
        $dept_filter = $_GET['classes_id'] ?? '';
        $pos_filter = $_GET['study_program_id'] ?? '';
        $sort = in_array($_GET['sort'] ?? '', ['fio_kids', 'years', 'created_at']) ? $_GET['sort'] : 'fio_kids';
        $order = strtoupper($_GET['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

        $sql = "SELECT s.*, c.name as classes_name, sp.name as study_program_name 
                FROM students s
                LEFT JOIN classes c ON s.classes_id = c.id
                LEFT JOIN study_program sp ON s.study_program_id = sp.id
                WHERE 1=1";

        $params = [];

        if ($search) {
            $sql .= " AND (s.fio_kids ILIKE :search OR s.fio_parent ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        if ($dept_filter) {
            $sql .= " AND s.classes_id = :classes_id";
            $params[':classes_id'] = $dept_filter;
        }
        if ($pos_filter) {
            $sql .= " AND s.study_program_id = :study_program_id";
            $params[':study_program_id'] = $pos_filter;
        }

        $sql .= " ORDER BY $sort $order";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        if (empty($students)): ?>
            <tr><td colspan="9" class="text-center text-muted">Нет данных</td></tr>
        <?php else:
            foreach ($students as $s): ?>
                <tr>
                    <td>
                        <a href="?page=students&edit_id=<?= $s['id'] ?>" class="text-decoration-none text-primary fw-bold">
                            <?= htmlspecialchars($s['fio_kids']) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($s['years']) ?></td>
                    <td><?= htmlspecialchars($s['classes_name']) ?></td>
                    <td><?= htmlspecialchars($s['study_program_name']) ?></td>
                    <td><?= htmlspecialchars($s['fio_parent']) ?></td>
                    <td><?= htmlspecialchars($s['phone']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['waitings']) ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($s['created_at'])) ?></td>
                </tr>
            <?php endforeach;
        endif; ?>
    </tbody>
</table>

<!-- Форма редактирования/удаления -->
<?php if (isset($_GET['edit_id'])):
    $id = (int)$_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $students = $stmt->fetch();

    if ($students):
        // Восстанавливаем данные и ошибки
        $data = $_SESSION['form_data'] ?? $students;
        $errors = $_SESSION['form_errors'] ?? [];
    ?>
        <div class="card mt-4 border-success">
            <div class="card-header bg-warning text-dark">Редактировать или удалить сотрудника</div>
            <div class="card-body">
                <form method="post" onsubmit="return confirm('Сохранить изменения?')">
                    <input type="hidden" name="update_students" value="1">
                    <input type="hidden" name="id" value="<?= $students['id'] ?>">
                    <?php include __DIR__ . '/foms/students_form.php'; ?>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        <a href="?page=students" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>

                <form method="post" onsubmit="return confirm('Удалить этого сотрудника? Это действие нельзя отменить.')">
                    <input type="hidden" name="delete_students" value="1">
                    <input type="hidden" name="id" value="<?= $students['id'] ?>">
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div id="error-alert" class="alert alert-danger alert-dismissible fade show" style="cursor: pointer;">Ученик не найден.</div>
    <?php endif; ?>
<?php endif; ?>