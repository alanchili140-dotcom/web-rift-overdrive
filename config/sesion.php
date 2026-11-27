<?php
/**
 * config/sesion.php — Sesiones seguras y funciones de autenticación (TP8)
 *
 * Lo usan: login.php, registro.php, logout.php, panel.php, includes/header.php
 * e includes/auth_guard.php. Reúne en un solo lugar todo lo relacionado con la sesión:
 *   - iniciar_sesion_segura()  → session_start() con cookie httponly / samesite / strict mode
 *   - token_csrf() / verificar_csrf() → protección de los formularios contra CSRF
 *   - cerrar_sesion_completa() → logout (vacía $_SESSION, borra la cookie y destruye la sesión)
 *   - redirigir() y BASE_URL   → redirecciones que funcionan desde cualquier carpeta
 */
require_once __DIR__ . '/env.php';

/** Segundos de inactividad tras los cuales la sesión se considera vencida. */
const SESION_INACTIVIDAD = 1800; // 30 minutos

/**
 * Hash bcrypt "de relleno". Si el correo no existe, igual se ejecuta password_verify()
 * contra este hash para que la respuesta tarde lo mismo y no se pueda averiguar
 * qué correos están registrados (enumeración de usuarios).
 */
const HASH_FALSO = '$2y$10$9uvHN3Sreza5DkFDigh...j2lOfsVmKshM/6d61TzAI28mM/U9Tbu';

/** Escapa una salida para HTML (protección XSS). */
function h($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Limpia un texto: sin espacios sobrantes ni caracteres de control. */
function texto_limpio(?string $texto): string
{
    $texto = trim($texto ?? '');
    return preg_replace('/[^\P{C}\n\r\t]+/u', '', $texto) ?? '';
}

/** ¿La conexión actual es HTTPS? (para decidir el flag "secure" de la cookie) */
function es_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
}

/**
 * Ruta web de la raíz del proyecto, por ejemplo "/web-rift-overdrive/" en XAMPP
 * o "/" si el sitio está en la raíz del servidor. Sirve para armar redirecciones
 * que funcionan igual desde la raíz, desde admin/ o desde tp5/.
 */
function calcular_base_url(): string
{
    $docroot_cfg = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $docroot = $docroot_cfg !== '' ? realpath($docroot_cfg) : false;
    if ($docroot === false) {
        return '/';
    }
    $docroot = rtrim(str_replace('\\', '/', $docroot), '/');
    $raiz    = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');

    if ($docroot !== '' && stripos($raiz, $docroot) === 0) {
        $ruta = trim(substr($raiz, strlen($docroot)), '/');
        if ($ruta === '') {
            return '/';
        }
        $segmentos = array_map('rawurlencode', explode('/', $ruta));
        return '/' . implode('/', $segmentos) . '/';
    }
    return '/';
}
define('BASE_URL', calcular_base_url());

/** Redirige a una ruta del sitio (relativa a la raíz del proyecto) y corta el script. */
function redirigir(string $ruta): void
{
    header('Location: ' . BASE_URL . ltrim($ruta, '/'));
    exit;
}

/**
 * session_start() con la configuración recomendada por OWASP:
 *  - use_only_cookies: el ID de sesión nunca viaja por la URL
 *  - use_strict_mode:  PHP rechaza IDs de sesión que no creó él (ayuda contra Session Fixation)
 *  - httponly:         JavaScript no puede leer la cookie (mitiga robo por XSS)
 *  - secure:           la cookie solo viaja por HTTPS. Se activa cuando el sitio está en HTTPS;
 *                      en localhost con http:// queda apagado para que el login funcione.
 *  - samesite=Lax:     el navegador no envía la cookie en peticiones cruzadas peligrosas (CSRF)
 * Además vence la sesión tras SESION_INACTIVIDAD segundos sin actividad.
 */
function iniciar_sesion_segura(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,            // la cookie dura hasta cerrar el navegador
        'path'     => '/',
        'secure'   => es_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    $ahora = time();
    if (isset($_SESSION['ultimo_acceso']) && ($ahora - (int) $_SESSION['ultimo_acceso']) > SESION_INACTIVIDAD) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['ultimo_acceso'] = $ahora;
}

/** ¿Hay un usuario autenticado en esta sesión? */
function usuario_logueado(): bool
{
    return isset($_SESSION['usuario_id']);
}

/** Token anti-CSRF de la sesión (se crea la primera vez que se pide). */
function token_csrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Compara el token recibido con el de la sesión (comparación segura contra timing attacks). */
function verificar_csrf($token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], $token);
}

/** Logout completo: vacía $_SESSION, borra la cookie del navegador y destruye la sesión. */
function cerrar_sesion_completa(): void
{
    // 1 · Vaciar las variables de sesión
    $_SESSION = [];

    // 2 · Borrar la cookie de sesión del navegador
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }

    // 3 · Destruir la sesión en el servidor
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}