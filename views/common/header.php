<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vending Machine System</title>
    
    <!-- Local Bootstrap CSS -->
    <link href="/public/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS (if needed) -->
    <!-- <link rel="stylesheet" href="/public/assets/css/style.css"> -->
</head>
<body>
    <header class="bg-light p-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4">Vending Machine System</h1>
            <?php if (isset($this->session['user_id'])): ?>
                <form action="/auth/logout" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            <?php else: ?>
                <a href="/auth/login" class="btn btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </header>
