<?php
require_once '../models/User.php';

class AuthController {
    public function login($data) {
        $user = new User();
        $result = $user->authenticate($data['email'], $data['password']);
        if ($result) {
            echo json_encode(["status" => "success", "user" => $result]);
        } else {
            echo json_encode(["status" => "error", "message" => "Credenciales inválidas"]);
        }
    }

    public function register($data) {
        $user = new User();
        $result = $user->create($data);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Usuario registrado correctamente"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al registrar"]);
        }
    }
}
