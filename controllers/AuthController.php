<?php

class AuthController {
    private $user;
    private $session;

    public function __construct($user, $session) {
        $this->user = $user;
        $this->session = $session;
    }

    /**
     * Show Register Page
     */
    public function registerPage() {
        include __DIR__. '/../views/auth/register.php';
    }

    /**
     * Register new User
     */
    public function register() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'] ?? 'User';

        // validate username duplicate
        $user = $this->user->getByUserName($username);
        if ($user) {
            $this->errors[] = "Username already exist.";
            include __DIR__ . '/../views/auth/register.php';
            return;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->user->create($username, $hashedPassword, $role);
        header('Location: /auth/login');
    }

    /**
     * Show Login Page
     */
    public function loginPage() {
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Login existing User
     */
    public function login() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = $this->user->getByUserName($username);

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: /products");
        } else {
            $this->errors[] = "Invalid username or password.";
            include __DIR__ . '/../views/auth/login.php';
            return;
        }
    }

    /**
     * Logout User
     */
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /auth/login");
    }
}
?>