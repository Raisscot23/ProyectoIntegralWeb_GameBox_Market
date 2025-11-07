<?php
include 'conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_input = trim($_POST['user_input']); // puede ser nombre de usuario o correo
    $password = trim($_POST['password']); 

    // Buscar usuario por nombre o correo
    $sql = "SELECT * FROM usuario WHERE user_name = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Comparar directamente las contraseñas (texto plano)
        if ($password === $user['password']) {
            // Guardar los datos de sesión
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            // ✅ Guardamos la imagen desde la columna 'img' de tipo LONGBLOB
            if (!empty($user['img'])) {
                $_SESSION['img'] = base64_encode($user['img']);
            } else {
                $_SESSION['img'] = null;
            }

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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="">
            <label>Usuario o Correo:</label>
            <input type="text" name="user_input" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Ingresar</button>
        </form>
        <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        <a href="index.php">Vovle a la pantalla de inicio</a>
    </div>
</body>
</html>
