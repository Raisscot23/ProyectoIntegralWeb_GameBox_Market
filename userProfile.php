<?php
include 'conn.php';
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

    // Si se subió una nueva imagen
    if (!empty($_FILES['img']['tmp_name'])) {
        $imgData = file_get_contents($_FILES['img']['tmp_name']);
        $sql_update = "UPDATE usuario SET nombre=?, apellido=?, user_name=?, email=?, phone=?, address=?, password=?, img=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssssi", $nombre, $apellido, $user_name, $email, $phone, $address, $password, $imgData, $user_id);
    } else {
        $sql_update = "UPDATE usuario SET nombre=?, apellido=?, user_name=?, email=?, phone=?, address=?, password=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssi", $nombre, $apellido, $user_name, $email, $phone, $address, $password, $user_id);
    }

    if ($stmt_update->execute()) {
        echo "<script>alert('Perfil actualizado correctamente.'); window.location.href='userProfile.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error al actualizar perfil.');</script>";
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

    <style>
        #userIcon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .logout-btn {
            background-color: #ff4040;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 15px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-name {
            font-weight: bold;
            color: white;
        }
    </style>
</head>
<body>

<header>
        <a href="index.php">
            <img id="logo_head" src="recursos/img/palceholder 2.svg" alt="Logo GameBox">
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
                        <a href="login.php"">Carrito de compras</a>
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

<div class="profile-container">
    <h1>Mi Perfil</h1>

    <div class="profile-pic">
        <img src="<?php echo htmlspecialchars($userImage); ?>" alt="Foto de perfil">
    </div>

    <form method="POST" enctype="multipart/form-data">
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
        <input type="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>

        <label>Foto de perfil:</label>
        <input type="file" name="img" accept="image/*">

        <button type="submit">Guardar cambios</button>
    </form>

    <a href="index.php" class="volver">⬅ Volver al inicio</a>
</div>

</body>
</html>
