<?php
// seguridad.php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 1. Configuración básica
$clave_secreta = 'tu_clave_secreta_super_segura_123';
$algoritmo = 'HS256';


/*Cuando desarrollas APIs, el mayor dolor de cabeza es que cada 
servidor web (Apache, Nginx, IIS) y cada configuración de PHP
 maneja los encabezados HTTP de forma ligeramente distinta.*/
 
// 2. Capturar el encabezado Authorization de forma robusta
$encabezado_auth = null;
if (isset($_SERVER['Authorization'])) {
    $encabezado_auth = trim($_SERVER["Authorization"]);
} elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $encabezado_auth = trim($_SERVER["HTTP_AUTHORIZATION"]);
} elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $encabezado_auth = trim($headers['Authorization']);
    }
}

// 3. Verificar si el token existe en el encabezado
$token = null;
if (!empty($encabezado_auth) && preg_match('/Bearer\s(\S+)/', $encabezado_auth, $matches)) {
    $token = $matches[1];
}

if (!$token) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso denegado. Token no suministrado.']);
    exit(); // Detiene la aplicación aquí mismo
}

// 4. Validar el token con la sintaxis moderna (Manejo de Excepciones)
try {
    // CORRECCIÓN CLAVE: Usamos 'new Key()' exigido por la biblioteca actual
    $datos_decodificados = JWT::decode($token, new Key($clave_secreta, $algoritmo));
    
    // Si todo sale bien, guardamos los datos del usuario en una variable global
    $usuario_autenticado = $datos_decodificados->data;

} catch (\Firebase\JWT\ExpiredException $e) {
    responderError('El token ha expirado.', 401);
} catch (\Firebase\JWT\SignatureInvalidException $e) {
    responderError('Firma de token inválida.', 401);
} catch (\Exception $e) {
    responderError('Token inválido o corrupto.', 401);
}

// Función auxiliar para no repetir código de error
function responderError($mensaje, $codigo) {
    http_response_code($codigo);
    header('Content-Type: application/json');
    echo json_encode(['error' => $mensaje]);
    exit();
}
?>