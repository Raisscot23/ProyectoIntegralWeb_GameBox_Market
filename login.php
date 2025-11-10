<?php
include 'conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_input = trim($_POST['user_input']);
    $password = trim($_POST['password']); 

    $sql = "SELECT * FROM usuario WHERE user_name = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // --- Verificar contraseña encriptada ---
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['img'] = !empty($user['img']) ? base64_encode($user['img']) : null;

            echo "<script>window.location.href='index.php';</script>";
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta.');</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado.');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - GameBoxMarket</title>
  <link rel="stylesheet" href="css/login-register.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <h1>Iniciar Sesión</h1>
      <form method="POST" action="" class="auth-form">
        <label for="user_input">Usuario o Correo:</label>
        <input type="text" id="user_input" name="user_input" placeholder="Ingresa tu usuario o correo" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

        <button type="submit" class="btn-primary">Ingresar</button>
      </form>

      <p class="auth-text">¿No tienes una cuenta? <a href="register.php" class="link">Regístrate aquí</a></p>
      <a href="index.php" class="volver-link">Volver al inicio</a>
    </div>
  </div>
</body>
</html>
