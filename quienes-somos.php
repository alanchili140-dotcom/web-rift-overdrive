<?php
$titulo_pagina = 'Equipo de Desarrollo de un FPS Futurista — RIFT OVERDRIVE';
$descripcion_pagina = 'Conoce al equipo detras de RIFT OVERDRIVE, un FPS futurista con portales desarrollado por estudiantes de escuela tecnica.';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Quiénes Somos: el Equipo detrás del FPS</h1>
    <img src="img/logo-studio.png" alt="Logo de Rift Studio" width="200">
    <p>
      Somos RIFT STUDIO, un equipo de estudiantes de una escuela técnica que
      está desarrollando RIFT OVERDRIVE como proyecto escolar.
    </p>

    <article class="modulo">
      <h2>Nuestra misión</h2>
      <p>Crear un FPS futurista divertido, con mecánicas de portales originales y una comunidad activa alrededor del juego.</p>
    </article>

    <article class="modulo">
      <h2>Nuestra historia</h2>
      <p>
        El proyecto nació en 4° año como trabajo práctico de la materia, y
        fue creciendo hasta convertirse en el juego que estamos armando hoy.
      </p>
    </article>

    <article class="modulo">
      <h2>El equipo</h2>

      <img src="img/integrante1.jpg" alt="Foto de Facundo Mauricio Cruz Mendez" width="60" height="60">
      <h3>Facundo Mauricio Cruz Mendez</h3>
      <p>Programador</p>

      <img src="img/integrante2.jpg" alt="Foto de Alan David Chiliguay" width="60" height="60">
      <h3>Alan David Chiliguay</h3>
      <p>Programador</p>

      <img src="img/integrante3.jpg" alt="Foto de Joaquín Leonel Chocobar Torres" width="60" height="60">
      <h3>Joaquín Leonel Chocobar Torres</h3>
      <p>Diseñador 3D</p>
    </article>

    <p>
      Seguinos en nuestras redes:
      <a href="https://discord.com" target="_blank" rel="noopener noreferrer">
        <img src="img/discord.png" alt="Discord" width="25" height="20">
      </a> ·
      <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
        <img src="img/instagram.png" alt="Instagram" width="25" height="20">
      </a> ·
      <a href="https://youtube.com" target="_blank" rel="noopener noreferrer">
        <img src="img/youtube.png" alt="YouTube" width="25" height="20">
      </a>
    </p>
    <p>
      <a href="mailto:<?= APP_EMAIL ?>" class="boton">Escribinos</a>
      <a href="tel:+543874123456" class="boton"><span aria-hidden="true">📞</span> Llamanos</a>
    </p>

    <hr>
    <footer class="footer-seccion">
      <p>Quiénes Somos — RIFT OVERDRIVE</p>
    </footer>
  </section>
  </main>

<?php require_once 'includes/footer.php'; ?>