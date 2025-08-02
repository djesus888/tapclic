public function getUserTickets($data) {
    $userId = $data['user_id'] ?? null;

    if (!$userId) {
        return [
            "status" => "error",
            "message" => "user_id is required"
        ];
    }

    try {
        $stmt = $this->conn->prepare("
            SELECT id, user_id, subject, message, status, created_at
            FROM support_tickets
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            "status" => "success",
            "data" => $tickets
        ];
    } catch (PDOException $e) {
        return [
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ];
    }
}
