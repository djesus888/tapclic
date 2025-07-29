<?php

class Notification {

    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAllByUser($userId) {
        $sql = "
            SELECT
                n.id,
                n.sender_id,
                s.name AS sender_name,
                s.role AS sender_role,
                n.receiver_id,
                n.receiver_role,
                n.title,
                n.message,
                n.is_read,
                n.created_at
            FROM notifications n
            LEFT JOIN users s ON n.sender_id = s.id
            WHERE n.receiver_id = :receiver_id
            ORDER BY n.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':receiver_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($senderId, $receiverId, $receiverRole, $title, $message) {
        $sql = "
            INSERT INTO notifications (sender_id, receiver_id, receiver_role, title, message, is_read)
            VALUES (:sender_id, :receiver_id, :receiver_role, :title, :message, 0)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':sender_id', $senderId, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_role', $receiverRole, PDO::PARAM_STR);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function markAsRead($id) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
