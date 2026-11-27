<?php
/**
 * includes/nav.php — Encabezado con logo + menú de navegación
 *
 * Marca con la clase "active" el link de la página actual.
 * Para las páginas con encabezado simplificado (login, descarga) se define
 * antes de incluir el header: $nav_simple = true;
 * y opcionalmente $nav_boton_texto / $nav_boton_link para el botón de la derecha.
 */
// Si la página ya definió $pagina_actual (ej.: las de clase5/), se respeta; si no, se detecta sola.
$pagina_actual = $pagina_actual ?? basename($_SERVER['PHP_SELF']);
$ruta_base = $ruta_base ?? '';
$nav_simple = $nav_simple ?? false;

require_once __DIR__ . '/../config/sesion.php';   // TP8: usuario_logueado() y h()

// Devuelve ' class="active"' si el link corresponde a la página actual
function clase_activa(string $archivo): string
{
    global $pagina_actual;
    return $pagina_actual === $archivo ? ' class="active" aria-current="page"' : '';
}
?>
  <header class="header">
    <a href="<?= $ruta_base ?>index.php">
      <img src="<?= $ruta_base ?>img/logo-header.png" alt="Logo de <?= htmlspecialchars(APP_NAME) ?>" class="logo" width="70" height="42">
    </a>
<?php if ($nav_simple): ?>
    <a href="<?= $nav_boton_link ?? $ruta_base . 'index.php' ?>" class="boton boton-header"><?= $nav_boton_texto ?? '‹ Volver al inicio' ?></a>
  </header>
<?php else: ?>
    <nav class="nav">
      <a href="<?= $ruta_base ?>index.php"<?= clase_activa('index.php') ?>>Inicio</a>
      <a href="<?= $ruta_base ?>index.php#tutorial">Tutorial</a>
      <a href="<?= $ruta_base ?>index.php#musica">Música</a>
      <a href="<?= $ruta_base ?>index.php#curso">Curso</a>
      <a href="<?= $ruta_base ?>blog.php"<?= clase_activa('blog.php') ?>>Blog</a>
      <a href="<?= $ruta_base ?>galeria.php"<?= clase_activa('galeria.php') ?>>Galería</a>
      <a href="<?= $ruta_base ?>quienes-somos.php"<?= clase_activa('quienes-somos.php') ?>>Quiénes somos</a>
      <a href="<?= $ruta_base ?>preguntas.php"<?= clase_activa('preguntas.php') ?>>FAQ</a>
      <a href="<?= $ruta_base ?>contacto.php"<?= clase_activa('contacto.php') ?>>Contacto</a>

      <div class="dropdown">
        <a href="<?= $ruta_base ?>descarga.php" id="btn-descargar"<?= clase_activa('descarga.php') ?> aria-expanded="false" aria-controls="menu-descargar">Descargar ▾</a>
        <div class="dropdown-menu" id="menu-descargar">
          <a href="<?= $ruta_base ?>descargas/rift-overdrive-windows.zip" download>Windows</a>
          <a href="<?= $ruta_base ?>descargas/rift-overdrive-linux.zip" download>Linux</a>
        </div>
      </div>
    </nav>

<?php if (usuario_logueado()): ?>
    <div class="nav-sesion">
      <a href="<?= $ruta_base ?>panel.php" class="boton boton-header<?= $pagina_actual === 'panel.php' ? ' active' : '' ?>">👤 <?= h($_SESSION['usuario_nombre']) ?></a>
      <a href="<?= $ruta_base ?>logout.php" class="boton boton-header">Cerrar sesión</a>
    </div>
<?php else: ?>
    <a href="<?= $ruta_base ?>login.php" class="boton boton-header<?= $pagina_actual === 'login.php' ? ' active' : '' ?>">⬇ Iniciar sesión</a>
<?php endif; ?>
  </header>
<?php endif; ?>