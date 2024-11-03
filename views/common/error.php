<?php if (isset($this->errors) && count($this->errors) > 0): ?>
    <div class="alert alert-danger">
        <h4 class="alert-heading">Errors:</h4>
        <ul>
            <?php foreach ($this->errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>