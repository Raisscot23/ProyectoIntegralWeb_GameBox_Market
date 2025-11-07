<?php
include 'conn.php';
session_start();

// Si el usuario no está logueado, lo enviamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $cantidad = intval($_POST['cantidad']);

    // 1️⃣ Buscar si el usuario ya tiene un carrito
    $sql = "SELECT carrito_id FROM carrito WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $carrito = $result->fetch_assoc();
        $carrito_id = $carrito['carrito_id'];
    } else {
        // Si no existe carrito, lo creamos
        $sql_insert = "INSERT INTO carrito (user_id) VALUES (?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("i", $user_id);
        $stmt_insert->execute();
        $carrito_id = $stmt_insert->insert_id;
    }

    // 2️⃣ Verificar si el producto ya está en el carrito
    $sql_check = "SELECT * FROM carrito_items WHERE carrito_id = ? AND product_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $carrito_id, $product_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Si ya existe, actualizamos la cantidad y el precio total
        $item = $result_check->fetch_assoc();
        $nueva_cantidad = $item['cantidad'] + $cantidad;

        $sql_update = "UPDATE carrito_items 
                       SET cantidad = ?, full_price = (SELECT price FROM producto WHERE product_id = ?) * ? 
                       WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iiii", $nueva_cantidad, $product_id, $nueva_cantidad, $item['id']);
        $stmt_update->execute();
    } else {
        // Si no está en el carrito, lo insertamos
        $sql_insert_item = "INSERT INTO carrito_items (carrito_id, product_id, cantidad, full_price)
                            VALUES (?, ?, ?, (SELECT price FROM producto WHERE product_id = ?) * ?)";
        $stmt_insert_item = $conn->prepare($sql_insert_item);
        $stmt_insert_item->bind_param("iiiii", $carrito_id, $product_id, $cantidad, $product_id, $cantidad);
        $stmt_insert_item->execute();
    }

    // 3️⃣ Redirigir al carrito visual
    header("Location: carrito.php");
    exit();
}
?>
