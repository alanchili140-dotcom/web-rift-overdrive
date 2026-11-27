<?php
/**
 * registro.php — Alta de usuarios (TP8)
 *
 * - Sanitiza y valida todo en el servidor (y escapa la salida con h() contra XSS).
 * - Verifica que el correo no exista (consulta preparada) y además la columna email
 *   es UNIQUE en la base, así que ni dos registros simultáneos pueden duplicarlo.
 * - Guarda la contraseña con password_hash($password, PASSWORD_BCRYPT): nunca en texto plano.
 * - Toda cuenta nueva recibe el rol "usuario". El rol "admin" no se puede elegir desde el formulario.
 */
$ruta_base = '';
$pagina_actual = 'registro.php';
$titulo_pagina = 'Crear cuenta — RIFT OVERDRIVE';
$descripcion_pagina = 'Creá tu cuenta de RIFT OVERDRIVE para participar de la Beta.';
$nav_simple = true;

require_once __DIR__ . '/config/sesion.php';
iniciar_sesion_segura();

if (usuario_logueado()) {
    redirigir('panel.php');
}

require_once __DIR__ . '/config/conexion.php';

$errores = [];
$error_sistema = '';
$datos = ['nombre' => '', 'apodo' => '', 'email' => '', 'nacimiento' => '', 'plataforma_id' => '', 'modo_id' => ''];

// Listas para los <select> (consultas fijas, sin datos del usuario)
$plataformas = [];
$modos = [];
$rol_usuario_id = false;
try {
    $plataformas = $pdo->query('SELECT id, nombre FROM plataformas ORDER BY id')->fetchAll();
    $modos       = $pdo->query('SELECT id, nombre FROM modos_juego ORDER BY id')->fetchAll();

    // Rol que se le asigna a las cuentas nuevas
    $stmt = $pdo->prepare('SELECT id FROM roles WHERE nombre = :nombre LIMIT 1');
    $stmt->execute([':nombre' => 'usuario']);
    $rol_usuario_id = $stmt->fetchColumn();
} catch (PDOException $ex) {
    error_log('registro.php (listas): ' . $ex->getMessage());
    $error_sistema = 'No se pudo preparar el formulario. Intentá de nuevo más tarde.';
}
if ($rol_usuario_id === false && $error_sistema === '') {
    $error_sistema = 'Falta el rol "usuario" en la base de datos. Ejecutá tp8/migracion.sql.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error_sistema === '') {

    if (!verificar_csrf($_POST['csrf'] ?? null)) {
        $errores['general'] = 'El formulario expiró. Recargá la página e intentá de nuevo.';
    } else {
        // 1) Captura + sanitización (las contraseñas no se modifican ni se vuelven a mostrar)
        $datos['nombre']        = texto_limpio($_POST['nombre'] ?? '');
        $datos['apodo']         = texto_limpio($_POST['apodo'] ?? '');
        $datos['email']         = trim((string) ($_POST['email'] ?? ''));
        $datos['nacimiento']    = trim((string) ($_POST['nacimiento'] ?? ''));
        $datos['plataforma_id'] = filter_input(INPUT_POST, 'plataforma_id', FILTER_VALIDATE_INT);
        $datos['modo_id']       = filter_input(INPUT_POST, 'modo_id', FILTER_VALIDATE_INT);
        $password  = (string) ($_POST['password'] ?? '');
        $password2 = (string) ($_POST['password2'] ?? '');

        // 2) Validación en el servidor
        $largo = fn(string $t) => mb_strlen($t, 'UTF-8');

        if ($largo($datos['nombre']) < 3 || $largo($datos['nombre']) > 80) {
            $errores['nombre'] = 'El nombre debe tener entre 3 y 80 caracteres.';
        } elseif (!preg_match('/^[\p{L}\s\'.-]+$/u', $datos['nombre'])) {
            $errores['nombre'] = 'El nombre solo puede tener letras y espacios.';
        }

        if ($datos['apodo'] !== '' && (!preg_match('/^[\p{L}\p{N}_.-]{2,20}$/u', $datos['apodo']))) {
            $errores['apodo'] = 'El apodo debe tener de 2 a 20 caracteres (letras, números, _ . -).';
        }

        if ($datos['email'] === '' || filter_var($datos['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errores['email'] = 'Ingresá un correo electrónico válido (ej.: nombre@dominio.com).';
        } elseif (strlen($datos['email']) > 150) {
            $errores['email'] = 'El correo es demasiado largo (máximo 150 caracteres).';
        }

        // bcrypt solo usa los primeros 72 bytes de la contraseña, por eso ese es el máximo
        if (strlen($password) < 8) {
            $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif (strlen($password) > 72) {
            $errores['password'] = 'La contraseña no puede superar los 72 caracteres.';
        } elseif (!preg_match('/\p{L}/u', $password) || !preg_match('/\d/', $password)) {
            $errores['password'] = 'La contraseña debe combinar letras y números.';
        }
        if (!isset($errores['password']) && !hash_equals($password, $password2)) {
            $errores['password2'] = 'Las contraseñas no coinciden.';
        }

        $nac = DateTime::createFromFormat('!Y-m-d', $datos['nacimiento']);
        if (!$nac || $nac->format('Y-m-d') !== $datos['nacimiento']) {
            $errores['nacimiento'] = 'Ingresá una fecha de nacimiento válida.';
        } elseif ($nac > new DateTime('today')) {
            $errores['nacimiento'] = 'La fecha de nacimiento no puede ser futura.';
        } elseif ($nac->diff(new DateTime('today'))->y < 12) {
            $errores['nacimiento'] = 'Tenés que tener 12 años o más.';
        }

        // Lista blanca: la plataforma y el modo tienen que existir en la base
        $ids_plataformas = array_map('intval', array_column($plataformas, 'id'));
        $ids_modos       = array_map('intval', array_column($modos, 'id'));
        if (!in_array($datos['plataforma_id'], $ids_plataformas, true)) {
            $errores['plataforma_id'] = 'Elegí una plataforma.';
        }
        if (!in_array($datos['modo_id'], $ids_modos, true)) {
            $errores['modo_id'] = 'Elegí tu modo de juego favorito.';
        }

        // 3) Correo no registrado todavía (consulta preparada)
        if (!isset($errores['email'])) {
            try {
                $stmt = $pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
                $stmt->execute([':email' => $datos['email']]);
                if ($stmt->fetchColumn()) {
                    $errores['email'] = 'Ese correo ya está registrado.';
                }
            } catch (PDOException $ex) {
                error_log('registro.php (email): ' . $ex->getMessage());
                $errores['general'] = 'No se pudo completar el registro. Intentá de nuevo más tarde.';
            }
        }

        // 4) Alta: la contraseña se guarda hasheada con bcrypt
        if (!$errores) {
            try {
                $sql = 'INSERT INTO usuarios
                            (rol_id, plataforma_id, modo_juego_id, nombre_completo, apodo, email, password_hash, fecha_nacimiento)
                        VALUES
                            (:rol, :plataforma, :modo, :nombre, :apodo, :email, :hash, :nacimiento)';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':rol'        => $rol_usuario_id,
                    ':plataforma' => $datos['plataforma_id'],
                    ':modo'       => $datos['modo_id'],
                    ':nombre'     => $datos['nombre'],
                    ':apodo'      => $datos['apodo'] !== '' ? $datos['apodo'] : null,
                    ':email'      => $datos['email'],
                    ':hash'       => password_hash($password, PASSWORD_BCRYPT),
                    ':nacimiento' => $datos['nacimiento'],
                ]);
                redirigir('login.php?registro=ok');
            } catch (PDOException $ex) {
                if ($ex->getCode() === '23000') { // clave duplicada: otro registro ganó la carrera
                    $errores['email'] = 'Ese correo ya está registrado.';
                } else {
                    error_log('registro.php (insert): ' . $ex->getMessage());
                    $errores['general'] = 'No se pudo completar el registro. Intentá de nuevo más tarde.';
                }
            }
        }
    }
}

// Fecha máxima del <input type="date">: hoy menos 12 años
$nacimiento_max = (new DateTime('today'))->modify('-12 years')->format('Y-m-d');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
  <main id="contenido-principal">

  <section class="seccion login-seccion">
    <div class="login-caja registro-caja">
      <h1>Crear cuenta</h1>
      <p>Registrate para participar de la Beta de RIFT OVERDRIVE.</p>

      <?php if ($error_sistema !== ''): ?>
        <div class="mensaje-error" role="alert"><?= h($error_sistema) ?></div>
      <?php endif; ?>
      <?php if (isset($errores['general'])): ?>
        <div class="mensaje-error" role="alert"><?= h($errores['general']) ?></div>
      <?php endif; ?>

      <form action="registro.php" method="post" class="formulario" novalidate>
        <input type="hidden" name="csrf" value="<?= h(token_csrf()) ?>">

        <div class="campo <?= isset($errores['nombre']) ? 'con-error' : '' ?>">
          <label for="nombre">Nombre y apellido:</label>
          <input type="text" id="nombre" name="nombre" maxlength="80" autocomplete="name"
                 value="<?= h($datos['nombre']) ?>" required>
          <span class="error-campo"><?= isset($errores['nombre']) ? h($errores['nombre']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['apodo']) ? 'con-error' : '' ?>">
          <label for="apodo">Apodo (opcional):</label>
          <input type="text" id="apodo" name="apodo" maxlength="20" autocomplete="nickname"
                 value="<?= h($datos['apodo']) ?>">
          <span class="error-campo"><?= isset($errores['apodo']) ? h($errores['apodo']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['email']) ? 'con-error' : '' ?>">
          <label for="email">Correo electrónico:</label>
          <input type="email" id="email" name="email" maxlength="150" autocomplete="email"
                 value="<?= h($datos['email']) ?>" required>
          <span class="error-campo"><?= isset($errores['email']) ? h($errores['email']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['password']) ? 'con-error' : '' ?>">
          <label for="password">Contraseña:</label>
          <input type="password" id="password" name="password" maxlength="72"
                 autocomplete="new-password" required>
          <small>Mínimo 8 caracteres, con letras y números.</small>
          <span class="error-campo"><?= isset($errores['password']) ? h($errores['password']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['password2']) ? 'con-error' : '' ?>">
          <label for="password2">Repetir contraseña:</label>
          <input type="password" id="password2" name="password2" maxlength="72"
                 autocomplete="new-password" required>
          <span class="error-campo"><?= isset($errores['password2']) ? h($errores['password2']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['nacimiento']) ? 'con-error' : '' ?>">
          <label for="nacimiento">Fecha de nacimiento:</label>
          <input type="date" id="nacimiento" name="nacimiento" max="<?= h($nacimiento_max) ?>"
                 value="<?= h($datos['nacimiento']) ?>" required>
          <small>Debés tener 12 años o más.</small>
          <span class="error-campo"><?= isset($errores['nacimiento']) ? h($errores['nacimiento']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['plataforma_id']) ? 'con-error' : '' ?>">
          <label for="plataforma_id">Plataforma:</label>
          <select id="plataforma_id" name="plataforma_id" required>
            <option value="">-- Elegí una opción --</option>
            <?php foreach ($plataformas as $p): ?>
              <option value="<?= h($p['id']) ?>" <?= (string) $datos['plataforma_id'] === (string) $p['id'] ? 'selected' : '' ?>><?= h($p['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <span class="error-campo"><?= isset($errores['plataforma_id']) ? h($errores['plataforma_id']) : '' ?></span>
        </div>

        <div class="campo <?= isset($errores['modo_id']) ? 'con-error' : '' ?>">
          <label for="modo_id">Modo de juego favorito:</label>
          <select id="modo_id" name="modo_id" required>
            <option value="">-- Elegí una opción --</option>
            <?php foreach ($modos as $m): ?>
              <option value="<?= h($m['id']) ?>" <?= (string) $datos['modo_id'] === (string) $m['id'] ? 'selected' : '' ?>><?= h($m['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <span class="error-campo"><?= isset($errores['modo_id']) ? h($errores['modo_id']) : '' ?></span>
        </div>

        <div class="botones-form">
          <button type="submit">Crear cuenta</button>
        </div>
      </form>

      <p class="login-alt">
        ¿Ya tenés cuenta?
        <a href="login.php">Iniciá sesión</a>
      </p>
    </div>

    <hr>
    <footer class="footer-seccion">
      <p>Registro — RIFT OVERDRIVE</p>
    </footer>
  </section>

  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>