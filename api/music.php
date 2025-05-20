<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
require_once '../config/db_connect.php';

// Verificar si hay una acción especificada
if (!isset($_GET['action'])) {
    echo json_encode(['success' => false, 'message' => 'No se especificó ninguna acción']);
    exit;
}

// Obtener la acción
$action = $_GET['action'];

// Procesar la acción
switch ($action) {
    case 'get_song':
        getSong();
        break;
    case 'get_album':
        getAlbum();
        break;
    case 'get_sencillo':
        getSencillo();
        break;
    case 'record_play':
        recordPlay();
        break;
    case 'search':
        searchMusic();
        break;
    case 'get_playlists':
        getPlaylists();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

/**
 * Obtiene la información de una canción
 */
function getSong() {
    global $conn;
    
    // Verificar si se proporcionó un ID
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de canción']);
        exit;
    }
    
    $songId = $_GET['id'];
    $songType = isset($_GET['type']) ? $_GET['type'] : 'album_cancion';
    
    try {
        if ($songType === 'album_cancion') {
            // Obtener información de la canción de álbum
            $stmt = $conn->prepare("
                SELECT c.id_cancion as id, c.nombre_cancion as titulo, a.imagen_album_path as portada, 
                       c.cancion_path as archivo_audio, u.nombre_usuario as artista, g.nombre_genero as genero
                FROM canciones c
                JOIN album a ON c.id_album = a.id_album
                JOIN artista art ON c.id_artista = art.id_artista
                JOIN usuario u ON art.usuario = u.id_usuario
                JOIN genero g ON a.id_genero = g.id_genero
                WHERE c.id_cancion = :id
            ");
        } else {
            // Obtener información del sencillo
            $stmt = $conn->prepare("
                SELECT s.id_sencillo as id, s.nombre_sencillo as titulo, s.imagen_sencillo_path as portada, 
                       s.cancion_path as archivo_audio, u.nombre_usuario as artista, g.nombre_genero as genero
                FROM sencillos s
                JOIN artista a ON s.id_artista = a.id_artista
                JOIN usuario u ON a.usuario = u.id_usuario
                JOIN genero g ON s.id_genero = g.id_genero
                WHERE s.id_sencillo = :id
            ");
        }
        
        $stmt->bindParam(':id', $songId, PDO::PARAM_INT);
        $stmt->execute();
        
        $song = $stmt->fetch();
        
        if (!$song) {
            echo json_encode(['success' => false, 'message' => 'Canción no encontrada']);
            exit;
        }
        
        // Asegurarse de que las rutas de archivo sean correctas
        $song['portada'] = file_exists($song['portada']) ? $song['portada'] : 'assets/img/default-cover.png';
        
        echo json_encode(['success' => true, 'song' => $song]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener la canción: ' . $e->getMessage()]);
    }
}

/**
 * Obtiene la información de un álbum
 */
function getAlbum() {
    global $conn;
    
    // Verificar si se proporcionó un ID
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de álbum']);
        exit;
    }
    
    $albumId = $_GET['id'];
    
    try {
        // Obtener información del álbum
        $stmt = $conn->prepare("
            SELECT a.id_album, a.nombre_album, a.descripcion, a.imagen_album_path, 
                   a.fecha_lanzamiento, a.precio, g.nombre_genero, 
                   u.nombre_usuario as artista_nombre
            FROM album a
            JOIN genero g ON a.id_genero = g.id_genero
            JOIN artista art ON a.id_artista = art.id_artista
            JOIN usuario u ON art.usuario = u.id_usuario
            WHERE a.id_album = :id
        ");
        $stmt->bindParam(':id', $albumId, PDO::PARAM_INT);
        $stmt->execute();
        
        $album = $stmt->fetch();
        
        if (!$album) {
            echo json_encode(['success' => false, 'message' => 'Álbum no encontrado']);
            exit;
        }
        
        // Obtener canciones del álbum
        $stmt = $conn->prepare("
            SELECT c.id_cancion, c.nombre_cancion, c.cancion_path
            FROM canciones c
            WHERE c.id_album = :id
            ORDER BY c.id_cancion
        ");
        $stmt->bindParam(':id', $albumId, PDO::PARAM_INT);
        $stmt->execute();
        
        $songs = $stmt->fetchAll();
        
        // Devolver la información del álbum y sus canciones
        echo json_encode([
            'success' => true, 
            'album' => $album, 
            'songs' => $songs
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener el álbum: ' . $e->getMessage()]);
    }
}

/**
 * Obtiene la información de un sencillo
 */
function getSencillo() {
    global $conn;
    
    // Verificar si se proporcionó un ID
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de sencillo']);
        exit;
    }
    
    $sencilloId = $_GET['id'];
    
    try {
        // Obtener información del sencillo
        $stmt = $conn->prepare("
            SELECT s.id_sencillo, s.nombre_sencillo, s.descripcion, s.imagen_sencillo_path, 
                   s.cancion_path, s.fecha_lanzamiento, s.precio, g.nombre_genero, 
                   u.nombre_usuario as artista_nombre
            FROM sencillos s
            JOIN genero g ON s.id_genero = g.id_genero
            JOIN artista a ON s.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE s.id_sencillo = :id
        ");
        $stmt->bindParam(':id', $sencilloId, PDO::PARAM_INT);
        $stmt->execute();
        
        $sencillo = $stmt->fetch();
        
        if (!$sencillo) {
            echo json_encode(['success' => false, 'message' => 'Sencillo no encontrado']);
            exit;
        }
        
        // Devolver la información del sencillo
        echo json_encode([
            'success' => true, 
            'sencillo' => $sencillo
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener el sencillo: ' . $e->getMessage()]);
    }
}

/**
 * Registra la reproducción de una canción
 */
function recordPlay() {
    global $conn;
    
    // Verificar si se proporcionó un ID
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de canción']);
        exit;
    }
    
    $songId = $_GET['id'];
    $songType = isset($_GET['type']) ? $_GET['type'] : 'album_cancion';
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    try {
        // Insertar reproducción en la base de datos
        $stmt = $conn->prepare("
            INSERT INTO reproducciones (id_usuario, id_cancion, id_sencillo, tipo, fecha_reproduccion)
            VALUES (:user_id, :cancion_id, :sencillo_id, :tipo, NOW())
        ");
        
        if ($songType === 'album_cancion') {
            $stmt->bindParam(':cancion_id', $songId, PDO::PARAM_INT);
            $stmt->bindValue(':sencillo_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':cancion_id', null, PDO::PARAM_NULL);
            $stmt->bindParam(':sencillo_id', $songId, PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':tipo', $songType, PDO::PARAM_STR);
        $stmt->execute();
        
        echo json_encode(['success' => true]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al registrar reproducción: ' . $e->getMessage()]);
    }
}

/**
 * Busca música por término
 */
function searchMusic() {
    global $conn;
    
    // Verificar si se proporcionó un término de búsqueda
    if (!isset($_GET['term'])) {
        echo json_encode(['success' => false, 'message' => 'No se proporcionó un término de búsqueda']);
        exit;
    }
    
    $term = '%' . $_GET['term'] . '%';
    
    try {
        // Buscar canciones
        $stmt = $conn->prepare("
            SELECT c.id_cancion as id, c.nombre_cancion as titulo, a.nombre_album as album, 
                   u.nombre_usuario as artista, 'album_cancion' as tipo
            FROM canciones c
            JOIN album a ON c.id_album = a.id_album
            JOIN artista art ON c.id_artista = art.id_artista
            JOIN usuario u ON art.usuario = u.id_usuario
            WHERE c.nombre_cancion LIKE :term
            LIMIT 10
        ");
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();
        
        $songs = $stmt->fetchAll();
        
        // Buscar sencillos
        $stmt = $conn->prepare("
            SELECT s.id_sencillo as id, s.nombre_sencillo as titulo, NULL as album, 
                   u.nombre_usuario as artista, 'sencillo' as tipo
            FROM sencillos s
            JOIN artista a ON s.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE s.nombre_sencillo LIKE :term
            LIMIT 10
        ");
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();
        
        $sencillos = $stmt->fetchAll();
        
        // Buscar álbumes
        $stmt = $conn->prepare("
            SELECT a.id_album as id, a.nombre_album as titulo, NULL as album, 
                   u.nombre_usuario as artista, 'album' as tipo
            FROM album a
            JOIN artista art ON a.id_artista = art.id_artista
            JOIN usuario u ON art.usuario = u.id_usuario
            WHERE a.nombre_album LIKE :term
            LIMIT 10
        ");
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();
        
        $albums = $stmt->fetchAll();
        
        // Buscar artistas
        $stmt = $conn->prepare("
            SELECT a.id_artista as id, u.nombre_usuario as titulo, NULL as album, 
                   NULL as artista, 'artista' as tipo
            FROM artista a
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE u.nombre_usuario LIKE :term
            LIMIT 10
        ");
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->execute();
        
        $artists = $stmt->fetchAll();
        
        // Combinar resultados
        $results = [
            'songs' => $songs,
            'sencillos' => $sencillos,
            'albums' => $albums,
            'artists' => $artists
        ];
        
        echo json_encode(['success' => true, 'results' => $results]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al buscar música: ' . $e->getMessage()]);
    }
}

/**
 * Obtiene las playlists del usuario
 */
function getPlaylists() {
    global $conn;
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
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
?>
