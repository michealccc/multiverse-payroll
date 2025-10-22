<?php

namespace Database;

use PDO;
use PDOException;

class Connection
{
    private $conn;

    public function __construct()
    {
        $host = getenv("DB_HOST") ?: "db";
        $dbname = getenv("DB_NAME") ?: "multiverse_db";
        $user = getenv("DB_USER") ?: "multiverse_user";
        $pass = getenv("DB_PASSWORD") ?: "multiverse_password";

        try
        {
            $this->conn = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass
            );
            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        }
        catch (PDOException $e)
        {
            http_response_code(500);
            echo json_encode([
                "error" => "Database connection failed",
                "message" => $e->getMessage(),
            ]);
            exit();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
