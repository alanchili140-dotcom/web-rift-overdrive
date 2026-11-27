<?php
/**
 * admin/_form.php — Formulario compartido por crear.php y editar.php
 * Variables esperadas: $datos, $errores, $listas, $accion (URL del form), $texto_boton
 */
?>
    <form action="<?= e($accion) ?>" method="post" class="formulario" novalidate>

      <div class="campo <?= isset($errores['titulo']) ? 'con-error' : '' ?>">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" maxlength="150" value="<?= e($datos['titulo']) ?>">
        <span class="error-campo"><?= isset($errores['titulo']) ? e($errores['titulo']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['contenido']) ? 'con-error' : '' ?>">
        <label for="contenido">Contenido:</label>
        <textarea id="contenido" name="contenido" rows="6"><?= e($datos['contenido']) ?></textarea>
        <span class="error-campo"><?= isset($errores['contenido']) ? e($errores['contenido']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['usuario_id']) ? 'con-error' : '' ?>">
        <label for="usuario_id">Autor:</label>
        <select id="usuario_id" name="usuario_id">
          <option value="">-- Elegí un autor --</option>
          <?php foreach ($listas['usuarios'] as $u): ?>
            <option value="<?= e($u['id']) ?>" <?= (string) $datos['usuario_id'] === (string) $u['id'] ? 'selected' : '' ?>><?= e($u['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error-campo"><?= isset($errores['usuario_id']) ? e($errores['usuario_id']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['categoria_id']) ? 'con-error' : '' ?>">
        <label for="categoria_id">Categoría:</label>
        <select id="categoria_id" name="categoria_id">
          <option value="">-- Elegí una categoría --</option>
          <?php foreach ($listas['categorias'] as $c): ?>
            <option value="<?= e($c['id']) ?>" <?= (string) $datos['categoria_id'] === (string) $c['id'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error-campo"><?= isset($errores['categoria_id']) ? e($errores['categoria_id']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['estado_id']) ? 'con-error' : '' ?>">
        <label for="estado_id">Estado:</label>
        <select id="estado_id" name="estado_id">
          <?php foreach ($listas['estados'] as $s): ?>
            <option value="<?= e($s['id']) ?>" <?= (string) $datos['estado_id'] === (string) $s['id'] ? 'selected' : '' ?>><?= e($s['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <span class="error-campo"><?= isset($errores['estado_id']) ? e($errores['estado_id']) : '' ?></span>
      </div>

      <div class="campo <?= isset($errores['fecha_publicacion']) ? 'con-error' : '' ?>">
        <label for="fecha_publicacion">Fecha de publicación (opcional):</label>
        <input type="datetime-local" id="fecha_publicacion" name="fecha_publicacion" value="<?= e($datos['fecha_publicacion']) ?>">
        <small>Si el estado es "publicada" y la dejás vacía, se usa la fecha y hora actuales.</small>
        <span class="error-campo"><?= isset($errores['fecha_publicacion']) ? e($errores['fecha_publicacion']) : '' ?></span>
      </div>

      <fieldset class="campo campo-checks">
        <legend>Etiquetas (opcional):</legend>
        <?php foreach ($listas['etiquetas'] as $et): ?>
          <label>
            <input type="checkbox" name="etiquetas[]" value="<?= e($et['id']) ?>"
                   <?= in_array((int) $et['id'], $datos['etiquetas'], true) ? 'checked' : '' ?>>
            <?= e($et['nombre']) ?>
          </label>
        <?php endforeach; ?>
      </fieldset>

      <div class="botones-form">
        <button type="submit"><?= e($texto_boton) ?></button>
      </div>
    </form>
    <p class="volver-clase5"><a href="index.php">‹ Volver al listado</a></p>
