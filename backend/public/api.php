<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\EmployeeController;
use Controllers\CompanyController;
use Controllers\CsvUploadController;
use Database\Connection;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
{
    http_response_code(204);
    exit;
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$path = parse_url($requestUri, PHP_URL_PATH);
$path = str_replace('/api.php', '', $path);
$pathParts = array_filter(explode('/', $path));
$pathParts = array_values($pathParts);

$connection = new Connection();
$employeeController = new EmployeeController($connection);
$companyController = new CompanyController($connection);
$csvUploadController = new CsvUploadController($connection);

try
{
    // Route: GET /employees - List all employees
    if ($requestMethod === 'GET' && count($pathParts) === 1 && $pathParts[0] === 'employees')
    {
        $employees = $employeeController->index();
        echo json_encode([
            'success' => true,
            'data' => $employees
        ]);
        exit;
    }

    // Route: GET /employees/{id} - Get single employee
    if ($requestMethod === 'GET' && count($pathParts) === 2 && $pathParts[0] === 'employees')
    {
        $id = (int)$pathParts[1];
        $employee = $employeeController->show($id);

        if ($employee === false)
        {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Employee not found'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $employee
        ]);
        exit;
    }

    // Route: POST /employees - Create new employee
    if ($requestMethod === 'POST' && count($pathParts) === 1 && $pathParts[0] === 'employees')
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['company_id'], $input['full_name'], $input['email'], $input['salary']))
        {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields: company_id, full_name, email, salary'
            ]);
            exit;
        }

        $employee = $employeeController->store($input);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'data' => $employee
        ]);
        exit;
    }

    // Route: PUT /employees/{id} - Update employee
    if ($requestMethod === 'PUT' && count($pathParts) === 2 && $pathParts[0] === 'employees')
    {
        $id = (int)$pathParts[1];
        $input = json_decode(file_get_contents('php://input'), true);

        $employee = $employeeController->update($id, $input);

        if ($employee === false)
        {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Employee not found'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $employee
        ]);
        exit;
    }

    // Route: DELETE /employees/{id} - Delete employee
    if ($requestMethod === 'DELETE' && count($pathParts) === 2 && $pathParts[0] === 'employees')
    {
        $id = (int)$pathParts[1];

        $result = $employeeController->destroy($id);

        if ($result)
        {
            echo json_encode([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);
        }
        else
        {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Employee not found'
            ]);
        }
        exit;
    }

    // Route: GET /companies - List all companies with average salaries
    if ($requestMethod === 'GET' && count($pathParts) === 1 && $pathParts[0] === 'companies')
    {
        $companies = $companyController->indexWithAverageSalaries();
        echo json_encode([
            'success' => true,
            'data' => $companies
        ]);
        exit;
    }

    // Route: GET /companies/{id} - Get single company with average salary
    if ($requestMethod === 'GET' && count($pathParts) === 2 && $pathParts[0] === 'companies')
    {
        $id = (int)$pathParts[1];
        $company = $companyController->showWithAverageSalary($id);

        if ($company === false)
        {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Company not found'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $company
        ]);
        exit;
    }

    // Route: POST /companies - Create new company
    if ($requestMethod === 'POST' && count($pathParts) === 1 && $pathParts[0] === 'companies')
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['name']))
        {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing required field: name'
            ]);
            exit;
        }

        $company = $companyController->store($input);

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'data' => $company
        ]);
        exit;
    }

    // Route: PUT /companies/{id} - Update company
    if ($requestMethod === 'PUT' && count($pathParts) === 2 && $pathParts[0] === 'companies')
    {
        $id = (int)$pathParts[1];
        $input = json_decode(file_get_contents('php://input'), true);

        $company = $companyController->update($id, $input);

        if ($company === false)
        {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Company not found'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $company
        ]);
        exit;
    }

    // Route: DELETE /companies/{id} - Delete company
    if ($requestMethod === 'DELETE' && count($pathParts) === 2 && $pathParts[0] === 'companies')
    {
        $id = (int)$pathParts[1];

        $result = $companyController->destroy($id);

        if ($result)
        {
            echo json_encode([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);
        }
        else
        {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Company not found'
            ]);
        }
        exit;
    }

    // Route: POST /csv/upload - Upload CSV file
    if ($requestMethod === 'POST' && count($pathParts) === 2 && $pathParts[0] === 'csv' && $pathParts[1] === 'upload')
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['csv_content']))
        {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing required field: csv_content'
            ]);
            exit;
        }

        $csvContent = $input['csv_content'];

        // Validate CSV headers
        if (!$csvUploadController->validateCsvHeaders($csvContent))
        {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid CSV headers. Required headers: Company Name, Employee Name, Email Address, Salary'
            ]);
            exit;
        }

        // Import CSV
        $result = $csvUploadController->importFromCsv($csvContent);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
        exit;
    }

    // No route matched
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Route not found'
    ]);
}
catch (PDOException $e)
{
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
catch (Exception $e)
{
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
