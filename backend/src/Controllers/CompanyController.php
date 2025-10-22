<?php

namespace Controllers;

use Database\Connection;
use PDO;

class CompanyController
{
    private $connection;
    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $connection->getConnection();
    }
}
