<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthApiController {
    private $userModel;
    private $secretKey = 'secret-key';  // Store securely, such as in environment variables

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);

        $user = $this->userModel->getByUsername($input['username']);
        if ($user && password_verify($input['password'], $user['password'])) {
            $payload = [
                'iss' => 'vending-machine-system',  // Issuer
                'sub' => $user['id'],               // Subject (user id)
                'role' => $user['role'],            // User role (Admin/User)
                'iat' => time(),                    // Issued at time
                'exp' => time() + 3600              // Expiration time (1 hour)
            ];
            $jwt = JWT::encode($payload, $this->secretKey, 'HS256');
            return $this->jsonResponse(['token' => $jwt]);
        } else {
            return $this->jsonResponse(['error' => 'Invalid credentials'], 401);
        }
    }

    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
