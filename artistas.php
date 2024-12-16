<?php
require_once 'conexion.php';
require_once 'encabezado.php';

// Verificar si la sesión ya está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Activar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta para obtener artistas con foto o al menos un álbum o sencillo
$query = "
    SELECT DISTINCT u.id_usuario, u.nombre, u.apellido1, u.apellido2, a.foto_path, u.nombre_usuario
    FROM usuario u
    JOIN artista a ON u.id_usuario = a.usuario
    LEFT JOIN album al ON a.id_artista = al.id_artista
    LEFT JOIN sencillos s ON a.id_artista = s.id_artista
    WHERE u.id_artista = 1
    AND (a.foto_path IS NOT NULL AND a.foto_path != ''
         OR al.id_album IS NOT NULL
         OR s.id_sencillo IS NOT NULL)
    GROUP BY u.id_usuario
";

$result = $conexion->query($query);

// Depuración: Verificar el estado de la consulta
if (!$result) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Verificar si hay resultados
if ($result->num_rows === 0) {
    die("No se encontraron artistas.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artistas - BassCulture</title>
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
            margin: 0;
            padding: 0;
        }
        .artist-card {
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s;
            max-width: 300px;
            margin: 0 auto;
        }
        .artist-card:hover {
            transform: translateY(-5px);
        }
        .artist-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-bottom: 2px solid #444;
        }
        .artist-info {
            padding: 1rem;
        }
        .artist-name {
            color: #fff;
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .artist-username {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .btn-view-profile {
            background-color: #1DB954;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 50px;
            transition: background-color 0.3s;
        }
        .btn-view-profile:hover {
            background-color: #1ED760;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Artistas</h1>
        <div class="row g-4">
            <?php 
            while ($artist = $result->fetch_assoc()): 
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="artist-card">
                        <?php if (!empty($artist['foto_path'])): ?>
                            <img 
                                src="<?php echo htmlspecialchars($artist['foto_path']); ?>" 
                                alt="<?php echo htmlspecialchars($artist['nombre_usuario']); ?> - Foto" 
                                class="artist-image"
                            >
                        <?php else: ?>
                            <div style="width: 100%; aspect-ratio: 1; background: #2a2a2a; display: flex; align-items: center; justify-content: center;">
                                <span>No image available</span>
                            </div>
                        <?php endif; ?>

                        <div class="artist-info">
                            <h2 class="artist-name">
                                <?php echo htmlspecialchars($artist['nombre'] . ' ' . $artist['apellido1'] . ' ' . $artist['apellido2']); ?>
                            </h2>
                            <p class="artist-username">
                                @<?php echo htmlspecialchars($artist['nombre_usuario']); ?>
                            </p>
                            <a href="artista.php?id=<?php echo $artist['id_usuario']; ?>" class="btn-view-profile">
                                Ver Perfil
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
            endwhile;
            ?>
        </div>
    </div>

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