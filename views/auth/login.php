<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-center mb-4">Login</h1>

    <form method="POST" action="/auth/login">
        <?php include_once __DIR__ . '/../common/error.php'; ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>
<?php include_once __DIR__ . '/../common/footer.php'; ?>