<?php
require_once 'conexion.php';

if (isset($_GET['id'])) {
    $song_id = $_GET['id'];
    
    $stmt = $conexion->prepare("SELECT cancion, nombre_cancion FROM canciones WHERE id_cancion = ?");
    $stmt->bind_param("i", $song_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();

    if ($song) {
        header("Content-Type: audio/mpeg");
        header("Content-Disposition: inline; filename=\"" . $song['nombre_cancion'] . ".mp3\"");
        echo $song['cancion'];
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Song not found";
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    echo "Invalid request";
}

