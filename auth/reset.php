<?php
$error = '';
$success = '';

// Verificar si se proporcionó un token
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $error = 'Token de recuperación no válido o expirado.';
} else {
    $token = $_GET['token'];
    
    // Verificar si el token es válido
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $error = 'Token de recuperación no válido o expirado.';
    } else {
        // Procesar el formulario de restablecimiento de contraseña
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if (empty($password) || empty($confirm_password)) {
                $error = 'Por favor, completa todos los campos.';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            } elseif ($password !== $confirm_password) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                // Actualizar la contraseña
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE usuarios SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                
                if ($stmt->execute([$hashed_password, $user['id']])) {
                    $success = 'Tu contraseña ha sido restablecida correctamente. Ahora puedes iniciar sesión con tu nueva contraseña.';
                } else {
                    $error = 'Error al restablecer la contraseña. Inténtalo de nuevo.';
                }
            }
        }
    }
}
?>

<section id="page-reset" class="page active" aria-label="Restablecer contraseña">
  <h1>Restablecer Contraseña</h1>
  
  <?php if ($error): ?>
  <div class="alert alert-danger"><?php echo $error; ?></div>
  <p class="form-footer">
    <a href="index.php?page=recover">Solicitar un nuevo enlace de recuperación</a>
  </p>
  <?php elseif ($success): ?>
  <div class="alert alert-success"><?php echo $success; ?></div>
  <p class="form-footer">
    <a href="index.php?page=login" class="btn-primary">Iniciar Sesión</a>
  </p>
  <?php else: ?>
  <p class="reset-intro">Ingresa tu nueva contraseña.</p>
  
  <form id="reset-form" class="animate-fade-up" method="POST" action="">
    <div class="form-group">
      <label for="reset-password">Nueva contraseña</label>
      <div class="input-group password-input-container">
        <span class="input-group-text"><i class="fas fa-lock"></i></span>
        <input type="password" id="reset-password" name="password" class="form-control" required minlength="6" />
        <button type="button" class="toggle-password" tabindex="-1"><i class="fas fa-eye"></i></button>
      </div>
      <div class="password-strength">
        <div class="strength-meter">
          <div class="strength-meter-fill" data-strength="0"></div>
        </div>
        <div class="strength-text">Fuerza de la contraseña: <span>Débil</span></div>
      </div>
    </div>
    
    <div class="form-group">
      <label for="reset-confirm-password">Confirmar nueva contraseña</label>
      <div class="input-group password-input-container">
        <span class="input-group-text"><i class="fas fa-lock"></i></span>
        <input type="password" id="reset-confirm-password" name="confirm_password" class="form-control" required minlength="6" />
        <button type="button" class="toggle-password" tabindex="-1"><i class="fas fa-eye"></i></button>
      </div>
    </div>
    
    <button type="submit" name="reset" id="reset-submit-btn"><i class="fas fa-key"></i> Restablecer Contraseña</button>
  </form>
  <?php endif; ?>
</section>

<style>
.reset-intro {
  margin-bottom: 25px;
  text-align: center;
  color: var(--text-secondary);
}

.form-group {
  margin-bottom: 20px;
}

.input-group {
  display: flex;
  position: relative;
}

.input-group-text {
  display: flex;
  align-items: center;
  padding: 0 15px;
  background-color: var(--bg-card);
  border: 1px solid var(--border-color);
  border-right: none;
  border-radius: 8px 0 0 8px;
  color: var(--primary-color);
}

.form-control {
  flex: 1;
  padding: 12px 15px;
  border: 1px solid var(--border-color);
  border-radius: 0 8px 8px 0;
  background-color: var(--bg-card);
  color: var(--text-color);
  font-size: 1rem;
}

.password-input-container {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: transparent;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  z-index: 10;
  padding: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.toggle-password:hover {
  color: var(--primary-color);
}

.password-strength {
  margin-top: 8px;
  font-size: 0.8rem;
}

.strength-meter {
  height: 4px;
  background-color: var(--bg-card);
  border-radius: 2px;
  margin-bottom: 5px;
  overflow: hidden;
}

.strength-meter-fill {
  height: 100%;
  border-radius: 2px;
  transition: width 0.3s ease, background-color 0.3s ease;
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
  color: var(--text-secondary);
}

.form-footer {
  margin-top: 20px;
  text-align: center;
  font-size: 0.9rem;
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

.btn-primary {
  display: inline-block;
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 20px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-primary:hover {
  background: var(--primary-hover);
  transform: translateY(-2px);
  text-decoration: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar botones para mostrar/ocultar contraseña
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
      const input = this.previousElementSibling;
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
  const passwordInput = document.getElementById('reset-password');
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
