<?php if (!empty($errors)): ?>
    <div id="error-alert" class="alert alert-danger alert-dismissible fade show" style="cursor: pointer;">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="mb-3">
    <label for="name">Название программы обучения *</label>
    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
           value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
    <?php if (isset($errors['name'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
    <?php endif; ?>
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