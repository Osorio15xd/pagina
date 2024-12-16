<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'funcioncarrito.php';
require_once 'conexion.php';

error_log(print_r($_SESSION, true));

$user_logged_in = isset($_SESSION['user_id']);
$username = $user_logged_in ? $_SESSION['username'] : '';
$id_usuario = $user_logged_in ? $_SESSION['user_id'] : null;
$items_carrito = $user_logged_in ? obtenerItemsCarrito($conexion, $id_usuario) : [];

$cart_count = 0;
if ($items_carrito) {
    foreach ($items_carrito as $item) {
        $cart_count += $item['cantidad'];
    }
}

$_SESSION['cart_count'] = $cart_count;

// Restore the artist detection functionality
$is_artist = false;
if ($user_logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conexion->prepare("SELECT u.nombre, u.nombre_usuario, u.apellido1, u.id_artista, a.foto_path FROM usuario u LEFT JOIN artista a ON u.id_usuario = a.usuario WHERE u.id_usuario = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $full_name = $user_data['nombre_usuario'];
    $is_artist = $user_data['id_artista'] == 1;
    $profile_pic = $is_artist ? $user_data['foto_path'] : null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BassCulture</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="principal.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .main-header {
            background-color: #2f4538;
            border-bottom: 1px solid var(--oscuro);
            padding: 1rem 0;
        }
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
        .nav-item {
            list-style-type: none;
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -15px;
            background-color: #2f4538;
            color: white;
            border-radius: 45%;
            padding: 5px 8px;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
        }
        .secondary-nav {
            font-family: 'Poppins', sans-serif;
        }
        .secondary-nav .nav-link {
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .secondary-nav .dropdown-menu {
            border-radius: 0;
            border: none;
            background-color: #1a1a1a;
        }
        .secondary-nav .dropdown-item {
            color: #fff;
            font-weight: 400;
            padding: 0.5rem 1rem;
            transition: background-color 0.3s ease;
        }
        .secondary-nav .dropdown-item:hover {
            background-color: #333;
        }
        @media (max-width: 768px) {
            .main-header .navbar-collapse,
            .secondary-nav .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: #2f4538;
                z-index: 1000;
            }
            .main-header .navbar-nav,
            .secondary-nav .navbar-nav {
                padding: 1rem;
            }
            .main-header .nav-link,
            .secondary-nav .nav-link {
                padding: 0.5rem 1rem;
            }
            .fixed-top-mobile {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1030;
                background-color: #2f4538;
                padding: 0.5rem;
            }
            .mobile-search-cart {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem; /* Espaciado alrededor */
    background-color: #2f4538; /* Color de fondo */
}

.mobile-search-cart .form-control {
    max-width: 150px; /* Ancho máximo del campo de búsqueda */
    flex-grow: 1; /* Permite que el campo de búsqueda crezca */
}

.cart-count {
    position: absolute;
    top: -5px;
    right: -15px;
    background-color: #2f4538;
    color: white;
    border-radius: 50%;
    padding: 5px 8px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
}
            body {
                padding-top: 56px;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <nav class="navbar navbar-expand-md navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="150.png" alt="BassCulture Logo" style="max-width: 100px;">
                    <h1 class="titulo d-inline-block ml-2">BassCulture</h1>
                </a>
                <div class="d-md-none fixed-top-mobile">
    <div class="mobile-search-cart d-flex justify-content-between align-items-center">
        <form class="d-flex flex-grow-1 me-2">
            <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search">
            <button class="btn btn-outline-light" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a class="nav-link position-relative" href="Carrito.php">
            <i class="fas fa-shopping-cart"></i>
            <?php if ($cart_count > 0): ?>
                <span class="cart-count"><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
</div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <form class="d-none d-md-flex mx-auto my-2 my-lg-0" id="searchForm">
                        <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search" id="searchInput">
                        <button class="btn btn-outline-success" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <?php if ($user_logged_in): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">
                                    <?php if ($profile_pic): ?>
                                        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Foto de perfil" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle"></i>
                                    <?php endif; ?>
                                    <span class="d-md-none d-lg-inline"><?php echo htmlspecialchars($full_name); ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="cerrar_sesion.php">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="d-md-none d-lg-inline">Cerrar sesión</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="inicio.php">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span class="d-md-none d-lg-inline">Iniciar sesión</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link position-relative" href="Carrito.php">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cart_count > 0): ?>
                                    <span class="cart-count"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <nav class="secondary-nav navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#secondaryNav" aria-controls="secondaryNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="secondaryNav">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="generos-btn" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-music"></i> Géneros
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="generos-btn">
                            <li><a class="dropdown-item" href="Electronic.php">Electronic</a></li>
                            <li><a class="dropdown-item" href="HipHop.php">Hip Hop</a></li>
                            <li><a class="dropdown-item" href="Indie.php">Indie</a></li>
                            <li><a class="dropdown-item" href="Pop.php">Pop</a></li>
                            <li><a class="dropdown-item" href="Rap.php">Rap</a></li>
                            <li><a class="dropdown-item" href="Rock.php">Rock</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sencillos.php"><i class="fas fa-compact-disc"></i> Sencillos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="artistas.php"><i class="fas fa-user-friends"></i> Artistas</a>
                    </li>
                    <?php if ($user_logged_in && $is_artist): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="subir_cancion.php"><i class="fas fa-upload"></i> Subir Sencillo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="subir_album.php"><i class="fas fa-folder-plus"></i> Subir álbum</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="eliminarmodif.php"><i class="fas fa-edit"></i> Eliminar/Modificar Sencillos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="eliminarmodifal.php"><i class="fas fa-tasks"></i> Eliminar/Modificar Album</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (!$user_logged_in): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <!-- Your main content goes here -->
    </main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl)
    });

    // Handle genres dropdown
    var generosBtn = document.getElementById('generos-btn');
    var generosDropdown = generosBtn.nextElementSibling;
    if (generosBtn && generosDropdown) {
        generosBtn.addEventListener('click', function(event) {
            if (window.innerWidth < 768) {
                event.preventDefault();
                event.stopPropagation();
                generosDropdown.classList.toggle('show');
            }
        });

        // Close genres dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!generosBtn.contains(event.target) && !generosDropdown.contains(event.target)) {
                generosDropdown.classList.remove('show');
            }
        });

        // Prevent dropdown from closing when clicking inside
        generosDropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    // Toggle menu on hamburger click
    function setupNavToggle(navId, togglerId) {
        var nav = document.getElementById(navId);
        var toggler = document.querySelector(togglerId);
        if (nav && toggler) {
            toggler.addEventListener('click', function() {
                nav.classList.toggle('show');
            });
        }
    }

    setupNavToggle('mainNav', '.main-header .navbar-toggler');
    setupNavToggle('secondaryNav', '.secondary-nav .navbar-toggler');

    // Close navbars when clicking outside
    document.addEventListener('click', function(event) {
        var mainNav = document.getElementById('mainNav');
        var secondaryNav = document.getElementById('secondaryNav');
        var mainToggler = document.querySelector('.main-header .navbar-toggler');
        var secondaryToggler = document.querySelector('.secondary-nav .navbar-toggler');

        if (mainNav && mainToggler && !mainNav.contains(event.target) && !mainToggler.contains(event.target)) {
            mainNav.classList.remove('show');
        }

        if (secondaryNav && secondaryToggler && !secondaryNav.contains(event.target) && !secondaryToggler.contains(event.target)) {
            secondaryNav.classList.remove('show');
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchTerm = searchInput.value.trim();
        if (searchTerm) {
            window.location.href = `resultados.php?q=${encodeURIComponent(searchTerm)}`;
        }
    });
});
</script>
</body>
</html>

