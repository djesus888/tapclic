<?php
require_once __DIR__ . '/../models/Service.php';


class ServiceController {
    public function getAllServices() {
        $service = new Service();
        $result = $service->getAll();
        echo json_encode($result);
    }

    public function createService($data) {
        $service = new Service();
        $result = $service->create($data);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Servicio creado"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo crear el servicio"]);
        }
    }
}
