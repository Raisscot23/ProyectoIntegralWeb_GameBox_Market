
// === Carrusel funcional ===
const carousel = document.querySelector('.carousel');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

// Funciones de movimiento
function moveNext() {
    carousel.scrollBy({ left: 300, behavior: 'smooth' });
}

function movePrev() {
    carousel.scrollBy({ left: -300, behavior: 'smooth' });
}

// Eventos para botones
nextBtn.addEventListener('click', moveNext);
prevBtn.addEventListener('click', movePrev);

// === Movimiento automático ===
let autoScroll = setInterval(moveNext, 2000); // cada 4 segundos

// Pausar el movimiento al pasar el cursor
const carouselContainer = document.querySelector('.carousel-container');
carouselContainer.addEventListener('mouseenter', () => clearInterval(autoScroll));
carouselContainer.addEventListener('mouseleave', () => {
    autoScroll = setInterval(moveNext, 4000);
});

// Reinicia al llegar al final
carousel.addEventListener('scroll', () => {
    if (carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 10) {
        setTimeout(() => { carousel.scrollTo({ left: 0, behavior: 'smooth' }); }, 4000);
    }
});
