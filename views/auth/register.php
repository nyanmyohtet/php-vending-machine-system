<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <form action="/auth/register" method="POST">
        <label>Username: </label><input type="text" name="username" required><br>
        <label>Password: </label><input type="password" name="password" required><br>
        <!-- <label>Role: </label>
        <select name="role">
            <option value="User">User</option>
            <option value="Admin">Admin</option>
        </select><br> -->
        <button type="submit">Register</button>
    </form>
</body>
</html>
