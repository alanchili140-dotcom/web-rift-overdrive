<?php
$titulo_pagina = 'Devlog y Novedades del Desarrollo — RIFT OVERDRIVE';
$descripcion_pagina = 'Seguí las novedades y avances del desarrollo de RIFT OVERDRIVE, semana a semana.';
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Blog de Desarrollo del Videojuego</h1>
    <p>Novedades, avances de desarrollo y notas del equipo de RIFT STUDIO.</p>

    <article class="entrada-blog">
      <h2>Devlog #1: Así funcionan los portales</h2>
      <p>
        Esta semana el equipo de programación terminó el sistema base de
        portales.<br>
        Todavía falta pulir las colisiones, pero ya se puede jugar el primer
        nivel de prueba.
      </p>
    </article>

    <article class="entrada-blog">
      <h2>Devlog #2: Primeros bocetos de enemigos</h2>
      <p>
        El equipo de arte compartió los primeros diseños de los robots
        enemigos.<br>
        Se viene una actualización con capturas en la sección
        <a href="galeria.php">Galería</a>.
      </p>
    </article>

    <article class="entrada-blog">
      <h2>Devlog #3: Rumbo a la Beta cerrada</h2>
      <p>
        Ya está abierto el <a href="index.php#registro">registro
        para la Beta</a>.<br>
        Gracias a todos los que se anotaron durante la primera semana.
      </p>
    </article>

    <section class="comentarios">
      <h3>Comentarios</h3>

      <div class="comentario">
        <p>
          <strong>Jugador_23:</strong> ¡Se ve buenísimo el sistema de portales!<br>
          Ansioso por probar la beta.
        </p>
      </div>

      <div class="comentario">
        <p>
          <strong>FPS_fan:</strong> ¿Va a tener modo cooperativo?<br>
          Sería genial poder jugarlo con amigos.
        </p>
      </div>

      <div class="comentario">
        <p>
          <strong>Retro_gamer:</strong> Los bocetos de los enemigos están muy
          logrados.<br>
          ¿Van a mostrar más diseños pronto?
        </p>
      </div>
    </section>

    <hr>
    <footer class="footer-seccion">
      <p>Blog — RIFT OVERDRIVE Studio</p>
    </footer>
  </section>
  </main>

<?php require_once 'includes/footer.php'; ?>