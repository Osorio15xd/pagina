<?php
session_start();
require_once '../config/db_connect.php';

// Obtener álbumes destacados
$stmt = $pdo->query('
    SELECT a.*, g.nombre_genero, ar.id_artista, u.nombre_usuario as nombre_artista
    FROM album a
    JOIN genero g ON a.id_genero = g.id_genero
    JOIN artista ar ON a.id_artista = ar.id_artista
    JOIN usuario u ON ar.usuario = u.id_usuario
    ORDER BY a.id_album DESC
    LIMIT 8
');
$albums = $stmt->fetchAll();

// Obtener artistas destacados
$stmt = $pdo->query('
    SELECT ar.*, u.nombre_usuario, u.nombre, u.apellido1
    FROM artista ar
    JOIN usuario u ON ar.usuario = u.id_usuario
    ORDER BY ar.popularidad DESC
    LIMIT 8
');
$artists = $stmt->fetchAll();

// Obtener sencillos destacados
$stmt = $pdo->query('
    SELECT s.*, g.nombre_genero, ar.id_artista, u.nombre_usuario as nombre_artista
    FROM sencillos s
    JOIN genero g ON s.id_genero = g.id_genero
    JOIN artista ar ON s.id_artista = ar.id_artista
    JOIN usuario u ON ar.usuario = u.id_usuario
    ORDER BY s.id_sencillo DESC
    LIMIT 8
');
$singles = $stmt->fetchAll();

// Obtener géneros musicales
$genres = getGenres();

include_once '../includes/header.php';
?>

<!-- Carrusel de destacados -->
<div id="featuredCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <?php if (isset($albums[0])): ?>
        <div class="carousel-item active">
            <img src="<?= htmlspecialchars($albums[0]['imagen_album_path']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($albums[0]['nombre_album']) ?>">
            <div class="carousel-caption d-none d-md-block">
                <h2><?= htmlspecialchars($albums[0]['nombre_album']) ?></h2>
                <p><?= htmlspecialchars($albums[0]['nombre_artista']) ?></p>
                <a href="/bassculture/pages/album.php?id=<?= $albums[0]['id_album'] ?>" class="btn btn-primary">Ver Álbum</a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($singles[0])): ?>
        <div class="carousel-item">
            <img src="<?= htmlspecialchars($singles[0]['imagen_sencillo_path']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($singles[0]['nombre_sencillo']) ?>">
            <div class="carousel-caption d-none d-md-block">
                <h2><?= htmlspecialchars($singles[0]['nombre_sencillo']) ?></h2>
                <p><?= htmlspecialchars($singles[0]['nombre_artista']) ?></p>
                <a href="/bassculture/pages/sencillo.php?id=<?= $singles[0]['id_sencillo'] ?>" class="btn btn-primary">Ver Sencillo</a>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($artists[0])): ?>
        <div class="carousel-item">
            <img src="<?= htmlspecialchars($artists[0]['foto_path']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($artists[0]['nombre_usuario']) ?>">
            <div class="carousel-caption d-none d-md-block">
                <h2><?= htmlspecialchars($artists[0]['nombre_usuario']) ?></h2>
                <p>Artista destacado</p>
                <a href="/bassculture/pages/artista.php?id=<?= $artists[0]['id_artista'] ?>" class="btn btn-primary">Ver Artista</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>

<!-- Géneros musicales -->
<section class="mb-5">
    <h2 class="section-title">Géneros Musicales</h2>
    <div class="row">
        <?php foreach ($genres as $genre): ?>
        <div class="col-md-2 col-sm-4 col-6 mb-4">
            <a href="/bassculture/pages/genero.php?id=<?= $genre['id_genero'] ?>" class="text-decoration-none">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($genre['nombre_genero']) ?></h5>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Álbumes destacados -->
<section class="mb-5">
    <h2 class="section-title">Álbumes Destacados</h2>
    <div class="row">
        <?php foreach ($albums as $album): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="<?= htmlspecialchars($album['imagen_album_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($album['nombre_album']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($album['nombre_album']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($album['nombre_artista']) ?></p>
                    <p class="card-text"><small class="text-muted"><?= htmlspecialchars($album['nombre_genero']) ?></small></p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="/bassculture/pages/album.php?id=<?= $album['id_album'] ?>" class="btn btn-sm btn-primary">Ver Álbum</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-sm btn-outline-primary like-button" data-bs-toggle="tooltip" title="Añadir a Me gusta" data-item-id="<?= $album['id_album'] ?>" data-item-type="album">
                        <i class="<?= isAlbumLiked($_SESSION['user_id'], $album['id_album']) ? 'fas fa-heart text-danger' : 'far fa-heart' ?>"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-3">
        <a href="/bassculture/pages/explorar.php?type=albums" class="btn btn-outline-primary">Ver Más Álbumes</a>
    </div>
</section>

<!-- Artistas destacados -->
<section class="mb-5">
    <h2 class="section-title">Artistas Destacados</h2>
    <div class="row">
        <?php foreach ($artists as $artist): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100 text-center">
                <img src="<?= htmlspecialchars($artist['foto_path']) ?>" class="card-img-artist mx-auto" alt="<?= htmlspecialchars($artist['nombre_usuario']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($artist['nombre_usuario']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($artist['nombre'] . ' ' . $artist['apellido1']) ?></p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="/bassculture/pages/artista.php?id=<?= $artist['id_artista'] ?>" class="btn btn-sm btn-primary">Ver Artista</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-sm btn-outline-primary like-button" data-bs-toggle="tooltip" title="Añadir a Me gusta" data-item-id="<?= $artist['id_artista'] ?>" data-item-type="artista">
                        <i class="<?= isArtistLiked($_SESSION['user_id'], $artist['id_artista']) ? 'fas fa-heart text-danger' : 'far fa-heart' ?>"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-3">
        <a href="/bassculture/pages/artistas.php" class="btn btn-outline-primary">Ver Más Artistas</a>
    </div>
</section>

<!-- Sencillos destacados -->
<section class="mb-5">
    <h2 class="section-title">Sencillos Destacados</h2>
    <div class="row">
        <?php foreach ($singles as $single): ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <img src="<?= htmlspecialchars($single['imagen_sencillo_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($single['nombre_sencillo']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($single['nombre_sencillo']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($single['nombre_artista']) ?></p>
                    <p class="card-text"><small class="text-muted"><?= htmlspecialchars($single['nombre_genero']) ?></small></p>
                </div>
                <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between">
                    <button class="btn btn-sm btn-primary play-song" 
                        data-song-id="<?= $single['id_sencillo'] ?>" 
                        data-song-type="sencillo" 
                        data-song-title="<?= htmlspecialchars($single['nombre_sencillo']) ?>" 
                        data-song-artist="<?= htmlspecialchars($single['nombre_artista']) ?>" 
                        data-song-cover="<?= htmlspecialchars($single['imagen_sencillo_path']) ?>" 
                        data-song-path="<?= htmlspecialchars($single['cancion_path']) ?>">
                        <i class="fas fa-play me-1"></i> Reproducir
                    </button>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div>
                        <button class="btn btn-sm btn-outline-primary like-button" data-bs-toggle="tooltip" title="Añadir a Me gusta" data-item-id="<?= $single['id_sencillo'] ?>" data-item-type="sencillo">
                            <i class="<?= isSingleLiked($_SESSION['user_id'], $single['id_sencillo']) ? 'fas fa-heart text-danger' : 'far fa-heart' ?>"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary add-to-cart" data-bs-toggle="tooltip" title="Añadir al carrito" data-product-id="<?= $single['id_sencillo'] ?>" data-product-type="sencillo">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-3">
        <a href="/bassculture/pages/explorar.php?type=singles" class="btn btn-outline-primary">Ver Más Sencillos</a>
    </div>
</section>

<?php include_once '../includes/footer.php'; ?>
