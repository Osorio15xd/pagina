<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$dbname = 'bassculture';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

// Opciones para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Cadena de conexión DSN
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    // Crear conexión PDO
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // En caso de error, mostrar mensaje y terminar script
    die('Error de conexión: ' . $e->getMessage());
}

// Función para obtener datos del usuario actual
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM usuario WHERE id_usuario = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Función para verificar si el usuario actual es un artista
function isArtist() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM artista WHERE usuario = ?');
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->rowCount() > 0;
    }
    return false;
}

// Función para obtener el ID de artista del usuario actual
function getCurrentArtistId() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT id_artista FROM artista WHERE usuario = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        return $result ? $result['id_artista'] : null;
    }
    return null;
}

// Función para formatear fechas
function formatDate($date) {
    if (!$date) return 'N/A';
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

// Función para formatear precios
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Función para verificar si un usuario ha comprado un producto
function hasUserPurchased($userId, $productId, $productType) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM compras WHERE id_usuario = ? AND id_producto = ? AND tipo_producto = ?');
    $stmt->execute([$userId, $productId, $productType]);
    return $stmt->fetchColumn() > 0;
}

// Función para obtener los géneros musicales
function getGenres() {
    global $pdo;
    $stmt = $pdo->query('SELECT * FROM genero ORDER BY nombre_genero');
    return $stmt->fetchAll();
}

// Función para obtener el nombre de un género por su ID
function getGenreName($genreId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT nombre_genero FROM genero WHERE id_genero = ?');
    $stmt->execute([$genreId]);
    $result = $stmt->fetch();
    return $result ? $result['nombre_genero'] : 'Desconocido';
}

// Función para obtener el nombre de un artista por su ID
function getArtistName($artistId) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT u.nombre, u.apellido1 
        FROM artista a 
        JOIN usuario u ON a.usuario = u.id_usuario 
        WHERE a.id_artista = ?
    ');
    $stmt->execute([$artistId]);
    $result = $stmt->fetch();
    return $result ? $result['nombre'] . ' ' . $result['apellido1'] : 'Desconocido';
}

// Función para obtener el nombre de usuario de un artista por su ID
function getArtistUsername($artistId) {
    global $pdo;
    $stmt = $pdo->prepare('
        SELECT u.nombre_usuario 
        FROM artista a 
        JOIN usuario u ON a.usuario = u.id_usuario 
        WHERE a.id_artista = ?
    ');
    $stmt->execute([$artistId]);
    $result = $stmt->fetch();
    return $result ? $result['nombre_usuario'] : 'Desconocido';
}

// Función para obtener la foto de un artista por su ID
function getArtistPhoto($artistId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT foto_path FROM artista WHERE id_artista = ?');
    $stmt->execute([$artistId]);
    $result = $stmt->fetch();
    return $result && $result['foto_path'] ? $result['foto_path'] : 'assets/img/default-artist.jpg';
}

// Función para obtener el nombre de un álbum por su ID
function getAlbumName($albumId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT nombre_album FROM album WHERE id_album = ?');
    $stmt->execute([$albumId]);
    $result = $stmt->fetch();
    return $result ? $result['nombre_album'] : 'Desconocido';
}

// Función para obtener la imagen de un álbum por su ID
function getAlbumImage($albumId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT imagen_album_path FROM album WHERE id_album = ?');
    $stmt->execute([$albumId]);
    $result = $stmt->fetch();
    return $result && $result['imagen_album_path'] ? $result['imagen_album_path'] : 'assets/img/default-album.jpg';
}

// Función para obtener el nombre de una canción por su ID
function getSongName($songId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT nombre_cancion FROM canciones WHERE id_cancion = ?');
    $stmt->execute([$songId]);
    $result = $stmt->fetch();
    return $result ? $result['nombre_cancion'] : 'Desconocido';
}

// Función para obtener la ruta de una canción por su ID
function getSongPath($songId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT cancion_path FROM canciones WHERE id_cancion = ?');
    $stmt->execute([$songId]);
    $result = $stmt->fetch();
    return $result ? $result['cancion_path'] : '';
}

// Función para obtener el nombre de un sencillo por su ID
function getSingleName($singleId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT nombre_sencillo FROM sencillos WHERE id_sencillo = ?');
    $stmt->execute([$singleId]);
    $result = $stmt->fetch();
    return $result ? $result['nombre_sencillo'] : 'Desconocido';
}

// Función para obtener la imagen de un sencillo por su ID
function getSingleImage($singleId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT imagen_sencillo_path FROM sencillos WHERE id_sencillo = ?');
    $stmt->execute([$singleId]);
    $result = $stmt->fetch();
    return $result && $result['imagen_sencillo_path'] ? $result['imagen_sencillo_path'] : 'assets/img/default-single.jpg';
}

// Función para obtener la ruta de un sencillo por su ID
function getSinglePath($singleId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT cancion_path FROM sencillos WHERE id_sencillo = ?');
    $stmt->execute([$singleId]);
    $result = $stmt->fetch();
    return $result ? $result['cancion_path'] : '';
}

// Función para verificar si una canción está en "Me gusta" del usuario
function isSongLiked($userId, $songId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM me_gusta WHERE id_usuario = ? AND id_item = ? AND tipo_item = "cancion"');
    $stmt->execute([$userId, $songId]);
    return $stmt->fetchColumn() > 0;
}

// Función para verificar si un sencillo está en "Me gusta" del usuario
function isSingleLiked($userId, $singleId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM me_gusta WHERE id_usuario = ? AND id_item = ? AND tipo_item = "sencillo"');
    $stmt->execute([$userId, $singleId]);
    return $stmt->fetchColumn() > 0;
}

// Función para verificar si un álbum está en "Me gusta" del usuario
function isAlbumLiked($userId, $albumId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM me_gusta WHERE id_usuario = ? AND id_item = ? AND tipo_item = "album"');
    $stmt->execute([$userId, $albumId]);
    return $stmt->fetchColumn() > 0;
}

// Función para verificar si un artista está en "Me gusta" del usuario
function isArtistLiked($userId, $artistId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM me_gusta WHERE id_usuario = ? AND id_item = ? AND tipo_item = "artista"');
    $stmt->execute([$userId, $artistId]);
    return $stmt->fetchColumn() > 0;
}

// Función para obtener el número de elementos en el carrito del usuario
function getCartCount($userId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM carrito WHERE id_usuario = ?');
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}
?>
