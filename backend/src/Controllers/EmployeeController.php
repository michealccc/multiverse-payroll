<?php

namespace Controllers;

use Database\Connection;
use PDO;

class EmployeeController
{
    private $connection;
    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $connection->getConnection();
    }

    /**
     * Get all employees
     */
    public function index(): array
    {
        $stmt = $this->pdo->query(
            "SELECT id, company_id, full_name, email, salary, created_at, updated_at
             FROM employees
             ORDER BY id ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single employee by ID
     */
    public function show(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, company_id, full_name, email, salary, created_at, updated_at
             FROM employees
             WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new employee
     */
    public function store(array $data): array
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO employees (company_id, full_name, email, salary)
             VALUES (:company_id, :full_name, :email, :salary)"
        );

        $stmt->execute([
            ':company_id' => $data['company_id'],
            ':full_name' => $data['full_name'],
            ':email' => $data['email'],
            ':salary' => $data['salary']
        ]);

        $id = $this->pdo->lastInsertId();

        return $this->show($id);
    }

    /**
     * Update employee
     */
    public function update(int $id, array $data): array|false
    {
        $fields = [];
        $params = [];

        // Build dynamic update query based on provided fields
        if (isset($data['company_id']))
        {
            $fields[] = "company_id = :company_id";
            $params[':company_id'] = $data['company_id'];
        }

        if (isset($data['full_name']))
        {
            $fields[] = "full_name = :full_name";
            $params[':full_name'] = $data['full_name'];
        }

        if (isset($data['email']))
        {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }

        if (isset($data['salary']))
        {
            $fields[] = "salary = :salary";
            $params[':salary'] = $data['salary'];
        }

        if (empty($fields))
        {
            return $this->show($id);
        }

        $params[':id'] = $id;

        $sql = "UPDATE employees SET " . implode(', ', $fields) . " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $this->show($id);
    }

    /**
     * Delete employee
     */
    public function destroy(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM employees WHERE id = ?");

        return $stmt->execute([$id]);
    }
}
