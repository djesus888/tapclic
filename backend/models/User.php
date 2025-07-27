<?php
// backend/models/User.php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $password;
    public $role;
    public $token_balance;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Crear usuario
     */
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                    (name, email, phone, password, role, token_balance, created_at)
                  VALUES
                    (:name, :email,:phone, :password, :role, :token_balance, NOW())";

        $stmt = $this->conn->prepare($query);

        // Si no tiene nombre o token_balance, ponemos valores por defecto
        $this->name          = $this->name ?? '';
        $this->token_balance = $this->token_balance ?? 0;

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':token_balance', $this->token_balance);

        return $stmt->execute();
    }

    /**
     * Buscar usuario por email
     */
    public function findByEmail($email) {
        $query = "SELECT * FROM {$this->table_name} WHERE email = :email LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id            = $row['id'];
            $this->name          = $row['name'];
            $this->email         = $row['email'];
            $this->phone         = $row['phone'];
            $this->password      = $row['password'];
            $this->role          = $row['role'];
            $this->token_balance = $row['token_balance'];
            $this->created_at    = $row['created_at'];
            return $row; // ✅ Devolvemos los datos
        }
        return false;
    }

    /**
     * ✅ Buscar usuario por teléfono
     */
    public function findByPhone($phone) {
        $query = "SELECT * FROM {$this->table_name} WHERE phone = :phone LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id            = $row['id'];
            $this->name          = $row['name'];
            $this->email         = $row['email'];
            $this->phone         = $row['phone'];
            $this->password      = $row['password'];
            $this->role          = $row['role'];
            $this->token_balance = $row['token_balance'];
            $this->created_at    = $row['created_at'];
            return $row; // ✅ Devolvemos los datos
        }
        return false;
    }

    /**
     * Leer todos los usuarios
     */
    public function readAll() {
        $query = "SELECT id, name, email, phone, role, token_balance, created_at FROM {$this->table_name}";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Leer un usuario por ID
     */
    public function readOne($id) {
        $query = "SELECT id, name, email, phone, role, token_balance, created_at
                  FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id            = $row['id'];
            $this->name          = $row['name'];
            $this->email         = $row['email'];
            $this->phone         = $row['phone'];
            $this->role          = $row['role'];
            $this->token_balance = $row['token_balance'];
            $this->created_at    = $row['created_at'];
            return $row; // ✅ Devolvemos datos igual que en findByEmail
        }
        return false;
    }
}
