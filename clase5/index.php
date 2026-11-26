<?php
/**
 * clase5/index.php — Página de entrada de la Clase 5
 * Reúne los dos formularios del TP: uno por POST y otro por GET.
 */
$ruta_base = '../';
$pagina_actual = 'clase5';
$titulo_pagina = 'Formularios (Clase 5) — RIFT OVERDRIVE';
$descripcion_pagina = 'Formularios de RIFT OVERDRIVE procesados en el servidor: opiniones por POST y buscador por GET.';

require_once '../includes/header.php';
require_once '../includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Formularios — Clase 5</h1>
    <p>
      Procesamiento de formularios en el servidor con PHP, con validación y
      sanitización para prevenir ataques XSS.
    </p>

    <article class="modulo">
      <h2>Opiniones <small class="etiqueta-metodo">POST</small></h2>
      <p>
        Dejá tu opinión sobre el juego. Se validan nombre, correo, tipo, puntuación
        y mensaje; si algo falla, se muestran los errores y se conservan los datos.
      </p>
      <p><a href="opiniones.php" class="boton">Ir al formulario de opiniones</a></p>
    </article>

    <article class="modulo">
      <h2>Buscador <small class="etiqueta-metodo">GET</small></h2>
      <p>
        Buscá armas, enemigos, mapas y modos de juego. La consulta queda en la URL
        (por ejemplo <code>buscar.php?q=plasma&amp;categoria=armas</code>).
      </p>
      <p><a href="buscar.php" class="boton">Ir al buscador</a></p>
    </article>

    <article class="modulo">
      <h2>¿Cómo se protege?</h2>
      <ul class="lista-seguridad">
        <li><strong>Existencia:</strong> <code>REQUEST_METHOD</code>, <code>isset</code> y <code>empty</code> antes de usar un dato.</li>
        <li><strong>Sanitización de entrada:</strong> <code>filter_input</code>, <code>trim</code> y <code>FILTER_SANITIZE_EMAIL</code>.</li>
        <li><strong>Validación:</strong> <code>FILTER_VALIDATE_EMAIL</code>, <code>FILTER_VALIDATE_INT</code>, largos y listas blancas.</li>
        <li><strong>Escape de salida:</strong> <code>htmlspecialchars</code> con <code>ENT_QUOTES</code> y UTF-8 antes de mostrar cualquier dato.</li>
      </ul>
    </article>
  </section>
  </main>

<?php require_once '../includes/footer.php'; ?>