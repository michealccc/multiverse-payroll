<?php

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Multiverse Payroll API',
    'version' => '1.0.0',
    'timestamp' => date('Y-m-d H:i:s')
]);
