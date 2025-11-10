<?php
session_start();

//--------------------------------------------------------------------- Cargar imagen de usuario en el header
$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';

// Verificar si hay una imagen guardada en la sesión
if (!empty($_SESSION['img'])) {
    $userImage = 'data:image/jpeg;base64,' . $_SESSION['img'];
} else {
    $userImage = 'recursos/img/NoImage.png'; // imagen por defecto
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/headerFooter.css">
  <link rel="stylesheet" href="css/AboutUs.css">
  <link rel="shortcut icon" href="recursos/icons/IconoClaro.ico" type="image/x-icon">
  <title>Sobre Nosotros</title>
</head>

<body>
  <header>
    <a href="index.php">
      <img id="logo_head" src="recursos/img/IconoClaro.png" alt="Logo GameBox">
    </a>
    <section id="menu_head">
      <ul>
        <?php if ($rol == 1): ?>
          <li><a href="create.php">CRUD</a></li>
        <?php endif; ?>

        <li><a href="catalogo.php">Catálogo</a></li>

        <li>
          <?php if ($rol): ?>
            <a href="carrito.php">Carrito de compras</a>
          <?php else: ?>
            <a href="login.php">Carrito de compras</a>
          <?php endif; ?>
        </li>

        <li class="user-info">
          <?php if ($rol): ?>
            <a href="userProfile.php">
              <img id="userIcon" src="<?php echo htmlspecialchars($userImage); ?>" alt="Perfil del usuario">
            </a>
            <span class="user-name"><?php echo htmlspecialchars($nombre); ?></span>
            <form method="POST" action="logout.php" style="display:inline;">
              <button type="submit" class="logout-btn">Cerrar sesión</button>
            </form>
          <?php else: ?>
            <a href="login.php" class="logout-btn" style="background-color:#007bff;">Iniciar sesión</a>
          <?php endif; ?>
        </li>
      </ul>
    </section>
  </header>

  <main class="about-container">
    <div class="about-card">
      <h1>Sobre Nosotros</h1>
      <p>
        En <strong>GameBox Market</strong> creemos en combinar creatividad, tecnología y diseño para brindar la mejor experiencia posible. 
        Nuestro propósito es crear una plataforma moderna, accesible y visualmente atractiva, dedicada al intercambio y venta de artículos de colección.
      </p>

      <div class="team-section">
        <h2>Desarrolladores</h2>
        <div class="team-grid">
          <div class="dev-card">
            <img src="recursos/img/placeholder.jpg" alt="Desarrollador 1">
            <h3>Ricardo Natanael Olivas González</h3>
            <p>Desarrollador Front-end</p>
            <blockquote>“La funcionalidad sin diseño es aburrida; el diseño sin funcionalidad, inútil.”</blockquote>
          </div>

          <div class="dev-card">
            <img src="recursos/img/placeholder.jpg" alt="Desarrollador 2">
            <h3>Juan Pablo Zaragora Garza</h3>
            <p>Desarrollador Back-end</p>
            <blockquote>“Cada línea de código cuenta una historia de mejora.” <br><br>Juanpapu de papus</blockquote>
          </div>
        </div>
      </div>

      <section class="faq-section">
        <h2>Preguntas Frecuentes (FAQ)</h2>
        <div class="faq-list">
          <div class="faq-item">
            <h3>¿Qué es GameBox Market?</h3>
            <p>Es una plataforma creada para los amantes del coleccionismo, donde podrás explorar, registrar y compartir tus artículos favoritos.</p>
          </div>

          <div class="faq-item">
            <h3>¿Necesito una cuenta para comprar?</h3>
            <p>Sí, solo los usuarios registrados pueden acceder al carrito de compras y realizar transacciones seguras.</p>
          </div>

          <div class="faq-item">
            <h3>¿Cómo puedo contactar al equipo?</h3>
            <p>Puedes escribirnos al correo <a href="mailto:contacto@gameboxmarket.com">contacto@gameboxmarket.com</a> o mediante nuestras redes sociales.</p>
          </div>
        </div>
      </section>
    </div>
  </main>

  <footer>
    <div id="redes">
      <ul>
        <li><a href="#"><img src="recursos/icons/icons (1).png" alt="icono red"></a></li>
        <li><a href="#"><img src="recursos/icons/icons (1).webp" alt="icono red"></a></li>
      </ul>
    </div>

    <div id="disclaimer">
      <h4>© 2025 GameBox Market | Todos los derechos reservados.</h4>
      <h4>Los diseños y productos que aparecen en el sitio pertenecen a sus respectivos creadores.</h4><br>
    </div>

    <h4>Si quieres conocer a los desarrolladores detrás del sitio, <a href="aboutUs.php">haz click aquí</a></h4>

    <div id="avisos">
      <ul>
        <li>Aviso de Cookies</li>
        <li>Términos de uso</li>
        <li>Aviso de privacidad</li>
        <li>Ayuda</li>
        <li>Política sobre uso de materiales</li>
        <li>Declaración de afiliación</li>
        <li>Directrices para transmisiones</li>
        <li>Update notes</li>
        <li>Licencias de plugins</li>
      </ul>
    </div>
  </footer>
</body>
</html>
