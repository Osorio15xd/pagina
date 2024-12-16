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
$stmt->close(); // Cerrar el statement

if (!$user || $user['id_artista'] != 1) {
    header("Location: index.php");
    exit();
}

$artist_username = $user['nombre_usuario'];
$artista_id = $user['artista_id'];

require_once 'encabezado.php';

$error_message = '';
$success_message = '';

// Manejo de eliminación de sencillos
if (isset($_POST['delete_sencillo'])) {
    $id_sencillo = $_POST['id_sencillo'];
    $stmt = $conexion->prepare("DELETE FROM sencillos WHERE id_sencillo = ? AND id_artista = ?");
    $stmt->bind_param("ii", $id_sencillo, $artista_id);
    $stmt->execute();
    $stmt->close(); // Liberar recursos
}

// Manejo de modificación de sencillos
if (isset($_POST['update_sencillo'])) {
    $id_sencillo = $_POST['id_sencillo'];

    // Obtener los valores actuales del sencillo
    $stmt = $conexion->prepare("SELECT imagen_sencillo_path, cancion_path FROM sencillos WHERE id_sencillo = ? AND id_artista = ?");
    $stmt->bind_param("ii", $id_sencillo, $artista_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sencillo_actual = $result->fetch_assoc();
    $stmt->close(); // Cerrar el statement

    // Asignar los valores actuales
    $imagen_sencillo_path = $sencillo_actual['imagen_sencillo_path'];
    $cancion_path = $sencillo_actual['cancion_path'];

    $nombre_sencillo = $_POST['nombre_sencillo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_genero = $_POST['id_genero']; // Asegúrate de que este campo esté en tu formulario

    // Verificar si el id_genero existe en la tabla genero
    $stmt = $conexion->prepare("SELECT COUNT(*) FROM genero WHERE id_genero = ?");
    $stmt->bind_param("i", $id_genero);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close(); // Cerrar el statement

    if ($count ==  0) {
        echo '<div class="alert alert-danger">El género seleccionado no es válido.</div>';
        exit();
    }

    // Manejo de la imagen
    if (isset($_FILES['portada']) && $_FILES['portada']['error'] == 0) {
        $portada = $_FILES['portada'];
        $portada_name = $portada['name'];
        $portada_tmp = $portada['tmp_name'];
        $portada_ext = strtolower(pathinfo($portada_name, PATHINFO_EXTENSION));
        
        // Validar tipo de archivo
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($portada_ext, $allowed_extensions)) {
            $portadas_folder = "Portadas_sencillos";
            if (!file_exists($portadas_folder)) {
                mkdir($portadas_folder, 0777, true);
            }

            $portada_new_name = uniqid('portada_') . '.' . $portada_ext; 
            $imagen_sencillo_path = $portadas_folder . '/' . $portada_new_name; // Actualiza solo si se sube una nueva
            move_uploaded_file($portada_tmp, $imagen_sencillo_path);
        }
    }

    // Manejo de la canción
    if (isset($_FILES['cancion']) && $_FILES['cancion']['error'] == 0) {
        $cancion = $_FILES['cancion'];
  
        $cancion_name = $cancion['name'];
        $cancion_tmp = $cancion['tmp_name'];
        $cancion_type = $cancion['type'];

        // Verificar si el tipo de archivo es audio
        if (strpos($cancion_type, 'audio') !== false) {
            $file_extension = pathinfo($cancion_name, PATHINFO_EXTENSION);
            $unique_filename = uniqid() . '.' . $file_extension;
            $cancion_path = "uploads/sencillos/" . $unique_filename; // Actualiza solo si se sube una nueva
            move_uploaded_file($cancion_tmp, $cancion_path);
        }
    } else {
        // Si no se subió un nuevo archivo, mantener el valor anterior
        $cancion_path = $sencillo_actual['cancion_path'];
    }

    // Actualizar en la base de datos
    $stmt = $conexion->prepare("UPDATE sencillos SET nombre_sencillo = ?, descripcion = ?, precio = ?, id_genero = ?, imagen_sencillo_path = ?, cancion_path = ? WHERE id_sencillo = ? AND id_artista = ?");
    $stmt->bind_param("ssissssi", $nombre_sencillo, $descripcion, $precio, $id_genero, $imagen_sencillo_path, $cancion_path, $id_sencillo, $artista_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $success_message = "Sencillo actualizado correctamente.";
        } else {
            $error_message = "No se realizaron cambios en el sencillo. Asegúrate de que los datos sean diferentes.";
        }
    } else {
        $error_message = "Error al actualizar el sencillo: " . $stmt->error;
    }
    $stmt->close(); // Liberar recursos
}

// Obtener los sencillos del artista
$stmt = $conexion->prepare("SELECT * FROM sencillos WHERE id_artista = ?");
$stmt->bind_param("i", $artista_id);
$stmt->execute();
$sencillos = $stmt->get_result();
$stmt->close(); // Liberar recursos

require_once 'encabezado.php'; // Incluye el encabezado
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar o Modificar Sencillos - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background-color: #333; /* Color de fondo oscuro */
            font-family: 'Arial', sans-serif;
            color: white; /* Color de texto blanco */
        }
        .container {
            background: #444; /* Fondo del contenedor */
            border-radius: 8px;
            padding: 10px; /* Reducir padding */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            margin-top: 20px; /* Reducir margen superior */
        }
        h2, h3 {
            color: #fff; /* Color de los encabezados */
            margin-bottom: 10px; /* Reducir margen inferior */
        }
        .form-group label {
            font-weight: bold;
            color: #ddd; /* Color de las etiquetas */
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 5px 10px; /* Reducir padding */
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 10px; /* Reducir margen superior */
        }
        .table {
            background-color: #555; /* Fondo de la tabla */
            color: white; /* Color de texto en la tabla */
        }
        .table th, .table td {
            padding: 5px; /* Reducir padding en celdas */
        }
        .table img {
            width: 100px; /* Ajusta el tamaño de la imagen */
            height: auto;
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





/* Remove any container backgrounds */
.container {
  background: none !important;
  box-shadow: none !important;
}
    </style>
</head>
<body>
    <div class="container">
        <h2>Sencillos de <?php echo htmlspecialchars($artist_username); ?></h2>

        <?php if ($success_message): ?>
            <div class="alert ```php
alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h3>Sencillos Existentes</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sencillo = $sencillos->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($sencillo['imagen_sencillo_path']); ?>" alt="Portada de <?php echo htmlspecialchars($sencillo['nombre_sencillo']); ?>"></td>
                    <td><?php echo htmlspecialchars($sencillo['nombre_sencillo']); ?></td>
                    <td><?php echo htmlspecialchars($sencillo['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($sencillo['precio']); ?></td>
                    <td>
                        <form method="POST" enctype="multipart/form-data" style="display:inline;">
                            <input type="hidden" name="id_sencillo" value="<?php echo $sencillo['id_sencillo']; ?>">
                            
                            <div class="form-group">
                                <label for="nombre_sencillo">Nombre del Sencillo:</label>
                                <input type="text" name="nombre_sencillo" value="<?php echo htmlspecialchars($sencillo['nombre_sencillo']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <textarea name="descripcion" required><?php echo htmlspecialchars($sencillo['descripcion']); ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="precio">Precio:</label>
                                <input type="number" name="precio" value="<?php echo htmlspecialchars($sencillo['precio']); ?>" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="id_genero">Género:</label>
                                <select name="id_genero" id="id_genero" required>
                                    <?php
                                    // Suponiendo que tienes una consulta para obtener los géneros
                                    $stmt = $conexion->prepare("SELECT id_genero, nombre_genero FROM genero");
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    while ($genero = $result->fetch_assoc()) {
                                        echo '<option value="' . $genero['id_genero'] . '">' . htmlspecialchars($genero['nombre_genero']) . '</option>';
                                    }
                                    $stmt->close(); // Cerrar el statement
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="portada">Subir Portada:</label>
                                <input type="file" name="portada" id="portada">
                            </div>
                            
                            <div class="form-group">
                                <label for="cancion">Subir Canción:</label>
                                <input type="file" name="cancion" id="cancion">
                            </div>
                            
                            <div>
                                <button type="submit" name="update_sencillo" class="btn btn-warning">Modificar</button>
                                <button type="submit" name="delete_sencillo" class="btn btn-danger">Eliminar</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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