<?php
// src/AuthService.php
namespace App;

interface AuthInterface {
    public function generarToken(string $username): string;
    public function validarToken(string $token): object;
    public function hashPassword(string $password): string;
    public function verificarPassword(string $password, string $hash): bool;
}