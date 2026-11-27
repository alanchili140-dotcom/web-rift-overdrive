<?php
/**
 * config/conexion.php — Conexión segura a MySQL con PDO
 *
 * Deja disponible la variable $pdo para quien incluya este archivo.
 * - Los datos de conexión vienen del .env (ver config/env.php).
 * - ERRMODE_EXCEPTION: los errores de SQL lanzan PDOException (se capturan con try-catch).
 * - EMULATE_PREPARES = false: las consultas preparadas las resuelve MySQL de verdad,
 *   así los datos nunca se mezclan con el SQL (protección contra inyección SQL).
 */
require_once __DIR__ . '/env.php';

$charset = 'utf8mb4';
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . $charset;

$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
} catch (PDOException $e) {
    // Guardamos el detalle técnico en el log del servidor...
    error_log('Error de conexión a la base de datos: ' . $e->getMessage());
    http_response_code(500);
    // ...y al visitante solo le mostramos el detalle si estamos en entorno local.
    $detalle = (APP_ENV === 'local') ? ' Detalle: ' . htmlspecialchars($e->getMessage()) : '';
    die('<p style="font-family:sans-serif;color:#fff;background:#111;padding:20px">'
      . 'No se pudo conectar con la base de datos. Revisá que MySQL esté iniciado en XAMPP '
      . 'y que hayas importado schema.sql.' . $detalle . '</p>');
}