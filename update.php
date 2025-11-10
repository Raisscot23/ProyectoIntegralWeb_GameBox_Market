<?php
include 'conn.php';
session_start();

//---------------------------------------------------------------------Cargar imagen de usuario en el header
$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';

// Verificar si hay una imagen guardada en la sesión
if (!empty($_SESSION['img'])) {
    $userImage = 'data:image/jpeg;base64,' . $_SESSION['img'];
} else {
    $userImage = 'recursos/img/NoImage.png'; // imagen por defecto
}
//---------------------------------------------------------------------

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

$successMessage = "";
$errorMessage = "";

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
        $successMessage = "Producto actualizado con éxito";
        $product['nombre'] = $nombre;
        $product['description'] = $description;
        $product['price'] = $price;
        $product['stock'] = $stock;
        $product['img'] = $nombreArchivo;
    } else {
        $errorMessage = "Error al actualizar producto: " . $stmt->error;
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
<link rel="shortcut icon" href="recursos/icons/IconoClaro.ico" type="image/x-icon">

<title>Editar Producto</title>
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
                    <a href="login.php" class="logout-btn" style="background-color:#007bff;">Iniciar sesión</a>
                <?php endif; ?>
            </li>
        </ul>
    </section>
</header>

<h2>Editar Producto</h2>

<form action="" method="POST" enctype="multipart/form-data" class="update-form">
    <div class="form-left">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($product['nombre']) ?>" required><br><br>

        <label>Descripción:</label><br>
        <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br><br>

        <label>Precio:</label><br>
        <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required><br><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br><br>

        <label>Cambiar imagen:</label><br>
        <input type="file" name="img" id="imgInput" accept="image/*"><br><br>

        <button type="submit" class="GuardarProducto">Actualizar Producto</button>
        <a href="catalogo.php" class="Volver">Volver al catálogo</a>
    </div>

    <div class="form-right">
        <label>Vista previa:</label><br>
        <?php if (!empty($product['img'])): ?>
            <img id="preview" 
                src="data:image/jpeg;base64,<?= base64_encode($product['img']) ?>" 
                alt="Vista previa del producto">
        <?php else: ?>
            <img id="preview" src="recursos/img/NoImage.png" alt="Sin imagen">
        <?php endif; ?>
    </div>


</form>

<?php if (!empty($successMessage) || !empty($errorMessage)): ?>
<div class="popup-overlay" id="popup">
  <div class="popup-content">
    <h3><?php echo !empty($successMessage) ? $successMessage : $errorMessage; ?></h3>
    <button id="closePopup">Aceptar</button>
  </div>
</div>

<script>
  const popup = document.getElementById('popup');
  const closeBtn = document.getElementById('closePopup');
  popup.style.display = 'flex';

  closeBtn.addEventListener('click', () => {
    popup.style.opacity = '0';
    setTimeout(() => popup.remove(), 300);
    <?php if (!empty($successMessage)): ?>
      window.location.href = "catalogo.php";
    <?php endif; ?>
  });

  // Desaparece automáticamente después de 3 segundos
  setTimeout(() => {
    popup.style.opacity = '0';
    setTimeout(() => {
      popup.remove();
      <?php if (!empty($successMessage)): ?>
        window.location.href = "catalogo.php";
      <?php endif; ?>
    }, 300);
  }, 3000);
</script>
<?php endif; ?>

<script>
// Vista previa de imagen
document.getElementById('imgInput').addEventListener('change', function (event) {
    const [file] = event.target.files;
    if (file) {
        document.getElementById('preview').src = URL.createObjectURL(file);
    }
});
</script>

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
