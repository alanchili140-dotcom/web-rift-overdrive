<?php
/**
 * clase5/funciones.php — Funciones de seguridad compartidas por los formularios
 *
 * Regla de la clase: "Sanitizar la entrada, escapar la salida".
 */

/**
 * ESCAPAR LA SALIDA: convierte caracteres especiales en entidades HTML.
 * Se usa SIEMPRE justo antes de imprimir un dato del usuario (echo / <?= ?>).
 * Así un "<script>alert(1)</script>" se muestra como texto y NO se ejecuta.
 */
function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * SANITIZAR LA ENTRADA: limpia un texto recibido del navegador.
 *  - trim(): saca espacios al principio y al final.
 *  - preg_replace(): elimina caracteres de control invisibles.
 * Si el dato no vino o llegó como array (ej.: nombre[]=x), devuelve ''.
 */
function limpiar_texto(mixed $valor): string
{
    if (!is_string($valor)) {
        return '';
    }
    $valor = trim($valor);
    return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $valor) ?? '';
}

/** Lee un campo de $_POST de forma segura (con filter_input) y lo sanitiza. */
function post(string $campo): string
{
    return limpiar_texto(filter_input(INPUT_POST, $campo, FILTER_DEFAULT));
}

/** Lee un parámetro de $_GET de forma segura (con filter_input) y lo sanitiza. */
function get(string $campo): string
{
    return limpiar_texto(filter_input(INPUT_GET, $campo, FILTER_DEFAULT));
}