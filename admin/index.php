<?php
/**
 * admin/index.php — LISTADO (Read)
 * Muestra las publicaciones del blog con su autor, categoría y estado (JOIN).
 */
$ruta_base = '../';
$pagina_actual = 'admin';
$titulo_pagina = 'Administración del blog — RIFT OVERDRIVE';
$descripcion_pagina = 'Panel para crear, editar y eliminar las publicaciones del blog de RIFT OVERDRIVE.';

require_once 'helpers.php';

$mensajes = [
    'created' => 'Publicación creada correctamente.',
    'updated' => 'Publicación actualizada correctamente.',
    'deleted' => 'Publicación eliminada correctamente.',
    'notfound' => 'La publicación solicitada no existe.',
    'error'   => 'No se pudo completar la operación.',
];
$status = $_GET['status'] ?? '';
$mensaje = $mensajes[$status] ?? '';

$error_bd = '';
$registros = [];
try {
    // Lectura: no lleva datos del usuario, pero igual usamos la misma conexión segura.
    $sql = "SELECT p.id, p.titulo, p.fecha_publicacion,
                   u.nombre_completo AS autor,
                   c.nombre AS categoria,
                   s.nombre AS estado,
                   (SELECT GROUP_CONCAT(et.nombre ORDER BY et.nombre SEPARATOR ', ')
                      FROM publicacion_etiqueta pe
                      JOIN etiquetas et ON et.id = pe.etiqueta_id
                     WHERE pe.publicacion_id = p.id) AS etiquetas
            FROM publicaciones p
            INNER JOIN usuarios u            ON u.id = p.usuario_id
            INNER JOIN categorias c          ON c.id = p.categoria_id
            INNER JOIN estados_publicacion s ON s.id = p.estado_id
            WHERE p.deleted_at IS NULL
            ORDER BY p.id DESC";
    $registros = $pdo->query($sql)->fetchAll();
} catch (PDOException $ex) {
    error_log('admin/index.php: ' . $ex->getMessage());
    $error_bd = 'No se pudieron leer las publicaciones.';
}

require_once '../includes/header.php';
require_once '../includes/nav.php';
?>
  <main id="contenido-principal">
  <section class="seccion">
    <h1>Administración del blog</h1>
    <p>Gestioná las publicaciones guardadas en la base de datos MySQL <code>rift_overdrive_db</code>.</p>

    <?php if ($mensaje !== ''): ?>
      <div class="mensaje-exito" role="status"><?= e($mensaje) ?></div>
    <?php endif; ?>
    <?php if ($error_bd !== ''): ?>
      <div class="mensaje-error" role="alert"><?= e($error_bd) ?></div>
    <?php endif; ?>

    <p><a href="crear.php" class="boton">+ Nueva publicación</a></p>

    <?php if (!$registros && $error_bd === ''): ?>
      <p>Todavía no hay publicaciones. ¡Creá la primera!</p>
    <?php else: ?>
    <div class="tabla-scroll">
      <table class="tabla-admin">
        <thead>
          <tr>
            <th>ID</th><th>Título</th><th>Autor</th><th>Categoría</th><th>Estado</th>
            <th>Etiquetas</th><th>Publicada</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($registros as $item): ?>
          <tr>
            <td><?= e($item['id']) ?></td>
            <td><?= e($item['titulo']) ?></td>
            <td><?= e($item['autor']) ?></td>
            <td><?= e($item['categoria']) ?></td>
            <td><span class="estado estado-<?= e($item['estado']) ?>"><?= e($item['estado']) ?></span></td>
            <td><?= e($item['etiquetas'] ?? '—') ?></td>
            <td><?= $item['fecha_publicacion'] ? e(date('d/m/Y H:i', strtotime($item['fecha_publicacion']))) : '—' ?></td>
            <td class="acciones">
              <a href="editar.php?id=<?= (int) $item['id'] ?>" class="btn-tabla">Editar</a>
              <!-- El borrado va por POST (no por un link) para que nadie pueda eliminar con solo abrir una URL -->
              <form action="eliminar.php" method="post" class="form-eliminar"
                    onsubmit="return confirm('¿Confirmás que querés eliminar esta publicación?');">
                <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                <button type="submit" class="btn-tabla peligro">Eliminar</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </section>
  </main>

<?php require_once '../includes/footer.php'; ?>
