<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: inicio.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user is an artist and get their username
$stmt = $conexion->prepare("SELECT u.id_artista, u.nombre_usuario, a.id_artista AS artista_id FROM usuario u LEFT JOIN artista a ON u.id_usuario = a.usuario WHERE u.id_usuario = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['id_artista'] != 1) {
    header("Location: index.php");
    exit();
}

$artist_username = $user['nombre_usuario'];
$artista_id = $user['artista_id'];

require_once 'encabezado.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar campos obligatorios
    $required_fields = ['nombre_sencillo', 'id_genero', 'descripcion', 'precio', 'fecha_lanzamiento'];
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $error_message = "Los siguientes campos son obligatorios: " . implode(', ', $missing_fields);
    } else {
        $nombre_sencillo = $_POST['nombre_sencillo'];
        $id_genero = $_POST['id_genero'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $fecha_lanzamiento = $_POST['fecha_lanzamiento'];

        // Validate price and release date
        if (!is_numeric($precio) || $precio < 0) {
            $error_message = "El precio debe ser un número positivo.";
        } elseif (!strtotime($fecha_lanzamiento)) {
            $error_message = "La fecha de lanzamiento no es válida.";
        } else {
            // Get genre name
            $stmt = $conexion->prepare("SELECT nombre_genero FROM genero WHERE id_genero = ?");
            $stmt->bind_param("i", $id_genero);
            $stmt->execute();
            $genre_result = $stmt->get_result();
            $genre = $genre_result->fetch_assoc();
            $genre_name = $genre['nombre_genero'];

            // Create folders structure
            $genre_folder = "uploads/sencillos/" . $genre_name;
            if (!file_exists($genre_folder)) {
                mkdir($genre_folder, 0777, true);
            }

            // Handle cover image upload
            $imagen_sencillo_path = '';
            if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
                $portada = $_FILES['portada'];
                $portada_name = $portada['name'];
                $portada_tmp = $portada['tmp_name'];
                $portada_ext = strtolower(pathinfo($portada_name, PATHINFO_EXTENSION));
                
                // Validate file type
                $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
                if (in_array($portada_ext, $allowed_extensions)) {
                    $portadas_folder = "Portadas_sencillos";
                    if (!file_exists($portadas_folder)) {
                        mkdir($portadas_folder, 0777, true);
                    }

                    $portada_new_name = uniqid('portada_') . '.' . $portada_ext;
                    $imagen_sencillo_path = $portadas_folder . '/' . $portada_new_name;
                    
                    if (!move_uploaded_file($portada_tmp, $imagen_sencillo_path)) {
                        $error_message = "Error al subir la portada del sencillo.";
                    }
                } else {
                    $error_message = "Tipo de archivo no permitido para la portada. Use JPG, JPEG, PNG o GIF.";
                }
            } else {
                $error_message = "Es necesario subir una portada para el sencillo.";
            }

            // Handle song upload
            $cancion_path = '';
            if (empty($error_message) && isset($_FILES['cancion']) && $_FILES['cancion']['error'] == 0) {
                $cancion = $_FILES['cancion'];
                $cancion_name = $cancion['name'];
                $cancion_tmp = $cancion['tmp_name'];
                $cancion_type = $cancion['type'];

                if (strpos($cancion_type, 'audio') !== false) {
                    $file_extension = pathinfo($cancion_name, PATHINFO_EXTENSION);
                    $unique_filename = uniqid() . '.' . $file_extension;
                    $cancion_path = $genre_folder . '/' . $unique_filename;

                    if (!move_uploaded_file($cancion_tmp, $cancion_path)) {
                        $error_message = "Error al subir el archivo de audio.";
                    }
                } else {
                    $error_message = "Por favor, sube un archivo de audio válido.";
                }
            } else {
                $error_message = "Es necesario subir un archivo de audio.";
            }

            if (empty($error_message)) {
                // Insert into database
                $stmt = $conexion->prepare("INSERT INTO sencillos (id_artista, nombre_sencillo, descripcion, id_genero, precio, imagen_sencillo_path, cancion_path, fecha_lanzamiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issidsss", $artista_id, $nombre_sencillo, $descripcion, $id_genero, $precio, $imagen_sencillo_path, $cancion_path, $fecha_lanzamiento);
                
                if ($stmt->execute()) {
                    $success_message = "Sencillo subido exitosamente.";
                } else {
                    $error_message = "Error al crear el sencillo: " . $stmt->error;
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
    <title>Subir Sencillo - BassCulture</title>
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
  background-color: #444;
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
        .artist-box {
            margin-bottom: 20px;
        }
        .artist-box strong {
            display: inline-block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Subir Sencillo</h2>
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
                <label for="nombre_sencillo">Nombre del Sencillo</label>
                <input type="text" class="form-control" id="nombre_sencillo" name="nombre_sencillo" required>
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
                <label for="descripcion">Descripción del Sencillo</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
            </div>
            <div class="form-group mb-3">
                <label for="precio">Precio ($)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="precio" name="precio" required>
            </div>
            <div class="form-group mb-3">
                <label for="fecha_lanzamiento">Fecha de Lanzamiento</label>
                <input type="date" class="form-control" id="fecha_lanzamiento" name="fecha_lanzamiento" required>
            </div>
            <div class="form-group mb-3">
                <label for="portada">Portada del Sencillo</label>
                <input type="file" class="form-control" id="portada" name="portada" accept="image/*" required>
            </div>
            <div class="form-group mb-3">
                <label for="cancion">Archivo de Audio</label>
                <input type="file" class="form-control" id="cancion" name="cancion" accept="audio/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Subir Sencillo</button>
        </form>
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