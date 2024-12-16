<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$id_usuario = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);

if ($data['action'] === 'empty_cart') {
    $query = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    
    if ($stmt->execute()) {
        // Vaciar también el carrito en la sesión
        $_SESSION['items_carrito'] = [];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al vaciar el carrito']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
?>

