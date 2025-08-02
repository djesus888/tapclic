<?php
class Service {
    private $conn;
    private $table = 'services';

    public $id;
    public $title;
    public $description;
    public $status;
    public $created_at;
    public $user_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (title, description, status, user_id)
                  VALUES (:title, :description, :status, :user_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':user_id', $this->user_id);

        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Cambiado aquí
    }

public function getUnratedByUser($user_id)
{
    $stmt = $this->pdo->prepare("
        SELECT * FROM services 
        WHERE user_id = :user_id 
          AND status = 'completed' 
          AND is_rated = FALSE
        ORDER BY created_at DESC
    ");

    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Cambiado aquí
    }
}
