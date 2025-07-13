<?php
// backend/models/User.php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $token_balance;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT id, name, email, role, token_balance, created_at FROM " . $this->table_name;
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT id, name, email, role, token_balance, created_at
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->id            = $row['id'];
            $this->name          = $row['name'];
            $this->email         = $row['email'];
            $this->role          = $row['role'];
            $this->token_balance = $row['token_balance'];
            $this->created_at    = $row['created_at'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, email=:email, password=:password, role=:role, token_balance=:token_balance";
        $stmt  = $this->conn->prepare($query);

        // sanitize
        $this->name          = htmlspecialchars(strip_tags($this->name));
        $this->email         = htmlspecialchars(strip_tags($this->email));
        $this->password      = password_hash($this->password, PASSWORD_BCRYPT);
        $this->role          = htmlspecialchars(strip_tags($this->role));
        $this->token_balance = htmlspecialchars(strip_tags($this->token_balance));

        // bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":token_balance", $this->token_balance);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET name=:name, email=:email, role=:role, token_balance=:token_balance
                  WHERE id=:id";
        $stmt  = $this->conn->prepare($query);

        // sanitize
        $this->name          = htmlspecialchars(strip_tags($this->name));
        $this->email         = htmlspecialchars(strip_tags($this->email));
        $this->role          = htmlspecialchars(strip_tags($this->role));
        $this->token_balance = htmlspecialchars(strip_tags($this->token_balance));
        $this->id            = htmlspecialchars(strip_tags($this->id));

        // bind
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":token_balance", $this->token_balance);
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
