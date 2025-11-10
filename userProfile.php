<?php
include 'conn.php';
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
//---------------------------------------------------------------------

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener datos del usuario
$sql = "SELECT nombre, apellido, user_name, email, phone, address, img, password FROM usuario WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Variables para mensajes del pop-up
$successMessage = "";
$errorMessage = "";

// Actualizar datos del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $imgData = null;

    // Hashear contraseña antes de guardar
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Si se subió una nueva imagen
    if (!empty($_FILES['img']['tmp_name'])) {
        $imgData = file_get_contents($_FILES['img']['tmp_name']);
        $sql_update = "UPDATE usuario SET nombre=?, apellido=?, user_name=?, email=?, phone=?, address=?, password=?, img=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssssi", $nombre, $apellido, $user_name, $email, $phone, $address, $hashedPassword, $imgData, $user_id);
    } else {
        $sql_update = "UPDATE usuario SET nombre=?, apellido=?, user_name=?, email=?, phone=?, address=?, password=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssi", $nombre, $apellido, $user_name, $email, $phone, $address, $hashedPassword, $user_id);
    }

    if ($stmt_update->execute()) {
        $successMessage = "Perfil actualizado correctamente.";
    } else {
        $errorMessage = "Error al actualizar perfil.";
    }
}

// Imagen del usuario
$userImage = $user['img'] ? 'data:image/jpeg;base64,' . base64_encode($user['img']) : 'recursos/img/NoImage.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/profile.css">
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
                    <a href="login.php" class="logout-btn login">Iniciar sesión</a>
                <?php endif; ?>
            </li>
        </ul>
    </section>
</header>

<div class="profile-container">
    <div class="profile-card">
        <h1>Perfil de <?php echo htmlspecialchars($user['user_name']); ?></h1>
        <div class="profile-content">
        <form method="POST" enctype="multipart/form-data" class="profile-form">
            <div class="form-section">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
            <label>Apellido:</label>
            <input type="text" name="apellido" value="<?php echo htmlspecialchars($user['apellido']); ?>" required>
            <label>Nombre de usuario:</label>
            <input type="text" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <label>Teléfono:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            <label>Dirección:</label>
            <textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
            <label>Contraseña:</label>
            <input type="password" name="password" value="" placeholder="Nueva contraseña" required>
            <label for="confirm_password">Confirmar contraseña:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite la contraseña" required>
            <button type="submit" class="Update-Profile">Guardar cambios</button>
            <a href="index.php" class="volver">⬅ Volver al inicio</a>
            </div>
            <div class="profile-preview">
            <h3>Vista previa:</h3>
            <div class="profile-pic">
            <img id="preview-img" src="<?php echo htmlspecialchars($userImage); ?>" alt="Foto de perfil">
            </div>
            <label class="upload-label">Cambiar foto:</label>
            <input type="file" name="img" accept="image/*" id="imgInput">
            </div>
        </form>
        </div>
    </div>
</div>

<?php if (!empty($successMessage) || !empty($errorMessage)): ?>
<div class="popup-overlay" id="popup">
  <div class="popup-content">
    <h3><?php echo !empty($successMessage) ? htmlspecialchars($successMessage) : htmlspecialchars($errorMessage); ?></h3>
    <button id="closePopup">Aceptar</button>
  </div>
</div>

<script>
  const popup = document.getElementById('popup');
  const closeBtn = document.getElementById('closePopup');
  popup.style.display = 'flex';
  popup.style.opacity = '1';

  closeBtn.addEventListener('click', () => {
    popup.style.opacity = '0';
    setTimeout(() => popup.remove(), 300);
    <?php if (!empty($successMessage)): ?>
      window.location.href = "userProfile.php";
    <?php endif; ?>
  });

  setTimeout(() => {
    popup.style.opacity = '0';
    setTimeout(() => {
      popup.remove();
      <?php if (!empty($successMessage)): ?>
        window.location.href = "userProfile.php";
      <?php endif; ?>
    }, 300);
  }, 3000);
</script>
<?php endif; ?>

<script>
  // Vista previa de imagen al seleccionar un nuevo archivo
  document.getElementById('imgInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('preview-img').src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });
</script>

</body>
</html>
