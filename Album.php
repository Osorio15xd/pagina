<?php
require_once 'conexion.php';
require_once 'encabezado.php';

// Redirige si no existe el parámetro 'id' en la URL
if (!isset($_GET['id_album'])) {
    header("Location: index.php"); // Redirige si no existe el parámetro
    exit();
}

$album_id = $_GET['id_album']; // Cambié 'id' por 'id_album'

// Ahora puedes realizar la consulta

// Obtener información del álbum
$stmt = $conexion->prepare("
    SELECT a.id_album, a.nombre_album, a.descripcion, a.imagen_album_path, a.fecha_lanzamiento, a.precio,
           ar.id_artista, u.nombre_usuario, u.nombre, u.apellido1, u.apellido2,
           g.nombre_genero
    FROM album a
    JOIN artista ar ON a.id_artista = ar.id_artista
    JOIN usuario u ON ar.usuario = u.id_usuario
    JOIN genero g ON a.id_genero = g.id_genero
    WHERE a.id_album = ?
");
$stmt->bind_param("i", $album_id); // Bind del parámetro
$stmt->execute();

// Usar get_result() para obtener los resultados y luego fetch_assoc()
$result = $stmt->get_result();
$album = $result->fetch_assoc();

if (!$album) {
    header("Location: index.php"); // Redirige si no se encuentra el álbum
    exit();
}

// Obtener canciones del álbum
$stmt = $conexion->prepare("
    SELECT id_cancion, nombre_cancion, cancion_path, precio
    FROM canciones
    WHERE id_album = ?
");
$stmt->bind_param("i", $album_id); // Bind del parámetro
$stmt->execute();

// Obtener los resultados para las canciones
$result = $stmt->get_result();
$songs = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($album['nombre_album']); ?> - BassCulture</title>
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
        
        .album-card {
            background: #082424;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        
        .album-card:hover {
            transform: translateY(-5px);
        }
        
        .album-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-bottom: 2px solid #444;
        }
        
        .album-info {
            padding: 1rem;
        }
        
        .album-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .album-description {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .album-details {
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
        
        .player-controls button {
            background-color: #1db954;
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }
        
        .player-controls button:hover {
            background-color: #158a3d;
        }
        
        .player-controls button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="album-card">
                    <?php if (!empty($album['imagen_album_path'])): ?>
                        <img src="<?php echo htmlspecialchars($album['imagen_album_path']); ?>" alt="<?php echo htmlspecialchars($album['nombre_album']); ?>" class="album-image">
                    <?php else: ?>
                        <div style="width: 100%; aspect-ratio: 1; background: #2a2a2a; display: flex; align-items: center; justify-content: center;">
                            <span>No image available</span>
                        </div>
                    <?php endif; ?>
                    <div class="album-info">
                        <h1 class="album-title"><?php echo htmlspecialchars($album['nombre_album']); ?></h1>
                        <p class="album-description"><?php echo htmlspecialchars($album['descripcion']); ?></p>
                        <p class="album-details">
                            Artista: <?php echo htmlspecialchars($album['nombre_usuario']); ?><br>
                            Género: <?php echo htmlspecialchars($album['nombre_genero']); ?><br>
                            Fecha de lanzamiento: <?php echo date("d-m-Y", strtotime($album['fecha_lanzamiento'])); ?><br>
                            Precio: $<?php echo number_format($album['precio'], 2); ?>
                        </p>
                        <button class="btn btn-success mt-3" onclick="agregarAlCarrito('album', <?php echo $album['id_album']; ?>)">Agregar al carrito</button>
                        <button class="btn btn-primary mt-3" onclick="comprarDirecto('album', <?php echo $album['id_album']; ?>)">Compra directa</button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h2 class="mb-4">Canciones</h2>
                <div class="song-list">
                    <?php foreach ($songs as $song): ?>
                        <div class="song-item">
                            <div class="song-info">
                                <p class="song-title"><?php echo htmlspecialchars($song['nombre_cancion']); ?></p>
                                <p>Precio: $<?php echo number_format($song['precio'], 2); ?></p>
                            </div>
                            <div>
                                <button class="play-button" onclick="playSong('<?php echo htmlspecialchars($song['cancion_path']); ?>', '<?php echo htmlspecialchars($song['nombre_cancion']); ?>', '<?php echo htmlspecialchars($album['nombre_usuario']); ?>', '<?php echo htmlspecialchars($album['imagen_album_path']); ?>')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/>
                                    </svg>
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="agregarAlCarrito('cancion', <?php echo $song['id_cancion']; ?>)">Agregar al carrito</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="player" id="audio-player">
        <img src="placeholder.jpg" alt="Album Art" id="album-art" style="height: 50px;">
        <div class="player-info">
            <div class="player-title" id="current-title">Selecciona una canción</div>
            <div class="player-artist" id="current-artist">Artista</div>
        </div>
        <div class="player-controls">
            <button onclick="previousSong()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-skip-backward-fill" viewBox="0 0 16 16">
                    <path d="M.5 3.5A.5.5 0 0 1 1 4v3.248l6.267-3.636c.52-.302 1.233.043 1.233.696v2.94l6.267-3.636c.52-.302 1.233.043 1.233.696v7.384c0 .653-.713.998-1.233.696L8.5 8.752v2.94c0 .653-.713.998-1.233.696L1 8.752V12a.5.5 0 0 1-1 0V4a.5.5 0 0 1 .5-.5z"/>
                </svg>
            </button>
            <button onclick="togglePlay()" id="play-toggle">
                <svg id="play-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/>
                </svg>
            </button>
            <button onclick="nextSong()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-skip-forward-fill" viewBox="0 0 16 16">
                    <path d="M15.5 3.5a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V8.752l-6.267 3.636c-.52.302-1.233-.043-1.233-.696v-2.94l-6.267 3.636C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696L7.5 7.248v-2.94c0-.653.713-.998 1.233-.696L15 7.248V4a.5.5 0 0 1 .5-.5z"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        let audio = new Audio();
        let playlist = <?php echo json_encode($songs); ?>;
        let currentIndex = 0;

        function playSong(src, title, artist, image) {
            audio.src = src;
            audio.play();

            document.getElementById('current-title').innerText = title;
            document.getElementById('current-artist').innerText = artist;
            document.getElementById('album-art').src = image || 'placeholder.jpg';

            currentIndex = playlist.findIndex(item => item.cancion_path === src);
        }

        function togglePlay() {
            const playIcon = document.getElementById('play-icon');
            
            if (audio.paused) {
                audio.play();
                playIcon.innerHTML = `
                    <path d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5zm5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5z"/>
                `;
            } else {
                audio.pause();
                playIcon.innerHTML = `
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/>
                `;
            }
        }

        function nextSong() {
            currentIndex = (currentIndex + 1) % playlist.length;
            let next = playlist[currentIndex];
            playSong(next.cancion_path, next.nombre_cancion, '<?php echo htmlspecialchars($album['nombre_usuario']); ?>', '<?php echo htmlspecialchars($album['imagen_album_path']); ?>');
        }

        function previousSong() {
            currentIndex = (currentIndex - 1 + playlist.length) % playlist.length;
            let previous = playlist[currentIndex];
            playSong(previous.cancion_path, previous.nombre_cancion, '<?php echo htmlspecialchars($album['nombre_usuario']); ?>', '<?php echo htmlspecialchars($album['imagen_album_path']); ?>');
        }

        function agregarAlCarrito(tipo, id) {
            fetch('agregar_al_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id_producto=${id}&tipo_producto=${tipo}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }


        function comprarDirecto(tipo, id) {
            // Aquí iría la lógica para la compra directa
            console.log(`Comprando ${tipo} con ID ${id}`);
            alert(`Redirigiendo a la página de pago para ${tipo}`);
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