<?php
// backend/models/Message.php

class Message {
    private $conn;
    private $table_name = "messages";

    public $id;
    public $service_id;
    public $sender_id;
    public $recipient_id;
    public $message;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readByService($service_id) {
        $query = "SELECT id, service_id, sender_id, recipient_id, message, created_at
                  FROM " . $this->table_name . " WHERE service_id = :service_id ORDER BY created_at ASC";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":service_id", $service_id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET service_id=:service_id, sender_id=:sender_id, recipient_id=:recipient_id, message=:message";
        $stmt  = $this->conn->prepare($query);

        // sanitize
        $this->service_id   = htmlspecialchars(strip_tags($this->service_id));
        $this->sender_id    = htmlspecialchars(strip_tags($this->sender_id));
        $this->recipient_id = htmlspecialchars(strip_tags($this->recipient_id));
        $this->message      = htmlspecialchars(strip_tags($this->message));

        // bind
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":sender_id", $this->sender_id);
        $stmt->bindParam(":recipient_id", $this->recipient_id);
        $stmt->bindParam(":message", $this->message);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
