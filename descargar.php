<?php
require_once 'conexion.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$id_usuario = $_SESSION['user_id'];

// Función para verificar si el usuario ha comprado el producto
function usuarioHaComprado($conexion, $id_usuario, $id_producto, $tipo_producto) {
    $query = "SELECT COUNT(*) as count FROM carrito WHERE id_usuario = ? AND id_producto = ? AND tipo_producto = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("iis", $id_usuario, $id_producto, $tipo_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Verificar el tipo de solicitud
if (isset($_GET['tipo']) && $_GET['tipo'] === 'album' && isset($_GET['id'])) {
    $id_album = intval($_GET['id']);
    
    // Verificar si el usuario ha comprado el álbum
    if (!usuarioHaComprado($conexion, $id_usuario, $id_album, 'album')) {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }

    // Obtener las canciones del álbum
    $query = "SELECT c.id_cancion, c.nombre_cancion, c.cancion_path, a.imagen_album_path 
              FROM canciones c 
              JOIN album a ON c.id_album = a.id_album 
              WHERE a.id_album = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $result = $stmt->get_result();

    $canciones = [];
    $imagen_album = '';
    while ($row = $result->fetch_assoc()) {
        $canciones[] = [
            'nombre' => $row['nombre_cancion'],
            'ruta' => $row['cancion_path']
        ];
        if (empty($imagen_album)) {
            $imagen_album = $row['imagen_album_path'];
        }
    }

    echo json_encode([
        'canciones' => $canciones,
        'imagen' => $imagen_album
    ]);
} elseif (isset($_GET['file'])) {
    $file = $_GET['file'];
    
    // Verificar si el archivo pertenece a un producto que el usuario ha comprado
    // Esto requeriría una consulta más compleja a la base de datos
    
    $base_dir = 'uploads/'; // Ajusta esto a la ruta correcta en tu servidor
    $file_path = $base_dir . $file;
    
    if (file_exists($file_path) && strpos(realpath($file_path), realpath($base_dir)) === 0) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    } else {
        http_response_code(404);
        echo "Archivo no encontrado.";
    }
} else {
    http_response_code(400);
    echo "Solicitud inválida.";
}
?>

