<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends BaseController
{
    public function updateProfile()
    {
        $user = $this->authenticate();
        $data = $this->getJsonInput();

        $model = new User($this->db);
        $result = $model->updateProfile($user['id'], $data);

        $this->jsonResponse(['status' => 'success', 'message' => 'Perfil actualizado', 'user' => $result]);
    }

    public function changePassword()
    {
        $user = $this->authenticate();
        $data = $this->getJsonInput();

        if (!$data['current_password'] || !$data['new_password']) {
            $this->jsonError('Faltan datos');
        }

        $model = new User($this->db);
        $success = $model->changePassword($user['id'], $data['current_password'], $data['new_password']);

        if ($success) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Contraseña actualizada']);
        } else {
            $this->jsonError('La contraseña actual no es válida');
        }
    }

    public function uploadAvatar()
    {
        $user = $this->authenticate();
        if (!isset($_FILES['avatar'])) {
            $this->jsonError('No se subió archivo');
        }

        $file = $_FILES['avatar'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        $path = __DIR__ . '/../uploads/avatars/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            $this->jsonError('Error al guardar imagen');
        }

        $model = new User($this->db);
        $model->updateAvatar($user['id'], '/uploads/avatars/' . $filename);

        $this->jsonResponse(['status' => 'success', 'avatar_url' => '/uploads/avatars/' . $filename]);
    }


public function getUserProfile($data) {
    $userId = $data['user_id'] ?? null;

    if (!$userId) {
        return ["status" => "error", "message" => "user_id is required"];
    }

    try {
        $stmt = $this->conn->prepare("SELECT id, name, email, avatar_url, preferences FROM users WHERE id = :id");
        $stmt->bindParam(":id", $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return ["status" => "success", "data" => $user];
        } else {
            return ["status" => "error", "message" => "User not found"];
        }

    } catch (PDOException $e) {
        return ["status" => "error", "message" => "Database error: " . $e->getMessage()];
    }
}




    public function updatePreferences()
    {
        $user = $this->authenticate();
        $data = $this->getJsonInput();

        $model = new User($this->db);
        $model->updatePreferences($user['id'], $data);

        $this->jsonResponse(['status' => 'success', 'message' => 'Preferencias actualizadas']);
    }

    public function updateProviderData()
    {
        $user = $this->authenticate();
        $data = $this->getJsonInput();

        $model = new User($this->db);
        $model->updateProviderData($user['id'], $data);

        $this->jsonResponse(['status' => 'success', 'message' => 'Datos del proveedor actualizados']);
    }
}
