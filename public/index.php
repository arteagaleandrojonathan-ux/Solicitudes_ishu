<?php

// Punto de entrada público. Las consultas y el formulario viven en app/.
require_once __DIR__ . '/../app/config/Conexion.php';
require_once __DIR__ . '/../app/models/SolicitudCatalogo.php';
require_once __DIR__ . '/../app/controllers/CatalogoController.php';

try {
    $whatsapp = getenv('ISHUME_WHATSAPP') ?: '51991842738';
    $controlador = new CatalogoController();
    $controlador->mostrar($whatsapp);
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'El catálogo no está disponible por el momento. Intenta nuevamente más tarde.';
}
