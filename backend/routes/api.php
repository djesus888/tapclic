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
require_once __DIR__ . '/../controllers/NotificationController.php';
require_once __DIR__ . '/..controllers/ServiceController.php';
require_once __DIR__ . '/../controllers/RequestController.php';
require_once __DIR__ . '/../controllers/SupportController.php';
require_once __DIR__ . '/../controllers/UserController.php';


// ✅ Crear conexión usando la clase Database
$db = new Database();
$conn = $db->getConnection();  // <-- Ahora $conn es un PDO listo para usar

// ✅ Instanciar controladores
$authController         = new AuthController($conn);
$serviceController      = new ServiceController($conn);
$chatController         = new ChatController($conn);
$notificationController = new NotificationController($conn); // ✅ NUEVO
$serviceController      = new ServiceController($conn); // ✅ NUEVO
$requestController      = new RequestController($conn);
$supportController      = new SupportController($conn);
$userController         = new UserController($conn);



// ✅ Normalizar la URI automáticamente, sin hardcode
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = '/' . trim(str_replace($scriptName, '', $requestUri), '/');

$method = $_SERVER['REQUEST_METHOD'];
$inputData = json_decode(file_get_contents("php://input"), true);

// ✅ Logs para debug
error_log("👉 URI recibido: " . $uri . " | Método: " . $method);

/*
=======================================
✅ 1. RUTAS DINÁMICAS CON PREG_MATCH
=======================================
*/

// ✅ Actualizar servicio dinámico: /api/services/{id}
if (preg_match('/^api\/services\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
    $serviceId = $matches[1];
    echo json_encode($serviceController->updateService($serviceId, $inputData));
    exit;
}

// ✅ Obtener chat dinámico: /api/chat/{serviceId}
if (preg_match('/^api\/chat\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    $serviceId = $matches[1];
    echo json_encode($chatController->getChatByService($serviceId));
    exit;
}
if (preg_match('/^api\/messages\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    $providerId = $matches[1];
    echo json_encode($chatController->getMessagesByProvider($providerId));
    exit;
}

// ✅ Obtener notificaciones de usuario: /api/notifications/{userId}
if (preg_match('/^api\/notifications\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    $userId = $matches[1];
    echo json_encode($notificationController->getNotifications($userId));
    exit;
}

// ✅ Marcar como leída: /api/notifications/{notifId}/read
if (preg_match('/^api\/notifications\/(\d+)\/read$/', $uri, $matches) && $method === 'PATCH') {
    $notifId = $matches[1];
    echo json_encode($notificationController->markAsRead($notifId));
    exit;
}

/*
=======================================
✅ 2. RUTAS ESTÁTICAS CON SWITCH
=======================================
*/
switch (true) {
    case $uri === 'api/register' && $method === 'POST':
        echo json_encode($authController->register($inputData));
        break;

    case $uri === 'api/login' && $method === 'POST':
        echo json_encode($authController->login($inputData));
        break;

    case $uri === 'api/forgot-password' && $method === 'POST':
        echo json_encode($authController->forgotPassword($inputData));
        break;

    case $uri === 'api/reset-password' && $method === 'POST':
        echo json_encode($authController->resetPassword($inputData));
        break;

    case $uri === 'api/services' && $method === 'GET':
        echo json_encode($serviceController->getAllServices());
        break;

    case $uri === 'api/services' && $method === 'POST':
        echo json_encode($serviceController->createService($inputData));
        break;

case $uri === 'api/services/unrated' && $method === 'POST':
    echo json_encode($serviceController->getUnratedServices($inputData));
    break;

    case $uri === 'api/chat' && $method === 'POST':
        echo json_encode($chatController->sendMessage($inputData));
        break;

    case $uri === 'api/notifications' && $method === 'POST':
        echo json_encode($notificationController->createNotification(
            $inputData['sender_id']     ?? null,
            $inputData['receiver_id']   ?? null,
            $inputData['receiver_role'] ?? '',
            $inputData['title']         ?? '',
            $inputData['message']       ?? ''
        ));
        break;

      /*
=======================================
✅ RUTAS PARA SOLICITUDES
=======================================
*/
case $uri === 'api/requests/get' && $method === 'POST':
    echo json_encode($requestController->getUserRequests($inputData));
    break;

case $uri === 'api/requests/accept' && $method === 'POST':
    echo json_encode($requestController->acceptRequest($inputData));
    break;

case $uri === 'api/requests/reject' && $method === 'POST':
    echo json_encode($requestController->rejectRequest($inputData));
    break;

/*
=======================================
✅ RUTAS PARA SOPORTE
=======================================
*/
case $uri === 'api/support/create' && $method === 'POST':
    echo json_encode($supportController->createTicket($inputData));
    break;

case $uri === 'api/support/tickets' && $method === 'POST':
    echo json_encode($supportController->getUserTickets($inputData));
    break;

case $uri === 'api/support/reply' && $method === 'POST':
    echo json_encode($supportController->replyToTicket($inputData));
    break;

/*
=======================================
✅ RUTAS PARA PERFIL Y CONFIGURACIÓN
=======================================
*/
case $uri === 'api/profile/update' && $method === 'POST':
    echo json_encode($userController->updateProfile($inputData));
    break;

case $uri === 'api/password/change' && $method === 'POST':
    echo json_encode($userController->changePassword($inputData));
    break;

case $uri === 'api/avatar/upload' && $method === 'POST':
    echo json_encode($userController->uploadAvatar());
    break;

case $uri === 'api/preferences/update' && $method === 'POST':
    echo json_encode($userController->updatePreferences($inputData));
    break;

/*
=======================================
✅ RUTAS PARA CALIFICAR SERVICIOS
=======================================
*/
case $uri === 'api/services/unrated' && $method === 'POST':
    echo json_encode($serviceController->getUnratedServices($inputData));
    break;

case $uri === 'api/services/rate' && $method === 'POST':
    echo json_encode($serviceController->rateService($inputData));
    break;

case $uri === 'api/services/available' && $method === 'GET':
    echo json_encode($serviceController->getAvailableServices());
    break;

case $uri === 'api/user/profile' && $method === 'POST':
    echo json_encode($userController->getUserProfile($inputData));
    break;































    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
        break;
}
