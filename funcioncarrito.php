<?php
require_once 'conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
$user_logged_in = isset($_SESSION['user_id']);
$id_usuario = $user_logged_in ? $_SESSION['user_id'] : null;

function obtenerItemsCarrito($conexion, $id_usuario) {
    $query = "SELECT c.id_carrito, c.id_producto, c.tipo_producto, c.cantidad, 
              CASE 
                WHEN c.tipo_producto = 'sencillo' THEN s.nombre_sencillo
                WHEN c.tipo_producto = 'album' THEN a.nombre_album
                WHEN c.tipo_producto = 'cancion' THEN ca.nombre_cancion
              END AS nombre_producto,
              CASE 
                WHEN c.tipo_producto = 'sencillo' THEN s.precio
                WHEN c.tipo_producto = 'album' THEN a.precio
                WHEN c.tipo_producto = 'cancion' THEN ca.precio
              END AS precio,
              CASE 
                WHEN c.tipo_producto = 'sencillo' THEN s.imagen_sencillo_path
                WHEN c.tipo_producto = 'album' THEN a.imagen_album_path
                WHEN c.tipo_producto = 'cancion' THEN al.imagen_album_path
              END AS imagen_producto,
              CASE 
                WHEN c.tipo_producto = 'sencillo' THEN s.cancion_path
                WHEN c.tipo_producto = 'album' THEN NULL  -- Cambiado a NULL
                WHEN c.tipo_producto = 'cancion' THEN ca.cancion_path
              END AS ruta_descarga
              FROM carrito c
              LEFT JOIN sencillos s ON c.id_producto = s.id_sencillo AND c.tipo_producto = 'sencillo'
              LEFT JOIN album a ON c.id_producto = a.id_album AND c.tipo_producto = 'album'
              LEFT JOIN canciones ca ON c.id_producto = ca.id_cancion AND c.tipo_producto = 'cancion'
              LEFT JOIN album al ON ca.id_album = al.id_album
              WHERE c.id_usuario = ?";
    
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
// Obtener items del carrito solo si el usuario está autenticado
$items_carrito = $user_logged_in ? obtenerItemsCarrito($conexion, $id_usuario) : [];

// Manejo de la eliminación de ítems
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_carrito'])) {
    $id_carrito = intval($_POST['id_carrito']);
    $query = "DELETE FROM carrito WHERE id_carrito = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_carrito);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el item.']);
    }
    exit; // Termina el script después de manejar la eliminación
}
// Función para manejar la compra
function manejarCompra($conexion, $items_carrito) {
    foreach ($items_carrito as $item) {
        $id_producto = $item['id_producto'];
        $tipo_producto = $item['tipo_producto'];

        if ($tipo_producto === 'album') {
            // Obtener todas las canciones del álbum
            $query = "SELECT ca.cancion_path, a.imagen_album_path 
                      FROM album a 
                      JOIN canciones ca ON a.id_album = ca.id_album 
                      WHERE a.id_album = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $canciones = [];
            while ($row = $result->fetch_assoc()) {
                $canciones[] = $row; // Almacena las canciones y la imagen del álbum
            }

            // Lógica para descargar las canciones y la imagen del álbum
            foreach ($canciones as $cancion) {
                // Descargar cada canción
                copy($cancion['cancion_path'], 'ruta/destino/' . basename($cancion['cancion_path']));
            }

            // Descargar la imagen del álbum
            if (!empty($canciones)) {
                copy($canciones[0]['imagen_album_path'], 'ruta/destino/' . basename($canciones[0]['imagen_album_path']));
            }

        } elseif ($tipo_producto === 'cancion') {
            // Obtener la canción y la imagen del álbum
            $query = "SELECT ca.cancion_path, al.imagen_album_path 
                      FROM canciones ca 
                      LEFT JOIN album al ON ca.id_album = al.id_album 
                      WHERE ca.id_cancion = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // Descargar la canción
                copy($row['cancion_path'], 'ruta/destino/' . basename($row['cancion_path']));
                
                // Descargar la imagen del álbum
                copy($row['imagen_album_path'], 'ruta/destino/' . basename($row['imagen_album_path']));
            }
        }
    }
}


?>