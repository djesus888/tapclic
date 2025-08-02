<?php
require_once __DIR__ . '/../config/db.php';      // <-- Este es tu archivo db.php
require_once __DIR__ . '/../models/Service.php';

class ServiceController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();   // <-- Usamos getConnection()
    }

    public function getAllServices() {
        $service = new Service($this->db);
        $result = $service->getAll();
        echo json_encode([
            "status" => "success",
            "services" => $result
        ]);
    }

public function getAvailableServices() {
    try {
        $stmt = $this->conn->prepare("SELECT * FROM services WHERE status = 'available'");
        $stmt->execute();
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "status" => "success",
            "data" => $services
        ];
    } catch (PDOException $e) {
        return [
            "status" => "error",
            "message" => "Error fetching available services: " . $e->getMessage()
        ];
    }
}


public function getUnratedServices($input)
{
    if (!isset($input['user_id'])) {
        return ['success' => false, 'message' => 'Falta user_id'];
    }

    $user_id = $input['user_id'];

    require_once __DIR__ . '/../models/Service.php';
    $serviceModel = new Service($this->db);
    $services = $serviceModel->getUnratedByUser($user_id);

    return ['success' => true, 'services' => $services];
}



    public function createService($data) {
        $service = new Service($this->db);

        $service->title = $data['title'] ?? null;
        $service->description = $data['description'] ?? null;
        $service->status = $data['status'] ?? 'activo'; // valor por defecto
        $service->user_id = $data['user_id'] ?? null;

        if ($service->create()) {
            echo json_encode(["status" => "success", "message" => "Servicio creado exitosamente."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al crear el servicio."]);
        }
    }
}
