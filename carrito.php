<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 🔹 Obtener carrito del usuario
$sql = "SELECT carrito_id FROM carrito WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h2>No tienes ningún producto en tu carrito.</h2>";
    echo "<a href='catalogo.php'>Volver al catálogo</a>";
    exit();
}

$carrito = $result->fetch_assoc();
$carrito_id = $carrito['carrito_id'];

// 🔹 Aumentar / Disminuir / Eliminar producto
if (isset($_POST['accion']) && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    if ($_POST['accion'] === 'aumentar') {
        $sql = "UPDATE carrito_items SET cantidad = cantidad + 1 WHERE id = ?";
    } elseif ($_POST['accion'] === 'disminuir') {
        $sql = "UPDATE carrito_items SET cantidad = GREATEST(cantidad - 1, 1) WHERE id = ?";
    } elseif ($_POST['accion'] === 'eliminar') {
        $sql = "DELETE FROM carrito_items WHERE id = ?";
    } else {
        $sql = null;
    }

    if ($sql) {
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $item_id);
            $stmt->execute();
        } else {
            die("Error en prepare(): " . $conn->error);
        }
    }
    header("Location: carrito.php");
    exit();
}

// 🔹 Comprar (simulado)
if (isset($_POST['comprar'])) {
    $delete_items = "DELETE FROM carrito_items WHERE carrito_id = ?";
    $stmt_del = $conn->prepare($delete_items);
    $stmt_del->bind_param("i", $carrito_id);
    $stmt_del->execute();

    echo "<script>alert('✅ Compra realizada con éxito. ¡Gracias por tu compra!'); window.location.href='catalogo.php';</script>";
    exit();
}

// 🔹 Obtener productos del carrito
$sql_items = "SELECT ci.id AS item_id, ci.*, p.nombre, p.price, p.img 
              FROM carrito_items ci
              INNER JOIN producto p ON ci.product_id = p.product_id
              WHERE ci.carrito_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $carrito_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$userImage = !empty($_SESSION['img'])
    ? 'data:image/jpeg;base64,' . $_SESSION['img']
    : 'recursos/img/placeholder.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de compras</title>
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/carrito.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #333;
            color: white;
        }

        tr:hover {
            background: #f2f2f2;
        }

        .acciones form {
            display: inline;
        }

        .acciones button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .acciones button:hover {
            background-color: #0056b3;
        }

        .btn-eliminar {
            background-color: #e74c3c;
        }

        .btn-eliminar:hover {
            background-color: #c0392b;
        }

        .btn-comprar {
            background-color: #27ae60;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 15px;
        }

        .btn-comprar:hover {
            background-color: #1e8449;
        }

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

        h1 {
            text-align: center;
        }

        .volver {
            display: inline-block;
            background-color: #555;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 20px;
        }

        .volver:hover {
            background-color: #333;
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
                    <a href="login.php" class="logout-btn" style="background-color:#007bff;">Iniciar sesión</a>
                <?php endif; ?>
            </li>
        </ul>
    </section>
</header>

<h1>🛒 Tu Carrito de Compras</h1>

<?php if ($result_items->num_rows === 0): ?>
    <p>Tu carrito está vacío.</p>
<?php else: ?>
    <form method="POST">
        <table>
            <tr>
                <th>Producto</th>
                <th>Imagen</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
            <?php
            $total = 0;
            while ($item = $result_items->fetch_assoc()):
                $subtotal = $item['price'] * $item['cantidad'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['nombre']) ?></td>
                <td><img src="data:image/jpeg;base64,<?= base64_encode($item['img']) ?>" width="60"></td>
                <td><?= $item['cantidad'] ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td class="acciones">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                        <button type="submit" name="accion" value="aumentar">➕</button>
                        <button type="submit" name="accion" value="disminuir">➖</button>
                        <button type="submit" name="accion" value="eliminar" class="btn-eliminar">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="4"><strong>Total:</strong></td>
                <td colspan="2"><strong>$<?= number_format($total, 2) ?></strong></td>
            </tr>
        </table>

        <div style="text-align:center;">
            <button type="submit" name="comprar" class="btn-comprar">💳 Comprar (simulado)</button>
        </div>
    </form>
<?php endif; ?>

<a href="catalogo.php" class="volver">⬅ Volver al catálogo</a>

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
</footer>

</body>
</html>
