<?php
include 'conn.php';
session_start();

// Obtener los tipos de producto para llenar el <select>
$tipos_query = "SELECT product_tipo_id, nombre FROM producto_tipo";
$tipos_result = $conn->query($tipos_query);

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $product_tipo_id = $_POST['product_tipo_id'];
    $nombre = $_POST['nombre'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Subir imagen correctamente
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $imagen = file_get_contents($_FILES['img']['tmp_name']);

        // Usamos prepared statement para insertar el producto
        $stmt = $conn->prepare("INSERT INTO producto (product_tipo_id, nombre, description, price, stock, img) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdis", $product_tipo_id, $nombre, $description, $price, $stock, $imagen);

        if ($stmt->execute()) {
        $successMessage = "Producto agregado con éxito";
        } else {
            $errorMessage = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<p>Error al subir la imagen.</p>";
    }
}

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

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/create.css">
    <link rel="shortcut icon" href="recursos/icons/IconoClaro.ico" type="image/x-icon">

    <title>Agregar Producto</title>
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

<h2>Agregar Producto</h2>

<form action="create.php" method="POST" enctype="multipart/form-data" class="product-form">
  <div class="form-container">

    <div class="form-fields">
      <label>Tipo de producto:</label><br>
      <select name="product_tipo_id" required>
        <option value="">-- Selecciona un tipo --</option>
        <?php
        if ($tipos_result->num_rows > 0) {
            while ($tipo = $tipos_result->fetch_assoc()) {
                echo "<option value='{$tipo['product_tipo_id']}'>{$tipo['nombre']}</option>";
            }
        } else {
            echo "<option value=''>No hay tipos registrados</option>";
        }
        ?>
      </select><br><br>

      <label>Nombre:</label><br>
      <input type="text" name="nombre" required><br><br>

      <label>Descripción:</label><br>
      <textarea name="description" required></textarea><br><br>

      <label>Precio:</label><br>
      <input type="number" step="0.01" name="price" required><br><br>

      <label>Stock:</label><br>
      <input type="number" name="stock" required><br><br>

      <label>Imagen:</label><br>
      <input type="file" name="img" accept="image/*" required><br><br>
    </div>

    <!-- Vista previa -->
    <div class="preview-container">
      <p>Vista previa:</p>
      <img id="preview-img" src="#" alt="Vista previa de la imagen" style="display:none;">
    </div>

  </div>

  <button type="submit" class="GuardarProducto">Guardar Producto</button>
</form>

<!-- Script para mostrar la vista previa -->
<script>
  const inputImagen = document.querySelector('input[type="file"]');
  const previewImg = document.getElementById('preview-img');

  inputImagen.addEventListener('change', function (event) {
    const archivo = event.target.files[0];
    if (archivo) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.style.display = 'block';
      };
      reader.readAsDataURL(archivo);
    } else {
      previewImg.src = '#';
      previewImg.style.display = 'none';
    }
  });
</script>


<br>
<a href="catalogo.php" class="VerProductos">Ver productos</a>

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
            });

            // También desaparecerá automáticamente después de 3 segundos
            setTimeout(() => {
                popup.style.opacity = '0';
                setTimeout(() => popup.remove(), 300);
            }, 3000);
            </script>
        <?php endif; ?>

</body>
</html>
