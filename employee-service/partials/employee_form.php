<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Исправьте ошибки:</strong>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Имя *</label>
        <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
        <?php if (isset($errors['first_name'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['first_name']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Фамилия *</label>
        <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
        <?php if (isset($errors['last_name'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['last_name']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Email *</label>
        <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
        <?php if (isset($errors['email'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Дата рождения *</label>
        <input type="date" name="birth_date" class="form-control <?= isset($errors['birth_date']) ? 'is-invalid' : '' ?>"
               value="<?= $data['birth_date'] ?? '' ?>" required>
        <?php if (isset($errors['birth_date'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['birth_date']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Должность *</label>
        <select name="position_id" class="form-select <?= isset($errors['position_id']) ? 'is-invalid' : '' ?>" required>
            <option value="">Выберите</option>
            <?php
            $positions = $pdo->query("SELECT * FROM positions ORDER BY name")->fetchAll();
            foreach ($positions as $p): ?>
                <option value="<?= $p['id'] ?>" <?= ($data['position_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['position_id'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['position_id']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Отдел *</label>
        <select name="department_id" class="form-select <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>" required>
            <option value="">Выберите</option>
            <?php
            $departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll();
            foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($data['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['department_id'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['department_id']) ?></div>
        <?php endif; ?>
    </div>
</div>