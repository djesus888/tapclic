<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ✅ Incluir conexión y controladores
require_once __DIR__ . '/../middleware/authMiddleware.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ServiceController.php';
require_once __DIR__ . '/../controllers/ChatController.php';
require_once __DIR__ . '/../controllers/NotificationController.php'; // ✅ NUEVO

// ✅ Crear conexión usando la clase Database
$db = new Database();
$conn = $db->getConnection();  // <-- Ahora $conn es un PDO listo para usar

// ✅ Instanciar controladores
$authController         = new AuthController($conn);
$serviceController      = new ServiceController($conn);
$chatController         = new ChatController($conn);
$notificationController = new NotificationController($conn); // ✅ NUEVO

// ✅ Normalizar la URI para quitar /backend/routes/api.php
$uriFull  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace("/backend/routes/api.php", "", $uriFull);
$uri      = rtrim($basePath, "/");  // quita barra final si la hay

$method = $_SERVER['REQUEST_METHOD'];

// ✅ Leer datos JSON del body si es POST/PUT/PATCH
$inputData = json_decode(file_get_contents("php://input"), true);

// ✅ Enrutador principal
switch (true) {

    // ✅ Registro de usuario
    case $uri === '/api/register' && $method === 'POST':
        echo json_encode($authController->register($inputData));
        break;

    // ✅ Login de usuario
    case $uri === '/api/login' && $method === 'POST':
        echo json_encode($authController->login($inputData));
        break;

    // ✅ Recuperar contraseña (enviar enlace)
    case $uri === '/api/forgot-password' && $method === 'POST':
        echo json_encode($authController->forgotPassword($inputData));
        break;

    // ✅ Cambiar contraseña
    case $uri === '/api/reset-password' && $method === 'POST':
        echo json_encode($authController->resetPassword($inputData));
        break;

    // ✅ Listar servicios
    case $uri === '/api/services' && $method === 'GET':
        echo json_encode($serviceController->getAllServices());
        break;

    // ✅ Crear servicio
    case $uri === '/api/services' && $method === 'POST':
        echo json_encode($serviceController->createService($inputData));
        break;

    // ✅ Actualizar servicio (ejemplo: /api/services/12)
    case preg_match('/^\/api\/services\/(\d+)$/', $uri, $matches):
        $serviceId = $matches[1];
        echo json_encode($serviceController->updateService($serviceId, $inputData));
        break;

    // ✅ Obtener chat por ID de servicio
    case preg_match('/^\/api\/chat\/(\d+)$/', $uri, $matches):
        $serviceId = $matches[1];
        echo json_encode($chatController->getChatByService($serviceId));
        break;

    // ✅ Enviar mensaje al chat
    case $uri === '/api/chat' && $method === 'POST':
        echo json_encode($chatController->sendMessage($inputData));
        break;

    /* 
    ===============================
    ✅ NOTIFICACIONES NUEVAS RUTAS
    ===============================
    */

    // ✅ Obtener notificaciones de un usuario: GET /api/notifications/5
    case preg_match('/^\/api\/notifications\/(\d+)$/', $uri, $matches) && $method === 'GET':
        $userId = $matches[1];
        echo json_encode($notificationController->index($userId));
        break;

    // ✅ Crear nueva notificación: POST /api/notifications
    case $uri === '/api/notifications' && $method === 'POST':
        echo json_encode($notificationController->store($inputData));
        break;

    // ✅ Marcar como leída: PATCH /api/notifications/12/read
    case preg_match('/^\/api\/notifications\/(\d+)\/read$/', $uri, $matches) && $method === 'PATCH':
        $notifId = $matches[1];
        echo json_encode($notificationController->markAsRead($notifId));
        break;

    // ✅ Si no coincide ninguna ruta
    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
        break;
}
