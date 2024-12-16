<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: inicio.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user information
$stmt = $conexion->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if the user is an artist
$is_artist = $user['id_artista'] == 1;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update user profile
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $telefono = $_POST['telefono'];
        $nombre_usuario = $_POST['nombre_usuario'];

        $conexion->begin_transaction();

        try {
            // Check if the new username is already taken
            $stmt = $conexion->prepare("SELECT id_usuario FROM usuario WHERE nombre_usuario = ? AND id_usuario != ?");
            $stmt->bind_param("si", $nombre_usuario, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                throw new Exception("El nombre de usuario ya está en uso. Por favor, elige otro.");
            }

            // Update usuario table
            $stmt = $conexion->prepare("UPDATE usuario SET nombre = ?, apellido1 = ?, apellido2 = ?, telefono = ?, nombre_usuario = ? WHERE id_usuario = ?");
            $stmt->bind_param("sssssi", $nombre, $apellido1, $apellido2, $telefono, $nombre_usuario, $user_id);
            $stmt->execute();

            if ($is_artist) {
                // Check if artist record exists
                $stmt = $conexion->prepare("SELECT * FROM artista WHERE usuario = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    // Insert new artist record
                    $stmt = $conexion->prepare("INSERT INTO artista (usuario) VALUES (?)");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                }

                // Handle profile picture upload for artists
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
                    $file = $_FILES['profile_picture'];
                    $file_name = $file['name'];
                    $file_tmp = $file['tmp_name'];
                    $file_type = $file['type'];

                    // Definir tipos de archivo permitidos
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    $max_file_size = 5 * 1024 * 1024; // 5 MB

                    if (in_array($file_type, $allowed_types) && $file['size'] <= $max_file_size) {
                        $upload_dir = 'imagenes_perfiles/';
                        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
                        $new_file_name = uniqid('profile_', true) . '.' . $file_extension;
                        $file_path = $upload_dir . $new_file_name;

                        if (move_uploaded_file($file_tmp, $file_path)) {
                            // Update artist's profile picture
                            $stmt = $conexion->prepare("UPDATE artista SET foto_path = ? WHERE usuario = ?");
                            $stmt->bind_param("si", $file_path, $user_id);
                            $stmt->execute();
                        } else {
                            throw new Exception("Error al subir la imagen.");
                        }
                    } else {
                        throw new Exception("Por favor, sube un archivo de imagen válido (JPG, PNG o GIF) de menos de 5 MB.");
                    }
                }
            }

            $conexion->commit();
            $_SESSION['success_message'] = "Perfil actualizado exitosamente.";
            $_SESSION['username'] = $nombre_usuario; // Update the session with the new username
            header("Location: index.php");
            exit();

        } catch (Exception $e) {
            $conexion->rollback();
            $_SESSION['error_message'] = " Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['delete_account'])) {
        $conexion->begin_transaction();

        try {
            // Delete artist record if exists
            if ($is_artist) {
                $stmt = $conexion->prepare("DELETE FROM artista WHERE usuario = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
            }

            // Delete user record
            $stmt = $conexion->prepare("DELETE FROM usuario WHERE id_usuario = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $conexion->commit();
            session_destroy();
            header("Location: inicio.php");
            exit();

        } catch (Exception $e) {
            $conexion->rollback();
            $_SESSION['error_message'] = "Error al eliminar la cuenta: " . $e->getMessage();
        }
    }
}

// Fetch artist information if the user is an artist
$artist_info = null;
if ($is_artist) {
    $stmt = $conexion->prepare("SELECT * FROM artista WHERE usuario = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $artist_info = $result->fetch_assoc();
}

// Now include the header
require_once 'encabezado.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="principal.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
           .main-header {
  background-color: #2f4538;
  border-bottom: 1px solid var(--oscuro);
  padding: 1rem 0;
}
.container {
  background: none !important;
  box-shadow: none !important;
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
        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-left: 10px;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Perfil de Usuario</h2>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label for="nombre_usuario">Nombre de Usuario</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($user['nombre_usuario']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="apellido1">Apellido Paterno</label>
                <input type="text" class="form-control" id="apellido1" name="apellido1" value="<?php echo htmlspecialchars($user['apellido1']); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="apellido ```php
2">Apellido Materno</label>
                <input type="text" class="form-control" id="apellido2" name="apellido2" value="<?php echo htmlspecialchars($user['apellido2']); ?>">
            </div>
            <div class="form-group mb-3">
                <label for="telefono">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($user['telefono']); ?>">
            </div>
            <?php if ($is_artist): ?>
            <div class="form-group mb-3">
                <label for="profile_picture">Foto de Perfil</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif">
                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5 MB.</small>
            </div>
            <?php if ($artist_info && $artist_info['foto_path']): ?>
                <div class="mb-3">
                    <img src="<?php echo htmlspecialchars($artist_info['foto_path']); ?>" alt="Foto de Perfil" class="img-thumbnail" style="max-width: 200px;">
                </div>
            <?php endif; ?>
            <?php endif; ?>
            <button type="submit" name="update_profile" class="btn btn-primary">Actualizar Perfil</button>
            <button type="submit" name="delete_account" class="btn btn-danger">Eliminar Cuenta</button>
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