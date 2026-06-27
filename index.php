<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\AuthService;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rutas públicas (no requieren token)
if (str_ends_with($uri, 'login.php')) {
    require __DIR__ . '/login.php'; exit;
}
if (str_ends_with($uri, 'register.php')) {
    require __DIR__ . '/register.php'; exit;
}

// ── Verificación del token ──
$authHeader = null;
if (isset($_SERVER['Authorization']))       $authHeader = trim($_SERVER['Authorization']);
elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) $authHeader = trim($_SERVER['HTTP_AUTHORIZATION']);
elseif (function_exists('apache_request_headers')) {
    $h = apache_request_headers();
    if (isset($h['Authorization'])) $authHeader = trim($h['Authorization']);
}

$token = null;
if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $m)) {
    $token = $m[1];
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Acceso denegado. Token no suministrado.']);
    exit;
}

try {
    $auth = new AuthService();
    $auth->validarToken($token); // lanza Exception si es inválido
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido o expirado: ' . $e->getMessage()]);
    exit;
}

// ── Centralización con switch ──
switch (true) {
    case str_contains($uri, 'products'):
        require __DIR__ . '/api/products.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Ruta no encontrada']);
}