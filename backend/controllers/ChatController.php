<?php
require_once __DIR__ . '/../models/Message.php';


class ChatController {
    public function getMessages($data) {
        $message = new Message();
        $result = $message->getMessagesBetween($data['sender_id'], $data['receiver_id']);
        echo json_encode($result);
    }

public function getMessagesByProvider($providerId) {
    try {
        $stmt = $this->conn->prepare("
            SELECT * FROM messages
            WHERE sender_id = :providerId OR receiver_id = :providerId
            ORDER BY timestamp ASC
        ");
        $stmt->bindParam(':providerId', $providerId);
        $stmt->execute();

        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "status" => "success",
            "data" => $messages
        ];
    } catch (PDOException $e) {
        return [
            "status" => "error",
            "message" => "Error fetching messages: " . $e->getMessage()
        ];
    }
}




    public function sendMessage($data) {
        $message = new Message();
        $result = $message->send($data);
        if ($result) {
            echo json_encode(["status" => "success", "message" => "Mensaje enviado"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No se pudo enviar el mensaje"]);
        }
    }
}
