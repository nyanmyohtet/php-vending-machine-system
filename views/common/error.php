<?php if (isset($errors) && count($errors) > 0): ?>
    <div class="alert alert-danger">
        <h4 class="alert-heading">Errors:</h4>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>