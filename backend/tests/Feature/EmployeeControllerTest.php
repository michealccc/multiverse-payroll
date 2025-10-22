<?php

use Controllers\EmployeeController;
use Database\Connection;

beforeEach(function () {
    $this->connection = new Connection();
    $this->controller = new EmployeeController($this->connection);

    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@test.com'");
});

afterEach(function () {
    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@test.com'");
});

test('can list all employees', function () {
    $employees = $this->controller->index();

    expect($employees)->toBeArray()
        ->and(count($employees))->toBeGreaterThan(0)
        ->and($employees[0])->toHaveKeys(['id', 'company_id', 'full_name', 'email', 'salary']);
});

test('can get single employee by id', function () {
    // Get first employee from seed data
    $employees = $this->controller->index();
    $firstEmployee = $employees[0];

    $employee = $this->controller->show($firstEmployee['id']);

    expect($employee)->toBeArray()
        ->and($employee['id'])->toBe($firstEmployee['id'])
        ->and($employee['email'])->toBe($firstEmployee['email']);
});

test('can create new employee', function () {
    $pdo = $this->connection->getConnection();

    // Get BingBong LLC company ID
    $stmt = $pdo->query("SELECT id FROM companies WHERE name='BingBong LLC'");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    $data = [
        'company_id' => $company['id'],
        'full_name' => 'Test Employee',
        'email' => 'testemployee@test.com',
        'salary' => 60000.00
    ];

    $employee = $this->controller->store($data);

    expect($employee)->toBeArray()
        ->and($employee['full_name'])->toBe('Test Employee')
        ->and($employee['email'])->toBe('testemployee@test.com')
        ->and($employee['salary'])->toBe('60000.00')
        ->and($employee['id'])->toBeGreaterThan(0);
});

test('can update employee', function () {
    $pdo = $this->connection->getConnection();

    // Get BingBong LLC company ID
    $stmt = $pdo->query("SELECT id FROM companies WHERE name='BingBong LLC'");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create test employee
    $createData = [
        'company_id' => $company['id'],
        'full_name' => 'Update Test',
        'email' => 'updatetest@test.com',
        'salary' => 45000.00
    ];

    $employee = $this->controller->store($createData);

    // Update employee
    $updateData = [
        'full_name' => 'Updated Name',
        'salary' => 50000.00
    ];

    $updated = $this->controller->update($employee['id'], $updateData);

    expect($updated)->toBeArray()
        ->and($updated['full_name'])->toBe('Updated Name')
        ->and($updated['salary'])->toBe('50000.00')
        ->and($updated['email'])->toBe('updatetest@test.com'); // Email unchanged
});

test('can delete employee', function () {
    $pdo = $this->connection->getConnection();

    // Get BingBong LLC company ID
    $stmt = $pdo->query("SELECT id FROM companies WHERE name='BingBong LLC'");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create test employee
    $createData = [
        'company_id' => $company['id'],
        'full_name' => 'Delete Test',
        'email' => 'deletetest@test.com',
        'salary' => 45000.00
    ];

    $employee = $this->controller->store($createData);
    $employeeId = $employee['id'];

    // Delete employee
    $result = $this->controller->destroy($employeeId);

    expect($result)->toBeTrue();

    // Verify employee is deleted
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$employeeId]);
    $deleted = $stmt->fetch(PDO::FETCH_ASSOC);

    expect($deleted)->toBeFalse();
});

test('cannot create employee with duplicate email', function () {
    $pdo = $this->connection->getConnection();

    // Get BingBong LLC company ID
    $stmt = $pdo->query("SELECT id FROM companies WHERE name='BingBong LLC'");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    $data = [
        'company_id' => $company['id'],
        'full_name' => 'Duplicate Email Test',
        'email' => 'bingbong@bingbong.com', // Existing email from seed data
        'salary' => 60000.00
    ];

    $this->controller->store($data);
})->throws(PDOException::class);

test('cannot create employee with invalid company_id', function () {
    $data = [
        'company_id' => 99999, // Non-existent company
        'full_name' => 'Invalid Company Test',
        'email' => 'invalidcompany@test.com',
        'salary' => 60000.00
    ];

    $this->controller->store($data);
})->throws(PDOException::class);
