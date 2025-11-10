<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $rol = 2; // Rol por defecto: cliente

    if (!empty($nombre) && !empty($apellido) && !empty($user_name) && !empty($email) && !empty($password) && !empty($phone) && !empty($address)) {

        // --- Hashear contraseña ---
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // --- Imagen opcional ---
        $imgData = null;
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $imgData = file_get_contents($_FILES['img']['tmp_name']);
        }

        // --- Insertar usuario ---
        $sql = "INSERT INTO usuario (nombre, apellido, user_name, email, password, phone, address, img, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssssssi", $nombre, $apellido, $user_name, $email, $hashedPassword, $phone, $address, $imgData, $rol);

            if ($stmt->execute()) {
                session_start();
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['rol'] = $rol;
                $_SESSION['img'] = $imgData ? base64_encode($imgData) : null;

                echo "<script>alert('Usuario registrado correctamente. Bienvenido, $nombre!'); window.location.href='index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error al registrar usuario.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error en la preparación de la consulta.');</script>";
        }
    } else {
        echo "<script>alert('Por favor, completa todos los campos.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Usuario - GameBoxMarket</title>
  <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card large">
      <h1>Crear Cuenta</h1>
      <form method="POST" enctype="multipart/form-data" class="auth-form two-columns" id="registerForm">
        <div class="form-section">
          <label>Nombre:</label>
          <input type="text" name="nombre" required>

          <label>Apellido:</label>
          <input type="text" name="apellido" required>

          <label>Usuario:</label>
          <input type="text" name="user_name" required>

          <label>Correo electrónico:</label>
          <input type="email" name="email" required>

          <label>Contraseña:</label>
          <input type="password" name="password" required>

          <label>Teléfono:</label>
          <input type="text" name="phone" required>

          <label>Dirección:</label>
          <textarea name="address" rows="2" required></textarea>

          <button type="submit" class="btn-primary">Registrar</button>
          <a href="login.php" class="volver-link">Ya tengo una cuenta</a>
        </div>

        <div class="profile-preview">
          <h3>Vista previa</h3>
          <div class="profile-pic">
            <img id="previewImg" src="recursos/icons/perfil.png" alt="Vista previa">
          </div>

          <label for="img" class="upload-label">Subir imagen</label>
          <input type="file" name="img" id="img" accept="image/*">
        </div>
      </form>
    </div>
  </div>

  <script>
    const imgInput = document.getElementById('img');
    const preview = document.getElementById('previewImg');

    imgInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = ev => preview.src = ev.target.result;
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
