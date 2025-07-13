<?php
// backend/routes/api.php

require __DIR__ . '/../config/db.php';
require __DIR__ . '/../controllers/AuthController.php';
require __DIR__ . '/../controllers/ServiceController.php';
require __DIR__ . '/../controllers/ChatController.php';

$database         = new Database();
$db               = $database->getConnection();
$authController   = new AuthController($db);
$serviceController= new ServiceController($db);
$chatController   = new ChatController($db);

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

switch (true) {
    case $uri === '/api/register' && $method === 'POST':
        $authController->register();
        break;
    case $uri === '/api/login' && $method === 'POST':
        $authController->login();
        break;
    case $uri === '/api/services' && $method === 'GET':
        $serviceController->getAllServices();
        break;
    case $uri === '/api/services' && $method === 'POST':
        $serviceController->createService();
        break;
    case preg_match('/\/api\/services\/\d+/', $uri) && $method === 'PUT':
        $serviceController->updateService();
        break;
    case preg_match('/\/api\/chat\/[0-9]+/', $uri) && $method === 'GET':
        $chatController->getChatByServiceId();
        break;
    case $uri === '/api/chat' && $method === 'POST':
        $chatController->sendMessage();
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Endpoint not found"]);
        break;
}
