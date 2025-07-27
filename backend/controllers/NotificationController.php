<?php

class NotificationController {

    private $db;

    public function __construct($pdo) {
        $this->db = $pdo; // Recibe conexión PDO
    }

    // ✅ Listar notificaciones de un usuario
    public function index($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE receiver_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($notifications);
    }

    // ✅ Crear una nueva notificación
    public function store($data) {
        $stmt = $this->db->prepare("INSERT INTO notifications (sender_id, receiver_id, receiver_role, title, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['sender_id'],
            $data['receiver_id'],
            $data['receiver_role'],
            $data['title'],
            $data['message']
        ]);

        echo json_encode(["message" => "Notificación creada con éxito"]);
    }

    // ✅ Marcar como leída
    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["message" => "Notificación marcada como leída"]);
    }
}
