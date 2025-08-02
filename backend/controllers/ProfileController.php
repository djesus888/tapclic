<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/SystemConfig.php';

class ProfileController {
    private $conn;
    private $userModel;
    private $systemConfig;

    public function __construct($db) {
        $this->conn = $db;
        $this->userModel = new User($db);
        $this->systemConfig = new SystemConfig($db);
    }

    public function updateProfile($userId, $data) {
        if (!$this->userModel->updateProfile($userId, $data)) {
            return ["status" => "error", "message" => "No se pudo actualizar el perfil"];
        }
        return ["status" => "success", "message" => "Perfil actualizado"];
    }

    public function changePassword($userId, $data) {
        if (empty($data['current_password']) || empty($data['new_password'])) {
            return ["status" => "error", "message" => "Faltan datos"];
        }
        return $this->userModel->changePassword($userId, $data['current_password'], $data['new_password']);
    }

    public function uploadAvatar($userId, $file) {
        if (!isset($file['avatar']) || $file['avatar']['error'] !== 0) {
            return ["status" => "error", "message" => "Archivo no válido"];
        }

        $filename = uniqid("avatar_") . "." . pathinfo($file['avatar']['name'], PATHINFO_EXTENSION);
        $targetPath = __DIR__ . '/../uploads/avatars/' . $filename;

        if (!move_uploaded_file($file['avatar']['tmp_name'], $targetPath)) {
            return ["status" => "error", "message" => "Error al guardar el archivo"];
        }

        $avatarUrl = "/uploads/avatars/" . $filename;
        if (!$this->userModel->updateAvatar($userId, $avatarUrl)) {
            return ["status" => "error", "message" => "No se pudo actualizar el avatar"];
        }

        return ["status" => "success", "avatar_url" => $avatarUrl];
    }

    public function updatePreferences($userId, $data) {
        if (!$this->userModel->updatePreferences($userId, $data)) {
            return ["status" => "error", "message" => "No se pudieron guardar las preferencias"];
        }
        return ["status" => "success", "message" => "Preferencias actualizadas"];
    }

    public function updateProviderData($userId, $data) {
        if (!$this->userModel->updateProviderData($userId, $data)) {
            return ["status" => "error", "message" => "No se pudieron guardar los datos del proveedor"];
        }
        return ["status" => "success", "message" => "Datos del proveedor actualizados"];
    }

    public function updateSystemConfig($userRole, $data) {
        if ($userRole !== 'admin') {
            return ["status" => "error", "message" => "No autorizado"];
        }
        if (!$this->systemConfig->updateConfig($data)) {
            return ["status" => "error", "message" => "No se pudo guardar la configuración"];
        }
        return ["status" => "success", "message" => "Configuración actualizada"];
    }
}
