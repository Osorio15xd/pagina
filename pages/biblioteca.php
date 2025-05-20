<?php
session_start();
require_once '../config/db_connect.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Obtener las compras del usuario
$stmt = $pdo->prepare('
    SELECT c.*, 
           CASE 
               WHEN c.tipo_producto = "album" THEN (SELECT nombre_album FROM album WHERE id_album = c.id_producto)
               WHEN c.tipo_producto = "sencillo" THEN (SELECT nombre_sencillo FROM sencillos WHERE id_sencillo = c.id_producto)
               WHEN c.tipo_producto = "cancion" THEN (SELECT nombre_cancion FROM canciones WHERE id_cancion = c.id_producto)
           END AS nombre_producto,
           CASE 
               WHEN c.tipo_producto = "album" THEN (SELECT imagen_album_path FROM album WHERE id_album = c.id_producto)
               WHEN c.tipo_producto = "sencillo" THEN (SELECT imagen_sencillo_path FROM sencillos WHERE id_sencillo = c.id_producto)
               WHEN c.tipo_producto = "cancion" THEN (SELECT imagen_album_path FROM album a JOIN canciones cn ON a.id_album = cn.id_album WHERE cn.id_cancion = c.id_producto)
           END AS imagen_producto,
           CASE 
               WHEN c.tipo_producto = "album" THEN (SELECT id_artista FROM album WHERE id_album = c.id_producto)
               WHEN c.tipo_producto = "sencillo" THEN (SELECT id_artista FROM sencillos WHERE id_sencillo = c.id_producto)
               WHEN c.tipo_producto = "cancion" THEN (SELECT id_artista FROM canciones WHERE id_cancion = c.id_producto)
           END AS id_artista
    FROM compras c
    WHERE c.id_usuario = ?
    ORDER BY c.fecha_compra DESC
');
$stmt->execute([$userId]);
$purchases = $stmt->fetchAll();

// Obtener los "Me gusta" del usuario
$stmt = $pdo->prepare('
    SELECT mg.*, 
           CASE 
               WHEN mg.tipo_item = "album" THEN (SELECT nombre_album FROM album WHERE id_album = mg.id_item)
               WHEN mg.tipo_item = "cancion" THEN (SELECT nombre_cancion FROM canciones WHERE id_cancion = mg.id_item)
               WHEN mg.tipo_item = "sencillo" THEN (SELECT nombre_sencillo FROM sencillos WHERE id_sencillo = mg.id_item)
               WHEN mg.tipo_item = "artista" THEN (SELECT nombre_usuario FROM usuario u JOIN artista a ON u.id_usuario = a.usuario WHERE a.id_artista = mg.id_item)
           END AS nombre_item,
           CASE 
               WHEN mg.tipo_item = "album" THEN (SELECT imagen_album_path FROM album WHERE id_album = mg.id_item)
               WHEN mg.tipo_item = "cancion" THEN (SELECT imagen_album_path FROM album a JOIN canciones cn ON a.id_album = cn.id_album WHERE cn.id_cancion = mg.id_item)
               WHEN mg.tipo_item = "sencillo" THEN (SELECT imagen_sencillo_path FROM sencillos WHERE id_sencillo = mg.id_item)
               WHEN mg.tipo_item = "artista" THEN (SELECT foto_path FROM artista WHERE id_artista = mg.id_item)
           END AS imagen_item,
           CASE 
               WHEN mg.tipo_item = "album" THEN (SELECT id_artista FROM album WHERE id_album = mg.id_item)
               WHEN mg.tipo_item = "cancion" THEN (SELECT id_artista FROM canciones WHERE id_cancion = mg.id_item)
               WHEN mg.tipo_item = "sencillo" THEN (SELECT id_artista FROM sencillos WHERE id_sencillo = mg.id_item)
               WHEN mg.tipo_item = "artista" THEN mg.id_item
           END AS id_artista
    FROM me_gusta mg
    WHERE mg.id_usuario = ?
    ORDER BY mg.fecha DESC
');
$stmt->execute([$userId]);
$likes = $stmt->fetchAll();

// Obtener las playlists del usuario
$stmt = $pdo->prepare('
    SELECT p.*, 
           (SELECT COUNT(*) FROM playlist_canciones WHERE id_playlist = p.id_playlist) AS total_canciones
    FROM playlists p
    WHERE p.id_usuario = ?
    ORDER BY p.fecha_actualizacion DESC
');
$stmt->execute([$userId]);
$playlists = $stmt->fetchAll();

include_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Mi Biblioteca</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="#purchases" class="list-group-item list-group-item-action" data-bs-toggle="tab">Mis Compras</a>
                <a href="#likes" class="list-group-item list-group-item-action" data-bs-toggle="tab">Me Gusta</a>
                <a href="#playlists" class="list-group-item list-group-item-action" data-bs-toggle="tab">Mis Playlists</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="tab-content">
            <!-- Mis Compras -->
            <div class="tab-pane fade show active" id="purchases">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Mis Compras</h2>
                </div>
                
                <?php if (empty($purchases)): ?>
                    <div class="alert alert-info">
                        No tienes compras realizadas. Visita nuestra <a href="music-store.php">tienda</a> para descubrir música.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($purchases as $purchase): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?= htmlspecialchars($purchase['imagen_producto']) ?>" class="card-img-top" alt="<?= htmlspecialchars($purchase['nombre_producto']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($purchase['nombre_producto']) ?></h5>
                                        <p class="card-text">Tipo: <?= htmlspecialchars($purchase['tipo_producto']) ?></p>
                                        <p class="card-text"><small class="text-muted">Comprado el: <?= htmlspecialchars($purchase['fecha_compra']) ?></small></p>
                                        <!-- Aquí podrías añadir un botón para reproducir la canción/álbum/sencillo -->
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Me Gusta -->
            <div class="tab-pane fade" id="likes">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Me Gusta</h2>
                </div>
                
                <?php if (empty($likes)): ?>
                    <div class="alert alert-info">
                        No has dado "Me gusta" a ningún elemento. Explora nuestra plataforma para descubrir contenido.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($likes as $like): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?= htmlspecialchars($like['imagen_item']) ?>" class="card-img-top" alt="<?= htmlspecialchars($like['nombre_item']) ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($like['nombre_item']) ?></h5>
                                        <p class="card-text">Tipo: <?= htmlspecialchars($like['tipo_item']) ?></p>
                                        <p class="card-text"><small class="text-muted">Agregado el: <?= htmlspecialchars($like['fecha']) ?></small></p>
                                        <!-- Aquí podrías añadir un botón para ver el artista/álbum/canción/sencillo -->
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Mis Playlists -->
            <div class="tab-pane fade" id="playlists">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Mis Playlists</h2>
                    <a href="/bassculture/pages/create_playlist.php" class="btn btn-primary">Crear Playlist</a>
                </div>
                
                <?php if (empty($playlists)): ?>
                    <div class="alert alert-info">
                        No has creado ninguna playlist. ¡Crea una ahora y organiza tu música!
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($playlists as $playlist): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($playlist['nombre_playlist']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($playlist['descripcion']) ?></p>
                                        <p class="card-text"><small class="text-muted">Total canciones: <?= htmlspecialchars($playlist['total_canciones']) ?></small></p>
                                        <a href="/bassculture/pages/playlist_detail.php?id=<?= $playlist['id_playlist'] ?>" class="btn btn-secondary">Ver Playlist</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
### Creación de BassCulture: Plataforma de Streaming Musical

Voy a crear una aplicación web moderna para BassCulture utilizando la estructura de base de datos que has proporcionado. Esta aplicación permitirá a los usuarios explorar música, crear playlists, y gestionar su biblioteca musical.
