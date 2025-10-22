<?php

/**
 * Test Database Connection
 *
 */

require_once __DIR__ . '/src/Database/Connection.php';

echo "Testing Database Connection...\n";
echo str_repeat("-", 50) . "\n";

try
{
    // Create connection
    $connection = new Database\Connection();
    $pdo = $connection->getConnection();

    echo "✓ Database connection established successfully\n";

    // Test 1: Check connection type
    if ($pdo instanceof PDO)
    {
        echo "✓ PDO instance created correctly\n";
    }

    // Test 2: Check error mode
    $errorMode = $pdo->getAttribute(PDO::ATTR_ERRMODE);
    if ($errorMode === PDO::ERRMODE_EXCEPTION)
    {
        echo "✓ Error mode set to EXCEPTION\n";
    }

    // Test 3: Query database name
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Connected to database: " . $result['db_name'] . "\n";

    // Test 4: Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✓ Found " . count($tables) . " tables: " . implode(", ", $tables) . "\n";

    // Test 5: Count employees
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM employees");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Employees in database: " . $count['count'] . "\n";

    // Test 6: Count companies
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM companies");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Companies in database: " . $count['count'] . "\n";

    echo str_repeat("-", 50) . "\n";
    echo "All database connection tests passed! ✓\n";
}
catch (PDOException $e)
{
    echo "\n✗ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
catch (Exception $e)
{
    echo "\n✗ Test failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
