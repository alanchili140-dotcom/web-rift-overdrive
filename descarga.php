<?php
$titulo_pagina = 'Descargar Juego FPS para PC — RIFT OVERDRIVE';
$descripcion_pagina = 'Descarga gratis RIFT OVERDRIVE, el FPS futurista con portales. Disponible para Windows y Linux.';
$nav_simple = true;
$nav_boton_texto = '⬇ Windows o Linux';
$nav_boton_link = '#descarga';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">

  <!-- ===================== DESCARGA ===================== -->
  <section id="descarga" class="seccion descarga-seccion">
    <div class="descarga-info">
      <h1>Descargar el Juego FPS de Portales</h1>
      <p>Elegí tu plataforma y descargá RIFT OVERDRIVE para comenzar tu entrenamiento.</p>

      <div class="botones-descarga-grande">
        <a href="descargas/rift-overdrive-windows.zip" class="boton-descarga windows" download>
          🪟 Descargar para <strong>Windows</strong> ›
        </a>
        <a href="descargas/rift-overdrive-linux.zip" class="boton-descarga linux" download>
          🚀 Descargar para <strong>Linux</strong> ›
        </a>
      </div>
    </div>

    <img src="img/enemigo.png" alt="Silueta de pie frente a un gran portal azul" class="descarga-img">
  </section>

  <div class="pasos-descarga">
    <h3>¿Cómo se descarga el juego?</h3>
    <div class="pasos">
      <div class="paso">
        <span class="paso-num">1</span>
        <div>
          <strong>Descarga el archivo</strong>
          <p>Elige tu plataforma y descarga el instalador del juego.</p>
        </div>
      </div>
      <span class="flecha">›</span>
      <div class="paso">
        <span class="paso-num">2</span>
        <div>
          <strong>Ejecuta el instalador</strong>
          <p>Abre el archivo descargado e inicia la instalación.</p>
        </div>
      </div>
      <span class="flecha">›</span>
      <div class="paso">
        <span class="paso-num">3</span>
        <div>
          <strong>¡A jugar!</strong>
          <p>Completa la instalación y entra en la acción.</p>
        </div>
      </div>
    </div>
  </div>

  <p class="volver-inicio">
    <a href="index.php" class="boton">‹ Volver al inicio</a>
  </p>

  <hr>
  <footer class="footer-seccion">
    <p>Descarga — RIFT OVERDRIVE</p>
  </footer>

  </main>

<?php require_once 'includes/footer.php'; ?>