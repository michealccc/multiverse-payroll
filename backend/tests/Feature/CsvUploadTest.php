<?php

use Controllers\CsvUploadController;
use Database\Connection;

beforeEach(function ()
{
    $this->connection = new Connection();
    $this->controller = new CsvUploadController($this->connection);

    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@csvtest.com'");
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@acme.com'");
    $pdo->exec("DELETE FROM companies WHERE name LIKE 'CSV Test%'");
    $pdo->exec("DELETE FROM companies WHERE name = 'ACME Corporation'");
});

afterEach(function ()
{
    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@csvtest.com'");
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@acme.com'");
    $pdo->exec("DELETE FROM companies WHERE name LIKE 'CSV Test%'");
    $pdo->exec("DELETE FROM companies WHERE name = 'ACME Corporation'");
});

test('can parse valid CSV content with company names', function ()
{
    $csvContent = "Company Name,Employee Name,Email Address,Salary\nACME Corporation,John Doe,johndoe@csvtest.com,50000\nACME Corporation,Jane Smith,janesmith@csvtest.com,60000";

    $result = $this->controller->parseCsv($csvContent);

    expect($result)->toBeArray()
        ->and(count($result))->toBe(2)
        ->and($result[0])->toHaveKeys(['Company Name', 'Employee Name', 'Email Address', 'Salary'])
        ->and($result[0]['Employee Name'])->toBe('John Doe')
        ->and($result[0]['Company Name'])->toBe('ACME Corporation')
        ->and($result[1]['Email Address'])->toBe('janesmith@csvtest.com');
});

test('can validate CSV headers', function ()
{
    $csvContent = "Company Name,Employee Name,Email Address,Salary\nCSV Corporation,John Doe,john@csvtest.com,50000";

    $result = $this->controller->validateCsvHeaders($csvContent);

    expect($result)->toBeTrue();
});

test('rejects CSV with missing required headers', function ()
{
    $csvContent = "Name,Email\nJohn Doe,john@csvtest.com";

    $result = $this->controller->validateCsvHeaders($csvContent);

    expect($result)->toBeFalse();
});

test('can import employees with companies from CSV', function ()
{
    $csvContent = "Company Name,Employee Name,Email Address,Salary\nACME Corporation,John Doe,johndoe@csvtest.com,50000\nACME Corporation,Jane Doe,janedoe@csvtest.com,55000";

    $result = $this->controller->importFromCsv($csvContent);

    expect($result)->toBeArray()
        ->and($result['success'])->toBeTrue()
        ->and($result['companies_created'])->toBe(1)
        ->and($result['employees_imported'])->toBe(2)
        ->and($result['employees_failed'])->toBe(0);

    // Verify company was created
    $pdo = $this->connection->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE name = ?");
    $stmt->execute(['ACME Corporation']);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    expect($company)->toBeArray()
        ->and($company['name'])->toBe('ACME Corporation');

    // Verify employees were created
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->execute(['johndoe@csvtest.com']);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    expect($employee)->toBeArray()
        ->and($employee['full_name'])->toBe('John Doe')
        ->and($employee['salary'])->toBe('50000.00')
        ->and($employee['company_id'])->toBe($company['id']);
});

test('handles duplicate emails in CSV import', function ()
{
    // CSV with duplicate email in same file
    $csvContent = "Company Name,Employee Name,Email Address,Salary
                   ACME Corporation,John Doe,johndoe@csvtest.com,50000
                   ACME Corporation,Jane Doe,johndoe@csvtest.com,55000";

    $result = $this->controller->importFromCsv($csvContent);

    expect($result)->toBeArray()
        ->and($result['employees_imported'])->toBe(1)
        ->and($result['employees_failed'])->toBe(1)
        ->and($result['errors'])->toBeArray()
        ->and(count($result['errors']))->toBe(1);
});

test('can handle empty CSV', function ()
{
    $csvContent = "Company Name,Employee Name,Email Address,Salary";

    $result = $this->controller->parseCsv($csvContent);

    expect($result)->toBeArray()
        ->and(count($result))->toBe(0);
});

