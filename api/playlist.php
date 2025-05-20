<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
require_once '../config/db_connect.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
    exit;
}

$userId = $_SESSION['user_id'];

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si hay una acción especificada
    if (!isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'No se especificó ninguna acción']);
        exit;
    }

    // Obtener la acción
    $action = $_POST['action'];

    // Procesar la acción
    switch ($action) {
        case 'create_playlist':
            createPlaylist();
            break;
        case 'update_playlist':
            updatePlaylist();
            break;
        case 'delete_playlist':
            deletePlaylist();
            break;
        case 'add_song':
            addSongToPlaylist();
            break;
        case 'remove_song':
            removeSongFromPlaylist();
            break;
        case 'reorder_songs':
            reorderSongs();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} else {
    // Si es una solicitud GET, verificar la acción
    if (!isset($_GET['action'])) {
        echo json_encode(['success' => false, 'message' => 'No se especificó ninguna acción']);
        exit;
    }

    $action = $_GET['action'];

    switch ($action) {
        case 'get_playlists':
            getPlaylists();
            break;
        case 'get_playlist':
            getPlaylist();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
}

/**
 * Crea una nueva playlist
 */
function createPlaylist() {
    global $conn, $userId;
    
    // Verificar si se proporcionaron los datos necesarios
    if (!isset($_POST['name'])) {
        echo json_encode(['success' => false, 'message' => 'Falta el nombre de la playlist']);
        exit;
    }
    
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $isPublic = isset($_POST['public']) ? (int)$_POST['public'] : 0;
    
    // Validar nombre
    if (strlen($name) < 3 || strlen($name) > 100) {
        echo json_encode(['success' => false, 'message' => 'El nombre debe tener entre 3 y 100 caracteres']);
        exit;
    }
    
    try {
        // Crear la playlist
        $stmt = $conn->prepare("
            INSERT INTO playlists (id_usuario, nombre_playlist, descripcion, es_publica)
            VALUES (:user_id, :name, :description, :is_public)
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':is_public', $isPublic, PDO::PARAM_INT);
        $stmt->execute();
        
        $playlistId = $conn->lastInsertId();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Playlist creada correctamente',
            'playlist_id' => $playlistId
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al crear la playlist: ' . $e->getMessage()]);
    }
}

/**
 * Actualiza una playlist existente
 */
function updatePlaylist() {
    global $conn, $userId;
    
    // Verificar si se proporcionaron los datos necesarios
    if (!isset($_POST['playlist_id']) || !isset($_POST['name'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios']);
        exit;
    }
    
    $playlistId = $_POST['playlist_id'];
    $name = trim($_POST['name']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $isPublic = isset($_POST['public']) ? (int)$_POST['public'] : 0;
    
    // Validar nombre
    if (strlen($name) < 3 || strlen($name) > 100) {
        echo json_encode(['success' => false, 'message' => 'El nombre debe tener entre 3 y 100 caracteres']);
        exit;
    }
    
    try {
        //  => 'El nombre debe tener entre 3 y 100 caracteres']);
        exit;
    }
    
    try {
        // Verificar que la playlist pertenece al usuario
        $stmt = $conn->prepare("
            SELECT id_playlist FROM playlists 
            WHERE id_playlist = :playlist_id AND id_usuario = :user_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar esta playlist']);
            exit;
        }
        
        // Actualizar la playlist
        $stmt = $conn->prepare("
            UPDATE playlists 
            SET nombre_playlist = :name, descripcion = :description, es_publica = :is_public, fecha_actualizacion = NOW()
            WHERE id_playlist = :playlist_id
        ");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':is_public', $isPublic, PDO::PARAM_INT);
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Playlist actualizada correctamente']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la playlist: ' . $e->getMessage()]);
    }
}

/**
 * Elimina una playlist
 */
function deletePlaylist() {
    global $conn, $userId;
    
    // Verificar si se proporcionó un ID de playlist
    if (!isset($_POST['playlist_id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de playlist']);
        exit;
    }
    
    $playlistId = $_POST['playlist_id'];
    
    try {
        // Verificar que la playlist pertenece al usuario
        $stmt = $conn->prepare("
            SELECT id_playlist FROM playlists 
            WHERE id_playlist = :playlist_id AND id_usuario = :user_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta playlist']);
            exit;
        }
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        // Eliminar canciones de la playlist
        $stmt = $conn->prepare("DELETE FROM playlist_canciones WHERE id_playlist = :playlist_id");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Eliminar la playlist
        $stmt = $conn->prepare("DELETE FROM playlists WHERE id_playlist = :playlist_id");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Playlist eliminada correctamente']);
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la playlist: ' . $e->getMessage()]);
    }
}

/**
 * Añade una canción a una playlist
 */
function addSongToPlaylist() {
    global $conn, $userId;
    
    // Verificar si se proporcionaron los datos necesarios
    if (!isset($_POST['playlist_id']) || (!isset($_POST['song_id']) && !isset($_POST['sencillo_id']))) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios']);
        exit;
    }
    
    $playlistId = $_POST['playlist_id'];
    $songId = isset($_POST['song_id']) ? $_POST['song_id'] : null;
    $sencilloId = isset($_POST['sencillo_id']) ? $_POST['sencillo_id'] : null;
    
    // Determinar tipo de canción
    $songType = $songId ? 'album_cancion' : 'sencillo';
    $songIdToAdd = $songId ?: $sencilloId;
    
    try {
        // Verificar que la playlist pertenece al usuario
        $stmt = $conn->prepare("
            SELECT id_playlist FROM playlists 
            WHERE id_playlist = :playlist_id AND id_usuario = :user_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para modificar esta playlist']);
            exit;
        }
        
        // Verificar si la canción ya está en la playlist
        $stmt = $conn->prepare("
            SELECT id_playlist_cancion FROM playlist_canciones 
            WHERE id_playlist = :playlist_id AND tipo_cancion = :song_type AND id_cancion = :song_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':song_type', $songType, PDO::PARAM_STR);
        $stmt->bindParam(':song_id', $songIdToAdd, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'La canción ya está en la playlist']);
            exit;
        }
        
        // Obtener el orden máximo actual
        $stmt = $conn->prepare("
            SELECT MAX(orden) as max_orden FROM playlist_canciones 
            WHERE id_playlist = :playlist_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        $newOrder = $result['max_orden'] ? $result['max_orden'] + 1 : 1;
        
        // Añadir la canción a la playlist
        $stmt = $conn->prepare("
            INSERT INTO playlist_canciones (id_playlist, tipo_cancion, id_cancion, orden)
            VALUES (:playlist_id, :song_type, :song_id, :orden)
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':song_type', $songType, PDO::PARAM_STR);
        $stmt->bindParam(':song_id', $songIdToAdd, PDO::PARAM_INT);
        $stmt->bindParam(':orden', $newOrder, PDO::PARAM_INT);
        $stmt->execute();
        
        // Actualizar fecha de actualización de la playlist
        $stmt = $conn->prepare("
            UPDATE playlists 
            SET fecha_actualizacion = NOW()
            WHERE id_playlist = :playlist_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Canción añadida a la playlist']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al añadir la canción: ' . $e->getMessage()]);
    }
}

/**
 * Elimina una canción de una playlist
 */
function removeSongFromPlaylist() {
    global $conn, $userId;
    
    // Verificar si se proporcionó un ID de playlist_cancion
    if (!isset($_POST['playlist_song_id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de canción en playlist']);
        exit;
    }
    
    $playlistSongId = $_POST['playlist_song_id'];
    
    try {
        // Verificar que la canción está en una playlist del usuario
        $stmt = $conn->prepare("
            SELECT pc.id_playlist 
            FROM playlist_canciones pc
            JOIN playlists p ON pc.id_playlist = p.id_playlist
            WHERE pc.id_playlist_cancion = :playlist_song_id AND p.id_usuario = :user_id
        ");
        $stmt->bindParam(':playlist_song_id', $playlistSongId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta canción']);
            exit;
        }
        
        $result = $stmt->fetch();
        $playlistId = $result['id_playlist'];
        
        // Eliminar la canción de la playlist
        $stmt = $conn->prepare("DELETE FROM playlist_canciones WHERE id_playlist_cancion = :playlist_song_id");
        $stmt->bindParam(':playlist_song_id', $playlistSongId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Actualizar fecha de actualización de la playlist
        $stmt = $conn->prepare("
            UPDATE playlists 
            SET fecha_actualizacion = NOW()
            WHERE id_playlist = :playlist_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Canción eliminada de la playlist']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la canción: ' . $e->getMessage()]);
    }
}

/**
 * Reordena las canciones de una playlist
 */
function reorderSongs() {
    global $conn, $userId;
    
    // Verificar si se proporcionaron los datos necesarios
    if (!isset($_POST['playlist_id']) || !isset($_POST['song_order'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios']);
        exit;
    }
    
    $playlistId = $_POST['playlist_id'];
    $songOrder = json_decode($_POST['song_order'], true);
    
    if (!is_array($songOrder)) {
        echo json_encode(['success' => false, 'message' => 'Formato de orden inválido']);
        exit;
    }
    
    try {
        // Verificar que la playlist pertenece al usuario
        $stmt = $conn->prepare("
            SELECT id_playlist FROM playlists 
            WHERE id_playlist = :playlist_id AND id_usuario = :user_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para modificar esta playlist']);
            exit;
        }
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        // Actualizar el orden de cada canción
        foreach ($songOrder as $index => $songId) {
            $stmt = $conn->prepare("
                UPDATE playlist_canciones 
                SET orden = :orden
                WHERE id_playlist_cancion = :song_id
            ");
            $orden = $index + 1;
            $stmt->bindParam(':orden', $orden, PDO::PARAM_INT);
            $stmt->bindParam(':song_id', $songId, PDO::PARAM_INT);
            $stmt->execute();
        }
        
        // Actualizar fecha de actualización de la playlist
        $stmt = $conn->prepare("
            UPDATE playlists 
            SET fecha_actualizacion = NOW()
            WHERE id_playlist = :playlist_id
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Orden actualizado correctamente']);
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el orden: ' . $e->getMessage()]);
    }
}

/**
 * Obtiene todas las playlists del usuario
 */
function getPlaylists() {
    global $conn, $userId;
    
    try {
        // Obtener playlists del usuario
        $stmt = $conn->prepare("
            SELECT id_playlist, nombre_playlist, descripcion, imagen_playlist, 
                   fecha_creacion, es_publica
            FROM playlists
            WHERE id_usuario = :user_id
            ORDER BY fecha_creacion DESC
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $playlists = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'playlists' => $playlists]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener playlists: ' . $e->getMessage()]);
    }
}

/**
 * Obtiene una playlist específica con sus canciones
 */
function getPlaylist() {
    global $conn, $userId;
    
    // Verificar si se proporcionó un ID de playlist
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de playlist']);
        exit;
    }
    
    $playlistId = $_GET['id'];
    
    try {
        // Obtener información de la playlist
        $stmt = $conn->prepare("
            SELECT p.id_playlist, p.nombre_playlist, p.descripcion, p.imagen_playlist, 
                   p.fecha_creacion, p.fecha_actualizacion, p.es_publica, 
                   u.nombre_usuario as creador
            FROM playlists p
            JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE p.id_playlist = :playlist_id AND (p.id_usuario = :user_id OR p.es_publica = 1)
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $playlist = $stmt->fetch();
        
        if (!$playlist) {
            echo json_encode(['success' => false, 'message' => 'Playlist no encontrada o sin acceso']);
            exit;
        }
        
        // Obtener canciones de la playlist
        $stmt = $conn->prepare("
            SELECT pc.id_playlist_cancion, pc.tipo_cancion, pc.id_cancion, pc.orden,
                   CASE 
                       WHEN pc.tipo_cancion = 'album_cancion' THEN c.nombre_cancion
                       WHEN pc.tipo_cancion = 'sencillo' THEN s.nombre_sencillo
                   END as nombre,
                   CASE 
                       WHEN pc.tipo_cancion = 'album_cancion' THEN a.nombre_album
                       WHEN pc.tipo_cancion = 'sencillo' THEN NULL
                   END as album,
                   CASE 
                       WHEN pc.tipo_cancion = 'album_cancion' THEN u_c.nombre_usuario
                       WHEN pc.tipo_cancion = 'sencillo' THEN u_s.nombre_usuario
                   END as artista,
                   CASE 
                       WHEN pc.tipo_cancion = 'album_cancion' THEN c.cancion_path
                       WHEN pc.tipo_cancion = 'sencillo' THEN s.cancion_path
                   END as archivo_audio,
                   CASE 
                       WHEN pc.tipo_cancion = 'album_cancion' THEN a.imagen_album_path
                       WHEN pc.tipo_cancion = 'sencillo' THEN s.imagen_sencillo_path
                   END as imagen
            FROM playlist_canciones pc
            LEFT JOIN canciones c ON pc.tipo_cancion = 'album_cancion' AND pc.id_cancion = c.id_cancion
            LEFT JOIN album a ON c.id_album = a.id_album
            LEFT JOIN artista art_c ON c.id_artista = art_c.id_artista
            LEFT JOIN usuario u_c ON art_c.usuario = u_c.id_usuario
            LEFT JOIN sencillos s ON pc.tipo_cancion = 'sencillo' AND pc.id_cancion = s.id_sencillo
            LEFT JOIN artista art_s ON s.id_artista = art_s.id_artista
            LEFT JOIN usuario u_s ON art_s.usuario = u_s.id_usuario
            WHERE pc.id_playlist = :playlist_id
            ORDER BY pc.orden
        ");
        $stmt->bindParam(':playlist_id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();
        
        $songs = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true, 
            'playlist' => $playlist,
            'songs' => $songs
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener la playlist: ' . $e->getMessage()]);
    }
}
?>
