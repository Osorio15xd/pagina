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

$error_message = '';
$success_message = '';

// Obtener los álbumes del artista
$stmt = $conexion->prepare("SELECT * FROM album WHERE id_artista = ?");
$stmt->bind_param("i", $artista_id);
$stmt->execute();
$albumes = $stmt->get_result();
$stmt->close(); // Liberar recursos

// Manejo de modificación de álbum
if (isset($_POST['update_album'])) {
    $id_album = $_POST['id_album'];
    $nombre_album = $_POST['nombre_album'];
    $descripcion = $_POST['descripcion'];
    $genero = $_POST['genero'];
    $imagen_album_path = $_FILES['imagen_album']['name'];

    // Obtener los datos actuales del álbum
    $stmt = $conexion->prepare("SELECT * FROM album WHERE id_album = ?");
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $album_actual = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Solo actualizar los campos que han sido modificados
    $nombre_album = !empty($nombre_album) ? $nombre_album : $album_actual['nombre_album'];
    $descripcion = !empty($descripcion) ? $descripcion : $album_actual['descripcion'];
    $genero = !empty($genero) ? $genero : $album_actual['genero'];

    // Manejo de la imagen
    if (!empty($imagen_album_path)) {
        move_uploaded_file($_FILES['imagen_album']['tmp_name'], "ruta/a/tu/carpeta/" . $imagen_album_path);
        $imagen_album_path = "ruta/a/tu/carpeta/" . $imagen_album_path; // Actualiza la ruta de la imagen
    } else {
        $imagen_album_path = $album_actual['imagen_album_path']; // Mantener la imagen actual si no se subió una nueva
    }

    // Actualizar el álbum en la base de datos
    $stmt = $conexion->prepare("UPDATE album SET nombre_album = ?, descripcion = ?, imagen_album_path = ?, genero = ? WHERE id_album = ?");
    $stmt->bind_param("ssssi", $nombre_album, $descripcion, $imagen_album_path, $genero, $id_album);
    $stmt->execute();
    $stmt->close();

    $success_message = "Álbum actualizado correctamente.";
}

// Manejo de eliminación de álbum
if (isset($_POST['delete_album'])) {
    $id_album = $_POST['id_album'];
    $stmt = $conexion->prepare("DELETE FROM album WHERE id_album = ?");
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $stmt->close();
    $success_message = "Álbum eliminado correctamente.";
}

// Manejo de eliminación de canción
if (isset($_POST['delete_song'])) {
    $id_song = $_POST['id_song'];
    $stmt = $conexion->prepare("DELETE FROM canciones WHERE id_cancion = ?");
    $stmt->bind_param("i", $id_song);
    $stmt->execute();
    $stmt->close();
    $success_message = "Canción eliminada correctamente.";
}

// Obtener las canciones del álbum seleccionado
$songs = [];
if (isset($_POST['id_album'])) {
    $id_album = $_POST['id_album'];

    $stmt = $conexion->prepare("SELECT * FROM canciones WHERE id_album = ?");
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $songs = $stmt->get_result(); // Asignar los resultados a la variable
    $stmt->close();
}

// Obtener los datos del álbum para el modal de edición
$album_data = [];
if (isset($_POST['id_album'])) {
    $id_album = $_POST['id_album'];
    $stmt = $conexion->prepare("SELECT * FROM album WHERE id_album = ?");
    $stmt->bind_param("i", $id_album);
    $stmt->execute();
    $album_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

require_once 'encabezado.php'; // Incluye el encabezado
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álbumes - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="principal.css" rel="stylesheet">
</head>
<style>
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
</style>
<body>
    <div class="container">
        <h2>Álbumes de <?php echo htmlspecialchars($artist_username); ?></h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <h3>Seleccionar Álbum para Modificar o Eliminar</h3>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="id_album">Selecciona un Álbum</label>
                <select name="id_album" class="form-control" required>
                    <option value="">Selecciona un álbum</option>
                    <?php 
                    // Reiniciar el puntero del resultado para volver a recorrerlo
                    $albumes->data_seek(0);
                    while ($album = $albumes->fetch_assoc()): ?>
                        <option value="<?php echo $album['id_album']; ?>" <?php echo (isset($id_album) && $id_album == $album['id_album']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($album['nombre_album']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="delete_album" class="btn btn-danger">Eliminar Álbum</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal">Modificar Álbum</button>
        </form>

        <!-- Modal para editar álbum -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Modificar Álbum</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_album" value="<?php echo isset($album_data['id_album']) ? $album_data['id_album'] : ''; ?>">
                            <div class="form-group">
                                <label for="nombre_album">Nombre del Álbum</label>
                                <input type="text" class="form-control" name="nombre_album" value="<?php echo isset($album_data['nombre_album']) ? htmlspecialchars($album_data['nombre_album']) : ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" name="descripcion" required><?php echo isset($album_data['descripcion']) ? htmlspecialchars($album_data['descripcion']) : ''; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="imagen_album">Imagen del Álbum</label>
                                <input type="file" class="form-control-file" name="imagen_album">
                            </div>
                            <div class="form-group">
                                <label for="genero">Género</label>
                                <select class="form-control" name="genero" required>
                                    <option value="Pop ```php
                                    <?php echo (isset($album_data['genero']) && $album_data['genero'] == 'Pop') ? 'selected' : ''; ?>>Pop</option>
                                    <option value="Rock" <?php echo (isset($album_data['genero']) && $album_data['genero'] == 'Rock') ? 'selected' : ''; ?>>Rock</option>
                                    <option value="Hip-Hop" <?php echo (isset($album_data['genero']) && $album_data['genero'] == 'Hip-Hop') ? 'selected' : ''; ?>>Hip-Hop</option>
                                    <option value="Jazz" <?php echo (isset($album_data['genero']) && $album_data['genero'] == 'Jazz') ? 'selected' : ''; ?>>Jazz</option>
                                    <option value="Clásica" <?php echo (isset($album_data['genero']) && $album_data['genero'] == 'Clásica') ? 'selected' : ''; ?>>Clásica</option>
                                    <!-- Agrega más géneros según sea necesario -->
                                </select>
                            </div>
                            <button type="submit" name="update_album" class="btn btn-primary">Actualizar Álbum</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h3>Canciones del Álbum Seleccionado</h3>
        <?php if (!empty($songs)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($song = $songs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($song['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($song['precio']); ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="id_song" value="<?php echo $song['id_cancion']; ?>">
                                    <button type="submit" name="delete_song" class="btn btn-danger">Eliminar Canción</button>
                                </form>
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#editSongModal" data-id="<?php echo $song['id_cancion']; ?>" data-titulo="<?php echo htmlspecialchars($song['titulo']); ?>" data-precio="<?php echo htmlspecialchars($song['precio']); ?>">Modificar Canción</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No hay canciones en este álbum.</p>
        <?php endif; ?>

        <!-- Modal para editar canción -->
        <div class="modal fade" id="editSongModal" tabindex="-1" role="dialog" aria-labelledby="editSongModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSongModalLabel">Modificar Canción</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="update_song.php">
                            <input type="hidden" name="id_song" id="modal_song_id">
                            <div class="form-group">
                                <label for="song_title">Título de la Canción</label>
                                <input type="text" class="form-control" name="song_title" id="modal_song_title" required>
                            </div>
                            <div class="form-group">
                                <label for="song_price">Precio</label>
                                <input type="number" class="form-control" name="song_price" id="modal_song_price" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar Canción</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para llenar el modal con los datos de la canción seleccionada
        $('#editSongModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Botón que activó el modal
            var songId = button.data('id'); // Extraer la información de los atributos data-* del botón
            var songTitle = button.data('titulo');
            var songPrice = button.data('precio');

            var modal = $(this);
            modal.find('#modal_song_id').val(songId);
            modal.find('#modal_song_title').val(songTitle);
            modal.find('#modal_song_price').val(songPrice);
        });
    </script>
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