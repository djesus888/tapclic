<?php
require_once __DIR__ . '/../vendor/autoload.php'; // <-- Autoload de Composer
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config.php'; // ✅ Aquí debería estar JWT_SECRET
use Firebase\JWT\JWT;

class AuthController {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Registro de usuario
     */
    public function register() {
        // Leer datos JSON del body
        $data = json_decode(file_get_contents("php://input"), true);

        // Validar datos mínimos
        if (!isset($data['email'], $data['password'], $data['role'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Datos incompletos para el registro"
            ]);
            exit;
        }

        // Crear modelo usuario
        $user = new User($this->conn);
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->role = $data['role'];

        // Intentar guardar en DB
        if ($user->create()) {
            echo json_encode([
                "status" => "success",
                "message" => "Usuario registrado correctamente"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo registrar (quizás ya existe)"
            ]);
        }
        exit; // ✅ Evitamos salida extra
    }















/**
 * Restablecer la contraseña con token
 */
public function resetPassword() {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['token'], $data['password'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Token y nueva contraseña son requeridos"
        ]);
        exit;
    }

    $token = trim($data['token']);
    $newPassword = trim($data['password']);

    if (strlen($newPassword) < 6) {
        echo json_encode([
            "status" => "error",
            "message" => "La contraseña debe tener al menos 6 caracteres"
        ]);
        exit;
    }

    // Buscar usuario con ese token y que no esté expirado
    $stmt = $this->db->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            "status" => "error",
            "message" => "Token inválido"
        ]);
        exit;
    }

    // Verificar expiración
    if (strtotime($user['reset_expires']) < time()) {
        echo json_encode([
            "status" => "error",
            "message" => "El enlace ha expirado, solicita uno nuevo"
        ]);
        exit;
    }

    // Hashear la nueva contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Guardar nueva contraseña y limpiar token
    $stmt = $this->db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
    $stmt->execute([$hashedPassword, $user['id']]);

    echo json_encode([
        "status" => "success",
        "message" => "Tu contraseña ha sido restablecida con éxito"
    ]);
    exit;
}
    /**
     * Login de usuario
     */
    public function login() {
        // Leer datos JSON del body
        $data = json_decode(file_get_contents("php://input"), true);

        // Validar datos mínimos
        if (!isset($data['email'], $data['password'])) {
            echo json_encode([
                "status" => "error",
                "message" => "Datos incompletos"
            ]);
            exit;
        }








        // Buscar usuario por email
        $user = new User($this->conn);
        if (!$user->findByEmail($data['email'])) {
            echo json_encode([
                "status" => "error",
/**
 * Recuperar contraseña - enviar enlace al correo
 */
public function forgotPassword() {
    // Leer JSON del body
    $data = json_decode(file_get_contents("php://input"), true);

    // Validar que venga el email
    if (!isset($data['email']) || empty(trim($data['email']))) {
        echo json_encode([
            "status" => "error",
            "message" => "El correo electrónico es obligatorio"
        ]);
        exit;
    }

    $email = trim($data['email']);

    // Buscar usuario en la BD
    $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            "status" => "error",
            "message" => "No existe ninguna cuenta con este correo"
        ]);
        exit;
    }

    // Generar token de recuperación y fecha de expiración
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Guardar token y expiración en la base de datos
    $stmt = $this->db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $stmt->execute([$token, $expires, $user['id']]);

    // Crear enlace de restablecimiento
    $resetLink = "https://tu-dominio.com/frontend/reset-password.html?token=" . $token;

    // Enviar correo (muy básico, se puede usar PHPMailer para más seguridad)
    $subject = "Recupera tu contraseña";
    $message = "Hola,\n\nHiciste una solicitud para restablecer tu contraseña.\n\n".
               "Haz clic en este enlace para crear una nueva contraseña:\n\n$resetLink\n\n".
               "Este enlace expirará en 1 hora.\n\nSi no solicitaste esto, ignora este mensaje.";
    $headers = "From: no-reply@tu-dominio.com\r\nContent-Type: text/plain; charset=UTF-8";

    // Enviar email
    mail($email, $subject, $message, $headers);

    // Respuesta final
    echo json_encode([
        "status" => "success",
        "message" => "Se ha enviado un enlace de recuperación a tu correo"
    ]);
    exit;
}                "message" => "Usuario no encontrado"
            ]);
            exit;
        }

        // Verificar contraseña
        if (!password_verify($data['password'], $user->password)) {
            echo json_encode([
                "status" => "error",
                "message" => "Contraseña incorrecta"
            ]);
            exit;
        }

        // ✅ Generar payload del JWT
        $payload = [
            'sub'   => $user->id,
            'email' => $user->email,
            'role'  => $user->role,
            'iat'   => time(),
            'exp'   => time() + (60 * 60 * 24) // Expira en 1 día
        ];

        // ✅ Verificar que JWT_SECRET esté definido
        if (!defined('JWT_SECRET')) {
            echo json_encode([
                "status" => "error",
                "message" => "JWT_SECRET no está definido en config.php"
            ]);
            exit;
        }

        // ✅ Generar el token JWT
        $jwt = JWT::encode($payload, JWT_SECRET, 'HS256');

        // ✅ Devolver respuesta JSON limpia
        echo json_encode([
            "status" => "success",
            "token"  => $jwt,
            "user"   => [
                "id"    => $user->id,
                "email" => $user->email,
                "role"  => $user->role
            ]
        ]);
        exit; // ✅ IMPORTANTE para evitar `null` extra
    }
}
