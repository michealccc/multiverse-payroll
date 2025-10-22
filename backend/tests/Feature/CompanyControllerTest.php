<?php

use Controllers\CompanyController;
use Database\Connection;

beforeEach(function ()
{
    $this->connection = new Connection();
    $this->controller = new CompanyController($this->connection);

    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM companies WHERE name LIKE '%Test Company%'");
});

afterEach(function ()
{
    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM companies WHERE name LIKE '%Test Company%'");
});

test('can list all companies', function ()
{
    $companies = $this->controller->index();

    expect($companies)->toBeArray()
        ->and(count($companies))->toBeGreaterThan(0)
        ->and($companies[0])->toHaveKeys(['id', 'name']);
});

test('can get single company by id', function ()
{
    // Get first company from seed data
    $companies = $this->controller->index();
    $firstCompany = $companies[0];

    $company = $this->controller->show($firstCompany['id']);

    expect($company)->toBeArray()
        ->and($company['id'])->toBe($firstCompany['id'])
        ->and($company['name'])->toBe($firstCompany['name']);
});

test('can create new company', function ()
{
    $data = [
        'name' => 'Test Company Inc'
    ];

    $company = $this->controller->store($data);

    expect($company)->toBeArray()
        ->and($company['name'])->toBe('Test Company Inc')
        ->and($company['id'])->toBeGreaterThan(0);
});

test('can update company', function ()
{
    // Create test company
    $createData = [
        'name' => 'Test Company Original'
    ];

    $company = $this->controller->store($createData);

    // Update company
    $updateData = [
        'name' => 'Test Company Updated'
    ];

    $updated = $this->controller->update($company['id'], $updateData);

    expect($updated)->toBeArray()
        ->and($updated['name'])->toBe('Test Company Updated')
        ->and($updated['id'])->toBe($company['id']);
});

test('can delete company without employees', function ()
{
    // Create test company
    $createData = [
        'name' => 'Test Company To Delete'
    ];

    $company = $this->controller->store($createData);
    $companyId = $company['id'];

    // Delete company
    $result = $this->controller->destroy($companyId);

    expect($result)->toBeTrue();

    // Verify company is deleted
    $pdo = $this->connection->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$companyId]);
    $deleted = $stmt->fetch(PDO::FETCH_ASSOC);

    expect($deleted)->toBeFalse();
});

test('cannot delete company with employees', function ()
{
    $pdo = $this->connection->getConnection();

    // Get BingBong LLC which has employees
    $stmt = $pdo->query("SELECT id FROM companies WHERE name='BingBong LLC'");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    // Attempt to delete should throw exception
    $this->controller->destroy($company['id']);
})->throws(PDOException::class);

test('cannot create company with duplicate name', function ()
{
    $data = [
        'name' => 'BingBong LLC' // Existing company from seed data
    ];

    $this->controller->store($data);
})->throws(PDOException::class);

test('can get company with employee count', function ()
{
    $pdo = $this->connection->getConnection();

    // Get BingBong LLC which has 2 employees
    $stmt = $pdo->query("SELECT id FROM companies WHERE name='BingBong LLC'");
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    $companyWithCount = $this->controller->showWithEmployeeCount($company['id']);

    expect($companyWithCount)->toBeArray()
        ->and($companyWithCount['name'])->toBe('BingBong LLC')
        ->and($companyWithCount['employee_count'])->toBeGreaterThanOrEqual(2);
});

test('can list all companies with employee counts', function ()
{
    $companies = $this->controller->indexWithEmployeeCounts();

    expect($companies)->toBeArray()
        ->and(count($companies))->toBeGreaterThan(0)
        ->and($companies[0])->toHaveKeys(['id', 'name', 'employee_count']);
});
