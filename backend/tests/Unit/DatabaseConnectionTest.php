<?php

use Database\Connection;

test('can establish database connection', function () {
    $connection = new Connection();
    $pdo = $connection->getConnection();

    expect($pdo)->toBeInstanceOf(PDO::class);
});

test('connection uses correct error mode', function () {
    $connection = new Connection();
    $pdo = $connection->getConnection();

    $errorMode = $pdo->getAttribute(PDO::ATTR_ERRMODE);

    expect($errorMode)->toBe(PDO::ERRMODE_EXCEPTION);
});

test('can query database', function () {
    $connection = new Connection();
    $pdo = $connection->getConnection();

    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    expect($result['test'])->toBe(1);
});

test('can access multiverse_db database', function () {
    $connection = new Connection();
    $pdo = $connection->getConnection();

    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    expect($result['db_name'])->toBe('multiverse_db');
});
