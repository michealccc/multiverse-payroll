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

    /**
     * Get all companies
     */
    public function index(): array
    {
        $stmt = $this->pdo->query("SELECT id, name FROM companies ORDER BY id ASC");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all companies with employee counts
     */
    public function indexWithEmployeeCounts(): array
    {
        $stmt = $this->pdo->query(
            "SELECT c.id, c.name, COUNT(e.id) as employee_count
             FROM companies c
             LEFT JOIN employees e ON c.id = e.company_id
             GROUP BY c.id, c.name
             ORDER BY c.id ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single company by ID
     */
    public function show(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT id, name FROM companies WHERE id = ?");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get single company with employee count
     */
    public function showWithEmployeeCount(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.name, COUNT(e.id) as employee_count
             FROM companies c
             LEFT JOIN employees e ON c.id = e.company_id
             WHERE c.id = ?
             GROUP BY c.id, c.name"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all companies with average salaries
     */
    public function indexWithAverageSalaries(): array
    {
        $stmt = $this->pdo->query(
            "SELECT c.id, c.name,
                    COUNT(e.id) as employee_count,
                    COALESCE(AVG(e.salary), 0) as average_salary
             FROM companies c
             LEFT JOIN employees e ON c.id = e.company_id
             GROUP BY c.id, c.name
             ORDER BY c.id ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single company with average salary
     */
    public function showWithAverageSalary(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT c.id, c.name,
                    COUNT(e.id) as employee_count,
                    COALESCE(AVG(e.salary), 0) as average_salary
             FROM companies c
             LEFT JOIN employees e ON c.id = e.company_id
             WHERE c.id = ?
             GROUP BY c.id, c.name"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new company
     */
    public function store(array $data): array
    {
        $stmt = $this->pdo->prepare("INSERT INTO companies (name) VALUES (:name)");

        $stmt->execute([':name' => $data['name']]);

        $id = $this->pdo->lastInsertId();

        return $this->show($id);
    }

    /**
     * Update company
     */
    public function update(int $id, array $data): array|false
    {
        if (isset($data['name']))
        {
            $stmt = $this->pdo->prepare("UPDATE companies SET name = :name WHERE id = :id");
            $stmt->execute([':name' => $data['name'], ':id' => $id]);
        }

        return $this->show($id);
    }

    /**
     * Delete company
     */
    public function destroy(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM companies WHERE id = ?");

        return $stmt->execute([$id]);
    }
}
