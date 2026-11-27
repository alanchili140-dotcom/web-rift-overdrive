<?php
/**
 * admin/editar.php — MODIFICACIÓN (Update)
 * Carga el registro con SELECT preparado y lo actualiza con UPDATE preparado.
 */
$ruta_base = '../';
$pagina_actual = 'admin';
$titulo_pagina = 'Editar publicación — RIFT OVERDRIVE';
$descripcion_pagina = 'Editar una publicación del blog de RIFT OVERDRIVE.';

require_once 'helpers.php';

// El id viaja siempre en la URL (editar.php?id=3), tanto al abrir la página como al enviar el formulario.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: index.php?status=notfound');
    exit;
}

$listas = listas_formulario($pdo);
$errores = [];
$error_bd = '';

// 1) Buscar el registro (consulta preparada)
$stmt = $pdo->prepare('SELECT usuario_id, categoria_id, estado_id, titulo, contenido, fecha_publicacion
                       FROM publicaciones WHERE id = :id AND deleted_at IS NULL');
$stmt->execute([':id' => $id]);
$datos = $stmt->fetch();

if (!$datos) {
    header('Location: index.php?status=notfound');
    exit;
}
// Adaptamos la fecha al formato del input datetime-local y traemos las etiquetas actuales
$datos['fecha_publicacion'] = $datos['fecha_publicacion']
    ? date('Y-m-d\TH:i', strtotime($datos['fecha_publicacion'])) : '';
$stmt = $pdo->prepare('SELECT etiqueta_id FROM publicacion_etiqueta WHERE publicacion_id = :id');
$stmt->execute([':id' => $id]);
$datos['etiquetas'] = array_map('intval', array_column($stmt->fetchAll(), 'etiqueta_id'));

// 2) Si se envió el formulario: validar y actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$datos, $errores] = validar_publicacion($listas);

    if (!$errores) {
        try {
            $pdo->beginTransaction();
            $sql = 'UPDATE publicaciones
                    SET usuario_id = :usuario_id, categoria_id = :categoria_id, estado_id = :estado_id,
                        titulo = :titulo, contenido = :contenido, fecha_publicacion = :fecha
                    WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id'   => $datos['usuario_id'],
                ':categoria_id' => $datos['categoria_id'],
                ':estado_id'    => $datos['estado_id'],
                ':titulo'       => $datos['titulo'],
                ':contenido'    => $datos['contenido'],
                ':fecha'        => $datos['fecha_sql'],
                ':id'           => $id,
            ]);
            guardar_etiquetas($pdo, $id, $datos['etiquetas']);
            $pdo->commit();

            header('Location: index.php?status=updated');
            exit;
        } catch (PDOException $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log('admin/editar.php: ' . $ex->getMessage());
            $error_bd = 'No se pudo actualizar la publicación. Probá de nuevo.';
        }
    }
}

require_once '../includes/header.php';
require_once '../includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Editar publicación #<?= (int) $id ?></h1>

    <?php if ($errores || $error_bd): ?>
      <div class="mensaje-error" role="alert">
        <strong>No se pudo guardar. Corregí lo siguiente:</strong>
        <ul>
          <?php foreach ($errores as $msg): ?><li><?= e($msg) ?></li><?php endforeach; ?>
          <?php if ($error_bd): ?><li><?= e($error_bd) ?></li><?php endif; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php $accion = 'editar.php?id=' . (int) $id; $texto_boton = 'Guardar cambios'; require '_form.php'; ?>
  </section>
  </main>

<?php require_once '../includes/footer.php'; ?>
