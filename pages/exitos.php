<?php
session_start();
require_once '../config/db_connect.php';

// Obtener los éxitos (canciones más populares)
try {
    // Obtener canciones más compradas
    $stmt = $pdo->query("
        SELECT c.id_cancion as id, c.nombre_cancion as titulo, a.nombre_album as album, 
               ar.id_artista, u.nombre_usuario as artista, g.nombre_genero as genero,
               a.imagen_album_path as imagen, c.cancion_path as audio_path,
               COUNT(co.id_compra) as compras, 'cancion' as tipo
        FROM canciones c
        JOIN album a ON c.id_album = a.id_album
        JOIN artista ar ON c.id_artista = ar.id_artista
        JOIN usuario u ON ar.usuario = u.id_usuario
        JOIN genero g ON a.id_genero = g.id_genero
        LEFT JOIN compras co ON co.id_producto = c.id_cancion AND co.tipo_producto = 'cancion'
        GROUP BY c.id_cancion
        ORDER BY compras DESC, c.id_cancion DESC
        LIMIT 10
    ");
    $canciones_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener sencillos más comprados
    $stmt = $pdo->query("
        SELECT s.id_sencillo as id, s.nombre_sencillo as titulo, NULL as album,
               ar.id_artista, u.nombre_usuario as artista, g.nombre_genero as genero,
               s.imagen_sencillo_path as imagen, s.cancion_path as audio_path,
               COUNT(co.id_compra) as compras, 'sencillo' as tipo
        FROM sencillos s
        JOIN artista ar ON s.id_artista = ar.id_artista
        JOIN usuario u ON ar.usuario = u.id_usuario
        JOIN genero g ON s.id_genero = g.id_genero
        LEFT JOIN compras co ON co.id_producto = s.id_sencillo AND co.tipo_producto = 'sencillo'
        GROUP BY s.id_sencillo
        ORDER BY compras DESC, s.id_sencillo DESC
        LIMIT 10
    ");
    $sencillos_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener álbumes más comprados
    $stmt = $pdo->query("
        SELECT a.id_album as id, a.nombre_album as titulo,
               ar.id_artista, u.nombre_usuario as artista, g.nombre_genero as genero,
               a.imagen_album_path as imagen, a.precio,
               COUNT(co.id_compra) as compras, 'album' as tipo
        FROM album a
        JOIN artista ar ON a.id_artista = ar.id_artista
        JOIN usuario u ON ar.usuario = u.id_usuario
        JOIN genero g ON a.id_genero = g.id_genero
        LEFT JOIN compras co ON co.id_producto = a.id_album AND co.tipo_producto = 'album'
        GROUP BY a.id_album
        ORDER BY compras DESC, a.id_album DESC
        LIMIT 10
    ");
    $albums_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener artistas más populares
    $stmt = $pdo->query("
        SELECT ar.id_artista as id, u.nombre_usuario as nombre, ar.foto_path as imagen,
               COUNT(DISTINCT co.id_compra) as compras
        FROM artista ar
        JOIN usuario u ON ar.usuario = u.id_usuario
        LEFT JOIN album a ON a.id_artista = ar.id_artista
        LEFT JOIN sencillos s ON s.id_artista = ar.id_artista
        LEFT JOIN compras co ON (co.id_producto = a.id_album AND co.tipo_producto = 'album') 
                             OR (co.id_producto = s.id_sencillo AND co.tipo_producto = 'sencillo')
        GROUP BY ar.id_artista
        ORDER BY compras DESC, ar.popularidad DESC
        LIMIT 10
    ");
    $artistas_populares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al cargar los éxitos: " . $e->getMessage() . "</div>";
    $canciones_populares = [];
    $sencillos_populares = [];
    $albums_populares = [];
    $artistas_populares = [];
}

// Combinar canciones y sencillos para el top general
$top_canciones = array_merge($canciones_populares, $sencillos_populares);
usort($top_canciones, function($a, $b) {
    return $b['compras'] - $a['compras'];
});
$top_canciones = array_slice($top_canciones, 0, 10);

// Incluir el encabezado
include_once '../includes/header.php';
?>

<div class="container">
    <h1 class="my-4 text-center">Los Éxitos de BassCulture</h1>
    
    <!-- Top 10 General -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Top 10 Canciones</h2>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Filtrar por
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item filter-item" href="#" data-filter="all">Todos los géneros</a></li>
                    <?php 
                    $stmt = $pdo->query("SELECT id_genero, nombre_genero FROM genero ORDER BY nombre_genero");
                    $generos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($generos as $genero): 
                    ?>
                    <li><a class="dropdown-item filter-item" href="#" data-filter="<?= htmlspecialchars($genero['nombre_genero']) ?>"><?= htmlspecialchars($genero['nombre_genero']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Artista</th>
                        <th>Álbum</th>
                        <th>Género</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_canciones as $index => $cancion): ?>
                    <tr class="song-item" data-genre="<?= htmlspecialchars($cancion['genero']) ?>">
                        <td><?= $index + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($cancion['imagen']) ?>" alt="<?= htmlspecialchars($cancion['titulo']) ?>" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <span><?= htmlspecialchars($cancion['titulo']) ?></span>
                            </div>
                        </td>
                        <td><a href="artista.php?id=<?= $cancion['id_artista'] ?>"><?= htmlspecialchars($cancion['artista']) ?></a></td>
                        <td><?= $cancion['album'] ? htmlspecialchars($cancion['album']) : 'Sencillo' ?></td>
                        <td><?= htmlspecialchars($cancion['genero']) ?></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary play-btn" data-audio="<?= htmlspecialchars($cancion['audio_path']) ?>" data-title="<?= htmlspecialchars($cancion['titulo']) ?>" data-artist="<?= htmlspecialchars($cancion['artista']) ?>" data-cover="<?= htmlspecialchars($cancion['imagen']) ?>">
                                    <i class="fas fa-play"></i>
                                </button>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-sm btn-outline-primary add-to-playlist-btn" data-id="<?= $cancion['id'] ?>" data-type="<?= $cancion['tipo'] ?>">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger add-to-cart-btn" data-id="<?= $cancion['id'] ?>" data-type="<?= $cancion['tipo'] ?>">
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
    </section>
    
    <!-- Álbumes Populares -->
    <section class="mb-5">
        <h2 class="mb-4">Álbumes Populares</h2>
        <div class="row">
            <?php foreach ($albums_populares as $album): ?>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($album['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($album['titulo']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($album['titulo']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($album['artista']) ?></p>
                        <p class="card-text"><small class="text-muted"><?= htmlspecialchars($album['genero']) ?></small></p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="album.php?id=<?= $album['id'] ?>" class="btn btn-sm btn-primary">Ver Álbum</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="btn btn-sm btn-outline-danger add-to-cart-btn" data-id="<?= $album['id'] ?>" data-type="album">
                            <i class="fas fa-shopping-cart"></i> $<?= number_format($album['precio'], 2) ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Artistas Populares -->
    <section class="mb-5">
        <h2 class="mb-4">Artistas Populares</h2>
        <div class="row">
            <?php foreach ($artistas_populares as $artista): ?>
            <div class="col-md-2 col-sm-4 col-6 mb-4">
                <div class="card text-center h-100">
                    <img src="<?= htmlspecialchars($artista['imagen']) ?>" class="card-img-top rounded-circle mx-auto mt-3" style="width: 120px; height: 120px; object-fit: cover;" alt="<?= htmlspecialchars($artista['nombre']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($artista['nombre']) ?></h5>
                        <a href="artista.php?id=<?= $artista['id'] ?>" class="btn btn-sm btn-primary">Ver Artista</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
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
                <h  src="/placeholder.svg" alt="Album Cover" class="img-fluid mb-3" style="max-height: 200px;">
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
    // Filtrado por género
    const filterItems = document.querySelectorAll('.filter-item');
    const songItems = document.querySelectorAll('.song-item');
    
    filterItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            
            songItems.forEach(song => {
                if (filter === 'all' || song.getAttribute('data-genre') === filter) {
                    song.style.display = '';
                } else {
                    song.style.display = 'none';
                }
            });
            
            // Actualizar texto del botón de filtro
            document.getElementById('dropdownMenuButton').textContent = 'Filtrar por: ' + (filter === 'all' ? 'Todos los géneros' : filter);
        });
    });
    
    // Reproducción de canciones
    const playButtons = document.querySelectorAll('.play-btn');
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
            const songId = this.closest('tr').querySelector('.add-to-playlist-btn').getAttribute('data-id');
            const songType = this.closest('tr').querySelector('.add-to-playlist-btn').getAttribute('data-type');
            
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
    
    // Botones de añadir al carrito en la tabla
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
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
});
</script>

<?php include_once '../includes/footer.php'; ?>
