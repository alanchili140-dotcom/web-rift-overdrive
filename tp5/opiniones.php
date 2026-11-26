<?php
/**
 * clase5/opiniones.php — FORMULARIO POR POST
 *
 * Formulario de opiniones sobre el juego. Cumple el pipeline de la clase:
 *   1) Verificar método y existencia  2) Sanitizar  3) Validar en el servidor
 *   4) Escapar la salida  5) Mostrar mensajes y conservar los datos si hay errores
 */
$ruta_base = '../';          // esta página está una carpeta más adentro
$pagina_actual = 'clase5';   // para que ningún link del menú quede marcado como activo
$titulo_pagina = 'Opiniones (POST) — Clase 5 — RIFT OVERDRIVE';
$descripcion_pagina = 'Formulario de opiniones de RIFT OVERDRIVE procesado en el servidor con POST y sanitización contra XSS.';

require_once 'funciones.php';

// Variables de control para la vista
$errores = [];
$exito = false;
$enviado = [];   // copia de lo que se envió correctamente (para el resumen)
$datos = ['nombre' => '', 'email' => '', 'tipo' => '', 'puntuacion' => '', 'mensaje' => '', 'acepta' => ''];

// Lista blanca: solo estos valores son válidos para el <select>
$tipos_validos = [
    'sugerencia' => 'Sugerencia',
    'bug'        => 'Reporte de error',
    'elogio'     => 'Elogio',
    'otro'       => 'Otro',
];

// 1) ¿La página fue pedida por POST? (si entran escribiendo la URL, es GET y no procesamos nada)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 2) Captura + sanitización de la entrada
    $datos['nombre']     = post('nombre');
    $datos['tipo']       = post('tipo');
    $datos['puntuacion'] = post('puntuacion');
    $datos['mensaje']    = post('mensaje');
    $datos['acepta']     = post('acepta');
    // El email se sanitiza con el filtro específico (saca caracteres no permitidos en un correo)
    $datos['email']      = filter_var(post('email'), FILTER_SANITIZE_EMAIL);

    // 3) Validación en el servidor (el HTML required se puede saltear; esto no)
    if ($datos['nombre'] === '') {
        $errores['nombre'] = 'El nombre es obligatorio.';
    } elseif (mb_strlen($datos['nombre']) < 2 || mb_strlen($datos['nombre']) > 40) {
        $errores['nombre'] = 'El nombre debe tener entre 2 y 40 caracteres.';
    } elseif (!preg_match('/^[\p{L}\s\'.-]+$/u', $datos['nombre'])) {
        $errores['nombre'] = 'El nombre solo puede tener letras y espacios.';
    }

    if ($datos['email'] === '') {
        $errores['email'] = 'El correo electrónico es obligatorio.';
    } elseif (filter_var($datos['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errores['email'] = 'Ingresá un correo electrónico válido (ej.: nombre@dominio.com).';
    }

    if ($datos['tipo'] === '') {
        $errores['tipo'] = 'Elegí un tipo de opinión.';
    } elseif (!array_key_exists($datos['tipo'], $tipos_validos)) {
        $errores['tipo'] = 'El tipo de opinión elegido no es válido.';
    }

    $puntos = filter_var($datos['puntuacion'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
    if ($datos['puntuacion'] === '') {
        $errores['puntuacion'] = 'Elegí una puntuación del 1 al 5.';
    } elseif ($puntos === false) {
        $errores['puntuacion'] = 'La puntuación debe ser un número entre 1 y 5.';
    }

    if ($datos['mensaje'] === '') {
        $errores['mensaje'] = 'El mensaje es obligatorio.';
    } elseif (mb_strlen($datos['mensaje']) < 10) {
        $errores['mensaje'] = 'El mensaje es muy corto (mínimo 10 caracteres).';
    } elseif (mb_strlen($datos['mensaje']) > 500) {
        $errores['mensaje'] = 'El mensaje es muy largo (máximo 500 caracteres).';
    }

    if ($datos['acepta'] !== 'si') {
        $errores['acepta'] = 'Tenés que aceptar que usemos tu opinión para mejorar el juego.';
    }

    // 4) Si no hubo errores: procesamiento (acá iría guardar en base de datos o enviar un mail)
    if (empty($errores)) {
        $exito = true;
        $enviado = $datos;
        $enviado['tipo_texto'] = $tipos_validos[$datos['tipo']];
        // Se vacía el formulario para que no se reenvíe lo mismo por accidente
        $datos = ['nombre' => '', 'email' => '', 'tipo' => '', 'puntuacion' => '', 'mensaje' => '', 'acepta' => ''];
    }
}

require_once '../includes/header.php';
require_once '../includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Opiniones sobre el juego <small class="etiqueta-metodo">POST</small></h1>
    <p>Contanos qué te parece RIFT OVERDRIVE. Los datos viajan por <strong>POST</strong> y se validan en el servidor.</p>

    <?php if ($exito): ?>
      <!-- MENSAJE DE ÉXITO + resumen de lo recibido (todo escapado con e()) -->
      <div class="mensaje-exito" role="status">
        <strong>¡Gracias, <?= e($enviado['nombre']) ?>!</strong> Recibimos tu opinión correctamente.
      </div>
      <article class="modulo resumen-envio">
        <h2>Resumen de tu envío</h2>
        <dl>
          <dt>Nombre</dt><dd><?= e($enviado['nombre']) ?></dd>
          <dt>Correo</dt><dd><?= e($enviado['email']) ?></dd>
          <dt>Tipo</dt><dd><?= e($enviado['tipo_texto']) ?></dd>
          <dt>Puntuación</dt><dd><?= e($enviado['puntuacion']) ?> / 5</dd>
          <dt>Mensaje</dt><dd><?= nl2br(e($enviado['mensaje'])) ?></dd>
        </dl>
      </article>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
      <!-- MENSAJES DE ERROR DE VALIDACIÓN -->
      <div class="mensaje-error" role="alert">
        <strong>No pudimos enviar tu opinión. Corregí lo siguiente:</strong>
        <ul>
          <?php foreach ($errores as $mensaje_error): ?>
            <li><?= e($mensaje_error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- novalidate: desactiva la validación del navegador para que se vea la del servidor -->
    <form action="opiniones.php" method="post" class="formulario" novalidate>

      <div class="campo <?= isset($errores['nombre']) ? 'con-error' : '' ?>">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" maxlength="40" placeholder="Ej: Alan Chiliguay"
               value="<?= e($datos['nombre']) ?>">
        <span class="error-campo"><?= isset($errores['nombre']) ? e($errores['nombre']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['email']) ? 'con-error' : '' ?>">
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="tuemail@ejemplo.com"
               value="<?= e($datos['email']) ?>">
        <span class="error-campo"><?= isset($errores['email']) ? e($errores['email']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['tipo']) ? 'con-error' : '' ?>">
        <label for="tipo">Tipo de opinión:</label>
        <select id="tipo" name="tipo">
          <option value="">-- Elegí una opción --</option>
          <?php foreach ($tipos_validos as $valor => $texto): ?>
            <option value="<?= e($valor) ?>" <?= $datos['tipo'] === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error-campo"><?= isset($errores['tipo']) ? e($errores['tipo']) : '' ?></span>
      </div>

      <fieldset class="campo <?= isset($errores['puntuacion']) ? 'con-error' : '' ?>">
        <legend>Puntuación:</legend>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <label><input type="radio" name="puntuacion" value="<?= $i ?>" <?= $datos['puntuacion'] === (string) $i ? 'checked' : '' ?>> <?= $i ?></label>
        <?php endfor; ?>
        <span class="error-campo"><?= isset($errores['puntuacion']) ? e($errores['puntuacion']) : '' ?></span>
      </fieldset>

      <div class="campo <?= isset($errores['mensaje']) ? 'con-error' : '' ?>">
        <label for="mensaje">Mensaje:</label>
        <textarea id="mensaje" name="mensaje" rows="5" maxlength="500"
                  placeholder="Escribí tu opinión (mínimo 10 caracteres)"><?= e($datos['mensaje']) ?></textarea>
        <span class="error-campo"><?= isset($errores['mensaje']) ? e($errores['mensaje']) : '' ?></span>
      </div>

      <div class="campo checkbox <?= isset($errores['acepta']) ? 'con-error' : '' ?>">
        <input type="checkbox" id="acepta" name="acepta" value="si" <?= $datos['acepta'] === 'si' ? 'checked' : '' ?>>
        <label for="acepta">Acepto que usen mi opinión para mejorar el juego.</label>
      </div>
      <span class="error-campo"><?= isset($errores['acepta']) ? e($errores['acepta']) : '' ?></span>

      <div class="botones-form">
        <button type="submit">Enviar opinión</button>
      </div>
    </form>

    <p class="volver-clase5"><a href="index.php">‹ Volver a los formularios de la Clase 5</a></p>
  </section>
  </main>

<?php require_once '../includes/footer.php'; ?>