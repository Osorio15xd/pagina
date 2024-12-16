<?php
require_once 'conexion.php';
require_once 'encabezado.php';

// Ejemplo de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Aquí deberías validar las credenciales del usuario
    // Si las credenciales son válidas:
    $_SESSION['user_id'] = $user_id; // Establece el ID del usuario en la sesión
    // Redirige o muestra un mensaje de éxito
}
// Activar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se está agregando un sencillo al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_sencillo'])) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para agregar productos al carrito.']);
        exit;
    }

    $id_sencillo = $_POST['id_sencillo'];
    $id_usuario = $_SESSION['user_id'];

    // Verificar si el sencillo ya está en el carrito
    $query = "SELECT * FROM carrito WHERE id_usuario = ? AND id_producto = ? AND tipo_producto = 'sencillo'";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $id_usuario, $id_sencillo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El sencillo ya está en el carrito.']);
        exit;
    }

    // Agregar el sencillo al carrito
    $query = "INSERT INTO carrito (id_usuario, id_producto, tipo_producto, cantidad) VALUES (?, ?, 'sencillo', 1)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("ii", $id_usuario, $id_sencillo);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Sencillo agregado al carrito correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el sencillo al carrito.']);
    }
    exit;
}

// Consulta para obtener los datos de la tabla 'sencillos'
$query = "SELECT id_sencillo, nombre_sencillo, descripcion, imagen_sencillo_path, cancion_path, fecha_lanzamiento, precio FROM sencillos";
$result = $conexion->query($query);

if ($result->num_rows === 0) {
    die("No se encontraron sencillos en la tabla.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sencillos - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
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
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .track-card {
            background: #1a1a1a;
            border-radius: 8px;
            margin-bottom: 20px;
            max-width: 300px;
            margin: 0 auto;
            overflow: hidden;
            transition: transform 0.2s;
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
        .audio-player {
            margin-top: 1rem;
        }
        .custom-audio-player {
            width: 100%;
            height: 40px;
            background: #333;
            display: flex;
            align-items: center;
            padding: 0 10px;
        }
        .play-pause-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        .progress-bar {
            flex-grow: 1;
            height: 5 ```php
px;
            background: #555;
            margin: 0 10px;
            position: relative;
        }
        .progress {
            height: 100%;
            background: #ff5722;
            width: 0;
        }
        .btn-add-to-cart {
            margin-top: 10px;
            width: 100%;
            background-color: #2D4343;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Lista de Sencillos</h1>
        <div id="message-container" class="text-center mt-3"></div> <!-- Contenedor para mensajes -->        <div class="row g-4">
            <?php 
            while ($row = $result->fetch_assoc()): 
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="track-card">
                        <img src="<?php echo htmlspecialchars($row['imagen_sencillo_path']); ?>" alt="Portada" class="track-image">
                        <div class="track-info">
                            <h2 class="track-title"><?php echo htmlspecialchars($row['nombre_sencillo']); ?></h2>
                            <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                            <p><strong>Precio:</strong> $<?php echo htmlspecialchars($row['precio']); ?></p>

                            <?php if (!empty($row['cancion_path'])): ?>
                                <div class="audio-player">
                                    <div class="custom-audio-player" data-src="<?php echo htmlspecialchars($row['cancion_path']); ?>">
                                        <button class="play-pause-btn">▶</button>
                                        <div class="progress-bar">
                                            <div class="progress"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <button class="btn-add-to-cart" onclick="addToCart(<?php echo $row['id_sencillo']; ?>)">Agregar al carrito</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

<script>
let currentlyPlaying = null;

document.querySelectorAll('.custom-audio-player').forEach(player => {
    const audio = new Audio(player.dataset.src);
    const playPauseBtn = player.querySelector('.play-pause-btn');
    const progressBar = player.querySelector('.progress');

    playPauseBtn.addEventListener('click', () => {
        if (currentlyPlaying && currentlyPlaying !== audio) {
            currentlyPlaying.pause();
            const currentlyPlayingBtn = currentlyPlaying.parentElement.querySelector('.play-pause-btn');
            currentlyPlayingBtn.textContent = '▶';
            currentlyPlaying = null;
        }

        if (audio.paused) {
            audio.play();
            playPauseBtn.textContent = '⏸';
            currentlyPlaying = audio;
        } else {
            audio.pause();
            playPauseBtn.textContent = '▶';
            currentlyPlaying = null;
        }
    });

    audio.addEventListener('timeupdate', () => {
        const progress = (audio.currentTime / 60) * 100;
        progressBar.style.width = `${progress}%`;

        if (audio.currentTime >= 60) {
            audio.pause();
            playPauseBtn.textContent = '▶';
            currentlyPlaying = null;
        }
    });

    audio.addEventListener('ended', () => {
        playPauseBtn.textContent = '▶';
        progressBar.style.width = '0%';
        currentlyPlaying = null;
    });
});
function addToCart(idSencillo) {
    console.log('Botón clickeado'); // Para depurar
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id_sencillo=' + idSencillo
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log(data); // Para depurar
        const messageContainer = document.getElementById('message-container');
        messageContainer.innerHTML = ''; // Limpiar mensajes anteriores

        if (data.success) {
            // Mostrar mensaje de éxito
            messageContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
        } else if (data.message === 'Debes iniciar sesión para agregar productos al carrito.') {
            messageContainer.innerHTML = `<div class="alert alert-warning">Debes iniciar sesión o registrarte para agregar productos al carrito.</div>`;
        } else {
            messageContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>

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