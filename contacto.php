<?php
$titulo_pagina = 'Contacto — RIFT OVERDRIVE';
$descripcion_pagina = 'Contactate con el equipo de RIFT OVERDRIVE por correo o telefono para consultas o soporte.';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Contacto</h1>
    <p>¿Tenés dudas, sugerencias o encontraste un error? Escribinos por cualquiera de estos medios.</p>

    <article class="modulo">
      <h2>Correo electrónico</h2>
      <p>Para consultas generales o reporte de errores (bugs):</p>
      <a href="mailto:<?= APP_EMAIL ?>" class="boton">
        <?= APP_EMAIL ?>
      </a>
    </article>

    <article class="modulo">
      <h2>Teléfono</h2>
      <p>Atención de lunes a viernes de 9 a 18 hs:</p>
      <a href="tel:+543874123456" class="boton">
        <span aria-hidden="true">📞</span> +54 387 412-3456
      </a>
    </article>

    <article class="modulo">
      <h2>Redes sociales</h2>
      <p>Seguinos para novedades del desarrollo:</p>
      <p>
        <a href="https://discord.com" target="_blank" rel="noopener noreferrer">Discord</a> ·
        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a> ·
        <a href="https://youtube.com" target="_blank" rel="noopener noreferrer">YouTube</a>
      </p>
    </article>

    <hr>
    <footer class="footer-seccion">
      <p>Contacto — RIFT OVERDRIVE</p>
    </footer>
  </section>
  </main>

<?php require_once 'includes/footer.php'; ?>