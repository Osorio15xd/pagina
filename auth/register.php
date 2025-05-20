<?php
session_start();
require_once '../config/db_connect.php';

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nombre = trim($_POST['nombre']);
    $apellido1 = trim($_POST['apellido1']);
    $apellido2 = trim($_POST['apellido2'] ?? '');
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono'] ?? '');
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_artist = isset($_POST['is_artist']) ? 1 : 0;
    
    if (empty($nombre) || empty($apellido1) || empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Por favor, completa todos los campos obligatorios.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        try {
            // Verificar si el email ya existe
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $error = 'Este correo electrónico ya está registrado.';
            } else {
                // Verificar si el nombre de usuario ya existe
                $stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE nombre_usuario = ?");
                $stmt->execute([$username]);
                if ($stmt->rowCount() > 0) {
                    $error = 'Este nombre de usuario ya está en uso.';
                } else {
                    // Crear el usuario
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("INSERT INTO usuario (nombre, apellido1, apellido2, correo, telefono, nombre_usuario, contraseña, id_artista, id_cliente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
                    if ($stmt->execute([$nombre, $apellido1, $apellido2, $email, $telefono, $username, $hashed_password, $is_artist])) {
                        $user_id = $pdo->lastInsertId();
                        
                        // Si es artista, crear entrada en la tabla artista
                        if ($is_artist) {
                            $stmt = $pdo->prepare("INSERT INTO artista (usuario) VALUES (?)");
                            $stmt->execute([$user_id]);
                        }
                        
                        // Iniciar sesión automáticamente
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_name'] = $nombre;
                        
                        // Redirigir a la página principal
                        header('Location: ../index.php');
                        exit;
                    } else {
                        $error = 'Error al registrar el usuario. Inténtalo de nuevo.';
                    }
                }
            }
        } catch (PDOException $e) {
            $error = 'Error al registrar el usuario: ' . $e->getMessage();
        }
    }
}

include_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - BassCulture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        
        .register-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        .register-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .register-logo img {
            height: 80px;
            border-radius: 50%;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #1DB954; /* Color verde de Spotify */
        }
        
        .form-control {
            background-color: #282828;
            border: none;
            color: #fff;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .form-control:focus {
            background-color: #333;
            color: #fff;
            box-shadow: none;
            border: 1px solid #1DB954;
        }
        
        .input-group-text {
            background-color: #282828;
            border: none;
            color: #1DB954;
        }
        
        button[type="submit"] {
            background-color: #1DB954;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 30px;
            width: 100%;
            font-weight: bold;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        
        button[type="submit"]:hover {
            background-color: #1ed760;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            border: 1px solid #2ecc71;
            color: #2ecc71;
        }
        
        .password-input-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #1DB954;
            cursor: pointer;
            z-index: 10;
        }
        
        .password-strength {
            margin-bottom: 20px;
        }
        
        .strength-meter {
            height: 5px;
            background-color: #444;
            border-radius: 3px;
            margin-bottom: 5px;
        }
        
        .strength-meter-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s;
        }
        
        .strength-meter-fill[data-strength="0"] {
            width: 0%;
            background-color: transparent;
        }
        
        .strength-meter-fill[data-strength="1"] {
            width: 25%;
            background-color: #e74c3c;
        }
        
        .strength-meter-fill[data-strength="2"] {
            width: 50%;
            background-color: #f39c12;
        }
        
        .strength-meter-fill[data-strength="3"] {
            width: 75%;
            background-color: #3498db;
        }
        
        .strength-meter-fill[data-strength="4"] {
            width: 100%;
            background-color: #2ecc71;
        }
        
        .strength-text {
            font-size: 0.8rem;
            text-align: right;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #1DB954;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .form-check-input {
            background-color: #282828;
            border-color: #1DB954;
        }
        
        .form-check-input:checked {
            background-color: #1DB954;
            border-color: #1DB954;
        }
        
        .form-label {
            margin-bottom: 5px;
        }
        
        .artist-option {
            margin-top: 15px;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            background-color: #282828;
            border: 1px solid #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-logo">
                <img src="../assets/img/logo.jpg" alt="BassCulture Logo">
            </div>
            <h1>Crear Cuenta</h1>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form id="register-form" method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <label for="register-name" class="form-label">Nombre *</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="register-name" name="nombre" class="form-control" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" />
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="register-lastname1" class="form-label">Primer Apellido *</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="register-lastname1" name="apellido1" class="form-control" required value="<?php echo isset($_POST['apellido1']) ? htmlspecialchars($_POST['apellido1']) : ''; ?>" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="register-lastname2" class="form-label">Segundo Apellido</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="register-lastname2" name="apellido2" class="form-control" value="<?php echo isset($_POST['apellido2']) ? htmlspecialchars($_POST['apellido2']) : ''; ?>" />
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="register-username" class="form-label">Nombre de Usuario *</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                            <input type="text" id="register-username" name="username" class="form-control" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="register-email" class="form-label">Correo Electrónico *</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="register-email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="register-phone" class="form-label">Teléfono</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" id="register-phone" name="telefono" class="form-control" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>" />
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <label for="register-password" class="form-label">Contraseña *</label>
                        <div class="input-group mb-3 password-input-container">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="register-password" name="password" class="form-control" required minlength="6" />
                            <button type="button" class="toggle-password" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="register-confirm-password" class="form-label">Confirmar Contraseña *</label>
                        <div class="input-group mb-3 password-input-container">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="register-confirm-password" name="confirm_password" class="form-control" required minlength="6" />
                            <button type="button" class="toggle-password" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>
                
                <div class="password-strength">
                    <div class="strength-meter">
                        <div class="strength-meter-fill" data-strength="0"></div>
                    </div>
                    <div class="strength-text">Fuerza de la contraseña: <span>Débil</span></div>
                </div>
                
                <div class="artist-option">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is-artist" name="is_artist" <?php echo isset($_POST['is_artist']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is-artist">
                            Registrarme como artista
                        </label>
                    </div>
                    <small class="text-muted">Al seleccionar esta opción, podrás subir tu música a la plataforma.</small>
                </div>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        Acepto los <a href="#" class="text-primary">términos y condiciones</a> y la <a href="#" class="text-primary">política de privacidad</a>
                    </label>
                </div>
                
                <button type="submit" name="register" id="register-submit-btn"><i class="fas fa-user-plus"></i> Registrarse</button>
                
                <div class="login-link">
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar botones para mostrar/ocultar contraseña
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.password-input-container').querySelector('input');
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                
                // Cambiar el icono
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
        });
        
        // Medidor de fuerza de contraseña
        const passwordInput = document.getElementById('register-password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strengthMeter = document.querySelector('.strength-meter-fill');
                const strengthText = document.querySelector('.strength-text span');
                
                // Calcular fuerza de la contraseña
                let strength = 0;
                
                // Longitud
                if (password.length >= 8) {
                    strength += 1;
                }
                
                // Letras mayúsculas y minúsculas
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
                    strength += 1;
                }
                
                // Números
                if (password.match(/\d/)) {
                    strength += 1;
                }
                
                // Caracteres especiales
                if (password.match(/[^a-zA-Z0-9]/)) {
                    strength += 1;
                }
                
                // Actualizar UI
                strengthMeter.setAttribute('data-strength', strength);
                
                // Actualizar texto
                switch (strength) {
                    case 0:
                        strengthText.textContent = 'Débil';
                        strengthText.style.color = '#e74c3c';
                        break;
                    case 1:
                        strengthText.textContent = 'Débil';
                        strengthText.style.color = '#e74c3c';
                        break;
                    case 2:
                        strengthText.textContent = 'Moderada';
                        strengthText.style.color = '#f39c12';
                        break;
                    case 3:
                        strengthText.textContent = 'Buena';
                        strengthText.style.color = '#3498db';
                        break;
                    case 4:
                        strengthText.textContent = 'Fuerte';
                        strengthText.style.color = '#2ecc71';
                        break;
                }
            });
        }
    });
    </script>
</body>
</html>
