<?php
require_once __DIR__ . '/../models/Notification.php';

class NotificationController {

    private $notificationModel;

    public function __construct($db) {
        $this->notificationModel = new Notification($db);
    }

    /**
     * ✅ Obtener todas las notificaciones para un usuario
     */
    public function getNotifications($userId) {
        try {
            $notifications = $this->notificationModel->getAllByUser($userId);
            return $this->json(['status' => 'success', 'data' => $notifications]);
        } catch (Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ Crear una nueva notificación
     */
    public function store($data) {
        $senderId     = $data['sender_id'] ?? null;
        $receiverId   = $data['receiver_id'] ?? null;
        $receiverRole = $data['receiver_role'] ?? null;
        $title        = $data['title'] ?? '';
        $message      = $data['message'] ?? '';

        if (!$senderId || !$receiverId || !$receiverRole || !$title || !$message) {
            return $this->json(['status' => 'error', 'message' => 'Todos los campos son obligatorios'], 400);
        }

        $result = $this->notificationModel->create($senderId, $receiverId, $receiverRole, $title, $message);

        if ($result) {
            return $this->json(['status' => 'success', 'message' => 'Notificación creada']);
        } else {
            return $this->json(['status' => 'error', 'message' => 'No se pudo crear la notificación'], 500);
        }
    }

    /**
     * ✅ Marcar una notificación como leída
     */
    public function markAsRead($notifId) {
        $success = $this->notificationModel->markAsRead($notifId);

        if ($success) {
            return $this->json(['status' => 'success', 'message' => 'Notificación marcada como leída']);
        } else {
            return $this->json(['status' => 'error', 'message' => 'Error al actualizar notificación'], 500);
        }
    }

    /**
     * ✅ Respuesta en formato JSON
     */
    private function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
