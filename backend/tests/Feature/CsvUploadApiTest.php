<?php

use Database\Connection;

beforeEach(function ()
{
    $this->connection = new Connection();

    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@apitest.com'");
    $pdo->exec("DELETE FROM companies WHERE name LIKE 'API Test%'");
});

afterEach(function ()
{
    // Clean up test data
    $pdo = $this->connection->getConnection();
    $pdo->exec("DELETE FROM employees WHERE email LIKE '%@apitest.com'");
    $pdo->exec("DELETE FROM companies WHERE name LIKE 'API Test%'");
});

test('can upload CSV via API endpoint', function ()
{
    $csvContent = "Company Name,Employee Name,Email Address,Salary
API Test Corp,John Doe,johndoe@apitest.com,50000
API Test Corp,Jane Smith,janesmith@apitest.com,60000";

    $response = makeApiRequest('POST', '/csv/upload', [
        'csv_content' => $csvContent
    ]);

    expect($response['success'])->toBeTrue()
        ->and($response['data'])->toHaveKeys(['companies_created', 'employees_imported', 'employees_failed', 'errors', 'total_rows'])
        ->and($response['data']['companies_created'])->toBe(1)
        ->and($response['data']['employees_imported'])->toBe(2)
        ->and($response['data']['employees_failed'])->toBe(0);
});

test('validates CSV headers via API', function ()
{
    $csvContent = "Name,Email
John Doe,john@apitest.com";

    $response = makeApiRequest('POST', '/csv/upload', [
        'csv_content' => $csvContent
    ], 400);

    expect($response['success'])->toBeFalse()
        ->and($response['message'])->toContain('Invalid CSV headers');
});

test('requires csv_content parameter', function ()
{
    $response = makeApiRequest('POST', '/csv/upload', [], 400);

    expect($response['success'])->toBeFalse()
        ->and($response['message'])->toContain('Missing required field: csv_content');
});

// Helper function to make API requests
function makeApiRequest(string $method, string $path, array $data = [], int $expectedStatusCode = 200): array
{
    $url = "http://multiverse_nginx/api" . $path;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data))
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    expect($statusCode)->toBe($expectedStatusCode);

    return json_decode($response, true);
}
