// ===================== VALIDACIÓN Y RESUMEN DEL FORMULARIO DE REGISTRO =====================
// Al enviar el formulario, primero se valida. Si está todo bien, en vez de
// enviarlo directo se muestra un resumen de los datos para que el usuario
// los confirme antes de que se envíen de verdad.

console.log("✅ RIFT-OVERDRIVE.js (versión nueva) se cargó correctamente");

const formRegistro = document.getElementById("form-registro");
const resumenOverlay = document.getElementById("resumen-overlay");

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

  // Traduce el value de cada radio/select a un texto legible para el resumen
  const textosPlataforma = {
    pc: "PC",
    ps5: "PlayStation 5",
    xbox: "Xbox Series X/S",
  };

  const textosModo = {
    campania: "Campaña",
    multijugador: "Multijugador",
    ambos: "Ambos",
  };

  let resumenLista, botonResumenConfirmar, botonResumenEditar;

  if (resumenOverlay) {
    resumenLista = document.getElementById("resumen-lista");
    botonResumenConfirmar = document.getElementById("resumen-confirmar");
    botonResumenEditar = document.getElementById("resumen-editar");
  }

  // Agrega un par término/valor al resumen. Si el dato está vacío, avisa que no se completó.
  function agregarFilaResumen(etiqueta, valor) {
    const dt = document.createElement("dt");
    dt.textContent = etiqueta;

    const dd = document.createElement("dd");
    dd.textContent = valor && valor.trim() !== "" ? valor : "(no completado)";

    resumenLista.appendChild(dt);
    resumenLista.appendChild(dd);
  }

  function mostrarResumenRegistro() {
    resumenLista.innerHTML = ""; // limpiamos el resumen anterior

    const datos = new FormData(formRegistro);
    const modoElegido = formRegistro.querySelector('input[name="modo"]:checked');

    agregarFilaResumen("Nombre y Apellido", datos.get("nombre"));
    agregarFilaResumen("Apodo", datos.get("apodo"));
    agregarFilaResumen("Correo electrónico", datos.get("email"));
    agregarFilaResumen("Fecha de nacimiento", datos.get("nacimiento"));
    agregarFilaResumen("Plataforma", textosPlataforma[datos.get("plataforma")] || "");
    agregarFilaResumen("Modo de juego favorito", modoElegido ? textosModo[modoElegido.value] : "");
    agregarFilaResumen("Código de invitación", datos.get("codigo"));
    agregarFilaResumen("Comentarios", datos.get("comentarios"));
    agregarFilaResumen("Recibir novedades por correo", datos.get("newsletter") ? "Sí" : "No");

    resumenOverlay.classList.add("abierto");
  }

  formRegistro.addEventListener("submit", function (evento) {
    console.log("📋 Se intentó enviar el formulario, corriendo validación...");
    evento.preventDefault(); // nunca enviamos directo: primero validamos y mostramos el resumen

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

    // Si pasó la validación, mostramos el resumen en vez de enviar directo
    if (esValido && resumenOverlay) {
      mostrarResumenRegistro();
    }
  });

  if (resumenOverlay) {
    botonResumenEditar.addEventListener("click", function () {
      resumenOverlay.classList.remove("abierto"); // vuelve al formulario, tal cual quedó cargado
    });

    botonResumenConfirmar.addEventListener("click", function () {
      // Este sitio es un proyecto escolar sin servidor real, así que simulamos el envío.
      // Con un backend real, acá iría: formRegistro.submit();
      resumenOverlay.classList.remove("abierto");
      alert("¡Listo! Tu registro para la Beta fue enviado.");
      formRegistro.reset();
    });

    // Clic afuera de la caja de resumen también vuelve a editar
    resumenOverlay.addEventListener("click", function (evento) {
      if (evento.target === resumenOverlay) {
        resumenOverlay.classList.remove("abierto");
      }
    });

    // Escape también cierra el resumen y vuelve al formulario
    document.addEventListener("keydown", function (evento) {
      if (evento.key === "Escape" && resumenOverlay.classList.contains("abierto")) {
        resumenOverlay.classList.remove("abierto");
      }
    });
  }
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

// ===================== MODO CLARO (toggle de tema) =====================
// Agrega o saca la clase "modo-claro" del <body>. Esa clase redefine las
// variables de color (ver CSS), así que con solo esa clase cambia el sitio entero.
// La elección se guarda en localStorage para que se mantenga al volver a entrar.

const checkboxModoClaro = document.getElementById("toggle-modo-claro");
const modoClaroActivado = localStorage.getItem("modoClaro") === "true";

function aplicarModoClaro(activado) {
  document.body.classList.toggle("modo-claro", activado);
}

aplicarModoClaro(modoClaroActivado); // se aplica apenas carga, con la preferencia guardada

if (checkboxModoClaro) {
  checkboxModoClaro.checked = modoClaroActivado;

  checkboxModoClaro.addEventListener("change", function () {
    aplicarModoClaro(checkboxModoClaro.checked);
    localStorage.setItem("modoClaro", checkboxModoClaro.checked);
  });
}

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

// ===================== GALERÍA CON IMAGEN AMPLIADA (LIGHTBOX) =====================
// Al hacer clic en una miniatura de la galería, se abre la imagen en grande.
// Con las flechas se puede pasar a la imagen siguiente o anterior sin cerrar
// la vista ampliada.

const itemsGaleria = document.querySelectorAll(".galeria-item");
const lightbox = document.getElementById("lightbox");

if (itemsGaleria.length > 0 && lightbox) {
  const lightboxImg = document.getElementById("lightbox-img");
  const botonCerrarLightbox = document.getElementById("lightbox-cerrar");
  const botonAnteriorLightbox = document.getElementById("lightbox-anterior");
  const botonSiguienteLightbox = document.getElementById("lightbox-siguiente");

  // Armamos la lista de imágenes a partir de las miniaturas que ya están en el HTML
  const imagenesGaleria = Array.from(itemsGaleria).map(function (item) {
    const img = item.querySelector("img");
    return { src: img.src, alt: img.alt };
  });

  let indiceLightboxActual = 0;

  function mostrarImagenLightbox(indice) {
    indiceLightboxActual = indice;
    lightboxImg.src = imagenesGaleria[indice].src;
    lightboxImg.alt = imagenesGaleria[indice].alt;
  }

  function abrirLightbox(indice) {
    mostrarImagenLightbox(indice);
    lightbox.classList.add("abierto");
  }

  function cerrarLightbox() {
    lightbox.classList.remove("abierto");
  }

  function cambiarImagenLightbox(direccion) {
    let nuevoIndice = indiceLightboxActual + direccion;

    if (nuevoIndice < 0) {
      nuevoIndice = imagenesGaleria.length - 1;
    }
    if (nuevoIndice >= imagenesGaleria.length) {
      nuevoIndice = 0;
    }

    mostrarImagenLightbox(nuevoIndice);
  }

  itemsGaleria.forEach(function (item, indice) {
    item.addEventListener("click", function () {
      abrirLightbox(indice);
    });
  });

  botonCerrarLightbox.addEventListener("click", cerrarLightbox);
  botonAnteriorLightbox.addEventListener("click", function () {
    cambiarImagenLightbox(-1);
  });
  botonSiguienteLightbox.addEventListener("click", function () {
    cambiarImagenLightbox(1);
  });

  // Clic en el fondo oscuro (fuera de la imagen y las flechas) también cierra
  lightbox.addEventListener("click", function (evento) {
    if (evento.target === lightbox) {
      cerrarLightbox();
    }
  });

  // Navegación con teclado: flechas para moverse, Escape para cerrar
  document.addEventListener("keydown", function (evento) {
    if (!lightbox.classList.contains("abierto")) {
      return;
    }
    if (evento.key === "Escape") {
      cerrarLightbox();
    } else if (evento.key === "ArrowRight") {
      cambiarImagenLightbox(1);
    } else if (evento.key === "ArrowLeft") {
      cambiarImagenLightbox(-1);
    }
  });
}