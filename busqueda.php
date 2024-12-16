<?php
require_once 'conexion.php';

function buscar($termino) {
    global $conexion;
    $termino = "%$termino%";
    
    $sql = "SELECT 'artista' as tipo, a.id_artista as id, u.nombre_usuario as nombre, '' as artista
            FROM artista a
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE u.nombre_usuario LIKE ?
            UNION
            SELECT 'album' as tipo, al.id_album as id, al.nombre_album as nombre, u.nombre_usuario as artista
            FROM album al
            JOIN artista a ON al.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE al.nombre_album LIKE ?
            UNION
            SELECT 'cancion' as tipo, c.id_cancion as id, c.nombre_cancion as nombre, u.nombre_usuario as artista
            FROM canciones c
            JOIN artista a ON c.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE c.nombre_cancion LIKE ?
            UNION
            SELECT 'sencillo' as tipo, s.id_sencillo as id, s.nombre_sencillo as nombre, u.nombre_usuario as artista
            FROM sencillos s
            JOIN artista a ON s.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE s.nombre_sencillo LIKE ?
            LIMIT 20";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $termino, $termino, $termino, $termino);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $resultados = [];
    while ($row = $result->fetch_assoc()) {
        $resultados[] = $row;
    }
    
    return $resultados;
}

// Si se recibe una solicitud POST, realizar la búsqueda
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['termino'])) {
    $resultados = buscar($_POST['termino']);
    echo json_encode($resultados);
    exit;
}
?>
