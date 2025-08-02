<?php

header('Content-Type: application/json');

require_once 'helpers/JsonStorage.php';
require_once 'models/User.php';
require_once 'controllers/UserController.php';
require_once 'routes/auth.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($path, '/auth') !== false) {
    require_once 'routes/auth.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}