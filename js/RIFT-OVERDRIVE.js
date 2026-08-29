// ===================== VALIDACIÓN DEL FORMULARIO DE REGISTRO =====================

console.log("✅ RIFT-OVERDRIVE.js (versión nueva) se cargó correctamente");

const formRegistro = document.getElementById("form-registro");

if (formRegistro) {
  console.log("✅ Formulario de registro encontrado, validación activa");

  const campoNombre = document.getElementById("nombre");
  const campoEmail = document.getElementById("email");
  const errorNombre = document.getElementById("error-nombre");
  const errorEmail = document.getElementById("error-email");

  // Expresión regular simple para validar formato de email: algo@algo.algo
  const patronEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  // Solo letras (con acentos y ñ) y espacios — nada de números ni símbolos
  const patronSoloLetras = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;

  formRegistro.addEventListener("submit", function (evento) {
    console.log("📋 Se intentó enviar el formulario, corriendo validación...");
    let esValido = true;

    // Limpiamos errores previos antes de revisar de nuevo
    errorNombre.textContent = "";
    errorEmail.textContent = "";

    // --- Validar que el nombre no esté vacío ---
    if (campoNombre.value.trim() === "") {
      errorNombre.textContent = "Por favor, ingresá tu nombre.";
      esValido = false;
    } else if (!patronSoloLetras.test(campoNombre.value.trim())) {
      errorNombre.textContent = "El nombre solo puede contener letras (nada de números o símbolos).";
      esValido = false;
    }

    // --- Validar formato de email ---
    if (campoEmail.value.trim() === "") {
      errorEmail.textContent = "Por favor, ingresá tu correo electrónico.";
      esValido = false;
    } else if (!patronEmail.test(campoEmail.value.trim())) {
      errorEmail.textContent = "Ese correo no parece válido. Ejemplo: nombre@dominio.com";
      esValido = false;
    }

    // Si algo falló, cancelamos el envío del formulario
    if (!esValido) {
      evento.preventDefault();
    }
  });
}

// ===================== RELOJ EN TIEMPO REAL =====================

const elementoReloj = document.getElementById("reloj");

function actualizarReloj() {
  const ahora = new Date(); // objeto Date con la fecha y hora actuales del dispositivo

  const opciones = {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  };

  // toLocaleString formatea la fecha en español, con el formato que definimos arriba
  elementoReloj.textContent = ahora.toLocaleString("es-AR", opciones);
}

if (elementoReloj) {
  actualizarReloj(); // lo mostramos apenas carga, sin esperar el primer segundo
  setInterval(actualizarReloj, 1000); // y lo repetimos cada 1000 ms = 1 segundo
}

// ===================== MENSAJE DE BIENVENIDA (con interruptor) =====================
// El usuario puede apagar este mensaje con el checkbox del pie de página.
// Usamos localStorage para recordar su elección aunque cierre el navegador.

const checkboxBienvenida = document.getElementById("toggle-bienvenida");

// Si nunca se guardó nada, localStorage.getItem() devuelve null.
// En ese caso, tratamos al mensaje como "activado" por defecto.
const bienvenidaActivada = localStorage.getItem("mostrarBienvenida") !== "false";

if (checkboxBienvenida) {
  // Al cargar, el checkbox refleja la preferencia guardada
  checkboxBienvenida.checked = bienvenidaActivada;

  // Cada vez que el usuario lo toca, guardamos su nueva elección
  checkboxBienvenida.addEventListener("change", function () {
    localStorage.setItem("mostrarBienvenida", checkboxBienvenida.checked);
  });
}

window.addEventListener("load", function () {
  const mostrar = localStorage.getItem("mostrarBienvenida") !== "false";
  if (mostrar) {
    alert("¡Bienvenido a RIFT OVERDRIVE! Prepárate para cruzar el portal...");
  }
});

// ===================== CARRUSEL DEL HERO (estilo Steam) =====================
// Cambia la imagen grande del hero al hacer clic en las flechas o en una miniatura.

const heroImagenes = [
  { src: "img/sala.png", alt: "Instalación de entrenamiento donde transcurre la historia" },
  { src: "img/batalla.jpg", alt: "Escenario de combate" },
  { src: "img/portal2.jpg", alt: "Uso de portal" },
  { src: "img/enemigo.png", alt: "Combate contra un enemigo" }
];

let heroIndiceActual = 0;

function mostrarHero(indice) {
  heroIndiceActual = indice;

  const imgPrincipal = document.getElementById("hero-img-principal");
  imgPrincipal.src = heroImagenes[indice].src;
  imgPrincipal.alt = heroImagenes[indice].alt;

  const miniaturas = document.querySelectorAll(".miniatura");
  miniaturas.forEach((boton, i) => {
    boton.classList.toggle("activa", i === indice);
  });
}

function cambiarHero(direccion) {
  let nuevoIndice = heroIndiceActual + direccion;

  if (nuevoIndice < 0) {
    nuevoIndice = heroImagenes.length - 1;
  }
  if (nuevoIndice >= heroImagenes.length) {
    nuevoIndice = 0;
  }

  mostrarHero(nuevoIndice);
}

// ===================== MENÚ DESPLEGABLE "DESCARGAR" =====================
// Antes se mostraba solo con CSS (:hover), pero eso falla en celular/tablet
// (no hay "hover" al tocar la pantalla) y en escritorio se cerraba apenas
// el mouse salía del link. Ahora se abre y cierra con clic.

const botonDescargar = document.getElementById("btn-descargar");
const menuDescargar = document.getElementById("menu-descargar");

function cerrarMenuDescargar() {
  menuDescargar.classList.remove("abierto");
  botonDescargar.setAttribute("aria-expanded", "false");
}

if (botonDescargar && menuDescargar) {
  botonDescargar.addEventListener("click", function (evento) {
    evento.preventDefault(); // no navegamos a descarga.html, solo abrimos el menú
    const estaAbierto = menuDescargar.classList.toggle("abierto");
    botonDescargar.setAttribute("aria-expanded", estaAbierto);
  });

  // Si el usuario hace clic afuera del menú, lo cerramos
  document.addEventListener("click", function (evento) {
    const clickFueraDelDropdown = !evento.target.closest(".dropdown");
    if (clickFueraDelDropdown) {
      cerrarMenuDescargar();
    }
  });

  // Con la tecla Escape también se cierra, por accesibilidad
  document.addEventListener("keydown", function (evento) {
    if (evento.key === "Escape") {
      cerrarMenuDescargar();
    }
  });
}

// ===================== ACORDEÓN DEL CURSO (Módulo 1, 2, 3) =====================
// Cada módulo empieza cerrado. Al hacer clic en su título se muestra u
// oculta el contenido de ESE módulo, sin afectar a los demás.

const botonesModulo = document.querySelectorAll(".modulo-titulo");

botonesModulo.forEach(function (boton) {
  boton.addEventListener("click", function () {
    const modulo = boton.closest(".modulo");
    const estaAbierto = modulo.classList.toggle("abierto");
    boton.setAttribute("aria-expanded", estaAbierto);
  });
});