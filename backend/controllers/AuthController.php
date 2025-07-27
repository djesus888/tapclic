<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db.php';   // ✅ Aquí importamos Database
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php';      // JWT_SECRET

use Firebase\JWT\JWT;

class AuthController {

    private $userModel;

    public function __construct() {
        // ✅ Creamos instancia de Database
        $database = new Database();
        $db = $database->getConnection();  // obtenemos el PDO

        // ✅ Pasamos la conexión al modelo User
        $this->userModel = new User($db);
    }

    public function register($data) {
        $name     = trim($data['name'] ?? '');
        $email    = trim($data['email'] ?? '');
        $phone    = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';
        $role     = $data['role'] ?? 'user';

        if (!$name || !$email || !$phone || !$password) {
            return $this->json(['status' => 'error', 'message' => 'Todos los campos son obligatorios']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['status' => 'error', 'message' => 'Correo electrónico inválido']);
        }

        if (!preg_match('/^[0-9]{7,15}$/', $phone)) {
            return $this->json(['status' => 'error', 'message' => 'Número de teléfono inválido']);
        }

        // Buscar duplicados
        if ($this->userModel->findByEmail($email)) {
            return $this->json(['status' => 'error', 'message' => 'El email ya está registrado']);
        }
        if ($this->userModel->findByPhone($phone)) {
            return $this->json(['status' => 'error', 'message' => 'El teléfono ya está registrado']);
        }

        // Hash de contraseña
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Asignar datos al modelo
        $this->userModel->name          = $name;
        $this->userModel->email         = $email;
        $this->userModel->phone         = $phone;
        $this->userModel->password      = $hashedPassword;
        $this->userModel->role          = $role;
        $this->userModel->token_balance = 0;

        if ($this->userModel->create()) {
            return $this->json(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
        } else {
            return $this->json(['status' => 'error', 'message' => 'No se pudo registrar el usuario']);
        }
    }

    public function login($data) {
        $email    = trim($data['email'] ?? '');
        $phone    = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';

        if ((!$email && !$phone) || !$password) {
            return $this->json(['status' => 'error', 'message' => 'Usuario y contraseña son obligatorios']);
        }

        $user = $email
            ? $this->userModel->findByEmail($email)
            : $this->userModel->findByPhone($phone);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->json(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos']);
        }

        $token = $this->generateJWT($user);

        return $this->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'] ?? '',
                'role'  => $user['role']
            ]
        ]);
    }

    private function generateJWT($user) {
        $issuedAt   = time();
        $expiration = $issuedAt + (60 * 60 * 24); // 24h

        $payload = [
            'iss'   => 'http://localhost',
            'iat'   => $issuedAt,
            'exp'   => $expiration,
            'sub'   => $user['id'],
            'email' => $user['email'],
            'role'  => $user['role']
        ];

        return JWT::encode($payload, JWT_SECRET, 'HS256');
    }

    private function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
