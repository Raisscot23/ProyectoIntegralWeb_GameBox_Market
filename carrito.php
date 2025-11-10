<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Obtener carrito del usuario
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

// Manejo de acciones
if (isset($_POST['accion']) && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);
    if ($_POST['accion'] === 'aumentar') {
        $sql = "UPDATE carrito_items SET cantidad = cantidad + 1 WHERE id = ?";
    } elseif ($_POST['accion'] === 'disminuir') {
        $sql = "UPDATE carrito_items SET cantidad = GREATEST(cantidad - 1, 1) WHERE id = ?";
    } elseif ($_POST['accion'] === 'eliminar') {
        $sql = "DELETE FROM carrito_items WHERE id = ?";
    }
    if ($sql) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
    }
    header("Location: carrito.php");
    exit();
}

// Comprar
if (isset($_POST['comprar'])) {
    $delete_items = "DELETE FROM carrito_items WHERE carrito_id = ?";
    $stmt_del = $conn->prepare($delete_items);
    $stmt_del->bind_param("i", $carrito_id);
    $stmt_del->execute();
    echo "<script>alert('Compra realizada con éxito.'); window.location.href='carrito.php';</script>";
    exit();
}

// Vaciar carrito
if (isset($_POST['vaciar'])) {
    $vaciar_sql = "DELETE FROM carrito_items WHERE carrito_id = ?";
    $stmt_vaciar = $conn->prepare($vaciar_sql);
    $stmt_vaciar->bind_param("i", $carrito_id);
    $stmt_vaciar->execute();
    echo "<script>alert('Tu carrito ha sido vaciado.'); window.location.href='carrito.php';</script>";
    exit();
}

// Obtener productos del carrito
$sql_items = "SELECT ci.id AS item_id, ci.*, p.nombre, p.price, p.img 
              FROM carrito_items ci
              INNER JOIN producto p ON ci.product_id = p.product_id
              WHERE ci.carrito_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $carrito_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// Info de usuario
$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';
$userImage = !empty($_SESSION['img']) ? 'data:image/jpeg;base64,' . $_SESSION['img'] : 'recursos/img/NoImage.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de compras</title>
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/carrito.css">
    <link rel="shortcut icon" href="recursos/icons/IconoClaro.ico" type="image/x-icon">
</head>
<body>

<header>
    <a href="index.php">
        <img id="logo_head" src="recursos/img/IconoClaro.png" alt="Logo GameBox">
    </a>
    <section id="menu_head">
        <ul>
            <?php if ($rol == 1): ?><li><a href="create.php">CRUD</a></li><?php endif; ?>
            <li><a href="catalogo.php">Catálogo</a></li>
            <li><a href="<?= $rol ? 'carrito.php' : 'login.php' ?>">Carrito de compras</a></li>
            <li class="user-info">
                <?php if ($rol): ?>
                    <a href="userProfile.php"><img id="userIcon" src="<?= htmlspecialchars($userImage) ?>" alt="Perfil"></a>
                    <span class="user-name"><?= htmlspecialchars($nombre) ?></span>
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

<div class="cart-container">
    <h2>Tu Carrito de Compras</h2>

    <?php if ($result_items->num_rows === 0): ?>
        <p style="text-align:center; margin:20px;">Tu carrito está vacío.</p>
        <a href="catalogo.php" class="VerProductos" >Volver al catálogo</a>
    <?php else: ?>
        <form method="POST" onsubmit="return confirmarCompra(event)">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    while ($item = $result_items->fetch_assoc()):
                        $subtotal = $item['price'] * $item['cantidad'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td><img src="data:image/jpeg;base64,<?= base64_encode($item['img']) ?>" class="cart-item-img" alt="Producto"></td>
                        <td><?= $item['cantidad'] ?></td>
                        <td>$<?= number_format($item['price'],2) ?></td>
                        <td>$<?= number_format($subtotal,2) ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                                <button type="submit" name="accion" value="aumentar" class="action-btn"><img src="recursos/icons/mas.png" alt="Mas"></button>
                                <button type="submit" name="accion" value="disminuir" class="action-btn"><img src="recursos/icons/menos.png" alt="Menos"></button>
                                <button type="submit" name="accion" value="eliminar" class="action-btn delete"><img src="recursos/icons/borrar.png" alt="Borrar"></button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total:</strong></td>
                        <td colspan="2"><strong>$<?= number_format($total,2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <button type="submit" name="comprar" class="btn-primary">Comprar</button>
                <button type="submit" name="vaciar" class="btn-danger" onclick="return confirmarVaciado()">Vaciar carrito</button>
            </div>
        </form>
        <a href="catalogo.php" class="VerProductos">Volver al catálogo</a>
    <?php endif; ?>
</div>

<script>
function confirmarVaciado() {
    return confirm("¿Estás seguro de que quieres vaciar todo el carrito?");
}
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
