<?php
// backend/index.php

// --- CONFIGURACIÓN DE CORS PARA API ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si es preflight de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Detectar la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Limpiar la URI para obtener solo el path después de /backend/
$base_path = str_replace('/backend', '', parse_url($request_uri, PHP_URL_PATH));


// Si se accede a la raíz del backend, mostrar el frontend
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/backend/' || $_SERVER['REQUEST_URI'] === '/backend/index.php') {
    $frontendPath = __DIR__ . '/../frontend/index.html';

    if (file_exists($frontendPath)) {
        header("Content-Type: text/html; charset=UTF-8");
        readfile($frontendPath);
        exit;
    } else {
        http_response_code(404);
        echo "<h1 style='text-align:center; color:red;'>❌ No se encontró frontend/index.html</h1>";
        exit;
    }
}



// --- CARGAR DEPENDENCIAS SOLO SI ES API ---
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/NotificationController.php';
require_once __DIR__ . '/models/Notification.php';
require_once __DIR__ . '/models/Service.php';
require_once __DIR__ . '/controllers/ServiceController.php';



// Leer datos JSON enviados
$input_data = json_decode(file_get_contents("php://input"), true);

// --- RUTAS API ---
switch (true) {

    // Registro de usuario
    case preg_match('#^/api/register$#', $base_path):
        if ($method === 'POST') {
            $auth = new AuthController();
            $auth->register($input_data);
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Método no permitido"]);
        }
        break;

    // Login de usuario
    case preg_match('#^/api/login$#', $base_path):
        if ($method === 'POST') {
            $auth = new AuthController();
            $auth->login($input_data);
        } else {
            http_response_code(405);
            echo json_encode(["status" => "error", "message" => "Método no permitido"]);
        }
        break;


// Obtener notificaciones por ID de receptor
case preg_match('#^/api/notifications/(\d+)$#', $base_path, $matches):
    if ($method === 'GET') {
        $userId = (int) $matches[1];
        $db = (new Database())->getConnection();
        $controller = new NotificationController($db);
        $controller->getNotifications($userId);
    } else {
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    }
    break;

// Marcar notificación como leída
case preg_match('#^/api/notifications/read/(\d+)$#', $base_path, $matches):
    if ($method === 'PUT') {
        $notifId = (int) $matches[1];
        $db = (new Database())->getConnection();
        $controller = new NotificationController($db);
        $controller->markAsRead($notifId);
    } else {
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    }
    break;

// Obtener todos los servicios
case preg_match('#^/api/services$#', $base_path):
    if ($method === 'GET') {
        $db = (new Database())->getConnection();
        $controller = new ServiceController($db);
        $controller->getAllServices();
    } else {
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    }
    break;











    // Si no existe la ruta
default:
    http_response_code(404);
    $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
    $isApiRequest = str_starts_with($base_path, '/api');

    if (strpos($acceptHeader, 'application/json') !== false || $isApiRequest) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Ruta no encontrada: " . $base_path
        ]);
    } else {
        // HTML bonito solo si es navegador normal (no API)
        // Si es un navegador, mostramos HTML con estilo
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Error 404 | TapClic</title>
          <style>
            body {
              background: linear-gradient(135deg, #ff416c, #ff4b2b);
              color: #fff;
              text-align: center;
              font-family: Arial, sans-serif;
              display: flex;
              flex-direction: column;
              justify-content: center;
              align-items: center;
              height: 100vh;
              margin: 0;
            }
            h1 {
              font-size: 4rem;
              margin-bottom: 1rem;
            }
            p {
              font-size: 1.2rem;
              margin-bottom: 2rem;
            }
            a {
              display: inline-block;
              padding: 12px 20px;
              background: #fff;
              color: #ff416c;
              text-decoration: none;
              border-radius: 8px;
              font-weight: bold;
              transition: 0.3s;
            }
            a:hover {
              background: #f0f0f0;
            }
            .emoji {
              font-size: 5rem;
              margin-bottom: 1rem;
            }
          </style>
        </head>
        <body>
          <div class="emoji">🚧</div>
          <h1>404</h1>
          <p>Ups... La ruta solicitada no existe en <strong>TapClic Backend</strong></p>
          <a href="/frontend/index.html">Volver al inicio</a>
        </body>
        </html>
        ';
    }
    break;    
}
 
