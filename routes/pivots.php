<?php

use Controllers\PivotController;
use Middlewares\AuthMiddleware;
use Helpers\JsonStorage;

require_once 'vendor/autoload.php';
require_once 'helpers/JsonStorage.php';
require_once 'models/Pivot.php';
require_once 'middlewares/AuthMiddleware.php';
require_once 'controllers/PivotController.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$auth = new AuthMiddleware();
$storage = new JsonStorage('storage/pivots.json');
$controller = new PivotController($storage);

if ($parts[1] === 'pivots') {
    $email = $auth->verify();
    if (!$email) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido']);
        return;
    }

    $id = $parts[2] ?? null;

    switch ($method) {
        case 'GET':
            echo json_encode($id ? $controller->show($id, $email) : $controller->read($email));
            break;

        case 'POST':
            $body = json_decode(file_get_contents('php://input'), true);
            $body['user_id'] = $email;
            echo json_encode($controller->create($body));
            break;

        case 'PUT':
            $body = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->update($id, $body, $email));
            break;

        case 'DELETE':
            echo json_encode($controller->delete($id, $email));
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
    }

}

