<?php include_once __DIR__ . '/../common/header.php'; ?>
<div class="container mt-4">
    <h1 class="text-center mb-4">Register</h1>

    <form method="POST" action="/auth/register" class="auth-form">
        <?php include_once __DIR__ . '/../common/error.php'; ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select name="role" id="role" class="form-select">
                <option value="User">User</option>
                <option value="Admin">Admin</option>
            </select>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>

    <div class="d-flex justify-content-center gap-1">
        <p class="mt-4 text-center text-muted fs-6">
            Already have an account? <a href="/auth/login" class="text-decoration-none">Login</a>
        </p>
    </div>
</div>
<?php include_once __DIR__ . '/../common/footer.php'; ?>
