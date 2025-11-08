<?php
include 'conn.php';

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $user_name = trim($_POST['user_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Rol por defecto: Cliente
    $rol = 2;

    // Validar que todos los campos estén llenos
    if (!empty($nombre) && !empty($apellido) && !empty($user_name) && !empty($email) && !empty($password) && !empty($phone) && !empty($address)) {
        
        // Imagen (opcional)
        $imgData = null;
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $imgData = file_get_contents($_FILES['img']['tmp_name']);
        }

        // Insertar usuario
        $sql = "INSERT INTO usuario (nombre, apellido, user_name, email, password, phone, address, img, rol)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssssssi", $nombre, $apellido, $user_name, $email, $password, $phone, $address, $imgData, $rol);

            if ($stmt->execute()) {
                // Obtener el ID del usuario recién insertado
                $nuevoUsuarioId = $stmt->insert_id;

                // Iniciar sesión automáticamente
                session_start();
                $_SESSION['user_id'] = $nuevoUsuarioId;
                $_SESSION['nombre'] = $nombre;
                $_SESSION['apellido'] = $apellido;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['email'] = $email;
                $_SESSION['rol'] = $rol;
                $_SESSION['img'] = $imgData ? base64_encode($imgData) : null;

                // Redirigir al index.php con sesión activa
                echo "<script>
                    alert('Usuario registrado correctamente. Bienvenido, $nombre!');
                    window.location.href = 'index.php';
                    </script>";
                exit();
            } else {
                echo "Error al registrar: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        echo "<script>alert('Por favor, completa todos los campos antes de continuar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
</head>
<body>

<h2>Registrar Usuario</h2>

<form method="POST" action="" enctype="multipart/form-data">
    <input type="text" name="nombre" placeholder="Nombre" required><br>
    <input type="text" name="apellido" placeholder="Apellido" required><br>
    <input type="text" name="user_name" placeholder="Nombre de usuario" required><br>
    <input type="email" name="email" placeholder="Correo electrónico" required><br>
    <input type="password" name="password" placeholder="Contraseña" required><br>
    <input type="text" name="phone" placeholder="Teléfono" required><br>
    <input type="text" name="address" placeholder="Dirección" required><br>

    <label for="img">Imagen de usuario (opcional):</label>
    <input type="file" name="img" accept="image/*"><br><br>

    <button type="submit">Registrar</button>
    <a href="index.php">Volver a la pantalla de inicio</a>
</form>

</body>
</html>
