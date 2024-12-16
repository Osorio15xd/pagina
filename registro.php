<?php
require_once 'conexion.php';

$error = null;
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $es_artista = isset($_POST['es_artista']) ? 1 : 0;

    // Verificar si el correo o teléfono ya existen
    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE correo = ? OR telefono = ?");
    $stmt->bind_param("ss", $correo, $telefono);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "El correo o teléfono ya están registrados.";
    } else {
        $stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido1, apellido2, correo, telefono, nombre_usuario, contraseña, id_artista, id_cliente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $id_cliente = $es_artista ? 0 : 1;
        $stmt->bind_param("sssssssii", $nombre, $apellido1, $apellido2, $correo, $telefono, $nombre_usuario, $contrasena, $es_artista, $id_cliente);
        
        if ($stmt->execute()) {
            $success = true;
            session_start();
            $_SESSION['registration_success'] = true;
            header("Location: inicio.php");
            exit();
        } else {
            $error = "Error al registrar el usuario.";
        }
    }
}

// Include the header after all logic
require_once 'encabezado.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="principal.css" rel="stylesheet">
    <style>
           .main-header {
  background-color: #2f4538;
  border-bottom: 1px solid var(--oscuro);
  padding: 1rem 0;
}
        .form-control {
            font-size: 1.1em;
            padding: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>Registro</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success">Registro exitoso. Serás redirigido a la página de inicio de sesión.</div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required pattern="[A-Za-zÁ-ÿ\s]+" title="Solo se permiten letras y espacios">
                    </div>
                    <div class="form-group">
                        <label for="apellido1">Apellido paterno</label>
                        <input type="text" class="form-control" id="apellido1" name="apellido1" required pattern="[A-Za-zÁ-ÿ\s]+" title="Solo se permiten letras y espacios">
                    </div>
                    <div class="form-group">
                        <label for="apellido2">Apellido materno</label>
                        <input type="text" class="form-control" id="apellido2" name="apellido2" pattern="[A-Za-zÁ-ÿ\s]+" title="Solo se permiten letras y espacios">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" required pattern="[0-9]+" title="Solo se permiten números">
                    </div>
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre de usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                    </div>
                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">Mostrar</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="es_artista" name="es_artista">
                            <label class="form-check-label" for="es_artista">¿Eres artista?</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </form>
            </div>
            <div class="col-md-6">
                <img src="150.png" alt="logo" class="img-fluid">
                <p class="mt-3">Te damos la bienvenida a esta página. Esperamos que puedas disfrutar y encontrar todos los artistas que deseas.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordInput = document.getElementById('contrasena');
            var type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Mostrar' : 'Ocultar';
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

