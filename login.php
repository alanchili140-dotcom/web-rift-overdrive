<?php
/**
 * login.php — Inicio de sesión (TP8)
 *
 * Flujo:
 *   1) Si ya hay sesión → directo al panel.
 *   2) Si llega por POST: valida el token CSRF y los datos, busca al usuario con una
 *      consulta preparada y compara la contraseña con password_verify().
 *   3) Si las credenciales son correctas: session_regenerate_id(true) (anti Session Fixation),
 *      guarda los datos clave en $_SESSION y redirige a panel.php.
 *   4) Si no: mensaje genérico (no se aclara si falló el correo o la contraseña).
 */
$ruta_base = '';
$pagina_actual = 'login.php';
$titulo_pagina = 'Iniciar sesión — RIFT OVERDRIVE';
$descripcion_pagina = 'Inicia sesion para acceder a tu cuenta de RIFT OVERDRIVE.';
$nav_simple = true;

require_once __DIR__ . '/config/sesion.php';
iniciar_sesion_segura();

if (usuario_logueado()) {
    redirigir('panel.php');
}

$errores = [];
$email = '';
$aviso = '';
$exito = '';

// Mensajes que llegan por la URL (solo valores conocidos; nunca se imprime la URL tal cual)
if (($_GET['error'] ?? '') === 'acceso_denegado') {
    $aviso = 'Tenés que iniciar sesión para ver esa página.';
}
if (($_GET['registro'] ?? '') === 'ok') {
    $exito = '¡Cuenta creada! Ya podés iniciar sesión.';
}
if (($_GET['salio'] ?? '') === '1') {
    $exito = 'Cerraste sesión correctamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/config/conexion.php';

    if (!verificar_csrf($_POST['csrf'] ?? null)) {
        $errores['general'] = 'El formulario expiró. Recargá la página e intentá de nuevo.';
    } else {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errores['email'] = 'Ingresá un correo electrónico válido.';
        }
        if ($password === '') {
            $errores['password'] = 'Ingresá tu contraseña.';
        }

        if (!$errores) {
            try {
                // Consulta preparada: el correo viaja como dato, nunca dentro del SQL
                $sql = 'SELECT u.id, u.nombre_completo, u.email, u.password_hash, r.nombre AS rol
                        FROM usuarios u
                        INNER JOIN roles r ON r.id = u.rol_id
                        WHERE u.email = :email AND u.estado = 1 AND u.deleted_at IS NULL
                        LIMIT 1';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':email' => $email]);
                $usuario = $stmt->fetch();

                // Se verifica SIEMPRE contra un hash (el real o el de relleno) para igualar los tiempos
                $hash = $usuario ? $usuario['password_hash'] : HASH_FALSO;
                $valido = password_verify($password, $hash) && $usuario !== false;

                if ($valido) {
                    // Anti Session Fixation: ID nuevo y se elimina la sesión anterior del servidor
                    session_regenerate_id(true);
                    unset($_SESSION['csrf']);

                    $_SESSION['usuario_id']     = (int) $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
                    $_SESSION['usuario_email']  = $usuario['email'];
                    $_SESSION['rol']            = $usuario['rol'];
                    $_SESSION['login_en']       = time();

                    redirigir('panel.php');
                }

                $errores['general'] = 'Correo o contraseña incorrectos.';
            } catch (PDOException $ex) {
                error_log('login.php: ' . $ex->getMessage());
                $errores['general'] = 'No se pudo iniciar sesión por un problema del servidor. Probá más tarde.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
  <main id="contenido-principal">

  <!-- ===================== INICIO DE SESIÓN ===================== -->
  <section class="seccion login-seccion">
    <div class="login-caja">
      <h1>Iniciar sesión</h1>
      <p>Ingresá a tu cuenta de RIFT OVERDRIVE.</p>

      <?php if ($aviso !== ''): ?>
        <div class="mensaje-error" role="alert"><?= h($aviso) ?></div>
      <?php endif; ?>
      <?php if ($exito !== ''): ?>
        <div class="mensaje-exito" role="status"><?= h($exito) ?></div>
      <?php endif; ?>
      <?php if (isset($errores['general'])): ?>
        <div class="mensaje-error" role="alert"><?= h($errores['general']) ?></div>
      <?php endif; ?>

      <form action="login.php" method="post" class="formulario" novalidate>
        <input type="hidden" name="csrf" value="<?= h(token_csrf()) ?>">

        <div class="campo <?= isset($errores['email']) ? 'con-error' : '' ?>">
          <label for="email">Correo electrónico:</label>
          <input type="email" id="email" name="email" placeholder="tuemail@ejemplo.com"
                 value="<?= h($email) ?>" autocomplete="username" required>
          <span class="error-campo"><?= isset($errores['email']) ? h($errores['email']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['password']) ? 'con-error' : '' ?>">
          <label for="password">Contraseña:</label>
          <input type="password" id="password" name="password" placeholder="Tu contraseña"
                 autocomplete="current-password" required>
          <span class="error-campo"><?= isset($errores['password']) ? h($errores['password']) : '' ?></span>
        </div>

        <div class="botones-form">
          <button type="submit">Iniciar sesión</button>
        </div>

      </form>

      <p class="login-alt">
        ¿No tenés cuenta?
        <a href="registro.php">Registrate</a>
      </p>
    </div>

    <hr>
    <footer class="footer-seccion">
      <p>Iniciar sesión — RIFT OVERDRIVE</p>
    </footer>
  </section>

  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>