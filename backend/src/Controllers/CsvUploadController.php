<?php

namespace Controllers;

use Database\Connection;
use PDO;

class CsvUploadController
{
    private $connection;
    private $pdo;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->pdo = $connection->getConnection();
    }

    /**
     * Parse CSV content into array
     */
    public function parseCsv(string $csvContent): array
    {
        $lines = explode("\n", trim($csvContent));
        if (empty($lines) || count($lines) <= 1)
        {
            return []; // Only header or empty
        }

        $headers = str_getcsv($lines[0], ',', '"', '');
        $data = [];

        for ($i = 1; $i < count($lines); $i++)
        {
            $line = trim($lines[$i]);
            if (empty($line))
            {
                continue;
            }

            $row = str_getcsv($line, ',', '"', '');
            $rowData = [];

            foreach ($headers as $index => $header)
            {
                $rowData[$header] = $row[$index] ?? '';
            }

            $data[] = $rowData;
        }

        return $data;
    }

    /**
     * Validate CSV headers
     */
    public function validateCsvHeaders(string $csvContent): bool
    {
        $requiredHeaders = ['Company Name', 'Employee Name', 'Email Address', 'Salary'];

        $lines = explode("\n", trim($csvContent));
        if (empty($lines))
        {
            return false;
        }

        $headers = str_getcsv($lines[0], ',', '"', '');

        foreach ($requiredHeaders as $required)
        {
            if (!in_array($required, $headers))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Import employees and companies from CSV
     */
    public function importFromCsv(string $csvContent): array
    {
        $rows = $this->parseCsv($csvContent);

        $stats = [
            'success' => true,
            'total_rows' => count($rows),
            'companies_created' => 0,
            'employees_imported' => 0,
            'employees_failed' => 0,
            'errors' => []
        ];

        $companyCache = [];

        foreach ($rows as $index => $row)
        {
            $rowNumber = $index + 2;

            try
            {
                $companyName = trim($row['Company Name'] ?? '');
                $employeeName = trim($row['Employee Name'] ?? '');
                $email = trim($row['Email Address'] ?? '');
                $salary = trim($row['Salary'] ?? '');

                // Validate required fields
                if (empty($companyName) || empty($employeeName) || empty($email) || empty($salary))
                {
                    $stats['employees_failed']++;
                    $stats['errors'][] = "Row {$rowNumber}: Missing required fields";
                    continue;
                }

                // Validate email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    $stats['employees_failed']++;
                    $stats['errors'][] = "Row {$rowNumber}: Invalid email format: {$email}";
                    continue;
                }

                // Validate salary
                if (!is_numeric($salary) || $salary < 0)
                {
                    $stats['employees_failed']++;
                    $stats['errors'][] = "Row {$rowNumber}: Invalid salary: {$salary}";
                    continue;
                }

                // Get or create company
                if (!isset($companyCache[$companyName]))
                {
                    $stmt = $this->pdo->prepare("SELECT id FROM companies WHERE name = ?");
                    $stmt->execute([$companyName]);
                    $company = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($company)
                    {
                        $companyCache[$companyName] = $company['id'];
                    }
                    else
                    {
                        $stmt = $this->pdo->prepare("INSERT INTO companies (name) VALUES (?)");
                        $stmt->execute([$companyName]);
                        $companyCache[$companyName] = $this->pdo->lastInsertId();
                        $stats['companies_created']++;
                    }
                }

                $companyId = $companyCache[$companyName];

                // Insert employee
                $stmt = $this->pdo->prepare("INSERT INTO employees (company_id, full_name, email, salary) VALUES (?, ?, ?, ?)");
                $stmt->execute([$companyId, $employeeName, $email, $salary]);
                $stats['employees_imported']++;
            }
            catch (\PDOException $e)
            {
                $stats['employees_failed']++;
                if (strpos($e->getMessage(), 'uq_employees_email') !== false)
                {
                    $stats['errors'][] = "Row {$rowNumber}: Duplicate email: {$email}";
                }
                else
                {
                    $stats['errors'][] = "Row {$rowNumber}: Database error: " . $e->getMessage();
                }
            }
        }

        return $stats;
    }
}
