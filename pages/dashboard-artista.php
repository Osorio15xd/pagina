<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado y es artista
if (!isset($_SESSION['user_id']) || !isset($_SESSION['artist_id'])) {
    header('Location: index.php');
    exit;
}

// Incluir la conexión a la base de datos
require_once 'config/db_connect.php';

$artistId = $_SESSION['artist_id'];
$userId = $_SESSION['user_id'];

// Obtener información del artista
try {
    $stmt = $pdo->prepare("
        SELECT a.*, u.nombre, u.apellido1, u.apellido2, u.nombre_usuario, u.correo, u.telefono, u.foto_perfil
        FROM artista a
        JOIN usuario u ON a.usuario = u.id_usuario
        WHERE a.id_artista = ?
    ");
    $stmt->execute([$artistId]);
    $artista = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$artista) {
        header('Location: index.php');
        exit;
    }
    
    // Obtener estadísticas
    // Total de canciones
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM canciones WHERE id_artista = ?
    ");
    $stmt->execute([$artistId]);
    $totalCanciones = $stmt->fetchColumn();
    
    // Total de álbumes
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM album WHERE id_artista = ?
    ");
    $stmt->execute([$artistId]);
    $totalAlbumes = $stmt->fetchColumn();
    
    // Total de sencillos
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM sencillos WHERE id_artista = ?
    ");
    $stmt->execute([$artistId]);
    $totalSencillos = $stmt->fetchColumn();
    
    // Total de ventas
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM compras 
        WHERE (tipo_producto = 'cancion' AND id_producto IN (SELECT id_cancion FROM canciones WHERE id_artista = ?))
        OR (tipo_producto = 'album' AND id_producto IN (SELECT id_album FROM album WHERE id_artista = ?))
        OR (tipo_producto = 'sencillo' AND id_producto IN (SELECT id_sencillo FROM sencillos WHERE id_artista = ?))
    ");
    $stmt->execute([$artistId, $artistId, $artistId]);
    $totalVentas = $stmt->fetchColumn();
    
    // Ingresos totales
    $stmt = $pdo->prepare("
        SELECT SUM(precio) as total FROM compras 
        WHERE (tipo_producto = 'cancion' AND id_producto IN (SELECT id_cancion FROM canciones WHERE id_artista = ?))
        OR (tipo_producto = 'album' AND id_producto IN (SELECT id_album FROM album WHERE id_artista = ?))
        OR (tipo_producto = 'sencillo' AND id_producto IN (SELECT id_sencillo FROM sencillos WHERE id_artista = ?))
    ");
    $stmt->execute([$artistId, $artistId, $artistId]);
    $totalIngresos = $stmt->fetchColumn() ?: 0;
    
} catch (PDOException $e) {
    die("Error al obtener información: " . $e->getMessage());
}

// Incluir el encabezado
include_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#canciones" data-bs-toggle="tab">
                            <i class="fas fa-music me-2"></i>
                            Mis Canciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#albumes" data-bs-toggle="tab">
                            <i class="fas fa-compact-disc me-2"></i>
                            Mis Álbumes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sencillos" data-bs-toggle="tab">
                            <i class="fas fa-play me-2"></i>
                            Mis Sencillos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ventas" data-bs-toggle="tab">
                            <i class="fas fa-chart-line me-2"></i>
                            Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#subir" data-bs-toggle="tab">
                            <i class="fas fa-upload me-2"></i>
                            Subir Música
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-9 col-lg-10">
            <div class="tab-content">
                <!-- Dashboard -->
                <div class="tab-pane fade show active" id="dashboard">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Dashboard de Artista</h1>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Canciones</h6>
                                            <h2 class="mb-0"><?php echo $totalCanciones; ?></h2>
                                        </div>
                                        <i class="fas fa-music fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Álbumes</h6>
                                            <h2 class="mb-0"><?php echo $totalAlbumes; ?></h2>
                                        </div>
                                        <i class="fas fa-compact-disc fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Sencillos</h6>
                                            <h2 class="mb-0"><?php echo $totalSencillos; ?></h2>
                                        </div>
                                        <i class="fas fa-play fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card text-white bg-danger">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Ventas</h6>
                                            <h2 class="mb-0"><?php echo $totalVentas; ?></h2>
                                        </div>
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Resumen de Ingresos</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2 class="mb-3">$<?php echo number_format($totalIngresos, 2); ?></h2>
                                    <p class="text-muted">Ingresos totales hasta la fecha</p>
                                </div>
                                <div class="col-md-6">
                                    <canvas id="ingresos-chart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Canciones -->
                <div class="tab-pane fade" id="canciones">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Mis Canciones</h1>
                        <button class="btn btn-primary" id="btn-subir-cancion">
                            <i class="fas fa-plus me-1"></i> Subir Canción
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Álbum</th>
                                    <th>Género</th>
                                    <th>Fecha</th>
                                    <th>Precio</th>
                                    <th>Ventas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="canciones-lista">
                                <!-- Aquí se cargarán las canciones del artista -->
                                <tr>
                                    <td colspan="8" class="text-center">Cargando canciones...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Álbumes -->
                <div class="tab-pane fade" id="albumes">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Mis Álbumes</h1>
                        <button class="btn btn-primary" id="btn-crear-album">
                            <i class="fas fa-plus me-1"></i> Crear Álbum
                        </button>
                    </div>
                    
                    <div class="row" id="albumes-container">
                        <!-- Aquí se cargarán los álbumes del artista -->
                        <div class="col-12 text-center">
                            <p>Cargando álbumes...</p>
                        </div>
                    </div>
                </div>

                <!-- Sencillos -->
                <div class="tab-pane fade" id="sencillos">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Mis Sencillos</h1>
                        <button class="btn btn-primary" id="btn-subir-sencillo">
                            <i class="fas fa-plus me-1"></i> Subir Sencillo
                        </button>
                    </div>
                    
                    <div class="row" id="sencillos-container">
                        <!-- Aquí se cargarán los sencillos del artista -->
                        <div class="col-12 text-center">
                            <p>Cargando sencillos...</p>
                        </div>
                    </div>
                </div>

                <!-- Ventas -->
                <div class="tab-pane fade" id="ventas">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Mis Ventas</h1>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Resumen de Ventas</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="ventas-chart" width="400" height="200"></canvas>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody id="ventas-lista">
                                <!-- Aquí se cargarán las ventas del artista -->
                                <tr>
                                    <td colspan="6" class="text-center">Cargando ventas...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Subir Música -->
                <div class="tab-pane fade" id="subir">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Subir Música</h1>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-music fa-4x mb-3 text-primary"></i>
                                    <h5 class="card-title">Subir Canción</h5>
                                    <p class="card-text">Sube una canción a un álbum existente.</p>
                                    <button class="btn btn-primary" id="btn-subir-cancion-2">
                                        <i class="fas fa-upload me-1"></i> Subir Canción
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-compact-disc fa-4x mb-3 text-success"></i>
                                    <h5 class="card-title">Crear Álbum</h5>
                                    <p class="card-text">Crea un nuevo álbum para tus canciones.</p>
                                    <button class="btn btn-success" id="btn-crear-album-2">
                                        <i class="fas fa-plus me-1"></i> Crear Álbum
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-play fa-4x mb-3 text-info"></i>
                                    <h5 class="card-title">Subir Sencillo</h5>
                                    <p class="card-text">Sube un sencillo independiente.</p>
                                    <button class="btn btn-info" id="btn-subir-sencillo-2">
                                        <i class="fas fa-upload me-1"></i> Subir Sencillo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar gráficos
    const ingresosChart = new Chart(document.getElementById('ingresos-chart'), {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ingresos',
                data: [500, 800, 1200, 1000, 1500, 2000, 1800, 2200, 2500, 2300, 2800, 3000],
                borderColor: 'rgba(40, 167, 69, 1)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
    
    const ventasChart = new Chart(document.getElementById('ventas-chart'), {
        type: 'bar',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas',
                data: [10, 15, 20, 18, 25, 30, 28, 35, 40, 38, 45, 50],
                backgroundColor: 'rgba(0, 123, 255, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Cargar datos del artista
    function cargarCanciones() {
        fetch('api/artist.php?action=get_songs&artist_id=<?php echo $artistId; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cancionesLista = document.getElementById('canciones-lista');
                    if (data.songs.length > 0) {
                        let html = '';
                        data.songs.forEach((cancion, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${cancion.nombre_cancion}</td>
                                    <td>${cancion.nombre_album || 'N/A'}</td>
                                    <td>${cancion.nombre_genero}</td>
                                    <td>${cancion.fecha_lanzamiento}</td>
                                    <td>$${cancion.precio}</td>
                                    <td>${cancion.ventas || 0}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-song" data-id="${cancion.id_cancion}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-song" data-id="${cancion.id_cancion}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        cancionesLista.innerHTML = html;
                    } else {
                        cancionesLista.innerHTML = '<tr><td colspan="8" class="text-center">No hay canciones disponibles</td></tr>';
                    }
                } else {
                    console.error('Error al cargar canciones:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    function cargarAlbumes() {
        fetch('api/artist.php?action=get_albums&artist_id=<?php echo $artistId; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const albumesContainer = document.getElementById('albumes-container');
                    if (data.albums.length > 0) {
                        let html = '';
                        data.albums.forEach(album => {
                            html += `
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <img src="${album.imagen_album_path || 'assets/img/default-album.jpg'}" class="card-img-top" alt="${album.nombre_album}">
                                        <div class="card-body">
                                            <h5 class="card-title">${album.nombre_album}</h5>
                                            <p class="card-text">${album.nombre_genero}</p>
                                            <p class="card-text"><small class="text-muted">Lanzado: ${album.fecha_lanzamiento}</small></p>
                                            <div class="d-flex justify-content-between">
                                                <button class="btn btn-sm btn-primary edit-album" data-id="${album.id_album}">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-album" data-id="${album.id_album}">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        albumesContainer.innerHTML = html;
                    } else {
                        albumesContainer.innerHTML = '<div class="col-12 text-center"><p>No hay álbumes disponibles</p></div>';
                    }
                } else {
                    console.error('Error al cargar álbumes:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    function cargarSencillos() {
        fetch('api/artist.php?action=get_singles&artist_id=<?php echo $artistId; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const sencillosContainer = document.getElementById('sencillos-container');
                    if (data.singles.length > 0) {
                        let html = '';
                        data.singles.forEach(sencillo => {
                            html += `
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <img src="${sencillo.imagen_sencillo_path || 'assets/img/default-single.jpg'}" class="card-img-top" alt="${sencillo.nombre_sencillo}">
                                        <div class="card-body">
                                            <h5 class="card-title">${sencillo.nombre_sencillo}</h5>
                                            <p class="card-text">${sencillo.nombre_genero}</p>
                                            <p class="card-text"><small class="text-muted">Lanzado: ${sencillo.fecha_lanzamiento}</small></p>
                                            <div class="d-flex justify-content-between">
                                                <button class="btn btn-sm btn-primary edit-single" data-id="${sencillo.id_sencillo}">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-single" data-id="${sencillo.id_sencillo}">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        sencillosContainer.innerHTML = html;
                    } else {
                        sencillosContainer.innerHTML = '<div class="col-12 text-center"><p>No hay sencillos disponibles</p></div>';
                    }
                } else {
                    console.error('Error al cargar sencillos:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    function cargarVentas() {
        fetch('api/artist.php?action=get_sales&artist_id=<?php echo $artistId; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const ventasLista = document.getElementById('ventas-lista');
                    if (data.sales.length > 0) {
                        let html = '';
                        data.sales.forEach(venta => {
                            html += `
                                <tr>
                                    <td>${venta.id_compra}</td>
                                    <td>${venta.nombre_producto}</td>
                                    <td>${venta.tipo_producto}</td>
                                    <td>${venta.fecha_compra}</td>
                                    <td>${venta.nombre_cliente}</td>
                                    <td>$${venta.precio}</td>
                                </tr>
                            `;
                        });
                        ventasLista.innerHTML = html;
                    } else {
                        ventasLista.innerHTML = '<tr><td colspan="6" class="text-center">No hay ventas disponibles</td></tr>';
                    }
                } else {
                    console.error('Error al cargar ventas:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    // Cargar datos iniciales
    cargarCanciones();
    cargarAlbumes();
    cargarSencillos();
    cargarVentas();
    
    // Eventos para los botones de subir/crear
    document.getElementById('btn-subir-cancion').addEventListener('click', function() {
        alert('Funcionalidad de subir canción en desarrollo');
    });
    
    document.getElementById('btn-crear-album').addEventListener('click', function() {
        alert('Funcionalidad de crear álbum en desarrollo');
    });
    
    document.getElementById('btn-subir-sencillo').addEventListener('click', function() {
        alert('Funcionalidad de subir sencillo en desarrollo');
    });
    
    document.getElementById('btn-subir-cancion-2').addEventListener('click', function() {
        alert('Funcionalidad de subir canción en desarrollo');
    });
    
    document.getElementById('btn-crear-album-2').addEventListener('click', function() {
        alert('Funcionalidad de crear álbum en desarrollo');
    });
    
    document.getElementById('btn-subir-sencillo-2').addEventListener('click', function() {
        alert('Funcionalidad de subir sencillo en desarrollo');
    });
    
    // Eventos para las pestañas
    document.querySelectorAll('.nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Desactivar todas las pestañas
            document.querySelectorAll('.nav-link').forEach(t => {
                t.classList.remove('active');
            });
            
            // Activar la pestaña actual
            this.classList.add('active');
            
            // Mostrar el contenido correspondiente
            const target = this.getAttribute('href').substring(1);
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            document.getElementById(target).classList.add('show', 'active');
        });
    });
});
</script>

<style>
.sidebar {
    background-color: var(--bg-secondary);
    border-right: 1px solid var(--border-color);
    min-height: calc(100vh - 56px);
}

.sidebar .nav-link {
    color: var(--text-color);
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
}

.sidebar .nav-link:hover {
    background-color: var(--bg-card);
}

.sidebar .nav-link.active {
    color: var(--primary-color);
    background-color: var(--bg-card);
    font-weight: 600;
}

.sidebar .nav-link i {
    margin-right: 0.5rem;
}

.card {
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.text-muted {
    color: var(--text-secondary) !important;
}

.table {
    color: var(--text-color);
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: var(--bg-card);
}

.table-hover tbody tr:hover {
    background-color: rgba(var(--primary-color-rgb), 0.1);
}
</style>

<?php include_once 'includes/footer.php'; ?>
