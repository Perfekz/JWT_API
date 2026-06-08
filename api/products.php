<?php


// dirname(__DIR__) le dice a PHP: "Dame la ruta absoluta de la carpeta padre de api"
require_once dirname(__DIR__) . '/seguridad.php';

// Si pasó el filtro de arriba, ejecutamos el código del CRUD con confianza
header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'mensaje' => 'Conexión segura establecida desde la carpeta API.',
    'usuario_id' => $usuario_autenticado->id
]);

?>