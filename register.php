<?php
require_once __DIR__ . '/vendor/autoload.php';
use App\AuthService;

// Función para leer variables de entorno desde un archivo .env
function leerEnv(string $key): string {
    foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        [$k, $v] = explode('=', $l, 2);
        if (trim($k) === $key) return trim($v);
    }
    return '';
}

// Conexión a la base de datos
$pdo = new PDO(
    "mysql:host=" . leerEnv('DB_HOST') . ";dbname=" . leerEnv('DB_NAME') . ";charset=utf8mb4",
    leerEnv('DB_USER'), leerEnv('DB_PASS'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Crear un usuario "houzheng" con contraseña hasheada (BCRYPT)
$auth = new AuthService();
$hash = $auth->hashPassword('houzheng67'); // password_hash() al cambiar los valores de la contraseña hay que volver a abrir register.php para que se vuelva a hashear y actualizar en la base de datos, si no se hace esto no se podrá loguear con la nueva contraseña.

// Insertar o actualizar el usuario en la base de datos
$stmt = $pdo->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE password = VALUES(password)");
$stmt->execute(['houzheng', $hash]); // Inserta o actualiza el usuario "houzheng" con la contraseña hasheada

header('Content-Type: application/json');
echo json_encode(['mensaje' => 'Usuario houzheng creado con contraseña hasheada (BCRYPT) ✅']);