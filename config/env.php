<?php
/**
 * config/env.php — Carga de variables de entorno
 *
 * Lee el archivo .env de la raíz del proyecto y define constantes globales.
 * El .env NO se sube a Git (está en .gitignore): cada persona tiene el suyo.
 * Si todavía no existe, usamos .env.example como plantilla para que el sitio
 * igual funcione al clonarlo por primera vez.
 */

function cargar_env(string $ruta): array
{
    $variables = [];

    foreach (file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $linea = trim($linea);

        // Ignoramos comentarios (# ...) y líneas sin "="
        if ($linea === '' || $linea[0] === '#' || !str_contains($linea, '=')) {
            continue;
        }

        [$clave, $valor] = explode('=', $linea, 2);
        // Sacamos espacios y comillas alrededor del valor
        $variables[trim($clave)] = trim(trim($valor), "\"'");
    }

    return $variables;
}

$raiz = dirname(__DIR__);
$archivo_env = file_exists($raiz . '/.env') ? $raiz . '/.env' : $raiz . '/.env.example';
$env = cargar_env($archivo_env);

// Constantes globales disponibles en todo el sitio (con valor por defecto por si falta alguna)
define('APP_NAME',  $env['APP_NAME']  ?? 'RIFT OVERDRIVE');
define('APP_EMAIL', $env['APP_EMAIL'] ?? 'riftoverdrive@example.com');
define('APP_ENV',   $env['APP_ENV']   ?? 'local');

// En "local" mostramos los errores de PHP para depurar; en producción se ocultan.
if (APP_ENV === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}