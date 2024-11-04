<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__. '/../models/User.php';

class AuthController {
    private $user;
    private $session;

    public function __construct($user, $session) {
        $this->user = $user;
        $this->session = $session;
    }

    public function registerPage() {
        include __DIR__. '/../views/auth/register.php';
    }

    public function register() {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'] ?? 'User';
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->user->create($username, $hashedPassword, $role);
        header('Location: /auth/login');
    }

    // Show login form
    public function loginPage() {
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Login User
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

    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /auth/login");
    }
}
?>