<?php
$error = '';
$success = '';

// Procesar el formulario de recuperación de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recover'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Por favor, ingresa tu correo electrónico.';
    } else {
        // Verificar si el email existe
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generar token de recuperación
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Guardar token en la base de datos
            $stmt = $conn->prepare("UPDATE usuarios SET reset_token = ?, reset_expires = ? WHERE id = ?");
            if ($stmt->execute([$token, $expires, $user['id']])) {
                // En un entorno real, aquí enviarías un correo electrónico con el enlace de recuperación
                // Para este ejemplo, simplemente mostraremos un mensaje de éxito
                $success = 'Se ha enviado un enlace de recuperación a tu correo electrónico. Por favor, revisa tu bandeja de entrada.';
                
                // En un entorno de desarrollo, mostramos el enlace directamente
                $resetLink = "index.php?page=reset&token=$token";
                $success .= "<br><br>Enlace de recuperación (solo para desarrollo): <a href='$resetLink'>$resetLink</a>";
            } else {
                $error = 'Error al procesar la solicitud. Inténtalo de nuevo.';
            }
        } else {
            // Por seguridad, no revelamos si el email existe o no
            $success = 'Si el correo electrónico está registrado, recibirás un enlace para restablecer tu contraseña.';
        }
    }
}
?>

<section id="page-recover" class="page active" aria-label="Recuperar contraseña">
  <h1>Recuperar Contraseña</h1>
  
  <?php if ($error): ?>
  <div class="alert alert-danger"><?php echo $error; ?></div>
  <?php endif; ?>
  
  <?php if ($success): ?>
  <div class="alert alert-success"><?php echo $success; ?></div>
  <?php else: ?>
  <p class="recover-intro">Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
  
  <form id="recover-form" class="animate-fade-up" method="POST" action="">
    <div class="form-group">
      <label for="recover-email">Correo electrónico</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
        <input type="email" id="recover-email" name="email" class="form-control" required />
      </div>
    </div>
    
    <button type="submit" name="recover" id="recover-submit-btn"><i class="fas fa-paper-plane"></i> Enviar Enlace</button>
    
    <p class="form-footer">
      <a href="index.php?page=login"><i class="fas fa-arrow-left"></i> Volver a Iniciar Sesión</a>
    </p>
  </form>
  <?php endif; ?>
</section>

<style>
.recover-intro {
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
</style>
