<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\EmployeeController;
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
