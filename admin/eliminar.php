<?php
/**
 * admin/eliminar.php — BAJA (Delete)
 * Solo acepta POST (lo envía el botón "Eliminar" del listado) y usa DELETE preparado.
 * Las filas de publicacion_etiqueta se borran solas por el ON DELETE CASCADE del esquema.
 */
require_once 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: index.php?status=notfound');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM publicaciones WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $estado = $stmt->rowCount() > 0 ? 'deleted' : 'notfound';
} catch (PDOException $ex) {
    error_log('admin/eliminar.php: ' . $ex->getMessage());
    $estado = 'error';
}

header('Location: index.php?status=' . $estado);
exit;
