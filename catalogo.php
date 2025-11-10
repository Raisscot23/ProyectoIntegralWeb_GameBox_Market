<?php
include 'conn.php';
 //---------------------------------------------------------------------Recordar la sesión inciada
session_start();

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

// Obtener tipos de producto de la tabla producto_tipo
$sqlTipos = "SELECT * FROM producto_tipo";
$resultTipos = $conn->query($sqlTipos);

// Obtener filtro si está seleccionado 
$filtro_tipo = isset($_GET['tipo']) ? intval($_GET['tipo']) : 0;

// Consulta de productos con filtro 
if ($filtro_tipo > 0) {
    $sql = "SELECT p.*, t.nombre AS tipo_nombre 
            FROM producto p 
            INNER JOIN producto_tipo t ON p.product_tipo_id = t.product_tipo_id 
            WHERE p.product_tipo_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $filtro_tipo);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT p.*, t.nombre AS tipo_nombre 
            FROM producto p 
            INNER JOIN producto_tipo t ON p.product_tipo_id = t.product_tipo_id";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<link rel="stylesheet" href="css/headerFooter.css">
<link rel="stylesheet" href="css/catalogo.css">
<link rel="shortcut icon" href="recursos/icons/IconoClaro.ico" type="image/x-icon">

<title>Catálogo</title>
</head>
<body>
<header>
    <a href="index.php">
        <img id="logo_head" src="recursos/img/IconoClaro.png" alt="Logo GameBox">
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
                    <a href="login.php" class="logout-btn" style="background-color:#0071bc;">Iniciar sesión</a>
                <?php endif; ?>
            </li>
        </ul>
    </section>
</header>

<h2>Lista de Productos</h2>

<!------------------------- Filtro btn --------------------------->
<form method="GET" action="catalogo.php">
    <label for="tipo">Filtrar por tipo:</label>
    <select name="tipo" id="tipo" onchange="this.form.submit()">
        <option value="0">Todos</option>
        <?php while($tipo = $resultTipos->fetch_assoc()): ?>
            <option value="<?= $tipo['product_tipo_id'] ?>" <?= $filtro_tipo == $tipo['product_tipo_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($tipo['nombre']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>
<!------------------------- Fin de filtro btn --------------------------->

<!------------------------- Botón Agregar Producto (solo admin) --------------------------->
<?php if ($rol == 1): // Mostrar Boton solo si el usuario es administrador ?>
    <a href="create.php" class="add-product-btn">
        <span class="icon"><img src="recursos/icons/subir.png" alt="Agregar"></span>
    </a>
<?php endif; ?>

<!------------------------- Fin de Botón Agregar Producto --------------------------->

<!------------------------- NUEVA VISTA EN TARJETAS --------------------------->
<?php mysqli_data_seek($result, 0); // Reinicia el puntero del resultado ?>
<div class="product-grid">
<?php while($row = $result->fetch_assoc()): ?>
    <div class="product-card">
        <div class="card-content">
            <?php if (!empty($row['img'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($row['img']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>">
            <?php else: ?>
                <img src="recursos/img/NoImage.png" alt="Sin imagen">
            <?php endif; ?>

            <div class="product-info">
                <h3><?= htmlspecialchars($row['nombre']) ?></h3>
                <p><?= htmlspecialchars($row['tipo_nombre']) ?></p>
                <p class="description"><?= htmlspecialchars($row['description']) ?></p>
                <p class="price">$<?= number_format($row['price'], 2) ?></p>
            </div>

            <div class="product-actions">
                <a href="read.php?id=<?= $row['product_id'] ?>"><img src="recursos/icons/masinfo.png" alt="Ver"></a>
                <?php if ($rol == 1): ?>
                    <a href="update.php?id=<?= $row['product_id'] ?>"><img src="recursos/icons/editar.png" alt="Editar"></a>
                    <a href="delete.php?id=<?= $row['product_id'] ?>" class="delete" onclick="return confirm('¿Seguro que deseas eliminar este producto?');"><img src="recursos/icons/borrar.png" alt="Eliminar"></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>
<!------------------------- FIN DE VISTA EN TARJETAS --------------------------->

<!------------------------- Footer --------------------------->
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

<?php $conn->close(); ?>
