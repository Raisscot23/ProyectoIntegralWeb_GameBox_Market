document.addEventListener("DOMContentLoaded", () => {
  const contenedor = document.querySelector(".destacados_contenedor");
  const tarjetas = document.querySelectorAll(".tarjetaDestacado");
  const viewport = document.querySelector(".destacados_viewport");
  const prevBtn = document.querySelector(".prev");
  const nextBtn = document.querySelector(".next");

  let currentIndex = 0;
  let visibleCards = calcularVisibleCards();
  let autoplayInterval;

  function calcularVisibleCards() {
    const cardWidth = tarjetas[0].offsetWidth + 24; // incluye gap
    return Math.floor(viewport.offsetWidth / cardWidth);
  }

  function updateCarousel() {
    const cardWidth = tarjetas[0].offsetWidth + 24;
    const offset = -(currentIndex * cardWidth);
    contenedor.style.transform = `translateX(${offset}px)`;
  }

  function nextSlide() {
    const totalCards = tarjetas.length;
    if (currentIndex < totalCards - visibleCards) {
      currentIndex++;
    } else {
      currentIndex = 0; // vuelve al inicio (bucle)
    }
    updateCarousel();
  }

  function prevSlide() {
    const totalCards = tarjetas.length;
    if (currentIndex > 0) {
      currentIndex--;
    } else {
      currentIndex = totalCards - visibleCards;
    }
    updateCarousel();
  }

  nextBtn.addEventListener("click", nextSlide);
  prevBtn.addEventListener("click", prevSlide);

  // Autoplay cada 3 segundos
  function startAutoplay() {
    autoplayInterval = setInterval(nextSlide, 3000);
  }

  function stopAutoplay() {
    clearInterval(autoplayInterval);
  }

  startAutoplay();

  // Pausar autoplay al pasar el cursor
  viewport.addEventListener("mouseenter", stopAutoplay);
  viewport.addEventListener("mouseleave", startAutoplay);

  // Recalcular al redimensionar
  window.addEventListener("resize", () => {
    visibleCards = calcularVisibleCards();
    updateCarousel();
  });
});