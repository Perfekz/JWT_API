<?PHP

include_once 'config/config.php';
// login.php
require_once str_replace('\\', '/', __DIR__) . '/vendor/autoload.php';
use Firebase\JWT\JWT;

// Simulación de datos recibidos (POST)
$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? '';

// Validamos (reemplaza con tu lógica de BD si quieres)
if ($usuario === JWT_USER_SECRET && $clave === JWT_CLAVE_SECRET) {
    $clave_secreta = "tu_clave_secreta_super_segura_123";
    $algoritmo = 'HS256';
    
    $payload = [
        'iss' => 'http://localhost',
        'iat' => time(),
        'exp' => time() + 3600, // Expira en 1 hora
        'data' => [
            'id' => 45,
            'usuario' => 'admin',
            'rol' => 'profesor'
        ]
    ];

    $jwt = JWT::encode($payload, $clave_secreta, $algoritmo);

    header('Content-Type: application/json');
    echo json_encode(['token' => $jwt]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales inválidas']);
}

?>