<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Rap.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user is an artist and get their artista_id
$stmt = $conexion->prepare("SELECT u.id_usuario, u.nombre_usuario, a.id_artista 
                            FROM usuario u 
                            LEFT JOIN artista a ON u.id_usuario = a.usuario 
                            WHERE u.id_usuario = ? AND u.id_artista = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['id_artista'] === null) {
    // If the user is marked as an artist but doesn't have an artista entry, create one
    if ($user && $user['id_artista'] === null) {
        $stmt = $conexion->prepare("INSERT INTO artista (usuario) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $artista_id = $conexion->insert_id;
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    $artista_id = $user['id_artista'];
}

$artist_username = $user['nombre_usuario'];


require_once 'encabezado.php';

$error_message = '';
$success_message = '';

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar que todos los campos necesarios estén presentes
    $required_fields = ['nombre_album', 'id_genero', 'descripcion', 'precio', 'fecha_lanzamiento'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $error_message = "Los siguientes campos son obligatorios: " . implode(', ', $missing_fields);
    } else {
        $nombre_album = sanitizeInput($_POST['nombre_album']);
        $id_genero = sanitizeInput($_POST['id_genero']);
        $descripcion = sanitizeInput($_POST['descripcion']);
        $precio = sanitizeInput($_POST['precio']);
        $fecha_lanzamiento = sanitizeInput($_POST['fecha_lanzamiento']);

        // Validate price
        if (!is_numeric($precio) || $precio < 0) {
            $error_message = "El precio debe ser un número positivo.";
        } 
        // Validate release date
        elseif (!strtotime($fecha_lanzamiento)) {
            $error_message = "La fecha de lanzamiento no es válida.";
        }
        else {
            // Get genre name
            $stmt = $conexion->prepare("SELECT nombre_genero FROM genero WHERE id_genero = ?");
            $stmt->bind_param("i", $id_genero);
            $stmt->execute();
            $genre_result = $stmt->get_result();
            $genre = $genre_result->fetch_assoc();
            $genre_name = $genre['nombre_genero'];

            // Create genre folder if it doesn't exist
            $genre_folder = "uploads/" . $genre_name;
            if (!file_exists($genre_folder)) {
                mkdir($genre_folder, 0777, true);
            }

            // Create album folder
            $album_folder = $genre_folder . '/' . $nombre_album;
            if (!file_exists($album_folder)) {
                mkdir($album_folder, 0777, true);
            }

            // Handle album cover upload
            $imagen_album_path = '';
            if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
                $portada = $_FILES['portada'];
                $portada_name = $portada['name'];
                $portada_tmp = $portada['tmp_name'];
                $portada_ext = strtolower(pathinfo($portada_name, PATHINFO_EXTENSION));
                
                // Validate file type
                $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
                if (in_array($portada_ext, $allowed_extensions)) {
                    // Create Portadas_album folder if it doesn't exist
                    $portadas_folder = "Portadas_album";
                    if (!file_exists($portadas_folder)) {
                        mkdir($portadas_folder, 0777, true);
                    }

                    $portada_new_name = uniqid('portada_') . '.' . $portada_ext;
                    $imagen_album_path = $portadas_folder . '/' . $portada_new_name;
                    
                    if (!move_uploaded_file($portada_tmp, $imagen_album_path)) {
                        $error_message = "Error al subir la portada del álbum.";
                    }
                } else {
                    $error_message = "Tipo de archivo no permitido para la portada. Use JPG, JPEG, PNG o GIF.";
                }
            } else {
                $error_message = "Es necesario subir una portada para el álbum.";
            }

            if (empty($error_message)) {
                // Start a transaction
                $conexion->begin_transaction();

                try {
                    // Insert album into database using artista_id instead of user_id
                    $stmt = $conexion->prepare("INSERT INTO album (id_artista, nombre_album, descripcion, id_genero, precio, imagen_album_path, fecha_lanzamiento) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issidss", $artista_id, $nombre_album, $descripcion, $id_genero, $precio, $imagen_album_path, $fecha_lanzamiento);
                    
                    if ($stmt->execute()) {
                        $album_id = $stmt->insert_id;

                        // Handle song uploads
                        if (isset($_FILES['canciones'])) {
                            $canciones = $_FILES['canciones'];
                            for ($i = 0; $i < count($canciones['name']); $i++) {
                                $nombre_cancion = $canciones['name'][$i];
                                $file_tmp = $canciones['tmp_name'][$i];
                                $file_type = $canciones['type'][$i];

                                if (strpos($file_type, 'audio') !== false) {
                                    // Generate unique filename
                                    $file_extension = pathinfo($nombre_cancion, PATHINFO_EXTENSION);
                                    $unique_filename = uniqid() . '.' . $file_extension;

                                    // Set the path for the file
                                    $file_path = $album_folder . '/' . $unique_filename;

                                    if (move_uploaded_file($file_tmp, $file_path)) {
                                        $stmt = $conexion->prepare("INSERT INTO canciones (id_album, id_artista, nombre_cancion, cancion_path) VALUES (?, ?, ?, ?)");
                                        $stmt->bind_param("iiss", $album_id, $artista_id, $nombre_cancion, $file_path);
                                        $stmt->execute();
                                    }
                                }
                            }
                        }

                        // If everything is successful, commit the transaction
                        $conexion->commit();
                        $success_message = "Álbum creado exitosamente.";
                    } else {
                        throw new Exception("Error al crear el álbum: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    // If there's an error, roll back the transaction
                    $conexion->rollback();
                    $error_message = $e->getMessage();
                }
            }
        }
    }
}

// Fetch genres
$genres_result = $conexion->query("SELECT id_genero, nombre_genero FROM genero");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Álbum - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="principal.css" rel="stylesheet">
    <style>
        
/* Remove any container backgrounds */
.container {
  background: none !important;
  box-shadow: none !important;
}
          .main-header {
  background-color: #2f4538;
  border-bottom: 1px solid var(--oscuro);
  padding: 1rem 0;
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
  background-color: #2f4538;
  color: white;
}
.input-group .btn {
    margin-left: 10px;
    border-radius: 5px;
    transition: background-color 0.3s, color 0.3s;
}

.input-group .btn:hover {
    background-color: #2f4538;
    color: white;
}
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #555;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Subir Álbum</h2>
        <div class="artist-box">
            <strong>Artista:</strong> 
            <input type="text" value="<?php echo htmlspecialchars($artist_username); ?>" readonly class="form-control-plaintext">
        </div>
        <?php
        if (!empty($success_message)) {
            echo "<div class='alert alert-success'>$success_message</div>";
        }
        if (!empty($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
 ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label for="nombre_album">Nombre del Álbum</label>
                <input type="text" class="form-control" id="nombre_album" name="nombre_album" required>
            </div>
            <div class="form-group mb-3">
                <label for="id_genero">Género</label>
                <select class="form-control" id="id_genero" name="id_genero" required>
                    <?php while ($genre = $genres_result->fetch_assoc()): ?>
                        <option value="<?php echo $genre['id_genero']; ?>"><?php echo htmlspecialchars($genre['nombre_genero']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="descripcion">Descripción del Álbum</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="precio">Precio del Álbum ($)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" required>
            </div>
            <div class="form-group mb-3">
                <label for="fecha_lanzamiento">Fecha de Lanzamiento</label>
                <input type="date" class="form-control" id="fecha_lanzamiento" name="fecha_lanzamiento" required>
            </div>
            <div class="form-group mb-3">
            <div class="form-group mb-3">
    <label for="portada">Portada del Álbum</label>
    <div class="input-group">
        <input type="file" class="form-control" id="portada" name="portada" accept="image/*" required>
        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('portada').click();">
            <i class="fas fa-upload"></i> Subir Portada
        </button>
    </div>
</div>
<div class="form-group mb-3">
    <label for="canciones">Canciones (selecciona múltiples archivos)</label>
    <div class="input-group">
        <input type="file" class="form-control" id="canciones" name="canciones[]" multiple required>
        <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('canciones').click();">
            <i class="fas fa-upload"></i> Subir Canciones
        </button>
    </div>
</div>
<button type="submit" class="btn btn-primary">Subir Álbum</button>
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

