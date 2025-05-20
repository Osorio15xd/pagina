<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay un tema guardado en la sesión
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
$bodyClass = $theme === 'dark' ? 'dark-mode' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BassCulture - Tu plataforma de música</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass; ?>">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/img/logo.jpg" alt="BassCulture Logo" width="40" height="40" class="d-inline-block align-text-top">
                    BassCulture
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo !isset($_GET['page']) || $_GET['page'] === 'inicio' ? 'active' : ''; ?>" href="inicio.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] === 'exitos' ? 'active' : ''; ?>" href="exitos.php">Éxitos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] === 'artistas' ? 'active' : ''; ?>" href="artistas.php">Artistas</a>
                        </li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isset($_GET['page']) && $_GET['page'] === 'biblioteca' ? 'active' : ''; ?>" href="biblioteca.php">Mi Biblioteca</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <form class="d-flex me-3" action="index.php" method="GET">
                        <input type="hidden" name="page" value="buscar">
                        <input class="form-control me-2" type="search" name="q" placeholder="Buscar música..." aria-label="Buscar">
                        <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="d-flex align-items-center">
                        <button id="theme-toggle" class="btn btn-outline-secondary me-2">
                            <?php if ($theme === 'dark'): ?>
                                <i class="fas fa-sun"></i>
                            <?php else: ?>
                                <i class="fas fa-moon"></i>
                            <?php endif; ?>
                        </button>
                        <a href="music-store.php" class="btn btn-outline-primary position-relative me-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                                0
                            </span>
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i> <?php echo $_SESSION['username']; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user-circle me-2"></i> Mi Perfil</a></li>
                                    <?php if (isset($_SESSION['artist_id'])): ?>
                                        <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-chart-line me-2"></i> Dashboard de Artista</a></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="configuraciones.php"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-primary me-2"><i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión</a>
                            <a href="auth/register.php" class="btn btn-outline-primary"><i class="fas fa-user-plus me-1"></i> Registrarse</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main>

