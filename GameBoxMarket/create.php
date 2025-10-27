<?php
include 'conn.php';

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = $_POST['nombre'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Subir imagen correctamente
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $imagen = file_get_contents($_FILES['img']['tmp_name']);

        // Usamos prepared statement para insertar el producto
        $stmt = $conn->prepare("INSERT INTO producto (product_tipo_id, nombre, description, price, stock, img) VALUES (1, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $nombre, $description, $price, $stock, $imagen);

        if ($stmt->execute()) {
            echo "<p>Producto agregado con éxito</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Error al subir la imagen.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/create.css">

    <title>Agregar Producto</title>
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

<h2>Agregar Producto</h2>

<form action="create.php" method="POST" enctype="multipart/form-data">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Precio:</label><br>
    <input type="number" step="0.01" name="price" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" required><br><br>

    <label>Imagen:</label><br>
    <input type="file" name="img" required><br><br>

    <button type="submit">Guardar Producto</button>
</form>

<br>
<a href="catalogo.php">Ver productos</a>

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
