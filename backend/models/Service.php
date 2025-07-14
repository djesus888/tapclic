<?php
// backend/models/Service.php

class Service {
    private $conn;
    private $table_name = "services";

    public $id;
    public $user_id;
    public $service_type;
    public $description;
    public $price;
    public $status;
    public $created_at;
    public $provider_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT s.id, s.service_type, s.description, s.price, s.status, s.created_at, u.name as provider_name
                  FROM " . $this->table_name . " s
                  JOIN users u ON s.user_id = u.id
                  ORDER BY s.created_at DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT s.id, s.service_type, s.description, s.price, s.status, s.created_at, u.name as provider_name
                  FROM " . $this->table_name . " s
                  JOIN users u ON s.user_id = u.id
                  WHERE s.id = ? LIMIT 0,1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->id            = $row['id'];
            $this->service_type  = $row['service_type'];
            $this->description   = $row['description'];
n
            $this->price         = $row['price'];
            $this->status        = $row['status'];
            $this->created_at    = $row['created_at'];
            $this->provider_name = $row['provider_name'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, service_type=:service_type, description=:description, price=:price, status=:status";
        $stmt  = $this->conn->prepare($query);

        // sanitize
        $this->user_id      = htmlspecialchars(strip_tags($this->user_id));
        $this->service_type = htmlspecialchars(strip_tags($this->service_type));
        $this->description  = htmlspecialchars(strip_tags($this->description));
        $this->price        = htmlspecialchars(strip_tags($this->price));
        $this->status       = htmlspecialchars(strip_tags($this->status));

        // bind
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":service_type", $this->service_type);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET service_type=:service_type, description=:description, price=:price, status=:status
                  WHERE id=:id";
        $stmt  = $this->conn->prepare($query);

        // sanitize
        $this->service_type = htmlspecialchars(strip_tags($this->service_type));
        $this->description  = htmlspecialchars(strip_tags($this->description));
        $this->price        = htmlspecialchars(strip_tags($this->price));
        $this->status       = htmlspecialchars(strip_tags($this->status));
        $this->id           = htmlspecialchars(strip_tags($this->id));

        // bind
        $stmt->bindParam(":service_type", $this->service_type);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
