<?php
session_start();
require_once 'conexion.php';
require_once 'encabezado.php';


// Consulta para obtener los álbumes del género 'Electrónica' (id_genero = 1)
$query = "SELECT id_album, nombre_album, descripcion, imagen_album_path, fecha_lanzamiento, precio 
          FROM album ";
$result = $conexion->query($query);
// Función para obtener los top 6 álbumes de la semana
function getTopAlbums($conexion)
{
    $query = "SELECT a.id_album, a.nombre_album, a.imagen_album_path, a.descripcion, a.precio, u.nombre_usuario 
              FROM album a 
              JOIN artista ar ON a.id_artista = ar.id_artista 
              JOIN usuario u ON ar.usuario = u.id_usuario 
              ORDER BY RAND() 
              LIMIT 6";
    $result = $conexion->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener los top 6 sencillos de la semana
function getTopSingles($conexion)
{
    $query = "SELECT id_sencillo, nombre_sencillo, imagen_sencillo_path, descripcion, precio, u.nombre_usuario 
              FROM sencillos s
              JOIN artista ar ON s.id_artista = ar.id_artista
              JOIN usuario u ON ar.usuario = u.id_usuario
              ORDER BY RAND() 
              LIMIT 6";
    $result = $conexion->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener las mejores canciones de la semana
function getTopSongs($conexion)
{
    $query = "SELECT c.id_cancion, c.nombre_cancion, a.imagen_album_path, a.descripcion, c.precio, u.nombre_usuario, 'album' as tipo
              FROM canciones c
              JOIN album a ON c.id_album = a.id_album
              JOIN artista ar ON a.id_artista = ar.id_artista
              JOIN usuario u ON ar.usuario = u.id_usuario
              UNION ALL
              SELECT s.id_sencillo, s.nombre_sencillo, s.imagen_sencillo_path, s.descripcion, s.precio, u.nombre_usuario, 'sencillo' as tipo
              FROM sencillos s
              JOIN artista ar ON s.id_artista = ar.id_artista
              JOIN usuario u ON ar.usuario = u.id_usuario
              ORDER BY RAND()
              LIMIT 6";
    $result = $conexion->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función para obtener 5 recomendaciones aleatorias
function getRecommendations($conexion)
{
    $query = "SELECT c.id_cancion as id, c.nombre_cancion as nombre, c.cancion_path, a.imagen_album_path as imagen, a.descripcion, a.precio, u.nombre_usuario, 'album' as tipo
              FROM canciones c
              JOIN album a ON c.id_album = a.id_album
              JOIN artista ar ON a.id_artista = ar.id_artista
              JOIN usuario u ON ar.usuario = u.id_usuario
              UNION ALL
              SELECT s.id_sencillo, s.nombre_sencillo, s.cancion_path, s.imagen_sencillo_path, s.descripcion, s.precio, u.nombre_usuario, 'sencillo' as tipo
              FROM sencillos s
              JOIN artista ar ON s.id_artista = ar.id_artista
              JOIN usuario u ON ar.usuario = u.id_usuario
              ORDER BY RAND()
              LIMIT 5";
    $result = $conexion->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$topAlbums = getTopAlbums($conexion);
$topSingles = getTopSingles($conexion);
$topSongs = getTopSongs($conexion);
$recommendations = getRecommendations($conexion);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BassCulture - Inicio</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="principal.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .main-header {
            background-color: #2f4538;
            border-bottom: 1px solid var(--oscuro);
            padding: 1rem 0;
        }

        .carousel-item img {
            object-fit: cover;
            /* Ajusta la imagen para cubrir el contenedor */
            height: 50vh;
            /* Aumenta la altura del carrusel */
            width: 100%;
            /* Asegura que la imagen ocupe todo el ancho */
        }

        .carousel {
            max-width: 1400px;
            border-radius: 10px;
            margin: 0 auto;
            height: 50vh;
            /* Ajusta la altura del contenedor del carrusel */
        }

        .track-card {
            background: #082424;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s;
            height: 100%;
        }

        .track-card:hover {
            transform: translateY(-5px);
        }

        .track-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
        }

        .track-info {
            padding: 1rem;
        }

        .track-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .track-artist {
            color: #ccc;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .track-description {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .track-price {
            color: #1db954;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .section-title {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .player {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #181818;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            z-index: 1000;
        }

        .player img {
            height: 60px;
            width: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 1rem;
        }

        .player-info {
            flex: 1;
        }

        .player-title {
            color: #fff;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .player-artist {
            color: #aaa;
            font-size: 0.9rem;
        }

        .player-controls button {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            margin: 0 0.5rem;
        }

        .song-list {
            margin: 20px 0;
        }

        .song-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #333;
        }

        .song-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #fff;
        }

        .play-button {
            background-color: #1db954;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #2D4343;
            border: none;
            padding: 10px;
            font-size: 1rem;
            font-weight: bold;
            text-align: center;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #3B5555;
        }

        /* Style dropdown menus */
        .dropdown-menu {
            background-color: #222;
            border: none;
            border-radius: 4px;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            color: white;
            padding: 0.5rem 1rem;
        }

        .dropdown-item:hover {
            background-color: #444;
            color: white;
        }
    </style>

</head>

<body>
    <div class="container-fluid mt-5">
        <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="travis.jpg" class="d-block w-100" alt="Travis Scott Concert">
                </div>
                <div class="carousel-item">
                    <img src="edc.jpg" class="d-block w-100" alt="Electronic Music Festival">
                </div>
                <div class="carousel-item">
                    <img src="coachela.png" class="d-block w-100" alt="Coachella">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>


        <h2 class="section-title mt-5 mb-4">Top 6 Álbumes de la Semana</h2>
        <div class="row">
            <?php foreach ($topAlbums as $album): ?>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="track-card">
                        <img src="<?php echo htmlspecialchars($album['imagen_album_path']); ?>" alt="<?php echo htmlspecialchars($album['nombre_album']); ?>" class="track-image">
                        <div class="track-info">
                            <div class="track-title"><?php echo htmlspecialchars($album['nombre_album']); ?></div>
                            <div class="track-artist"><?php echo htmlspecialchars($album['nombre_usuario']); ?></div>
                            <div class="track-description"><?php echo htmlspecialchars(substr($album['descripcion'], 0, 100)) . '...'; ?></div>
                            <div class="track-price">$<?php echo number_format($album['precio'], 2); ?></div>
                            <!-- Botón Ver más -->
                            <a href="Album.php?id_album=<?php echo $album['id_album']; ?>" class="btn btn-primary w-100">Ver más</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title mt-5 mb-4">Top 6 Sencillos de la Semana</h2>
        <div class="row">
            <?php foreach ($topSingles as $single): ?>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="track-card">
                        <img src="<?php echo htmlspecialchars($single['imagen_sencillo_path']); ?>" alt="<?php echo htmlspecialchars($single['nombre_sencillo']); ?>" class="track-image">
                        <div class="track-info">
                            <div class="track-title"><?php echo htmlspecialchars($single['nombre_sencillo']); ?></div>
                            <div class="track-artist"><?php echo htmlspecialchars($single['nombre_usuario']); ?></div>
                            <div class="track-description"><?php echo htmlspecialchars(substr($single['descripcion'], 0, 100)) . '...'; ?></div>
                            <div class="track-price">$<?php echo number_format($single['precio'], 2); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title mt-5 mb-4">Mejores Canciones de la Semana</h2>
        <div class="row">
            <?php foreach ($topSongs as $song): ?>
                <div class="col-md-4 col-lg-2 mb-4">
                    <div class="track-card">
                        <img src="<?php echo htmlspecialchars($song['imagen_album_path']); ?>" alt="<?php echo htmlspecialchars($song['nombre_cancion']); ?>" class="track-image">
                        <div class="track-info">
                            <div class="track-title"><?php echo htmlspecialchars($song['nombre_cancion']); ?></div>
                            <div class="track-artist"><?php echo htmlspecialchars($song['nombre_usuario']); ?></div>
                            <div class="track-description"><?php echo htmlspecialchars(substr($song['descripcion'], 0, 100)) . '...'; ?></div>
                            <div class="track-price">$<?php echo number_format($song['precio'], 2); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 class="section-title mt-5 mb-4">Recomendaciones para ti</h2>
        <div id="recommendations" class="row">
            <?php foreach ($recommendations as $track): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="track-card">
                        <img src="<?php echo htmlspecialchars($track['imagen']); ?>" alt="<?php echo htmlspecialchars($track['nombre']); ?>" class="track-image">
                        <div class="track-info">
                            <h3 class="track-title"><?php echo htmlspecialchars($track['nombre']); ?></h3>
                            <p class="track-artist"><?php echo htmlspecialchars($track['nombre_usuario']); ?></p>
                            <p class="track-type"><?php echo ucfirst($track['tipo']); ?></p>
                            <button class="btn btn-primary play-button" onclick="playSong('<?php echo htmlspecialchars($track['cancion_path']); ?>', '<?php echo htmlspecialchars($track['nombre']); ?>', '<?php echo htmlspecialchars($track['nombre_usuario']); ?>', '<?php echo htmlspecialchars($track['imagen']); ?>')">
                                <i class="fas fa-play"></i> Reproducir
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Botón flotante para abrir el chatbot -->
        <button id="chatbot-toggle" style="position: fixed; bottom: 20px; right: 20px; background-color: #1db954; color: white; border: none; border-radius: 50%; width: 70px; height: 70px; cursor: pointer; z-index: 1000; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); transition: background-color 0.3s, transform 0.3s;">
            <img src="botn mas cerca.png" alt="Chat" style="width: 70px; height: 70px;"> <!-- Ajusta la ruta y el tamaño -->
        </button>

        <!-- Iframe del chatbot, inicialmente oculto -->
        <div id="chatbot-container" style="display: none; position: fixed; bottom: 80px; right: 20px; width: 350px; height: 450px; border-radius: 15px; overflow: hidden; z-index: 1000; transition: transform 0.3s; transform: translateY(20px); background-color: rgba(59, 90, 74, 0.9);"> <!-- Color de fondo y transparencia -->
            <iframe src="chatbot.html" width="100%" height="100%" style="border: none; border-radius: 15px;"></iframe> <!-- Bordes redondeados -->
        </div>


        <div class="player" id="audio-player" style="display: none;">
            <img src="placeholder.jpg" alt="Album Art" id="album-art">
            <div class="player-info">
                <div class="player-title" id="current-title">Selecciona una canción</div>
                <div class="player-artist" id="current-artist">Usuario</div>
            </div>
            <div class="player-controls">
                <button onclick="previousSong()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-skip-backward-fill" viewBox="0 0 16 16">
                        <path d="M.5 3.5A.5.5 0 0 0 0 4v8a.5.5 0 0 0 1 0V8.753l6.267 3.636c.54.313 1.233-.066 1.233-.697v-2.94l6.267 3.636c.54.314 1.233-.065 1.233-.696V4.308c0-.63-.693-1.01-1.233-.696L8.5 7.248v-2.94c0-.63-.692-1.01-1.233-.696L1 7.248V4a.5.5 0 0 0-.5-.5z" />
                    </svg>
                </button>
                <button onclick="togglePlay()" id="play-pause-btn">
                    <svg id="play-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-play-fill" viewBox="0 0 16 16">
                        <path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z" />
                    </svg>
                </button>
                <button onclick="nextSong()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-skip-forward-fill" viewBox="0 0 16 16">
                        <path d="M15.5 3.5a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V8.753l-6.267 3.636c-.54.313-1.233-.066-1.233-.697v-2.94l-6.267 3.636C.693 12.703 0 12.324 0 11.693V4.308c0-.63.693-1.01 1.233-.696L7.5 7.248v-2.94c0-.63.693-1.01 1.233-.696L15 7.248V4a.5.5 0 0 1 .5-.5z" />
                    </svg>
                </button>

            </div>
        </div>
    </div>


</body>
<footer>
    <div id="footer-placeholder"></div>
</footer>
<script>
    // Función para mostrar/ocultar el chatbot
    document.getElementById('chatbot-toggle').onclick = function() {
        var chatbotContainer = document.getElementById('chatbot-container');
        var chatbotIframe = chatbotContainer.querySelector('iframe');

        if (chatbotContainer.style.display === 'none') {
            chatbotContainer.style.display = 'block'; // Muestra el chatbot
            chatbotContainer.style.transform = 'translateY(0)'; // Desplaza el chatbot hacia arriba
        } else {
            chatbotContainer.style.transform = 'translateY(20px)'; // Desplaza el chatbot hacia abajo
            setTimeout(function() {
                chatbotContainer.style.display = 'none'; // Oculta el chatbot después de la animación
            }, 300); // Espera 300ms para que la animación termine
            chatbotIframe.src = ''; // Reinicia la conversación al ocultar
            chatbotIframe.src = 'chatbot.html'; // Vuelve a cargar el chatbot
        }
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    let audio = new Audio();
    let playlist = <?php echo json_encode($recommendations); ?>;
    let currentIndex = 0;

    function playSong(src, title, artist, image) {
        audio.src = src;
        audio.play();
        updatePlayerInfo(title, artist, image);
        document.getElementById('audio-player').style.display = 'flex';
        updatePlayPauseButton();
    }

    function updatePlayerInfo(title, artist, image) {
        document.getElementById('current-title').innerText = title;
        document.getElementById('current-artist').innerText = artist;
        document.getElementById('album-art').src = image;
    }

    function togglePlay() {
        if (audio.paused) {
            audio.play();
        } else {
            audio.pause();
        }
        updatePlayPauseButton();
    }

    function updatePlayPauseButton() {
        const playIcon = document.getElementById('play-icon');
        if (audio.paused) {
            playIcon.innerHTML = '<path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>';
        } else {
            playIcon.innerHTML = '<path d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5zm5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5z"/>';
        }
    }

    function nextSong() {
        currentIndex = (currentIndex + 1) % playlist.length;
        playSong(playlist[currentIndex].cancion_path, playlist[currentIndex].nombre, playlist[currentIndex].nombre_usuario, playlist[currentIndex].imagen);
    }

    function previousSong() {
        currentIndex = (currentIndex - 1 + playlist.length) % playlist.length;
        playSong(playlist[currentIndex].cancion_path, playlist[currentIndex].nombre, playlist[currentIndex].nombre_usuario, playlist[currentIndex].imagen);
    }

    // Actualizar recomendaciones cada semana
    function updateRecommendations() {
        fetch('get_recommendations.php')
            .then(response => response.json())
            .then(data => {
                playlist = data;
                updateRecommendationsList();
            })
            .catch(error => console.error('Error:', error));
    }

    function updateRecommendationsList() {
        const recommendationsContainer = document.getElementById('recommendations');
        recommendationsContainer.innerHTML = '';

        playlist.forEach((track, index) => {
            const songItem = document.createElement('div');
            songItem.className = 'song-item';
            songItem.innerHTML = `
                    <div class="song-info">
                        <p class="song-title">${track.nombre} (${track.tipo})</p>
                        <p>Artista: ${track.nombre_usuario}</p>
                    </div>
                    <button class="play-button" onclick="playSong('${track.cancion_path}', '${track.nombre}', '${track.nombre_usuario}', '${track.imagen}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.0935 0 0 0 0-.814l-3.5-2.5z"/>
                        </svg>
                    </button>
                `;
            recommendationsContainer.appendChild(songItem);
        });
    }

    // Actualizar recomendaciones cada semana (604800000 ms = 1 semana)
    setInterval(updateRecommendations, 604800000);

    // Inicializar las recomendaciones al cargar la página
    updateRecommendations();
</script>
<script>
    // Cargar el footer
    fetch('footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-placeholder').innerHTML = data;
        })
        .catch(error => console.error('Error al cargar el footer:', error));
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</html>