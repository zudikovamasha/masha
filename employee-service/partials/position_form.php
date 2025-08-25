<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="mb-3">
    <label>Название *</label>
    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
           value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
    <?php if (isset($errors['name'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
    <?php endif; ?>
</div>