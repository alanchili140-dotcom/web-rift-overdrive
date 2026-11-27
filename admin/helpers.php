<?php
/**
 * admin/helpers.php — Funciones compartidas del módulo CRUD
 */
// TP8: todo el módulo CRUD queda protegido. Hay que estar logueado Y tener rol "admin".
// (crear.php, editar.php, eliminar.php e index.php incluyen este archivo antes de cualquier salida.)
require_once __DIR__ . '/../includes/auth_guard.php';
verificarRol('admin');

require_once __DIR__ . '/../config/conexion.php';

/** Escapa una salida para HTML (protección XSS). */
function e($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Listas para los <select> y las casillas del formulario (consultas fijas, sin datos del usuario). */
function listas_formulario(PDO $pdo): array
{
    return [
        'usuarios'   => $pdo->query('SELECT id, nombre_completo AS nombre FROM usuarios WHERE deleted_at IS NULL ORDER BY nombre_completo')->fetchAll(),
        'categorias' => $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll(),
        'estados'    => $pdo->query('SELECT id, nombre FROM estados_publicacion ORDER BY id')->fetchAll(),
        'etiquetas'  => $pdo->query('SELECT id, nombre FROM etiquetas ORDER BY nombre')->fetchAll(),
    ];
}

/** Limpia un texto: sin espacios sobrantes ni caracteres de control. */
function limpiar(?string $texto): string
{
    $texto = trim($texto ?? '');
    return preg_replace('/[^\P{C}\n\r\t]+/u', '', $texto) ?? '';
}

/**
 * Lee y valida los datos del formulario (POST).
 * Devuelve [datos limpios, errores].
 */
function validar_publicacion(array $listas): array
{
    $datos = [
        'titulo'            => limpiar($_POST['titulo'] ?? ''),
        'contenido'         => limpiar($_POST['contenido'] ?? ''),
        'usuario_id'        => filter_input(INPUT_POST, 'usuario_id', FILTER_VALIDATE_INT),
        'categoria_id'      => filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT),
        'estado_id'         => filter_input(INPUT_POST, 'estado_id', FILTER_VALIDATE_INT),
        'fecha_publicacion' => trim($_POST['fecha_publicacion'] ?? ''),
        'etiquetas'         => [],
    ];
    $errores = [];
    $len = fn(string $t) => mb_strlen($t, 'UTF-8');

    if ($len($datos['titulo']) < 3 || $len($datos['titulo']) > 150) {
        $errores['titulo'] = 'El título debe tener entre 3 y 150 caracteres.';
    }
    if ($len($datos['contenido']) < 10) {
        $errores['contenido'] = 'El contenido debe tener al menos 10 caracteres.';
    }

    // Lista blanca: el valor tiene que existir realmente en la base
    $ids = fn(array $filas) => array_column($filas, 'id');
    if (!in_array($datos['usuario_id'], $ids($listas['usuarios']), true)) {
        $errores['usuario_id'] = 'Elegí un autor válido.';
    }
    if (!in_array($datos['categoria_id'], $ids($listas['categorias']), true)) {
        $errores['categoria_id'] = 'Elegí una categoría válida.';
    }
    if (!in_array($datos['estado_id'], $ids($listas['estados']), true)) {
        $errores['estado_id'] = 'Elegí un estado válido.';
    }

    // Etiquetas (opcionales, varias): solo se aceptan ids que existan
    $enviadas = $_POST['etiquetas'] ?? [];
    if (is_array($enviadas)) {
        foreach ($enviadas as $valor) {
            $id = filter_var($valor, FILTER_VALIDATE_INT);
            if ($id !== false && in_array($id, $ids($listas['etiquetas']), true)) {
                $datos['etiquetas'][] = $id;
            }
        }
        $datos['etiquetas'] = array_values(array_unique($datos['etiquetas']));
    }

    // Fecha de publicación (opcional): formato del input datetime-local
    $fecha_sql = null;
    if ($datos['fecha_publicacion'] !== '') {
        $f = DateTime::createFromFormat('Y-m-d\TH:i', $datos['fecha_publicacion']);
        if (!$f || $f->format('Y-m-d\TH:i') !== $datos['fecha_publicacion']) {
            $errores['fecha_publicacion'] = 'La fecha no es válida.';
        } else {
            $fecha_sql = $f->format('Y-m-d H:i:s');
        }
    } elseif (!isset($errores['estado_id'])) {
        // Si se marca como "publicada" y no se eligió fecha, se usa el momento actual
        foreach ($listas['estados'] as $est) {
            if ($est['id'] === $datos['estado_id'] && $est['nombre'] === 'publicada') {
                $fecha_sql = date('Y-m-d H:i:s');
            }
        }
    }
    $datos['fecha_sql'] = $fecha_sql;

    return [$datos, $errores];
}

/** Reemplaza las etiquetas de una publicación (se usa dentro de una transacción). */
function guardar_etiquetas(PDO $pdo, int $publicacion_id, array $etiquetas): void
{
    $stmt = $pdo->prepare('DELETE FROM publicacion_etiqueta WHERE publicacion_id = :pid');
    $stmt->execute([':pid' => $publicacion_id]);

    $stmt = $pdo->prepare('INSERT INTO publicacion_etiqueta (publicacion_id, etiqueta_id) VALUES (:pid, :eid)');
    foreach ($etiquetas as $eid) {
        $stmt->execute([':pid' => $publicacion_id, ':eid' => $eid]);
    }
}