<?php
require_once '../models/Message.php';

class ChatController {
    public function getMessages($data) {
        $message = new Message();
        $result = $message->getMessagesBetween($data['sender_id'], $data['receiver_id']);
        echo json_encode($result);
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
