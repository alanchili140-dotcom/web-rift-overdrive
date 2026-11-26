<?php
/**
 * clase5/buscar.php — FORMULARIO POR GET
 *
 * Buscador de contenido del juego (armas, enemigos, mapas y modos).
 * Se usa GET porque es una consulta de solo lectura: los datos quedan en la URL
 * (ej.: buscar.php?q=plasma&categoria=armas) y se pueden compartir o guardar.
 */
$ruta_base = '../';
$pagina_actual = 'clase5';
$titulo_pagina = 'Buscador (GET) — Clase 5 — RIFT OVERDRIVE';
$descripcion_pagina = 'Buscador de armas, enemigos, mapas y modos de RIFT OVERDRIVE procesado en el servidor con GET.';

require_once 'funciones.php';

// "Base de datos" de ejemplo (en un proyecto real vendría de MySQL)
$catalogo = [
    ['nombre' => 'Rifle de plasma',     'categoria' => 'armas',    'descripcion' => 'Arma primaria de cadencia alta y daño constante.'],
    ['nombre' => 'Escopeta de pulso',   'categoria' => 'armas',    'descripcion' => 'Muy potente a corta distancia; ideal para combates en pasillos.'],
    ['nombre' => 'Francotirador Rift',  'categoria' => 'armas',    'descripcion' => 'Disparo de largo alcance que atraviesa portales.'],
    ['nombre' => 'Portal gun',          'categoria' => 'armas',    'descripcion' => 'Abre y cierra portales para crear atajos tácticos.'],
    ['nombre' => 'Centinela',           'categoria' => 'enemigos', 'descripcion' => 'Torreta móvil que patrulla las salas de entrenamiento.'],
    ['nombre' => 'Soldado sintético',   'categoria' => 'enemigos', 'descripcion' => 'Enemigo básico con ataques a media distancia.'],
    ['nombre' => 'Guardián del núcleo', 'categoria' => 'enemigos', 'descripcion' => 'Jefe final con escudo de energía y ataques de área.'],
    ['nombre' => 'Sala de pruebas',     'categoria' => 'mapas',    'descripcion' => 'Primer nivel: aprendé movimiento, dash y portales.'],
    ['nombre' => 'Laboratorio nivel 2', 'categoria' => 'mapas',    'descripcion' => 'Pasillos industriales con plataformas y portales encadenados.'],
    ['nombre' => 'Núcleo Rift',         'categoria' => 'mapas',    'descripcion' => 'Arena final donde se decide el experimento.'],
    ['nombre' => 'Campaña',             'categoria' => 'modos',    'descripcion' => 'Modo historia para un jugador.'],
    ['nombre' => 'Contrarreloj',        'categoria' => 'modos',    'descripcion' => 'Completá los niveles lo más rápido posible usando portales.'],
];

// Lista blanca de categorías permitidas
$categorias_validas = [
    'todas'    => 'Todas',
    'armas'    => 'Armas',
    'enemigos' => 'Enemigos',
    'mapas'    => 'Mapas',
    'modos'    => 'Modos de juego',
];

$errores = [];
$resultados = [];
$busqueda_realizada = false;
$q = '';
$categoria = 'todas';

// 1) ¿Llegó algún parámetro por GET? (si entran sin buscar, solo se muestra el formulario)
if (isset($_GET['q']) || isset($_GET['categoria'])) {
    $busqueda_realizada = true;

    // 2) Captura + sanitización (filter_input + trim + limpieza de caracteres de control)
    $q = get('q');
    $categoria = get('categoria');
    if ($categoria === '') {
        $categoria = 'todas';
    }

    // 3) Validación en el servidor
    if (mb_strlen($q) > 40) {
        $errores['q'] = 'El texto de búsqueda no puede superar los 40 caracteres.';
    } elseif ($q !== '' && mb_strlen($q) < 2) {
        $errores['q'] = 'Escribí al menos 2 caracteres para buscar.';
    }

    if (!array_key_exists($categoria, $categorias_validas)) {
        $errores['categoria'] = 'La categoría elegida no es válida.';
        $categoria = 'todas';   // volvemos a un valor seguro para el formulario
    }

    if (empty($errores) && $q === '' && $categoria === 'todas') {
        $errores['q'] = 'Escribí algo para buscar o elegí una categoría.';
    }

    // 4) Si todo está bien, filtramos el catálogo
    if (empty($errores)) {
        foreach ($catalogo as $item) {
            $coincide_categoria = ($categoria === 'todas' || $item['categoria'] === $categoria);
            $coincide_texto = ($q === '')
                || mb_stripos($item['nombre'], $q) !== false
                || mb_stripos($item['descripcion'], $q) !== false;

            if ($coincide_categoria && $coincide_texto) {
                $resultados[] = $item;
            }
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Buscador del juego <small class="etiqueta-metodo">GET</small></h1>
    <p>Buscá armas, enemigos, mapas y modos. Los datos viajan por <strong>GET</strong>: fijate cómo cambia la dirección del navegador.</p>

    <form action="buscar.php" method="get" class="formulario formulario-busqueda" novalidate>
      <div class="campo <?= isset($errores['q']) ? 'con-error' : '' ?>">
        <label for="q">Buscar:</label>
        <input type="text" id="q" name="q" maxlength="40" placeholder="Ej: plasma, portal, jefe..."
               value="<?= e($q) ?>">
        <span class="error-campo"><?= isset($errores['q']) ? e($errores['q']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['categoria']) ? 'con-error' : '' ?>">
        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria">
          <?php foreach ($categorias_validas as $valor => $texto): ?>
            <option value="<?= e($valor) ?>" <?= $categoria === $valor ? 'selected' : '' ?>><?= e($texto) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error-campo"><?= isset($errores['categoria']) ? e($errores['categoria']) : '' ?></span>
      </div>

      <div class="botones-form">
        <button type="submit">Buscar</button>
      </div>
    </form>

    <?php if (!empty($errores)): ?>
      <div class="mensaje-error" role="alert">
        <strong>No pudimos realizar la búsqueda:</strong>
        <ul>
          <?php foreach ($errores as $mensaje_error): ?>
            <li><?= e($mensaje_error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ($busqueda_realizada): ?>
      <div class="mensaje-exito" role="status">
        Se encontraron <strong><?= count($resultados) ?></strong> resultado(s)
        <?php if ($q !== ''): ?>para «<strong><?= e($q) ?></strong>»<?php endif; ?>
        en <strong><?= e($categorias_validas[$categoria]) ?></strong>.
      </div>

      <?php if (empty($resultados)): ?>
        <p>No hay coincidencias. Probá con otra palabra o con otra categoría.</p>
      <?php else: ?>
        <div class="resultados">
          <?php foreach ($resultados as $item): ?>
            <article class="modulo resultado-item">
              <span class="etiqueta-categoria"><?= e($categorias_validas[$item['categoria']]) ?></span>
              <h2><?= e($item['nombre']) ?></h2>
              <p><?= e($item['descripcion']) ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <p class="volver-clase5"><a href="index.php">‹ Volver a los formularios de la Clase 5</a></p>
  </section>
  </main>

<?php require_once '../includes/footer.php'; ?>