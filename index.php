<?php
include 'conn.php';
session_start();

// Datos del usuario
$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';

if (!empty($_SESSION['img'])) {
    $userImage = 'data:image/jpeg;base64,' . $_SESSION['img'];
} else {
    $userImage = 'recursos/img/NoImage.png';
}

// Obtener productos destacados (los 4 más nuevos)
$sql = "SELECT p.*, t.nombre AS tipo_nombre 
        FROM producto p 
        INNER JOIN producto_tipo t ON p.product_tipo_id = t.product_tipo_id 
        ORDER BY p.product_id DESC 
        LIMIT 4";
$result = $conn->query($sql);

// Obtener artículos recientes (los siguientes 8)
$sqlRecientes = "SELECT p.*, t.nombre AS tipo_nombre 
                 FROM producto p 
                 INNER JOIN producto_tipo t ON p.product_tipo_id = t.product_tipo_id 
                 ORDER BY p.product_id DESC 
                 LIMIT 8 OFFSET 4";
$resultRecientes = $conn->query($sqlRecientes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GameBoxMarket - Inicio</title>
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="shortcut icon" href="recursos/icons/IconoClaro.ico" type="image/x-icon">
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
            <li><a href="aboutUs.php">Nosotros</a></li>
            <li>
                <?php if ($rol): ?>
                    <a href="carrito.php">Carrito</a>
                <?php else: ?>
                    <a href="login.php">Carrito</a>
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
                    <a href="login.php" class="logout-btn" style="background-color:#0071bc;">Iniciar sesión</a>
                <?php endif; ?>
            </li>
        </ul>
    </section>
</header>

<!-- === HERO === -->
<section class="hero">
    <div class="hero-overlay"></div>
    <img src="recursos/img/Logos.png" alt="Logo Gamebox" class="hero-logo">
    <h1>Bienvenido a GameBox Market</h1>
    <p>Tu espacio creativo para encontrar diseños únicos y personalizados</p>
    <a href="catalogo.php" class="hero-btn">Explorar Catálogo</a>
</section>

<!-- Sección de bienvenida personalizada -->
<?php if ($rol): ?>
<section class="bienvenida">
    <div class="bienvenida-card">
        <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Usuario" class="bienvenida-img">
        <div class="bienvenida-texto">
            <h2>¡Hola, <?php echo htmlspecialchars($nombre); ?>!</h2>
            <p>Nos alegra verte de nuevo en <strong>GameBoxMarket</strong>.  
               Descubre nuevos productos y continúa expandiendo tu colección gamer 🎮</p>
            <a href="userProfile.php" class="btn-perfil">Ver mi perfil</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Productos destacados -->
<section class="destacados">
    <h2>Productos destacados</h2>
    <div class="product-preview">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <?php if (!empty($row['img'])): ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($row['img']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>">
                <?php else: ?>
                    <img src="recursos/img/NoImage.png" alt="Sin imagen">
                <?php endif; ?>
                <div class="info">
                    <h3><?= htmlspecialchars($row['nombre']) ?></h3>
                    <p><?= htmlspecialchars($row['tipo_nombre']) ?></p>
                    <span>$<?= number_format($row['price'], 2) ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <a href="catalogo.php" class="ver-mas">Ver más productos</a>
</section>

<!-- Artículos recientes - Carrusel -->
<section class="recientes">
    <h2>Artículos recientes</h2>
    <div class="carousel-container">
        <button class="carousel-btn left" id="prevBtn">&#10094;</button>
        <div class="carousel">
            <?php while($rowRec = $resultRecientes->fetch_assoc()): ?>
                <div class="product-card">
                    <?php if (!empty($rowRec['img'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($rowRec['img']) ?>" alt="<?= htmlspecialchars($rowRec['nombre']) ?>">
                    <?php else: ?>
                        <img src="recursos/img/NoImage.png" alt="Sin imagen">
                    <?php endif; ?>
                    <div class="info">
                        <h3><?= htmlspecialchars($rowRec['nombre']) ?></h3>
                        <p><?= htmlspecialchars($rowRec['tipo_nombre']) ?></p>
                        <span>$<?= number_format($rowRec['price'], 2) ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <button class="carousel-btn right" id="nextBtn">&#10095;</button>
    </div>
</section>

<!-- Footer -->
<footer>
    <div id="redes">
        <ul>
            <li><a href=""><img src="recursos/icons/icons (1).png" alt=""></a></li>
            <li><a href=""><img src="recursos/icons/icons (1).webp" alt=""></a></li>
        </ul>
    </div>
    
    <div id="disclaimer">
        <h4>© 2025 GameBox Market | Todos los derechos reservados.</h4>
        <h4>Los diseños y productos que aparecen en el sitio pertenecen a sus respectivos creadores.</h4><br>
    </div>

    <h4>¿Quieres conocer a los desarrolladores detrás del sitio? <a href="aboutUs.php">Haz clic aquí</a></h4>

    <div id="avisos">
        <ul>
            <li>Aviso de Cookies</li>
            <li>Términos de uso</li>
            <li>Aviso de privacidad</li>
            <li>Ayuda</li>
            <li>Política de materiales</li>
            <li>Declaración de afiliación</li>
            <li>Update notes</li>
        </ul>
    </div>
</footer>

<script src="js/carousel.js"></script>
</body>
</html>
<?php $conn->close(); ?>
