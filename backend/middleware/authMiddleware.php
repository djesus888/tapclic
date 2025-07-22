<?php
/**
 * Middleware para validar JWT en rutas protegidas
 */

require_once __DIR__ . '/../config.php';  // ✅ JWT_SECRET
require_once __DIR__ . '/../vendor/autoload.php'; // ✅ Cargar librería JWT

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verificarToken() {
    // ✅ Verificamos si existe el header Authorization
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "No autorizado. Falta token"]);
        exit;
    }

    // ✅ El token debería venir así: Bearer <token>
    $authHeader = $headers['Authorization'];
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Formato de token inválido"]);
        exit;
    }

    $jwt = $matches[1]; // Extraemos el token real

    try {
        // ✅ Decodificar token
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET, 'HS256'));
        return $decoded; // ✅ Devuelve el payload (email, role, etc.)
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Token inválido o expirado"]);
        exit;
    }
}
