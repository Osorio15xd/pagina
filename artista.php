<?php
require_once 'conexion.php';
require_once 'encabezado.php';

if (!isset($_GET['id'])) {
    header("Location: artistas.php");
    exit();
}

$user_id = $_GET['id'];

// Obtener información del artista
$stmt = $conexion->prepare("
    SELECT a.id_artista, u.nombre, u.nombre_usuario, u.apellido1, u.apellido2, a.foto_path 
    FROM usuario u 
    JOIN artista a ON u.id_usuario = a.usuario 
    WHERE u.id_usuario = ? AND u.id_artista = 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result->fetch_assoc();

if (!$artist) {
    header("Location: artistas.php");
    exit();
}

// Obtener álbumes del artista
$stmt = $conexion->prepare("
    SELECT id_album, nombre_album, imagen_album_path, precio, descripcion 
    FROM album 
    WHERE id_artista = ?
");
$stmt->bind_param("i", $artist['id_artista']);
$stmt->execute();
$albums_result = $stmt->get_result();

if ($albums_result === false) {
    echo "Error en la consulta: " . $conexion->error;
} elseif ($albums_result->num_rows == 0) {
    echo "No se encontraron álbumes para este artista.";
}

// Obtener canciones del artista
$stmt = $conexion->prepare("
    SELECT c.id_cancion, c.nombre_cancion, c.cancion_path, a.nombre_album 
    FROM canciones c 
    LEFT JOIN album a ON c.id_album = a.id_album 
    WHERE c.id_artista = ?
");
$stmt->bind_param("i", $artist['id_artista']);
$stmt->execute();
$songs_result = $stmt->get_result();

// Obtener sencillos del artista
$stmt = $conexion->prepare("
    SELECT id_sencillo, nombre_sencillo, descripcion, imagen_sencillo_path, cancion_path, precio, fecha_lanzamiento 
    FROM sencillos 
    WHERE id_artista = ?
");
$stmt->bind_param("i", $artist['id_artista']);
$stmt->execute();
$singles_result = $stmt->get_result();

// Obtener 10 canciones aleatorias
// Obtener 10 canciones aleatorias
$sql = "
    (SELECT 
        c.id_cancion AS id, 
        c.nombre_cancion AS nombre, 
        c.cancion_path AS path, 
        'Canción' AS tipo, 
        a.nombre_album, 
        a.imagen_album_path AS imagen,
        u.nombre_usuario AS usuario
    FROM canciones c
    LEFT JOIN album a ON c.id_album = a.id_album
    LEFT JOIN artista ar ON c.id_artista = ar.id_artista
    LEFT JOIN usuario u ON ar.usuario = u.id_usuario
    WHERE c.id_artista = ?)
    UNION
    (SELECT 
        s.id_sencillo AS id, 
        s.nombre_sencillo AS nombre, 
        s.cancion_path AS path, 
        'Sencillo' AS tipo, 
        NULL AS nombre_album, 
        s.imagen_sencillo_path AS imagen,
        u.nombre_usuario AS usuario
    FROM sencillos s
    LEFT JOIN artista ar ON s.id_artista = ar.id_artista
    LEFT JOIN usuario u ON ar.usuario = u.id_usuario
    WHERE s.id_artista = ?)
    ORDER BY RAND() 
    LIMIT 10
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $artist['id_artista'], $artist['id_artista']);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
if ($result === false) {
    echo "Error en la consulta: " . $conexion->error;
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}




// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artist['nombre'] . ' ' . $artist['apellido1']); ?> - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="principal.css" rel="stylesheet">
 
   <style>
    .main-header {
            background-color: #2f4538;
            border-bottom: 1px solid var(--oscuro);
            padding: 1rem 0;
        }
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
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        
        .track-card {
            background: #082424;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s;
            max-width: 300px;
            margin: 0 auto;
        }
        .track-card:hover {
            transform: translateY(-5px);
        }
        .track-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-bottom: 2px solid #444;
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
        .track-description {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .track-date, .track-price {
            color: #888;
            font-size: 0.9rem;
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
        }

        .play-button {
            background-color: #1db954;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Reproductor */
        .player {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #181818;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.5);
        }

        .player img {
            height: 50px;
            border-radius: 5px;
        }

        .player-info {
            flex: 1;
            margin-left: 15px;
        }

        .player-title {
            font-size: 1rem;
            font-weight: bold;
        }

        .player-artist {
            font-size: 0.9rem;
            color: #b3b3b3;
        }

      /* Estilo para los botones del reproductor */
.player-controls button {
    background-color: #1db954; /* Verde similar a Spotify */
    border: none;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    margin: 0 5px;
}

.player-controls button:hover {
    background-color: #158a3d; /* Más oscuro al pasar el cursor */
}

.player-controls button:active {
    transform: scale(0.95); /* Pequeña reducción al hacer clic */
}
.control-button {
    background-color: #1db954;
    border: none;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    margin: 0 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}

.control-button:hover {
    background-color: #158a3d;
}

.material-symbols-outlined {
    font-size: 28px; /* Ajusta el tamaño del ícono */
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





    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="artist-header text-center">
            <?php if ($artist['foto_path']): ?>
                <img src="<?php echo htmlspecialchars($artist['foto_path']); ?>" alt="<?php echo htmlspecialchars($artist['nombre']); ?>" class="rounded-circle mb-3" style="max-width: 150px;">
            <?php else: ?>
                <img src="placeholder.jpg" alt="Placeholder" class="rounded-circle mb-3" style="max-width: 150px;">
            <?php endif; ?>
            <h1><?php echo htmlspecialchars($artist['nombre_usuario']); ?></h1>
            <h2><?php echo htmlspecialchars($artist['nombre'] . ' ' . $artist['apellido1'] . ' ' . $artist['apellido2']); ?></h2>
        </div>

        <h3 class="mt-4">Álbumes</h3>
<div class="row g-4">
    <?php while ($album = $albums_result->fetch_assoc()): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="track-card">
                <?php if (!empty($album['imagen_album_path'])): ?>
                    <img src="<?php echo htmlspecialchars($album['imagen_album_path']); ?>" alt="<?php echo htmlspecialchars($album['nombre_album']); ?>" class="track-image">
                <?php else: ?>
                    <div style="width: 100%; aspect-ratio: 1; background: #2a2a2a; display: flex; align-items: center; justify-content: center;">
                        <span>No image available</span>
                    </div>
                <?php endif; ?>
                <div class="track-info">
                    <h2 class="track-title"><?php echo htmlspecialchars($album['nombre_album']); ?></h2>
                    
                    <!-- Descripción truncada -->
                    <p class="track-description">
                        <?php echo htmlspecialchars(substr($album['descripcion'], 0, 100)) . '...'; ?>
                    </p>
                    
                    <!-- Precio del álbum -->
                    <p class="track-price">
                        Precio: $<?php echo number_format($album['precio'], 2); ?>
                    </p>
                    <a href="Album.php?id_album=<?php echo $album['id_album']; ?>" class="btn btn-primary w-100">Ver más</a>

                </div>
            </div>
        </div>
    <?php endwhile; ?>



      

    <h3 class="mt-4">Sencillos</h3>
<div class="row g-4">
    <?php while ($single = $singles_result->fetch_assoc()): ?>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="track-card">
                <?php if (!empty($single['imagen_sencillo_path'])): ?>
                    <img src="<?php echo htmlspecialchars($single['imagen_sencillo_path']); ?>" alt="<?php echo htmlspecialchars($single['nombre_sencillo']); ?>" class="track-image">
                <?php else: ?>
                    <div style="width: 100%; aspect-ratio: 1; background: #2a2a2a; display: flex; align-items: center; justify-content: center;">
                        <span>No image available</span>
                    </div>
                <?php endif; ?>
                <div class="track-info">
                    <h2 class="track-title"><?php echo htmlspecialchars($single['nombre_sencillo']); ?></h2>
                    
                    <!-- Descripción truncada -->
                    <p class="track-description">
                        <?php echo htmlspecialchars(substr($single['descripcion'], 0, 100)) . '...'; ?>
                    </p>

                    <!-- Precio del sencillo -->
                    <p class="track-price">
                        Precio: $<?php echo number_format($single['precio'], 2); ?>
                    </p>

                    
                  

                    <!-- Fecha de lanzamiento -->
                    <p class="track-release-date">
                        Fecha de lanzamiento: <?php echo date("d-m-Y", strtotime($single['fecha_lanzamiento'])); ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

    </div>
    <h1 class="text-center my-4">10 Canciones o Sencillos</h1>
    <div class="song-list">
        <?php foreach ($items as $item): ?>
            <div class="song-item">
                <div class="song-info">
                    <p class="song-title"><?php echo htmlspecialchars($item['nombre']); ?> (<?php echo htmlspecialchars($item['tipo']); ?>)</p>
                    <p>Artista: <?php echo htmlspecialchars($item['usuario']); ?></p>
                    <?php if (!empty($item['nombre_album'])): ?>
                        <p>Álbum: <?php echo htmlspecialchars($item['nombre_album']); ?></p>
                    <?php endif; ?>
                </div>
                <button class="play-button" onclick="playSong('<?php echo htmlspecialchars($item['path']); ?>', '<?php echo htmlspecialchars($item['nombre']); ?>', '<?php echo htmlspecialchars($item['usuario']); ?>', '<?php echo htmlspecialchars($item['imagen']); ?>')" > <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814z"/>
</svg></button>
                </div>
        <?php endforeach; ?>
    </div>
</div>

    </div>

    <!-- Reproductor -->
   
    <div class="player" id="audio-player">
    <img src="placeholder.jpg" alt="Album Art" id="album-art" style="height: 50px;">
    <div class="player-info">
        <div class="player-title" id="current-title">Selecciona una canción</div>
        <div class="player-artist" id="current-artist">Usuario</div>
    </div>
    <div class="player-controls">
        <button onclick="previousSong()" ><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-skip-backward-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-4.79-2.907L8.5 7.028V5.5a.5.5 0 0 0-.79-.407L5 7.028V5.5a.5.5 0 0 0-1 0v5a.5.5 0 0 0 1 0V8.972l2.71 1.935a.5.5 0 0 0 .79-.407V8.972l2.71 1.935A.5.5 0 0 0 12 10.5v-5a.5.5 0 0 0-.79-.407"/>
</svg> </button>
<button onclick="togglePlay()" id="play-toggle">
    <svg id="play-icon" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814z"/>
    </svg>
</button>

        <button onclick="nextSong()">
        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-skip-forward-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M4.79 5.093A.5.5 0 0 0 4 5.5v5a.5.5 0 0 0 .79.407L7.5 8.972V10.5a.5.5 0 0 0 .79.407L11 8.972V10.5a.5.5 0 0 0 1 0v-5a.5.5 0 0 0-1 0v1.528L8.29 5.093a.5.5 0 0 0-.79.407v1.528z"/>
</svg>
        </button>
    </div>
</div>





<script>
   let audio = new Audio();
let playlist = <?php echo json_encode($items); ?>;
let currentIndex = 0;

function playSong(src, title, user, image) {
    audio.src = src;
    audio.play();

    // Actualizar detalles del reproductor
    document.getElementById('current-title').innerText = title;
    document.getElementById('current-artist').innerText = user;
    document.getElementById('album-art').src = image || 'placeholder.jpg';

    // Actualizar índice actual en la lista
    currentIndex = playlist.findIndex(item => item.path === src);
}

function togglePlay() {
    const playIcon = document.getElementById('play-icon');
    
    if (audio.paused) {
        audio.play();
        
        // Cambia el ícono a pausa
        playIcon.outerHTML = `
            <svg id="play-icon" xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-pause-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0"/>
                <path d="M5 6.25a1.25 1.25 0 1 1 2.5 0v3.5a1.25 1.25 0 1 1-2.5 0zm3.5 0a1.25 1.25 0 1 1 2.5 0v3.5a1.25 1.25 0 1 1-2.5 0z"/>
            </svg>`;
    } else {
        audio.pause();
        
        // Cambia el ícono a reproducción
        playIcon.outerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-pause-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.25 5C5.56 5 5 5.56 5 6.25v3.5a1.25 1.25 0 1 0 2.5 0v-3.5C7.5 5.56 6.94 5 6.25 5m3.5 0c-.69 0-1.25.56-1.25 1.25v3.5a1.25 1.25 0 1 0 2.5 0v-3.5C11 5.56 10.44 5 9.75 5"/>
</svg>`;
    }
}



function nextSong() {
    currentIndex = (currentIndex + 1) % playlist.length;
    let next = playlist[currentIndex];
    playSong(next.path, next.nombre, next.usuario, next.imagen);
}

function previousSong() {
    currentIndex = (currentIndex - 1 + playlist.length) % playlist.length;
    let previous = playlist[currentIndex];
    playSong(previous.path, previous.nombre, previous.usuario, previous.imagen);
}

</script>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
   
</body>
<footer>
<div id="footer-placeholder"></div>
<script>
        // Cargar el footer
        fetch('footer.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('footer-placeholder').innerHTML = data;
            })
            .catch(error => console.error('Error al cargar el footer:', error));
    </script>
</footer>
</html>