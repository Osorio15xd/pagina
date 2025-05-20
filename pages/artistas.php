<?php
session_start();
require_once '../config/db_connect.php';

// Obtener todos los artistas
try {
    $stmt = $pdo->query("
        SELECT 
            a.id_artista, 
            u.nombre_usuario as nombre, 
            u.nombre, 
            u.apellido1,
            a.foto_path as foto, 
            a.popularidad,
            (SELECT COUNT(*) FROM canciones c WHERE c.id_artista = a.id_artista) + 
            (SELECT COUNT(*) FROM sencillos s WHERE s.id_artista = a.id_artista) as canciones
        FROM artista a
        JOIN usuario u ON a.usuario = u.id_usuario
        ORDER BY a.popularidad DESC, u.nombre_usuario
    ");
    $artistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al cargar artistas: " . $e->getMessage() . "</div>";
    $artistas = [];
}

// Obtener artista específico si se solicita
$artista_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$artista_actual = null;
$canciones_artista = [];
$albums_artista = [];
$sencillos_artista = [];

if ($artista_id > 0) {
    try {
        // Obtener información del artista
        $stmt = $pdo->prepare("
            SELECT a.id_artista, u.nombre, u.nombre_usuario, u.apellido1, u.apellido2, a.foto_path 
            FROM usuario u 
            JOIN artista a ON u.id_usuario = a.usuario 
            WHERE a.id_artista = ?
        ");
        $stmt->execute([$artista_id]);
        $artista_actual = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($artista_actual) {
            // Obtener canciones del artista (de álbumes)
            $stmt = $pdo->prepare("
                SELECT 
                    c.id_cancion as id, 
                    c.nombre_cancion as titulo, 
                    a.imagen_album_path as portada, 
                    c.cancion_path as archivo_audio, 
                    g.nombre_genero as genero,
                    a.nombre_album as album,
                    'cancion' as tipo
                FROM canciones c 
                JOIN album a ON c.id_album = a.id_album 
                JOIN genero g ON a.id_genero = g.id_genero
                WHERE c.id_artista = ?
                ORDER BY c.nombre_cancion
            ");
            $stmt->execute([$artista_id]);
            $canciones_album = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener sencillos del artista
            $stmt = $pdo->prepare("
                SELECT 
                    s.id_sencillo as id, 
                    s.nombre_sencillo as titulo, 
                    s.imagen_sencillo_path as portada, 
                    s.cancion_path as archivo_audio, 
                    g.nombre_genero as genero,
                    NULL as album,
                    'sencillo' as tipo
                FROM sencillos s
                JOIN genero g ON s.id_genero = g.id_genero
                WHERE s.id_artista = ?
                ORDER BY s.nombre_sencillo
            ");
            $stmt->execute([$artista_id]);
            $sencillos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Guardar sencillos por separado
            $sencillos_artista = $sencillos;
            
            // Combinar canciones de álbumes y sencillos
            $canciones_artista = array_merge($canciones_album, $sencillos);

            // Obtener álbumes del artista
            $stmt = $pdo->prepare("
                SELECT 
                    id_album, 
                    nombre_album, 
                    imagen_album_path, 
                    fecha_lanzamiento,
                    precio,
                    id_genero,
                    (SELECT nombre_genero FROM genero WHERE id_genero = album.id_genero) as genero
                FROM album
                WHERE id_artista = ?
                ORDER BY fecha_lanzamiento DESC
            ");
            $stmt->execute([$artista_id]);
            $albums_artista = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error al cargar información del artista: " . $e->getMessage() . "</div>";
    }
}

// Incluir el encabezado
include_once '../includes/header.php';
?>

<div class="container">
    <?php if ($artista_actual): ?>
        <!-- Vista detallada del artista -->
        <div class="artist-detail mt-4">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <a href="artistas.php" class="btn btn-outline-primary mb-3">
                        <i class="fas fa-arrow-left"></i> Volver a artistas
                    </a>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <img src="<?php echo $artista_actual['foto_path']; ?>" alt="<?php echo $artista_actual['nombre']; ?>" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" onerror="this.src='../assets/img/default-user.png';">
                                </div>
                                <div class="col-md-9">
                                    <h1 class="mb-3"><?php echo $artista_actual['nombre_usuario']; ?></h1>
                                    <p class="text-muted"><?php echo $artista_actual['nombre'] . ' ' . $artista_actual['apellido1']; ?></p>
                                    <p><i class="fas fa-music me-2"></i> <?php echo count($canciones_artista); ?> canciones</p>
                                    <p><i class="fas fa-compact-disc me-2"></i> <?php echo count($albums_artista); ?> álbumes</p>
                                    
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <div class="mt-3">
                                        <button class="btn btn-outline-primary me-2">
                                            <i class="fas fa-heart"></i> Seguir
                                        </button>
                                        <button class="btn btn-outline-secondary">
                                            <i class="fas fa-share-alt"></i> Compartir
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (count($albums_artista) > 0): ?>
                <h2 class="mb-3">Álbumes de <?php echo $artista_actual['nombre_usuario']; ?></h2>
                <div class="row mb-5">
                    <?php foreach ($albums_artista as $album): ?>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo $album['imagen_album_path']; ?>" alt="Portada de <?php echo $album['nombre_album']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" onerror="this.src='../assets/img/default-cover.png';" />
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $album['nombre_album']; ?></h5>
                                    <p class="card-text text-muted"><?php echo date('Y', strtotime($album['fecha_lanzamiento'])); ?></p>
                                    <p class="card-text"><span class="badge bg-secondary"><?php echo $album['genero']; ?></span></p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="album.php?id=<?php echo $album['id_album']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-compact-disc me-1"></i> Ver Álbum
                                    </a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <button class="btn btn-outline-danger btn-sm add-to-cart-btn" data-id="<?php echo $album['id_album']; ?>" data-type="album">
                                        <i class="fas fa-shopping-cart me-1"></i> $<?php echo number_format($album['precio'], 2); ?>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (count($sencillos_artista) > 0): ?>
                <h2 class="mb-3">Sencillos de <?php echo $artista_actual['nombre_usuario']; ?></h2>
                <div class="row mb-5">
                    <?php foreach ($sencillos_artista as $sencillo): ?>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo $sencillo['portada']; ?>" alt="Portada de <?php echo $sencillo['titulo']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;" onerror="this.src='../assets/img/default-cover.png';" />
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $sencillo['titulo']; ?></h5>
                                    <p class="card-text"><span class="badge bg-secondary"><?php echo $sencillo['genero']; ?></span></p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <button class="btn btn-primary btn-sm play-btn" data-audio="<?php echo $sencillo['archivo_audio']; ?>" data-title="<?php echo $sencillo['titulo']; ?>" data-artist="<?php echo $artista_actual['nombre_usuario']; ?>" data-cover="<?php echo $sencillo['portada']; ?>" data-id="<?php echo $sencillo['id']; ?>" data-type="<?php echo $sencillo['tipo']; ?>">
                                        <i class="fas fa-play me-1"></i> Reproducir
                                    </button>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <button class="btn btn-outline-danger btn-sm add-to-cart-btn" data-id="<?php echo $sencillo['id']; ?>" data-type="sencillo">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 class="mb-3">Todas las canciones de <?php echo $artista_actual['nombre_usuario']; ?></h2>
            <?php if (count($canciones_artista) > 0): ?>
                <div class="table-responsive mb-5">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Álbum</th>
                                <th>Género</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($canciones_artista as $index => $cancion): ?>
                                <tr data-id="<?php echo $cancion['id']; ?>" data-type="<?php echo $cancion['tipo']; ?>">
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $cancion['portada']; ?>" alt="Portada" class="me-2" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.src='../assets/img/default-cover.png';">
                                            <span><?php echo $cancion['titulo']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $cancion['album'] ? $cancion['album'] : 'Sencillo'; ?></td>
                                    <td><?php echo $cancion['genero']; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary play-btn" data-audio="<?php echo $cancion['archivo_audio']; ?>" data-title="<?php echo $cancion['titulo']; ?>" data-artist="<?php echo $artista_actual['nombre_usuario']; ?>" data-cover="<?php echo $cancion['portada']; ?>" data-id="<?php echo $cancion['id']; ?>" data-type="<?php echo $cancion['tipo']; ?>">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <button class="btn btn-sm btn-outline-primary add-to-playlist-btn" title="Añadir a playlist" data-id="<?php echo $cancion['id']; ?>" data-type="<?php echo $cancion['tipo']; ?>">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger add-to-cart-btn" title="Añadir al carrito" data-id="<?php echo $cancion['id']; ?>" data-type="<?php echo $cancion['tipo']; ?>">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No hay canciones disponibles para este artista.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Lista de artistas -->
        <h1 class="my-4">Artistas</h1>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="search-artist" class="form-control" placeholder="Buscar artista...">
                    <button class="btn btn-primary" type="button" id="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="btn-group float-end">
                    <button type="button" class="btn btn-outline-primary active" data-view="grid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Vista de cuadrícula (predeterminada) -->
        <div id="grid-view" class="row">
            <?php foreach ($artistas as $artista): ?>
                <div class="col-md-3 col-sm-6 mb-4 artist-item">
                    <div class="card h-100 text-center">
                        <img src="<?php echo $artista['foto']; ?>" alt="Foto de <?php echo $artista['nombre']; ?>" class="card-img-top rounded-circle mx-auto mt-3" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.src='../assets/img/default-user.png';" />
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $artista['nombre']; ?></h4>
                            <p class="card-text text-muted"><?php echo $artista['nombre'] . ' ' . $artista['apellido1']; ?></p>
                            <p class="card-text"><i class="fas fa-music me-1"></i> <?php echo $artista['canciones']; ?> canciones</p>
                            <a href="artistas.php?id=<?php echo $artista['id_artista']; ?>" class="btn btn-primary">
                                <i class="fas fa-user me-1"></i> Ver Perfil
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Vista de lista (oculta por defecto) -->
        <div id="list-view" class="d-none">
            <div class="list-group">
                <?php foreach ($artistas as $artista): ?>
                    <a href="artistas.php?id=<?php echo $artista['id_artista']; ?>" class="list-group-item list-group-item-action artist-item">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo $artista['foto']; ?>" alt="Foto de <?php echo $artista['nombre']; ?>" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='../assets/img/default-user.png';" />
                            <div>
                                <h5 class="mb-1"><?php echo $artista['nombre']; ?></h5>
                                <p class="mb-1 text-muted"><?php echo $artista['nombre'] . ' ' . $artista['apellido1']; ?></p>
                                <small><i class="fas fa-music me-1"></i> <?php echo $artista['canciones']; ?> canciones</small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Reproductor Modal -->
<div class="modal fade" id="playerModal" tabindex="-1" aria-labelledby="playerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="playerModalLabel">Reproduciendo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalCover" src="/placeholder.svg" alt="Album Cover" class="img-fluid mb-3" style="max-height: 200px;">
                <h5 id="modalTitle" class="mb-2"></h5>
                <p id="modalArtist" class="text-muted"></p>
                
                <div class="audio-player">
                    <audio id="audioPlayer" controls class="w-100">
                        <source id="audioSource" src="/placeholder.svg" type="audio/mpeg">
                        Tu navegador no soporta el elemento de audio.
                    </audio>
                </div>
            </div>
            <div class="modal-footer">
                <?php if (isset($_SESSION['user_id'])): ?>
                <button type="button" class="btn btn-outline-primary" id="addToPlaylistBtn">
                    <i class="fas fa-plus"></i> Añadir a Playlist
                </button>
                <button type="button" class="btn btn-outline-danger" id="addToCartBtn">
                    <i class="fas fa-shopping-cart"></i> Añadir al Carrito
                </button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar entre vista de cuadrícula y lista
    const viewButtons = document.querySelectorAll('.btn-group button[data-view]');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    
    if (viewButtons.length > 0) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Quitar clase active de todos los botones
                viewButtons.forEach(btn => btn.classList.remove('active'));
                // Añadir clase active al botón clickeado
                this.classList.add('active');
                
                const view = this.getAttribute('data-view');
                if (view === 'grid') {
                    gridView.classList.remove('d-none');
                    listView.classList.add('d-none');
                } else {
                    gridView.classList.add('d-none');
                    listView.classList.remove('d-none');
                }
            });
        });
    }
    
    // Búsqueda de artistas
    const searchInput = document.getElementById('search-artist');
    const searchBtn = document.getElementById('search-btn');
    const artistItems = document.querySelectorAll('.artist-item');
    
    if (searchInput && searchBtn) {
        const performSearch = () => {
            const searchTerm = searchInput.value.toLowerCase();
            
            artistItems.forEach(item => {
                const artistName = item.querySelector('.card-title, h5').textContent.toLowerCase();
                
                if (artistName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        };
        
        searchBtn.addEventListener('click', performSearch);
        
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
    
    // Reproducción de canciones
    const playButtons = document.querySelectorAll('.play-btn');
    
    if (playButtons.length > 0) {
        const playerModal = new bootstrap.Modal(document.getElementById('playerModal'));
        const audioPlayer = document.getElementById('audioPlayer');
        const audioSource = document.getElementById('audioSource');
        const modalCover = document.getElementById('modalCover');
        const modalTitle = document.getElementById('modalTitle');
        const modalArtist = document.getElementById('modalArtist');
        const addToPlaylistBtn = document.getElementById('addToPlaylistBtn');
        const addToCartBtn = document.getElementById('addToCartBtn');
        
        playButtons.forEach(button => {
            button.addEventListener('click', function() {
                const audioPath = this.getAttribute('data-audio');
                const title = this.getAttribute('data-title');
                const artist = this.getAttribute('data-artist');
                const cover = this.getAttribute('data-cover');
                const songId = this.getAttribute('data-id');
                const songType = this.getAttribute('data-type');
                
                // Actualizar modal
                modalCover.src = cover;
                modalTitle.textContent = title;
                modalArtist.textContent = artist;
                audioSource.src = audioPath;
                audioPlayer.load();
                audioPlayer.play();
                
                // Actualizar botones de acción
                if (addToPlaylistBtn) {
                    addToPlaylistBtn.setAttribute('data-id', songId);
                    addToPlaylistBtn.setAttribute('data-type', songType);
                }
                
                if (addToCartBtn) {
                    addToCartBtn.setAttribute('data-id', songId);
                    addToCartBtn.setAttribute('data-type', songType);
                }
                
                // Mostrar modal
                playerModal.show();
            });
        });
        
        // Añadir a playlist
        if (addToPlaylistBtn) {
            addToPlaylistBtn.addEventListener('click', function() {
                const songId = this.getAttribute('data-id');
                const songType = this.getAttribute('data-type');
                
                // Aquí iría la lógica para mostrar un modal de selección de playlist
                alert('Funcionalidad de añadir a playlist en desarrollo');
            });
        }
        
        // Añadir al carrito
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const songId = this.getAttribute('data-id');
                const songType = this.getAttribute('data-type');
                
                // Enviar solicitud para añadir al carrito
                fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add&product_id=${songId}&product_type=${songType}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Producto añadido al carrito correctamente');
                    } else {
                        alert(data.message || 'Error al añadir al carrito');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al comunicarse con el servidor');
                });
            });
        }
    }
    
    // Botones de añadir al carrito
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    if (addToCartButtons.length > 0) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const productType = this.getAttribute('data-type');
                
                // Enviar solicitud para añadir al carrito
                fetch('../api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add&product_id=${productId}&product_type=${productType}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Producto añadido al carrito correctamente');
                    } else {
                        alert(data.message || 'Error al añadir al carrito');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al comunicarse con el servidor');
                });
            });
        });
    }
});
</script>

<?php include_once '../includes/footer.php'; ?>
