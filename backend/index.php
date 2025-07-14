<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require_once 'controllers/AuthController.php';
require_once 'controllers/ServiceController.php';
require_once 'controllers/ChatController.php';

$requestMethod = $_SERVER["REQUEST_METHOD"];
$action = $_GET['action'] ?? '';

$data = json_decode(file_get_contents("php://input"), true);

switch ($action) {
    case 'login':
        if ($requestMethod === 'POST') {
            $auth = new AuthController();
            $auth->login($data);
        }
        break;

    case 'register':
        if ($requestMethod === 'POST') {
            $auth = new AuthController();
            $auth->register($data);
        }
        break;

    case 'get_services':
        if ($requestMethod === 'GET') {
            $service = new ServiceController();
            $service->getAllServices();
        }
        break;

    case 'create_service':
        if ($requestMethod === 'POST') {
            $service = new ServiceController();
            $service->createService($data);
        }
        break;

    case 'get_messages':
        if ($requestMethod === 'POST') {
            $chat = new ChatController();
            $chat->getMessages($data);
        }
        break;

    case 'send_message':
        if ($requestMethod === 'POST') {
            $chat = new ChatController();
            $chat->sendMessage($data);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Acción no válida"]);
        break;
}
