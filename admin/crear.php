<?php
/**
 * admin/crear.php — ALTA (Create)
 * Formulario + INSERT con consulta preparada (publicación + etiquetas en una transacción).
 */
$ruta_base = '../';
$pagina_actual = 'admin';
$titulo_pagina = 'Nueva publicación — RIFT OVERDRIVE';
$descripcion_pagina = 'Crear una publicación nueva en el blog de RIFT OVERDRIVE.';

require_once 'helpers.php';

$listas = listas_formulario($pdo);
$datos = ['titulo' => '', 'contenido' => '', 'usuario_id' => '', 'categoria_id' => '',
          'estado_id' => 1, 'fecha_publicacion' => '', 'etiquetas' => []];
$errores = [];
$error_bd = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$datos, $errores] = validar_publicacion($listas);

    if (!$errores) {
        try {
            // Transacción: o se guarda todo (publicación + etiquetas) o no se guarda nada
            $pdo->beginTransaction();
            $sql = 'INSERT INTO publicaciones (usuario_id, categoria_id, estado_id, titulo, contenido, fecha_publicacion)
                    VALUES (:usuario_id, :categoria_id, :estado_id, :titulo, :contenido, :fecha)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id'   => $datos['usuario_id'],
                ':categoria_id' => $datos['categoria_id'],
                ':estado_id'    => $datos['estado_id'],
                ':titulo'       => $datos['titulo'],
                ':contenido'    => $datos['contenido'],
                ':fecha'        => $datos['fecha_sql'],
            ]);
            guardar_etiquetas($pdo, (int) $pdo->lastInsertId(), $datos['etiquetas']);
            $pdo->commit();

            header('Location: index.php?status=created');
            exit;
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('admin/crear.php: ' . $ex->getMessage());
            $error_bd = 'No se pudo guardar la publicación. Probá de nuevo.';
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Nueva publicación</h1>

    <?php if ($errores || $error_bd): ?>
      <div class="mensaje-error" role="alert">
        <strong>No se pudo guardar. Corregí lo siguiente:</strong>
        <ul>
          <?php foreach ($errores as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
          <?php if ($error_bd): ?><li><?= e($error_bd) ?></li><?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php $accion = 'crear.php'; $texto_boton = 'Guardar publicación'; require '_form.php'; ?>
  </section>
  </main>

<?php require_once '../includes/footer.php'; ?>
