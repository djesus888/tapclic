<?php
require_once 'BaseController.php';
require_once __DIR__ . '/../models/SystemConfig.php';

class AdminController extends BaseController
{
    public function updateSystemConfig()
    {
        $admin = $this->authenticate();
        if ($admin['role'] !== 'admin') {
            $this->jsonError('No autorizado', 403);
        }

        $data = $this->getJsonInput();
        $model = new SystemConfig($this->db);
        $model->updateConfig($data);

        $this->jsonResponse(['status' => 'success', 'message' => 'Configuración del sistema actualizada']);
    }
}
