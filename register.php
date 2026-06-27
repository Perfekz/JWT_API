<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\AuthService;

function leerEnv(string $key): string {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        [$k, $v] = explode('=', $l, 2);
        if (trim($k) === $key) return trim($v);
    }
    return '';
}

$pdo = new PDO(
    "mysql:host=" . leerEnv('DB_HOST') . ";dbname=" . leerEnv('DB_NAME') . ";charset=utf8mb4",
    leerEnv('DB_USER'), leerEnv('DB_PASS'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$auth = new AuthService();
$hash = $auth->hashPassword('admin123'); // password_hash()

$stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE password = VALUES(password)");
$stmt->execute(['admin', $hash]);

header('Content-Type: application/json');
echo json_encode(['mensaje' => 'Usuario admin creado con contraseña hasheada (BCRYPT) ✅']);