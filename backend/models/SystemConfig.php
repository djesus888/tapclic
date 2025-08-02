<?php
// models/SystemConfig.php

class SystemConfig
{
    private $conn;
    private $table = "system_config";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    // updateConfig (solo existe un único registro con id = 1)
    public function updateConfig(array $data): bool
    {
        $sql = "UPDATE {$this->table} SET
                    hostname         = :hostname,
                    system_online    = :online,
                    maintenance_mode = :maintenance,
                    api_key          = :apikey,
                    timezone         = :tz,
                    version          = :ver,
                    system_name      = :name,
                    system_logo_url  = :logo,
                    support_email    = :email,
                    default_theme    = :theme,
                    updated_at       = NOW()
                WHERE id = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':hostname',   $data['hostname']);
        $stmt->bindParam(':online',     $data['system_online'],    PDO::PARAM_INT);
        $stmt->bindParam(':maintenance',$data['maintenance_mode'], PDO::PARAM_INT);
        $stmt->bindParam(':apikey',     $data['api_key']);
        $stmt->bindParam(':tz',         $data['timezone']);
        $stmt->bindParam(':ver',        $data['version']);
        $stmt->bindParam(':name',       $data['system_name']);
        $stmt->bindParam(':logo',       $data['system_logo_url']);
        $stmt->bindParam(':email',      $data['support_email']);
        $stmt->bindParam(':theme',      $data['default_theme']);
        return $stmt->execute();
    }

    // opcional: método para leer config
    public function getConfig(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = 1";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
