
// backend/controllers/AuthController.php

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db   = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function register($data) {
        $user = new User($this->conn);
        $user->email    = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_BCRYPT);
        $user->role     = $data['role'];  // cliente o prestador
        return $user->create();
    }

    public function login($email, $password) {
        $user = new User($this->conn);
        if (!$user->findByEmail($email)) {
            return null;
        }
        if (!password_verify($password, $user->password)) {
            return null;
        }
        // Generar JWT
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (60*60*24),
        ];
        $jwt = JWT::encode($payload, JWT_SECRET);
        return ['token' => $jwt, 'user' => $user];
    }
}
