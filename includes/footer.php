<?php
/**
 * includes/footer.php — Pie de página + scripts de JavaScript
 *
 * Si la página define $mostrar_config = true (solo el inicio), se agregan
 * el reloj y los interruptores de bienvenida / modo claro.
 */
$mostrar_config = $mostrar_config ?? false;
$ruta_base = $ruta_base ?? '';
?>

  <footer class="footer">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?> — Proyecto escolar, Escuela Técnica.</p>
    <p><a href="<?= $ruta_base ?>terminos.php">Términos y Condiciones</a> · <a href="<?= $ruta_base ?>clase5/index.php">Formularios (Clase 5)</a></p>
<?php if ($mostrar_config): ?>
    <p id="reloj" class="reloj-tiempo"></p>

    <div class="config-sitio">
      <label class="config-item">
        <input type="checkbox" id="toggle-bienvenida" checked>
        Mostrar mensaje de bienvenida al entrar
      </label>
      <label class="config-item">
        <input type="checkbox" id="toggle-modo-claro">
        Modo claro
      </label>
    </div>
<?php endif; ?>
  </footer>

  <script src="<?= $ruta_base ?>js/RIFT-OVERDRIVE.js"></script>
</body>
</html>