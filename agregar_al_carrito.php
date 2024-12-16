<?php
require_once 'conexion.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para agregar productos al carrito.']);
    exit();
}

$id_usuario = $_SESSION['user_id'];
$id_producto = $_POST['id_producto'];
$tipo_producto = $_POST['tipo_producto'];

// Verificar si el producto ya está en el carrito
$query = "SELECT id_carrito, cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ? AND tipo_producto = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("iis", $id_usuario, $id_producto, $tipo_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si el producto ya está en el carrito, actualiza la cantidad
    $row = $result->fetch_assoc();
    $nueva_cantidad = $row['cantidad'] + 1; // Incrementar la cantidad
    $update_query = "UPDATE carrito SET cantidad = ? WHERE id_carrito = ?";
    $update_stmt = $conexion->prepare($update_query);
    $update_stmt->bind_param("ii", $nueva_cantidad, $row['id_carrito']);
    $update_stmt->execute();
} else {
    // Si el producto no está en el carrito, insertarlo
    $insert_query = "INSERT INTO carrito (id_usuario, id_producto, tipo_producto, cantidad) VALUES (?, ?, ?, 1)";
    $insert_stmt = $conexion->prepare($insert_query);
    $insert_stmt->bind_param("iis", $id_usuario, $id_producto, $tipo_producto);
    $insert_stmt->execute();
}

echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito.']);
?>