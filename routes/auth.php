<?php

use Controllers\UserController;
use Middlewares\AuthMiddleware;

require_once 'vendor/autoload.php';
require_once 'helpers/JsonStorage.php';
require_once 'models/User.php';
require_once 'controllers/UserController.php';
require_once 'middlewares/AuthMiddleware.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

header('Content-Type: application/json');

$controller = new UserController();
$auth = new AuthMiddleware();

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

if ($parts[0] === 'auth' && $parts[1] === 'users') {
    $userEmail = $auth->verify();

    if (!$userEmail) {
        http_response_code(401);
        echo json_encode(['error' => 'Acesso negado. Token inválido ou ausente.']);
        return;
    }

    $id = $parts[2] ?? null;

    switch ($method) {
        case 'GET':
            echo json_encode($id ? $controller->getById($id) : $controller->getAll());
            break;

        case 'POST':
            echo json_encode($controller->create($body));
            break;

        case 'PUT':
            echo json_encode($controller->update($id, $body));
            break;

        case 'DELETE':
            echo json_encode($controller->delete($id));
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}