const container = document.querySelector('.destacados_contenedor');
const prev = document.querySelector('.prev');
const next = document.querySelector('.next');

let index = 0;
const totalItems = document.querySelectorAll('.tarjetaDestacado').length;
const visibleItems = 3; // Se muestran 3 productos por vista

next.addEventListener('click', () => {
    if (index >= totalItems - visibleItems) {
        index = 0; 
    } else {
        index++;
    }
    updateCarousel();
});

prev.addEventListener('click', () => {
    if (index <= 0) {
        index = totalItems - visibleItems;
    } else {
        index--;
    }
    updateCarousel();
});

function updateCarousel() {
    const slide = document.querySelector('.tarjetaDestacado');
    const slideWidth = slide.offsetWidth + 20;
    container.style.transform = `translateX(-${index * slideWidth}px)`;
}

