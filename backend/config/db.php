<?php
// backend/config/db.php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "tapclic"; // cambia si tu DB es diferente
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error de conexión: " . $exception->getMessage()
    ]);
    exit; // Detiene ejecución
}

        return $this->conn;
    }
}
?>
