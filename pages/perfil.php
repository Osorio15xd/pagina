<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Incluir la conexión a la base de datos
require_once '../config/db_connect.php';

// Obtener información del usuario
$userId = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("
        SELECT id_usuario, nombre, nombre_usuario, correo, foto_perfil, biografia, telefono, apellido1, apellido2
        FROM usuario
        WHERE id_usuario = ?
    ");
    $stmt->execute([$userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "Usuario no encontrado";
        exit;
    }
    
    // Obtener estadísticas del usuario
    // Total de canciones en biblioteca
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM biblioteca_usuario WHERE id_usuario = ?
    ");
    $stmt->execute([$userId]);
    $totalBiblioteca = $stmt->fetchColumn();
    
    // Total de playlists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM playlist WHERE id_usuario = ?
    ");
    $stmt->execute([$userId]);
    $totalPlaylists = $stmt->fetchColumn();
    
    // Total de reproducciones
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM estadisticas_reproduccion WHERE id_usuario = ?
    ");
    $stmt->execute([$userId]);
    $totalReproducciones = $stmt->fetchColumn();
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// Incluir el encabezado
include_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="<?php echo !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'assets/img/default-user.jpg'; ?>" alt="Foto de perfil" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?php echo $usuario['nombre_usuario']; ?></h4>
                    <p class="text-muted"><?php echo $usuario['nombre'] . ' ' . $usuario['apellido1']; ?></p>
                    <button class="btn btn-primary" id="cambiar-foto-btn">Cambiar Foto</button>
                    <input type="file" id="foto-input" class="d-none" accept="image/*">
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Estadísticas</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="text-center">
                            <h5><?php echo $totalBiblioteca; ?></h5>
                            <small class="text-muted">Canciones</small>
                        </div>
                        <div class="text-center">
                            <h5><?php echo $totalPlaylists; ?></h5>
                            <small class="text-muted">Playlists</small>
                        </div>
                        <div class="text-center">
                            <h5><?php echo $totalReproducciones; ?></h5>
                            <small class="text-muted">Reproducciones</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Información Personal</h5>
                </div>
                <div class="card-body">
                    <form id="form-perfil">
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
                        <div class="mb-3">
                            <label for="biografia" class="form-label">Biografía</label>
                            <textarea class="form-control" id="biografia" rows="4"><?php echo $usuario['biografia']; ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4">
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
                        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar foto de perfil
    const cambiarFotoBtn = document.getElementById('cambiar-foto-btn');
    const fotoInput = document.getElementById('foto-input');
    
    if (cambiarFotoBtn && fotoInput) {
        cambiarFotoBtn.addEventListener('click', function() {
            fotoInput.click();
        });
        
        fotoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('action', 'update_photo');
                formData.append('photo', this.files[0]);
                
                fetch('api/user.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar la foto en la interfaz
                        document.querySelector('.card img').src = data.photo_url;
                        showToast('Foto de perfil actualizada correctamente', 'success');
                    } else {
                        showToast(data.message || 'Error al actualizar la foto', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error al comunicarse con el servidor', 'error');
                });
            }
        });
    }
    
    // Guardar cambios del perfil
    const formPerfil = document.getElementById('form-perfil');
    
    if (formPerfil) {
        formPerfil.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update_profile');
            formData.append('nombre', document.getElementById('nombre').value);
            formData.append('apellido1', document.getElementById('apellido1').value);
            formData.append('apellido2', document.getElementById('apellido2').value);
            formData.append('nombre_usuario', document.getElementById('nombre_usuario').value);
            formData.append('correo', document.getElementById('correo').value);
            formData.append('telefono', document.getElementById('telefono').value);
            formData.append('biografia', document.getElementById('biografia').value);
            
            fetch('api/user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Perfil actualizado correctamente', 'success');
                } else {
                    showToast(data.message || 'Error al actualizar el perfil', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al comunicarse con el servidor', 'error');
            });
        });
    }
    
    // Cambiar contraseña
    const formPassword = document.getElementById('form-password');
    
    if (formPassword) {
        formPassword.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const passwordActual = document.getElementById('password-actual').value;
            const passwordNuevo = document.getElementById('password-nuevo').value;
            const passwordConfirmar = document.getElementById('password-confirmar').value;
            
            if (passwordNuevo !== passwordConfirmar) {
                showToast('Las contraseñas no coinciden', 'error');
                return;
            }
            
            fetch('api/user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=change_password&current_password=${passwordActual}&new_password=${passwordNuevo}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Contraseña actualizada correctamente', 'success');
                    formPassword.reset();
                } else {
                    showToast(data.message || 'Error al actualizar la contraseña', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al comunicarse con el servidor', 'error');
            });
        });
    }
    
    // Función para mostrar toast
    function showToast(message, type = 'info') {
        // Crear el elemento toast
        const toast = document.createElement('div');
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%) translateY(-100px)';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '5px';
        toast.style.boxShadow = '0 3px 10px rgba(0,0,0,0.3)';
        toast.style.zIndex = '1000';
        toast.style.transition = 'all 0.3s ease';
        
        // Establecer colores según el tipo
        if (type === 'error') {
            toast.style.background = '#e74c3c';
            toast.style.color = '#fff';
            toast.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        } else if (type === 'success') {
            toast.style.background = 'var(--primary-color)';
            toast.style.color = 'var(--bg-color)';
            toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        } else {
            toast.style.background = '#3498db';
            toast.style.color = '#fff';
            toast.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
        }
        
        document.body.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.style.transform = 'translateX(-50%) translateY(0)';
        }, 10);
        
        // Eliminar después de 3 segundos
        setTimeout(() => {
            toast.style.transform = 'translateX(-50%) translateY(-100px)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>
