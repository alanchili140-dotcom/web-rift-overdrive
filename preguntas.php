<?php
$titulo_pagina = 'Preguntas Frecuentes del Juego FPS — RIFT OVERDRIVE';
$descripcion_pagina = 'Respondemos las dudas mas comunes sobre RIFT OVERDRIVE: genero, plataformas, portales y como reportar errores.';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Preguntas Frecuentes sobre el FPS de Portales</h1>
    <p>Estas son las dudas más comunes sobre RIFT OVERDRIVE.</p>

    <article class="modulo">
      <h2>¿En qué plataformas está disponible?</h2>
      <p>Por el momento, RIFT OVERDRIVE está disponible para Windows y Linux.</p>
    </article>

    <article class="modulo">
      <h2>¿El juego tiene modo multijugador?</h2>
      <p>
        La versión actual es para un jugador.<br>
        Estamos evaluando agregar un modo cooperativo en una futura actualización.
      </p>
    </article>

    <article class="modulo">
      <h2>¿Cuáles son los requisitos mínimos?</h2>
      <p>Podés consultarlos en la sección Requisitos de la página principal.</p>
      <h3>Resumen rápido</h3>
      <pre class="controles">
SO:        Windows 10 64 bit
Memoria:   8 GB RAM
Gráficos:  GTX 1050 Ti / RX 560
      </pre>
    </article>

    <article class="modulo">
      <h2>¿Cómo puedo reportar un error (bug)?</h2>
      <img src="img/error-de-bug.png" alt="Ejemplo de captura de pantalla para reportar un error" width="300">
      <p>
        Escribinos por correo a
        <a href="mailto:<?= APP_EMAIL ?>"><?= APP_EMAIL ?></a>
        o llamanos al <a href="tel:+543874123456">+54 387 412-3456</a>,
        contándonos qué pasó y, si podés, adjuntá una captura de pantalla
        como la de arriba.
      </p>
    </article>

    <hr>
    <footer class="footer-seccion">
      <p>Preguntas Frecuentes — RIFT OVERDRIVE</p>
    </footer>
  </section>
  </main>

<?php require_once 'includes/footer.php'; ?>