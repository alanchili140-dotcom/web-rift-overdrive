<?php
/**
 * includes/nav.php — Encabezado con logo + menú de navegación
 *
 * Marca con la clase "active" el link de la página actual.
 * Para las páginas con encabezado simplificado (login, descarga) se define
 * antes de incluir el header: $nav_simple = true;
 * y opcionalmente $nav_boton_texto / $nav_boton_link para el botón de la derecha.
 */
$pagina_actual = basename($_SERVER['PHP_SELF']);
$nav_simple = $nav_simple ?? false;

// Devuelve ' class="active"' si el link corresponde a la página actual
function clase_activa(string $archivo): string
{
    global $pagina_actual;
    return $pagina_actual === $archivo ? ' class="active" aria-current="page"' : '';
}
?>
  <header class="header">
    <a href="index.php">
      <img src="img/logo-header.png" alt="Logo de <?= htmlspecialchars(APP_NAME) ?>" class="logo" width="70" height="42">
    </a>
<?php if ($nav_simple): ?>
    <a href="<?= $nav_boton_link ?? 'index.php' ?>" class="boton boton-header"><?= $nav_boton_texto ?? '‹ Volver al inicio' ?></a>
  </header>
<?php else: ?>
    <nav class="nav">
      <a href="index.php"<?= clase_activa('index.php') ?>>Inicio</a>
      <a href="index.php#tutorial">Tutorial</a>
      <a href="index.php#musica">Música</a>
      <a href="index.php#curso">Curso</a>
      <a href="blog.php"<?= clase_activa('blog.php') ?>>Blog</a>
      <a href="galeria.php"<?= clase_activa('galeria.php') ?>>Galería</a>
      <a href="quienes-somos.php"<?= clase_activa('quienes-somos.php') ?>>Quiénes somos</a>
      <a href="preguntas.php"<?= clase_activa('preguntas.php') ?>>FAQ</a>
      <a href="contacto.php"<?= clase_activa('contacto.php') ?>>Contacto</a>

      <div class="dropdown">
        <a href="descarga.php" id="btn-descargar"<?= clase_activa('descarga.php') ?> aria-expanded="false" aria-controls="menu-descargar">Descargar ▾</a>
        <div class="dropdown-menu" id="menu-descargar">
          <a href="descargas/rift-overdrive-windows.zip" download>Windows</a>
          <a href="descargas/rift-overdrive-linux.zip" download>Linux</a>
        </div>
      </div>
    </nav>

    <a href="login.php" class="boton boton-header<?= $pagina_actual === 'login.php' ? ' active' : '' ?>">⬇ Iniciar sesión</a>
  </header>
<?php endif; ?>