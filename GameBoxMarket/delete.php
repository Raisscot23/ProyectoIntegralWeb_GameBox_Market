<?php
include 'conn.php';
$uploadDir = "uploads/";

if (!isset($_GET['id'])) die("ID no especificado");

$id = $_GET['id'];

// Obtener imagen actual
$stmt = $conn->prepare("SELECT img FROM producto WHERE product_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) die("Producto no encontrado");

// Borrar registro
$stmt = $conn->prepare("DELETE FROM producto WHERE product_id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Borrar archivo de imagen si existe
    if (!empty($product['img']) && file_exists($uploadDir . $product['img'])) {
        unlink($uploadDir . $product['img']);
    }
    echo "<p>Producto eliminado con éxito.</p>";
} else {
    echo "<p>Error al eliminar: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>

<br>
<a href="catalogo.php">Volver al catálogo</a>
