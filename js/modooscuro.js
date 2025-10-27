const body = document.body;

// Aplicar preferencia guardada si existe
if (localStorage.getItem('theme') === 'dark') {
  body.classList.add('dark-mode');
  toggleButton.textContent = 'Modo claro';
}