<?php
include 'conn.php';
$uploadDir = "uploads/";

if (!isset($_GET['id'])) {
    die("ID de producto no especificado.");
}

$id = $_GET['id'];

// Obtener datos del producto
$stmt = $conn->prepare("SELECT * FROM producto WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) die("Producto no encontrado.");

// Si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Si se sube nueva imagen
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $nombreArchivo = uniqid() . "_" . basename($_FILES['img']['name']);
        $rutaDestino = $uploadDir . $nombreArchivo;

        if (move_uploaded_file($_FILES['img']['tmp_name'], $rutaDestino)) {
            // Borrar imagen anterior si existe
            if (!empty($product['img']) && file_exists($uploadDir . $product['img'])) {
                unlink($uploadDir . $product['img']);
            }
        }
    } else {
        // Mantener la imagen anterior
        $nombreArchivo = $product['img'];
    }

    // Actualizar producto
    $stmt = $conn->prepare("UPDATE producto SET nombre=?, description=?, price=?, stock=?, img=? WHERE product_id=?");
    $stmt->bind_param("ssdssi", $nombre, $description, $price, $stock, $nombreArchivo, $id);

    if ($stmt->execute()) {
        echo "<p>Producto actualizado con éxito</p>";
        $product['nombre'] = $nombre;
        $product['description'] = $description;
        $product['price'] = $price;
        $product['stock'] = $stock;
        $product['img'] = $nombreArchivo;
    } else {
        echo "<p>Error al actualizar producto: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<link rel="stylesheet" href="css/headerFooter.css">
<link rel="stylesheet" href="css/update.css">

<title>Editar Producto</title>
</head>
<body>

<header>
        <a href="index.html">
            <img id="logo_head" src="recursos/img/palceholder 2.svg" alt="Logo GameBox">
        </a>

        <section id="menu_head">
            <ul>
                <!--Esta parte es temporal para revidar el crud-->
                <li><a href="create.php">CRUD</a></li>
                <li><a href="catalogo.php">Catálogo</a></li>
                <li><a href="carrito.html">Carrito de compas</a></li>
                <li><a href="userProfile.html"><img id="userIcon" src="recursos/img/placeholder.jpg" alt="Perfil del usuario"></a></li>
            </ul>
        </section>
</header>

<h2>Editar Producto</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?= htmlspecialchars($product['nombre']) ?>" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <label>Precio:</label><br>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br><br>

    <label>Imagen actual:</label><br>
    <?php if (!empty($product['img']) && file_exists($uploadDir . $product['img'])): ?>
        <img src="<?= $uploadDir . htmlspecialchars($product['img']) ?>" width="100">
    <?php else: ?>
        Sin imagen
    <?php endif; ?>
    <br><br>

    <label>Cambiar imagen:</label><br>
    <input type="file" name="img"><br><br>

    <button type="submit">Actualizar Producto</button>
</form>

<br>
<a href="catalogo.php">Volver al catálogo</a>

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

            <h4>Si quieres conocer a los desarrolladores detrás del sitio, has click <a href="aboutUs.html">aquí</a></h4>

            <div id="avisos">
                <ul>
                    <li>Aviso de Cookies</li>
                    <li>Términos de uso</li>
                    <li>Aviso de privacidad</li>
                    <li>Ayuda</li>
                    <li>Política sobre uso de materiales</li>
                    <li>Declaración de afilación</li>
                    <li>Directrices para transmisiones</li>
                    <li>Update notes</li>
                    <li>Licencias de plugins</li>
                </ul>
            </div>
        </footer>

</body>
</html>

<?php $conn->close(); ?>
