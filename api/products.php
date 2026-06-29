<?php
// Token ya validado en index.php

// Función para leer variables de entorno desde un archivo .env
function leerEnv(string $key): string {
    foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        [$k, $v] = explode('=', $l, 2);
        if (trim($k) === $key) return trim($v);
    }
    return '';
}

// Conexión a la base de datos
try {
    $pdo = new PDO(
        "mysql:host=" . leerEnv('DB_HOST') . ";dbname=" . leerEnv('DB_NAME') . ";charset=utf8mb4",
        leerEnv('DB_USER'), leerEnv('DB_PASS'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error BD: ' . $e->getMessage()]); exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$id     = $_GET['id'] ?? null;

switch ($method) {

    case 'GET':
        if ($id) {
            $s = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
            $s->execute([$id]);
            $p = $s->fetch(PDO::FETCH_ASSOC);
            if (!$p) { http_response_code(404); echo json_encode(['error' => 'Producto no encontrado']); }
            else      { http_response_code(200); echo json_encode($p); }
        } else {
            $s = $pdo->query("SELECT * FROM productos");
            http_response_code(200);
            echo json_encode($s->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        foreach (['codigo','producto','precio','cantidad'] as $campo) {
            if (empty($body[$campo])) {
                http_response_code(400);
                echo json_encode(['error' => "Campo '$campo' requerido"]); exit;
            }
        }
        try {
            $s = $pdo->prepare("INSERT INTO productos (codigo,producto,precio,cantidad) VALUES (?,?,?,?)");
            $s->execute([$body['codigo'], $body['producto'], (float)$body['precio'], (int)$body['cantidad']]);
            http_response_code(201);
            echo json_encode(['mensaje' => 'Producto creado', 'id' => $pdo->lastInsertId()]);
        } catch (PDOException $e) {
            http_response_code(409);
            echo json_encode(['error' => 'Código duplicado: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requerido (?id=X)']); exit; }
        foreach (['codigo','producto','precio','cantidad'] as $campo) {
            if (!isset($body[$campo])) {
                http_response_code(400);
                echo json_encode(['error' => "Campo '$campo' requerido"]); exit;
            }
        }
        $s = $pdo->prepare("UPDATE productos SET codigo=?,producto=?,precio=?,cantidad=? WHERE id=?");
        $s->execute([$body['codigo'], $body['producto'], (float)$body['precio'], (int)$body['cantidad'], $id]);
        if ($s->rowCount() === 0) { http_response_code(404); echo json_encode(['error' => 'No encontrado o sin cambios']); }
        else                      { http_response_code(200); echo json_encode(['mensaje' => 'Producto actualizado']); }
        break;

    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID requerido (?id=X)']); exit; }
        $s = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $s->execute([$id]);
        if ($s->rowCount() === 0) { http_response_code(404); echo json_encode(['error' => 'No encontrado']); }
        else                      { http_response_code(200); echo json_encode(['mensaje' => 'Producto eliminado']); }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}