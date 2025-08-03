<?php

use Controllers\UserController;

require_once 'vendor/autoload.php';
require_once 'helpers/JsonStorage.php';
require_once 'models/User.php';
require_once 'controllers/UserController.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: application/json');

$controller = new UserController();

$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents('php://input'), true);
$basePath = 'api-irriga';
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = str_replace($basePath . '/', '', $path);
$parts = explode('/', $path);

if ($method === 'POST' && $parts[0] === 'auth' && $parts[1] === 'register') {
    echo json_encode($controller->create($body));
    return;
}

if ($method === 'POST' && $parts[0] === 'auth' && $parts[1] === 'login') {
    echo json_encode($controller->login($body));
    return;
}

// Caso a rota não seja reconhecida
http_response_code(404);
echo json_encode(['error' => 'Rota não encontrada']);
