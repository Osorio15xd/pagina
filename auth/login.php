<?php
session_start();
require_once '../config/db_connect.php';

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';

// Procesar el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        try {
            // Buscar usuario por email
            $stmt = $pdo->prepare("SELECT id_usuario, nombre, nombre_usuario, correo, contraseña, id_artista, id_cliente FROM usuario WHERE correo = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['contraseña'])) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['username'] = $user['nombre_usuario'];
                $_SESSION['user_email'] = $user['correo'];
                $_SESSION['user_name'] = $user['nombre'];
                
                // Verificar si es artista
                if ($user['id_artista'] == 1) {
                    $stmt = $pdo->prepare("SELECT id_artista FROM artista WHERE usuario = ?");
                    $stmt->execute([$user['id_usuario']]);
                    $artista = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($artista) {
                        $_SESSION['artist_id'] = $artista['id_artista'];
                    }
                }
                
                // Redirigir a la página principal
                header('Location: ../index.php');
                exit;
            } else {
                $error = 'Email o contraseña incorrectos.';
            }
        } catch (PDOException $e) {
            $error = 'Error al iniciar sesión: ' . $e->getMessage();
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
    <title>Iniciar Sesión - BassCulture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #121212;
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        
        .login-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background-color: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo img {
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
            margin-bottom: 20px;
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
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 5px;
        }
        
        .forgot-password {
            color: #1DB954;
            text-decoration: none;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
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
        
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .register-link a {
            color: #1DB954;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .social-login {
            margin-top: 30px;
            text-align: center;
        }
        
        .social-login p {
            margin-bottom: 15px;
            position: relative;
        }
        
        .social-login p:before,
        .social-login p:after {
            content: "";
            position: absolute;
            top: 50%;
            width: 35%;
            height: 1px;
            background-color: #444;
        }
        
        .social-login p:before {
            left: 0;
        }
        
        .social-login p:after {
            right: 0;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #282828;
            color: #fff;
            font-size: 20px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            transform: translateY(-3px);
        }
        
        .social-icons a.facebook:hover {
            background-color: #3b5998;
        }
        
        .social-icons a.google:hover {
            background-color: #db4437;
        }
        
        .social-icons a.twitter:hover {
            background-color: #1da1f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-logo">
                <img src="../assets/img/logo.jpg" alt="BassCulture Logo">
            </div>
            <h1>Iniciar Sesión</h1>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form id="login-form" method="POST" action="">
                <div class="input-group mb-3">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" id="login-email" name="email" class="form-control" placeholder="Correo electrónico" required />
                </div>
                
                <div class="input-group mb-3 password-input-container">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" id="login-password" name="password" class="form-control" placeholder="Contraseña" required />
                    <button type="button" class="toggle-password" tabindex="-1"><i class="fas fa-eye"></i></button>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember-me" name="remember_me">
                        <label for="remember-me">Recordarme</label>
                    </div>
                    <a href="../index.php?page=recover" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>
                
                <button type="submit" name="login" id="login-submit-btn"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
                
                <div class="register-link">
                    <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
                </div>
                
                <div class="social-login">
                    <p>O inicia sesión con</p>
                    <div class="social-icons">
                        <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="google"><i class="fab fa-google"></i></a>
                        <a href="#" class="twitter"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar botón para mostrar/ocultar contraseña
        const toggleButton = document.querySelector('.toggle-password');
        
        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                const input = document.getElementById('login-password');
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
        }
    });
    </script>
</body>
</html>
