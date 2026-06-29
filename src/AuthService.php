<?php
// src/AuthService.php
namespace App;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

require_once __DIR__ . '/AuthServiceInterface.php';

class AuthService implements AuthInterface {

    private string $secretKey;
    private int $expiracion = 3600;

    public function __construct() {
        $this->secretKey = $this->leerEnv('JWT_SECRET_KEY');
        if (!$this->secretKey) {
            throw new Exception("JWT_SECRET_KEY no configurada en .env");
        }
    }

    // generar token con firebase/php-jwt
    public function generarToken(string $username): string {
        $payload = [
            'iss' => 'http://localhost',
            'iat' => time(),
            'exp' => time() + $this->expiracion,
            'data' => [
                'usuario' => $username,
                'rol'     => 'admin'
            ]
        ];
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    //validar token
    public function validarToken(string $token): object {
        return JWT::decode($token, new Key($this->secretKey, 'HS256'));
    }

    // password_hash con BCRYPT
    public function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // password_verify
    public function verificarPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    // Función privada para leer variables de entorno desde un archivo .env
    private function leerEnv(string $key): string {
        $archivo = __DIR__ . '/../.env';
        if (!file_exists($archivo)) return '';
        foreach (file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
            if (str_starts_with(trim($linea), '#')) continue;
            [$k, $v] = explode('=', $linea, 2);
            if (trim($k) === $key) return trim($v);
        }
        return '';
    }
}