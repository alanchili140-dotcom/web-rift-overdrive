// ===================== VALIDACIÓN DEL FORMULARIO DE REGISTRO =====================

const formRegistro = document.getElementById("form-registro");

if (formRegistro) {
  const campoNombre = document.getElementById("nombre");
  const campoEmail = document.getElementById("email");
  const errorNombre = document.getElementById("error-nombre");
  const errorEmail = document.getElementById("error-email");

  // Expresión regular simple para validar formato de email: algo@algo.algo
  const patronEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  formRegistro.addEventListener("submit", function (evento) {
    let esValido = true;

    // Limpiamos errores previos antes de revisar de nuevo
    errorNombre.textContent = "";
    errorEmail.textContent = "";

    // --- Validar que el nombre no esté vacío ---
    if (campoNombre.value.trim() === "") {
      errorNombre.textContent = "Por favor, ingresá tu nombre.";
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