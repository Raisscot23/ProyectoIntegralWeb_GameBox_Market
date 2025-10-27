<?php
include 'conn.php';

$sql = "SELECT * FROM producto";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<link rel="stylesheet" href="css/headerFooter.css">
<link rel="stylesheet" href="css/catalogo.css">

<title>Catálogo</title>
<style>

table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
img { display: block; max-width: 100px; height: auto; }
a.button { padding: 5px 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
a.button.delete { background-color: #f44336; }
</style>

</head>
<body>

<header>
        <a href="index.html">
            <img id="logo_head" src="recursos/img/palceholder 2.svg" alt="Logo GameBox">
        </a>

        <section id="menu_head">
            <ul>
                <!--Esta parte es temporal para revidar el crud-->
                <li><a href="create.php">CRUD</a></li>
                <li><a href="catalogo.php">Catálogo</a></li>
                <li><a href="carrito.html">Carrito de compas</a></li>
                <li><a href="userProfile.html"><img id="userIcon" src="recursos/img/placeholder.jpg" alt="Perfil del usuario"></a></li>
            </ul>
        </section>
</header>

<h2>Lista de Productos</h2>

<a href="create.php" class="button">Agregar Nuevo Producto</a><br><br>

<table>
<tr>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Precio</th>
    <th>Stock</th>
    <th>Imagen</th>
    <th>Acciones</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['nombre']) ?></td>
    <td><?= htmlspecialchars($row['description']) ?></td>
    <td>$<?= number_format($row['price'], 2) ?></td>
    <td><?= $row['stock'] ?></td>
    <td>
        <?php if (!empty($row['img'])): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($row['img']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>">
        <?php else: ?>
            Sin imagen
        <?php endif; ?>
    </td>
    <td>
        <a href="update.php?id=<?= $row['product_id'] ?>" class="button">Editar</a>
        <a href="delete.php?id=<?= $row['product_id'] ?>" class="button delete" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">Eliminar</a>
    </td>
</tr>
<?php endwhile; ?>
</table>


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

            <h4>Si quieres conocer a los desarrolladores detrás del sitio, has click <a href="aboutUs.html">aquí</a></h4>

            <div id="avisos">
                <ul>
                    <li>Aviso de Cookies</li>
                    <li>Términos de uso</li>
                    <li>Aviso de privacidad</li>
                    <li>Ayuda</li>
                    <li>Política sobre uso de materiales</li>
                    <li>Declaración de afilación</li>
                    <li>Directrices para transmisiones</li>
                    <li>Update notes</li>
                    <li>Licencias de plugins</li>
                </ul>
            </div>
        </footer>

</body>
</html>

<?php $conn->close(); ?>
