<?php
require_once 'conexion.php';

function getRecommendations() {
    global $conexion;
    
    $sql = "SELECT 'cancion' as tipo, c.id_cancion as id, c.nombre_cancion as nombre, c.cancion_path, al.imagen_album_path as imagen, u.nombre_usuario as usuario
            FROM canciones c
            JOIN album al ON c.id_album = al.id_album
            JOIN artista a ON c.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            UNION
            SELECT 'sencillo' as tipo, s.id_sencillo as id, s.nombre_sencillo as nombre, s.cancion_path, s.imagen_sencillo_path as imagen, u.nombre_usuario as usuario
            FROM sencillos s
            JOIN artista a ON s.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            ORDER BY RAND()
            LIMIT 10";
    
    $result = $conexion->query($sql);
    
    $recommendations = [];
    while ($row = $result->fetch_assoc()) {
        $recommendations[] = $row;
    }
    
    return $recommendations;
}

$recommendations = getRecommendations();
echo json_encode($recommendations);
?>