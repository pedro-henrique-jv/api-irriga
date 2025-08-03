<?php

header('Content-Type: application/json');

require_once 'helpers/JsonStorage.php';
require_once 'controllers/UserController.php';
require_once 'controllers/PivotController.php';
require_once 'controllers/IrrigationController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($path, '/auth') !== false) {
    require_once 'routes/auth.php';
} elseif (strpos($path, '/pivots') !== false) {
    require_once 'routes/pivots.php';
} elseif (strpos($path, '/irrigations') !== false) {
    require_once 'routes/irrigations.php';
} elseif ($path === '/api-irrigacao' || $path === '/api-irrigacao/') {
    echo json_encode([
        "nome" => "API de Irrigação",
        "descricao" => "API REST para controle de irrigação inteligente.",
    ]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}
