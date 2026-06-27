<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\AuthService;

header('Content-Type: application/json');

function leerEnv(string $key): string {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        [$k, $v] = explode('=', $l, 2);
        if (trim($k) === $key) return trim($v);
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// Acepta tanto JSON como form-urlencoded (como en el repo original)
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (str_contains($contentType, 'application/json')) {
    $body    = json_decode(file_get_contents('php://input'), true);
    $usuario = trim($body['usuario'] ?? '');
    $clave   = trim($body['clave'] ?? '');
} else {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave   = trim($_POST['clave'] ?? '');
}

if (!$usuario || !$clave) {
    http_response_code(400);
    echo json_encode(['error' => 'usuario y clave son requeridos']);
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=" . leerEnv('DB_HOST') . ";dbname=" . leerEnv('DB_NAME') . ";charset=utf8mb4",
        leerEnv('DB_USER'), leerEnv('DB_PASS'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $auth = new AuthService();

    // password_verify()
    if (!$user || !$auth->verificarPassword($clave, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit;
    }

    $token = $auth->generarToken($usuario);
    http_response_code(200);
    echo json_encode([
        'token'     => $token,
        'expira_en' => '3600 segundos'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}