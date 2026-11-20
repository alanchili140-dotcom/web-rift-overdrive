<?php
/**
 * includes/footer.php — Pie de página + scripts de JavaScript
 *
 * Si la página define $mostrar_config = true (solo el inicio), se agregan
 * el reloj y los interruptores de bienvenida / modo claro.
 */
$mostrar_config = $mostrar_config ?? false;
?>

  <footer class="footer">
    <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?> — Proyecto escolar, Escuela Técnica.</p>
    <p><a href="terminos.php">Términos y Condiciones</a></p>
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

  <script src="js/RIFT-OVERDRIVE.js"></script>
</body>
</html>