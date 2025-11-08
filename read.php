<?php
include 'conn.php';
session_start();

// Validar que venga el ID en la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Producto no especificado.'); window.location.href='catalogo.php';</script>";
    exit();
}

$product_id = intval($_GET['id']);

// Buscar el producto por ID
$sql = "SELECT p.*, t.nombre AS tipo_nombre 
        FROM producto p
        INNER JOIN producto_tipo t ON p.product_tipo_id = t.product_tipo_id
        WHERE p.product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Producto no encontrado.'); window.location.href='catalogo.php';</script>";
    exit();
}

$producto = $result->fetch_assoc();

 //---------------------------------------------------------------------Cargar imagen de usurio en el header
$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';

// Verificar si hay una imagen guardada en la sesión
if (!empty($_SESSION['img'])) {
    $userImage = 'data:image/jpeg;base64,' . $_SESSION['img'];
} else {
    $userImage = 'recursos/img/NoImage.png'; // imagen por defecto
}
 //---------------------------------------------------------------------

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<link rel="stylesheet" href="css/headerFooter.css">
<link rel="stylesheet" href="css/read.css">

<title>Editar Producto</title>

<style>
        #userIcon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .logout-btn {
            background-color: #ff4040;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 15px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-name {
            font-weight: bold;
            color: white;
        }
    </style>
</head>
<body>
<header>
        <a href="index.php">
            <img id="logo_head" src="recursos/img/palceholder 2.svg" alt="Logo GameBox">
        </a>

        <section id="menu_head">
            <ul>
                <?php if ($rol == 1): // Mostrar CRUD solo si el usuario es administrador ?>
                    <li><a href="create.php">CRUD</a></li>
                <?php endif; ?>

                <li><a href="catalogo.php">Catálogo</a></li>


                <li>
                    <?php if ($rol): ?>
                        <a href="carrito.php">Carrito de compras</a>
                    <?php else: ?>
                        <a href="login.php"">Carrito de compras</a>
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

<div class="container">
    <img src="data:image/jpeg;base64,<?= base64_encode($producto['img']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>">

    <div class="info">
        <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
        <p class="tipo">Categoría: <?= htmlspecialchars($producto['tipo_nombre']) ?></p>
        <p><?= nl2br(htmlspecialchars($producto['description'])) ?></p>
        <p class="price">$<?= number_format($producto['price'], 2) ?></p>
        <p><strong>Stock disponible:</strong> <?= $producto['stock'] ?></p>

        <form action="agregar_carrito.php" method="POST">
        <input type="hidden" name="product_id" value="<?= $producto['product_id'] ?>">
        <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" required>
        <button type="submit" class="btn">🛒 Añadir al carrito</button>
</form>

    </div>
</div>

<div class="volver">
    <a href="catalogo.php">← Volver al catálogo</a>
</div>

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

            <h4>Si quieres conocer a los desarrolladores detrás del sitio,<a href="aboutUs.php"> haz click aquí</a></h4>

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