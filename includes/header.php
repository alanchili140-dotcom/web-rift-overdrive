<?php
/**
 * includes/header.php — <head> común a todas las páginas
 *
 * Cada página define ANTES de incluir este archivo:
 *   $titulo_pagina      (obligatoria)  → texto de la pestaña del navegador
 *   $descripcion_pagina (opcional)     → meta description para SEO
 *   $ruta_base          (opcional)     → prefijo para las rutas cuando la página
 *                                        está en una subcarpeta (ej.: '../' en clase5/)
 */
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/sesion.php';
iniciar_sesion_segura();   // TP8: la sesión se inicia antes de cualquier salida HTML

$titulo_pagina = $titulo_pagina ?? APP_NAME;
$descripcion_pagina = $descripcion_pagina ?? '';
$ruta_base = $ruta_base ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo_pagina) ?></title>
<?php if ($descripcion_pagina !== ''): ?>
  <meta name="description" content="<?= htmlspecialchars($descripcion_pagina) ?>">
<?php endif; ?>
  <link rel="stylesheet" href="<?= $ruta_base ?>css/RIFT-OVERDRIVE.css">
  <link rel="stylesheet" href="<?= $ruta_base ?>css/tp8.css">
</head>
<body>

  <a href="#contenido-principal" class="skip-link">Saltar al contenido principal</a>