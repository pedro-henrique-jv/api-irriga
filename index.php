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
} else if(strpos($path, '/irrigations') !== false) {
    require_once 'routes/irrigations.php';
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Rota não encontrada']);
}
