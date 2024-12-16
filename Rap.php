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

// Consulta para obtener los álbumes del género 'Electrónica' (id_genero = 1)
$query = "SELECT id_album, nombre_album, descripcion, imagen_album_path, fecha_lanzamiento, precio 
          FROM album 
          WHERE id_genero = 5";

$result = $conexion->query($query);

// Depuración: Verificar el estado de la consulta
if (!$result) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Verificar si hay resultados
if ($result->num_rows === 0) {
    die("No se encontraron álbumes del género Rap.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álbumes de Rap - BassCulture</title>
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
        .track-card {
            background: #1a1a1a;
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
        <h1 class="text-center">Álbumes de Rap</h1>
        <div class="row g-4">
            <?php 
            // Mostrar los resultados
            while ($row = $result->fetch_assoc()): 
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="track-card">
                        <!-- Imagen -->
                        <?php if (!empty($row['imagen_album_path'])): ?>
                            <img 
                                src="<?php echo htmlspecialchars($row['imagen_album_path']); ?>" 
                                alt="<?php echo htmlspecialchars($row['nombre_album']); ?> - Portada" 
                                class="track-image"
                            >
                        <?php else: ?>
                            <div style="width: 100%; aspect-ratio: 1; background: #2a2a2a; display: flex; align-items: center; justify-content: center;">
                                <span>No image available</span>
                            </div>
                        <?php endif; ?>

                        <!-- Información -->
                        <div class="track-info">
                            <h2 class="track-title">
                                <?php echo htmlspecialchars($row['nombre_album']); ?>
                            </h2>
                            <p class="track-description">
                                <?php 
                                $descripcion = htmlspecialchars($row['descripcion']);
                                echo strlen($descripcion) > 100 
                                    ? substr($descripcion, 0, 100) . "..." 
                                    : $descripcion; 
                                ?>
                            </p>
                            <p class="track-date">
                                <strong>Fecha de lanzamiento:</strong> <?php echo date('d/m/Y', strtotime($row['fecha_lanzamiento'])); ?>
                            </p>
                            <p class="track-price">
                                <strong>Precio:</strong> $<?php echo htmlspecialchars($row['precio']); ?>
                            </p>
                            <!-- Botón Ver más -->
                            <a href="Album.php?id_album=<?php echo $row['id_album']; ?>" class="btn btn-primary w-100">Ver más</a>
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
