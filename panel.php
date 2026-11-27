<?php
/**
 * panel.php — Panel del usuario (página protegida #1 del TP8)
 *
 * Cualquier usuario autenticado puede entrar. Si no hay sesión, auth_guard.php
 * redirige a login.php?error=acceso_denegado. Si el rol es "admin" se muestra además
 * el acceso a la administración del blog (página protegida #2, solo para admin).
 */
$ruta_base = '';
$pagina_actual = 'panel.php';
$titulo_pagina = 'Mi panel — RIFT OVERDRIVE';
$descripcion_pagina = 'Panel de la cuenta de RIFT OVERDRIVE.';

require_once __DIR__ . '/includes/auth_guard.php';   // ← protege la página
require_once __DIR__ . '/config/conexion.php';

// Datos frescos desde la base (consulta preparada con el id guardado en la sesión)
$perfil = null;
$error_bd = '';
try {
    $sql = 'SELECT u.email, u.apodo, u.created_at, p.nombre AS plataforma, m.nombre AS modo
            FROM usuarios u
            LEFT JOIN plataformas p ON p.id = u.plataforma_id
            LEFT JOIN modos_juego m ON m.id = u.modo_juego_id
            WHERE u.id = :id
            LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $perfil = $stmt->fetch() ?: null;
} catch (PDOException $ex) {
    error_log('panel.php: ' . $ex->getMessage());
    $error_bd = 'No se pudieron leer los datos de tu cuenta.';
}

$es_admin = ($_SESSION['rol'] ?? '') === 'admin';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Bienvenido, <?= h($_SESSION['usuario_nombre']) ?></h1>
    <p>Esta página solo se puede ver con una sesión iniciada.</p>

    <?php if ($error_bd !== ''): ?>
      <div class="mensaje-error" role="alert"><?= h($error_bd) ?></div>
    <?php endif; ?>

    <dl class="panel-datos">
      <dt>Rol</dt>
      <dd><span class="badge-rol badge-<?= h($_SESSION['rol']) ?>"><?= h($_SESSION['rol']) ?></span></dd>

      <dt>Correo</dt>
      <dd><?= h($_SESSION['usuario_email']) ?></dd>

      <?php if ($perfil): ?>
        <?php if (!empty($perfil['apodo'])): ?>
          <dt>Apodo</dt>
          <dd><?= h($perfil['apodo']) ?></dd>
        <?php endif; ?>
        <dt>Plataforma</dt>
        <dd><?= h($perfil['plataforma'] ?? '—') ?></dd>
        <dt>Modo favorito</dt>
        <dd><?= h($perfil['modo'] ?? '—') ?></dd>
        <dt>Miembro desde</dt>
        <dd><?= h(date('d/m/Y', strtotime($perfil['created_at']))) ?></dd>
      <?php endif; ?>

      <dt>Sesión iniciada</dt>
      <dd><?= h(date('d/m/Y H:i', (int) $_SESSION['login_en'])) ?></dd>

      <dt>ID de sesión</dt>
      <dd><code><?= h(substr(session_id(), 0, 8)) ?>…</code> <small>(solo los primeros caracteres)</small></dd>
    </dl>

    <?php if ($es_admin): ?>
      <p>Tenés rol de administrador, así que podés gestionar el blog:</p>
      <p><a href="admin/index.php" class="boton">Administración del blog</a></p>
    <?php else: ?>
      <p>La administración del blog es solo para el rol <strong>admin</strong>. Si intentás abrir
         <code>admin/index.php</code> escribiendo la URL, el sitio responde <strong>403 Prohibido</strong>.</p>
    <?php endif; ?>

    <p><a href="logout.php" class="boton">Cerrar sesión</a></p>
  </section>
  </main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
