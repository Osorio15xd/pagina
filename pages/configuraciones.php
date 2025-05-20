<?php
session_start();
require_once '../config/db_connect.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Obtener información del usuario
try {
    $stmt = $pdo->prepare("
        SELECT * FROM usuario WHERE id_usuario = ?
    ");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        header('Location: ../index.php');
        exit;
    }
    
} catch (PDOException $e) {
    die("Error al obtener información: " . $e->getMessage());
}

// Incluir el encabezado
include_once '../includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Configuración</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#cuenta" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="fas fa-user me-2"></i> Cuenta
                    </a>
                    <a href="#seguridad" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-lock me-2"></i> Seguridad
                    </a>
                    <a href="#apariencia" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-paint-brush me-2"></i> Apariencia
                    </a>
                    <a href="#notificaciones" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-bell me-2"></i> Notificaciones
                    </a>
                    <a href="#privacidad" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="fas fa-shield-alt me-2"></i> Privacidad
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Cuenta -->
                <div class="tab-pane fade show active" id="cuenta">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Información de Cuenta</h5>
                        </div>
                        <div class="card-body">
                            <form id="form-cuenta">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" value="<?php echo $usuario['nombre']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido1" class="form-label">Primer Apellido</label>
                                        <input type="text" class="form-control" id="apellido1" value="<?php echo $usuario['apellido1']; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="apellido2" class="form-label">Segundo Apellido</label>
                                        <input type="text" class="form-control" id="apellido2" value="<?php echo $usuario['apellido2']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                                        <input type="text" class="form-control" id="nombre_usuario" value="<?php echo $usuario['nombre_usuario']; ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="correo" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="correo" value="<?php echo $usuario['correo']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="telefono" value="<?php echo $usuario['telefono']; ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Seguridad -->
                <div class="tab-pane fade" id="seguridad">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Cambiar Contraseña</h5>
                        </div>
                        <div class="card-body">
                            <form id="form-password">
                                <div class="mb-3">
                                    <label for="password-actual" class="form-label">Contraseña Actual</label>
                                    <input type="password" class="form-control" id="password-actual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password-nuevo" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password-nuevo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password-confirmar" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password-confirmar" required>
                                </div>
                                <div class="password-strength mb-3">
                                    <div class="strength-meter">
                                        <div class="strength-meter-fill" data-strength="0"></div>
                                    </div>
                                    <div class="strength-text">Fuerza de la contraseña: <span>Débil</span></div>
                                </div>
                                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Sesiones Activas</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Este dispositivo</h6>
                                        <small class="text-success">Activo ahora</small>
                                    </div>
                                    <p class="mb-1">Navegador: <?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
                                    <small>IP: <?php echo $_SERVER['REMOTE_ADDR']; ?></small>
                                </div>
                            </div>
                            <button class="btn btn-danger mt-3">Cerrar todas las sesiones</button>
                        </div>
                    </div>
                </div>
                
                <!-- Apariencia -->
                <div class="tab-pane fade" id="apariencia">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tema</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tema" id="tema-claro" value="light" checked>
                                    <label class="form-check-label" for="tema-claro">Tema Claro</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tema" id="tema-oscuro" value="dark">
                                    <label class="form-check-label" for="tema-oscuro">Tema Oscuro</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tema" id="tema-sistema" value="system">
                                    <label class="form-check-label" for="tema-sistema">Usar tema del sistema</label>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body bg-light text-dark">
                                            <h6 class="card-title">Vista previa: Tema Claro</h6>
                                            <p class="card-text">Así se verá el tema claro en la aplicación.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body bg-dark text-light">
                                            <h6 class="card-title">Vista previa: Tema Oscuro</h6>
                                            <p class="card-text">Así se verá el tema oscuro en la aplicación.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary" id="guardar-tema">Guardar Preferencias</button>
                        </div>
                    </div>
                </div>
                
                <!-- Notificaciones -->
                <div class="tab-pane fade" id="notificaciones">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Preferencias de Notificaciones</h5>
                        </div>
                        <div class="card-body">
                            <form id="form-notificaciones">
                                <h6 class="mb-3">Notificaciones en la aplicación</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notif-nuevas-canciones" checked>
                                        <label class="form-check-label" for="notif-nuevas-canciones">Nuevas canciones de artistas que sigo</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notif-playlist" checked>
                                        <label class="form-check-label" for="notif-playlist">Actualizaciones de playlists</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notif-ofertas" checked>
                                        <label class="form-check-label" for="notif-ofertas">Ofertas y promociones</label>
                                    </div>
                                </div>
                                
                                <h6 class="mb-3 mt-4">Notificaciones por correo electrónico</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email-nuevas-canciones" checked>
                                        <label class="form-check-label" for="email-nuevas-canciones">Nuevas canciones de artistas que sigo</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email-playlist" checked>
                                        <label class="form-check-label" for="email-playlist">Actualizaciones de playlists</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email-ofertas" checked>
                                        <label class="form-check-label" for="email-ofertas">Ofertas y promociones</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="email-newsletter" checked>
                                        <label class="form-check-label" for="email-newsletter">Newsletter semanal</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Privacidad -->
                <div class="tab-pane fade" id="privacidad">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configuración de Privacidad</h5>
                        </div>
                        <div class="card-body">
                            <form id="form-privacidad">
                                <h6 class="mb-3">Visibilidad del perfil</h6>
                                <div class="mb-3">
                                    <label class="form-label">¿Quién puede ver tu perfil?</label>
                                    <select class="form-select" id="perfil-visibilidad">
                                        <option value="public">Todos</option>
                                        <option value="followers">Solo seguidores</option>
                                        <option value="private">Solo yo</option>
                                    </select>
                                </div>
                                
                                <h6 class="mb-3 mt-4">Historial de reproducción</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="historial-publico">
                                        <label class="form-check-label" for="historial-publico">Hacer público mi historial de reproducción</label>
                                    </div>
                                </div>
                                
                                <h6 class="mb-3 mt-4">Playlists</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="playlists-publicas" checked>
                                        <label class="form-check-label" for="playlists-publicas">Hacer públicas mis playlists por defecto</label>
                                    </div>
                                </div>
                                
                                <h6 class="mb-3 mt-4">Datos y privacidad</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="recomendaciones-personalizadas" checked>
                                        <label class="form-check-label" for="recomendaciones-personalizadas">Permitir recomendaciones personalizadas basadas en mi historial</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="compartir-datos" checked>
                                        <label class="form-check-label" for="compartir-datos">Compartir datos de uso anónimos para mejorar el servicio</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                                
                                <hr class="my-4">
                                
                                <h6 class="text-danger">Zona de peligro</h6>
                                <p class="text-muted">Estas acciones son irreversibles. Ten cuidado.</p>
                                
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalExportarDatos">
                                        Exportar mis datos
                                    </button>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarCuenta">
                                        Eliminar mi cuenta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Exportar Datos -->
<div class="modal fade" id="modalExportarDatos" tabindex="-1" aria-labelledby="modalExportarDatosLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalExportarDatosLabel">Exportar mis datos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Puedes solicitar una copia de todos tus datos personales que tenemos almacenados. Esto incluye:</p>
                <ul>
                    <li>Información de perfil</li>
                    <li>Historial de reproducciones</li>
                    <li>Playlists</li>
                    <li>Compras</li>
                </ul>
                <p>El proceso puede tardar hasta 48 horas. Recibirás un correo electrónico cuando tus datos estén listos para descargar.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Solicitar mis datos</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar Cuenta -->
<div class="modal fade" id="modalEliminarCuenta" tabindex="-1" aria-labelledby="modalEliminarCuentaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarCuentaLabel">Eliminar mi cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Advertencia!</strong> Esta acción es irreversible.
                </div>
                <p>Al eliminar tu cuenta:</p>
                <ul>
                    <li>Perderás acceso a todas tus playlists</li>
                    <li>Tu historial de reproducciones será eliminado</li>
                    <li>Tus compras seguirán siendo accesibles por 30 días</li>
                    <li>Tu información personal será eliminada de nuestros servidores</li>
                </ul>
                <div class="mb-3">
                    <label for="password-confirmar-eliminar" class="form-label">Ingresa tu contraseña para confirmar</label>
                    <input type="password" class="form-control" id="password-confirmar-eliminar" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger">Eliminar mi cuenta</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar medidor de fuerza de contraseña
    const passwordInput = document.getElementById('password-nuevo');
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
    
    // Guardar tema
    const guardarTemaBtn = document.getElementById('guardar-tema');
    if (guardarTemaBtn) {
        guardarTemaBtn.addEventListener('click', function() {
            const tema = document.querySelector('input[name="tema"]:checked').value;
            
            // Guardar tema en localStorage
            localStorage.setItem('theme', tema);
            
            // Aplicar tema
            applyTheme(tema);
            
            alert('Preferencias de tema guardadas correctamente');
        });
    }
    
    // Cargar tema guardado
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.querySelector(`input[name="tema"][value="${savedTheme}"]`).checked = true;
    applyTheme(savedTheme);
    
    // Función para aplicar tema
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
        } else if (theme === 'light') {
            document.body.classList.remove('dark-mode');
        } else if (theme === 'system') {
            // Detectar preferencia del sistema
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
        }
    }
    
    // Formulario de cuenta
    const formCuenta = document.getElementById('form-cuenta');
    if (formCuenta) {
        formCuenta.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update_profile');
            formData.append('nombre', document.getElementById('nombre').value);
            formData.append('apellido1', document.getElementById('apellido1').value);
            formData.append('apellido2', document.getElementById('apellido2').value);
            formData.append('nombre_usuario', document.getElementById('nombre_usuario').value);
            formData.append('correo', document.getElementById('correo').value);
            formData.append('telefono', document.getElementById('telefono').value);
            
            fetch('../api/user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Información de cuenta actualizada correctamente');
                } else {
                    alert(data.message || 'Error al actualizar la información');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al comunicarse con el servidor');
            });
        });
    }
    
    // Formulario de cambio de contraseña
    const formPassword = document.getElementById('form-password');
    if (formPassword) {
        formPassword.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const passwordActual = document.getElementById('password-actual').value;
            const passwordNuevo = document.getElementById('password-nuevo').value;
            const passwordConfirmar = document.getElementById('password-confirmar').value;
            
            if (passwordNuevo !== passwordConfirmar) {
                alert('Las contraseñas no coinciden');
                return;
            }
            
            fetch('../api/user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=change_password&password_actual=${passwordActual}&password_nuevo=${passwordNuevo}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Contraseña actualizada correctamente');
                    formPassword.reset();
                } else {
                    alert(data.message || 'Error al actualizar la contraseña');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al comunicarse con el servidor');
            });
        });
    }
});
</script>

<style>
/* Estilos para el medidor de fuerza de contraseña */
.password-strength {
    margin-bottom: 20px;
}

.strength-meter {
    height: 5px;
    background-color: #e9ecef;
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

/* Estilos para el modo oscuro */
body.dark-mode {
    background-color: #121212;
    color: #f8f9fa;
}

body.dark-mode .card {
    background-color: #1e1e1e;
    border-color: #333;
}

body.dark-mode .card-header {
    background-color: #2c2c2c;
    border-color: #333;
}

body.dark-mode .list-group-item {
    background-color: #1e1e1e;
    border-color: #333;
    color: #f8f9fa;
}

body.dark-mode .list-group-item.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

body.dark-mode .form-control,
body.dark-mode .form-select {
    background-color: #2c2c2c;
    border-color: #333;
    color: #f8f9fa;
}

body.dark-mode .form-control:focus,
body.dark-mode .form-select:focus {
    background-color: #2c2c2c;
    border-color: #0d6efd;
    color: #f8f9fa;
}

body.dark-mode .form-check-input {
    background-color: #2c2c2c;
    border-color: #333;
}

body.dark-mode .modal-content {
    background-color: #1e1e1e;
    border-color: #333;
}

body.dark-mode .modal-header,
body.dark-mode .modal-footer {
    border-color: #333;
}

body.dark-mode .text-muted {
    color: #adb5bd !important;
}
</style>

<?php include_once '../includes/footer.php'; ?>
