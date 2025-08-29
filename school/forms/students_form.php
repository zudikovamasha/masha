<?php if (!empty($errors)): ?>
    <div id="error-alert" class="alert alert-danger alert-dismissible fade show" style="cursor: pointer;">
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
        <label class="form-label">ФИО ребенка *</label>
        <input type="text" name="fio_kids" class="form-control <?= isset($errors['fio_kids']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['fio_kids'] ?? '') ?>" required>
        <?php if (isset($errors['fio_kids'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['fio_kids']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
    <label class="form-label">Возраст *</label>
    <input type="number" name="years" class="form-control <?= isset($errors['years']) ? 'is-invalid' : '' ?>"
           value="<?= htmlspecialchars($data['years'] ?? '') ?>" min="6" max="18" required>
    <?php if (isset($errors['years'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['years']) ?></div>
    <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Группа *</label>
        <select name="classes_id" class="form-select <?= isset($errors['classes_id']) ? 'is-invalid' : '' ?>" required>
            <option value="">Выберите группу</option>
            <?php
            $group = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll();
            foreach ($group as $g): ?>
                <option value="<?= $g['id'] ?>" <?= ($data['classes_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['classes_id'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['classes_id']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Программа обучения *</label>
        <select name="study_program_id" class="form-select <?= isset($errors['study_program_id']) ? 'is-invalid' : '' ?>" required>
            <option value="">Выберите программу обучения</option>
            <?php
            $study_program = $pdo->query("SELECT * FROM study_program ORDER BY name")->fetchAll();
            foreach ($study_program as $sp): ?>
                <option value="<?= $sp['id'] ?>" <?= ($data['study_program_id'] ?? '') == $sp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sp['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['study_program_id'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['study_program_id']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">ФИО родителя *</label>
        <input type="text" name="fio_parent" class="form-control <?= isset($errors['fio_parent']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['fio_parent'] ?? '') ?>" required>
        <?php if (isset($errors['fio_parent'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['fio_parent']) ?></div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
    <label class="form-label">Телефон *</label>
    <input type="tel" name="phone" 
           class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
           value="<?= htmlspecialchars($data['phone'] ?? '') ?>" 
           placeholder="+7 999 999-99-99" required>
    <?php if (isset($errors['phone'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone']) ?></div>
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
        <label class="form-label">Цель обучения *</label>
        <input type="text" name="waitings" class="form-control <?= isset($errors['waitings']) ? 'is-invalid' : '' ?>"
               value="<?= htmlspecialchars($data['waitings'] ?? '') ?>" required>
        <?php if (isset($errors['waitings'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['waitings']) ?></div>
        <?php endif; ?>
    </div>
</div>

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