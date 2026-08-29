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