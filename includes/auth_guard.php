<?php
/**
 * includes/auth_guard.php — Guardián de rutas (TP8)
 *
 * Se incluye al INICIO de cada página protegida, antes de cualquier salida HTML:
 *
 *     require_once __DIR__ . '/includes/auth_guard.php';   // exige estar logueado
 *     verificarRol('admin');                               // (opcional) exige un rol
 *
 * 1) Autenticación: si no hay sesión iniciada → redirige a login.php?error=acceso_denegado
 * 2) Autorización:  verificarRol() responde 403 si el rol del usuario no es el requerido
 */
require_once __DIR__ . '/../config/sesion.php';
iniciar_sesion_segura();

// 1 · Autenticación
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'login.php?error=acceso_denegado');
    exit;
}

// 2 · Autorización por rol (se pueden pasar varios: verificarRol('admin', 'editor'))
function verificarRol(string ...$rolesPermitidos): void
{
    $rol = $_SESSION['rol'] ?? null;

    if ($rol === null || !in_array($rol, $rolesPermitidos, true)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
           . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
           . '<title>403 Prohibido — ' . h(APP_NAME) . '</title>'
           . '<link rel="stylesheet" href="' . BASE_URL . 'css/RIFT-OVERDRIVE.css">'
           . '</head><body><main class="seccion">'
           . '<h1>403 — Prohibido</h1>'
           . '<p>No tenés permisos para acceder a este recurso.</p>'
           . '<p><a href="' . BASE_URL . 'panel.php" class="boton">Ir a mi panel</a></p>'
           . '</main></body></html>';
        exit;
    }
}