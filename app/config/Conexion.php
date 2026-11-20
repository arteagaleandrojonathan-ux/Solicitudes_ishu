<?php

class Conexion {

    // Las claves vienen del servidor o de un archivo local excluido de Git.
    public static function abrir(): PDO {
        $archivoLocal = __DIR__ . '/config.local.php';
        $local = is_file($archivoLocal) ? require $archivoLocal : [];

        $host = getenv('ISHUME_DB_HOST') ?: ($local['host'] ?? '127.0.0.1');
        $puerto = getenv('ISHUME_DB_PORT') ?: ($local['puerto'] ?? '3306');
        $base = getenv('ISHUME_DB_NAME') ?: ($local['base'] ?? 'BD_ISHUME_1');
        $usuario = getenv('ISHUME_DB_USER') ?: ($local['usuario'] ?? '');
        $clave = getenv('ISHUME_DB_PASSWORD');
        if ($clave === false) {
            $clave = $local['clave'] ?? '';
        }

        if ($usuario === '') {
            throw new RuntimeException('Falta configurar el usuario de la base de datos.');
        }

        return new PDO("mysql:host={$host};port={$puerto};dbname={$base};charset=utf8mb4", $usuario, $clave, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
