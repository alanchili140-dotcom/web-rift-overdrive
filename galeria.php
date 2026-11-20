<?php
$titulo_pagina = 'Galería de Capturas FPS — RIFT OVERDRIVE';
$descripcion_pagina = 'Mira capturas de pantalla del gameplay, los portales y los enemigos de RIFT OVERDRIVE.';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Galería de Capturas del FPS</h1>
    <p>Capturas de pantalla del desarrollo de RIFT OVERDRIVE.</p>

    <div class="galeria">
      <button class="galeria-item" type="button">
        <img src="img/sala.png" alt="Captura de pantalla: escenario principal">
      </button>
      <button class="galeria-item" type="button">
        <img src="img/batalla.jpg" alt="Captura de pantalla: uso de portal">
      </button>
      <button class="galeria-item" type="button">
        <img src="img/enemigo.png" alt="Captura de pantalla: combate contra enemigo">
      </button>
    </div>

    <article class="modulo">
      <h2>Escenarios</h2>
      <p>Ambientaciones de la instalación de entrenamiento donde transcurre la historia del juego.</p>
    </article>

    <article class="modulo">
      <h2>Mecánica de portales</h2>
      <p>Capturas del sistema de portales en acción, mostrando cómo se combinan para moverse por el mapa.</p>
    </article>

    <article class="modulo">
      <h2>Combate</h2>
      <p>Enfrentamientos contra los robots enemigos, mostrando las distintas armas disponibles.</p>
    </article>

    <hr>
    <footer class="footer-seccion">
      <p>Galería — RIFT OVERDRIVE</p>
    </footer>
  </section>
  </main>

  <!-- ===================== IMAGEN AMPLIADA (LIGHTBOX) ===================== -->
  <div class="lightbox" id="lightbox">
    <button class="lightbox-cerrar" id="lightbox-cerrar" aria-label="Cerrar imagen ampliada">✕</button>
    <button class="lightbox-flecha lightbox-flecha-izq" id="lightbox-anterior" aria-label="Imagen anterior">‹</button>
    <img src="" alt="" class="lightbox-img" id="lightbox-img">
    <button class="lightbox-flecha lightbox-flecha-der" id="lightbox-siguiente" aria-label="Imagen siguiente">›</button>
  </div>

<?php require_once 'includes/footer.php'; ?>