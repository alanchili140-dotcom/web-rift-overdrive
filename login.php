<?php
$titulo_pagina = 'Iniciar sesión — RIFT OVERDRIVE';
$descripcion_pagina = 'Inicia sesion para acceder a la Beta cerrada de RIFT OVERDRIVE.';
$nav_simple = true;
require_once 'includes/header.php';
require_once 'includes/nav.php';
?>
  <main id="contenido-principal">

  <!-- ===================== INICIO DE SESIÓN ===================== -->
  <section class="seccion login-seccion">
    <div class="login-caja">
      <h1>Iniciar sesión</h1>
      <p>Ingresá a tu cuenta para acceder a la Beta de RIFT OVERDRIVE.</p>

      <form action="/login.php" method="post" class="formulario">

        <div class="campo">
          <label for="email">Correo electrónico:</label>
          <input type="email" id="email" name="email" placeholder="tuemail@ejemplo.com" required>
        </div>

        <div class="campo">
          <label for="password">Contraseña:</label>
          <input type="password" id="password" name="password" placeholder="Tu contraseña"
                 required minlength="6">
        </div>

        <div class="campo checkbox">
          <input type="checkbox" id="recordar" name="recordar" value="si">
          <label for="recordar">Recordarme en este dispositivo</label>
        </div>

        <div class="botones-form">
          <button type="submit">Iniciar sesión</button>
        </div>

      </form>

      <p class="login-alt">
        ¿No tenés cuenta?
        <a href="index.php#registro">Registrate para la Beta</a>
      </p>
    </div>

    <hr>
    <footer class="footer-seccion">
      <p>Iniciar sesión — RIFT OVERDRIVE</p>
    </footer>
  </section>

  </main>

<?php require_once 'includes/footer.php'; ?>