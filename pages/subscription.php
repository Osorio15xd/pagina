<?php
// Verificar si el usuario ya tiene una suscripción activa
$tiene_suscripcion = false;
$suscripcion = null;

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, plan, fecha_inicio, fecha_fin, activo FROM suscripciones WHERE usuario_id = ? AND activo = 1 ORDER BY fecha_fin DESC LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $suscripcion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($suscripcion) {
        $tiene_suscripcion = true;
    }
}

// Procesar selección de plan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['select_plan'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }
    
    $plan = $_POST['plan'];
    
    if ($plan != 'mensual' && $plan != 'anual') {
        $mensaje = '<div class="alert alert-danger">Plan no válido.</div>';
    } else {
        // Calcular fechas
        $fecha_inicio = date('Y-m-d');
        $fecha_fin = $plan == 'mensual' ? date('Y-m-d', strtotime('+1 month')) : date('Y-m-d', strtotime('+1 year'));
        
        // Si ya tiene una suscripción activa, desactivarla
        if ($tiene_suscripcion) {
            $stmt = $conn->prepare("UPDATE suscripciones SET activo = 0 WHERE id = ?");
            $stmt->execute([$suscripcion['id']]);
        }
        
        // Crear nueva suscripción
        $stmt = $conn->prepare("INSERT INTO suscripciones (usuario_id, plan, fecha_inicio, fecha_fin, activo) VALUES (?, ?, ?, ?, 1)");
        if ($stmt->execute([$_SESSION['user_id'], $plan, $fecha_inicio, $fecha_fin])) {
            header('Location: index.php?page=perfil');
            exit;
        } else {
            $mensaje = '<div class="alert alert-danger">Error al procesar la suscripción. Inténtalo de nuevo.</div>';
        }
    }
}
?>

<section id="page-subscription" class="page active" aria-label="Suscripción">
  <h1>Comprar Suscripción</h1>
  <p>Elige el plan que más te convenga.</p>
  
  <?php if (isset($mensaje)) echo $mensaje; ?>
  
  <?php if ($tiene_suscripcion): ?>
    <div class="alert alert-info">
      <i class="fas fa-info-circle"></i> Ya tienes una suscripción activa (Plan <?php echo ucfirst($suscripcion['plan']); ?>).
      Si seleccionas un nuevo plan, se cancelará tu suscripción actual y comenzará la nueva inmediatamente.
    </div>
  <?php endif; ?>
  
  <div class="row">
    <div class="col-md-6 mb-4">
      <div class="card h-100 subscription-card" style="background: var(--bg-card); border: 1px solid var(--border-color); transition: all 0.3s ease;">
        <div class="card-body text-center">
          <h3>Plan Mensual</h3>
          <div class="price">$5<span>/mes</span></div>
          <ul class="list-unstyled mt-3 mb-4">
            <li>Acceso a toda la música</li>
            <li>Sin anuncios</li>
            <li>Descarga hasta 100 canciones</li>
            <li>Calidad estándar</li>
          </ul>
          <form method="POST" action="">
            <input type="hidden" name="plan" value="mensual">
            <button type="submit" name="select_plan" class="btn btn-lg w-100" style="background: var(--primary-color); color: var(--bg-color);">Seleccionar</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-6 mb-4">
      <div class="card h-100 subscription-card" style="background: var(--bg-card); border: 1px solid var(--primary-color); transform: scale(1.05); box-shadow: 0 5px 20px rgba(29, 185, 84, 0.3); transition: all 0.3s ease;">
        <div class="card-body text-center">
          <span class="badge" style="background: var(--primary-color); position: absolute; top: -10px; right: 10px; padding: 5px 10px; border-radius: 20px;">Recomendado</span>
          <h3>Plan Anual</h3>
          <div class="price">$50<span>/año</span></div>
          <div class="savings">Ahorro del 17%</div>
          <ul class="list-unstyled mt-3 mb-4">
            <li>Acceso a toda la música</li>
            <li>Sin anuncios</li>
            <li>Descarga ilimitada</li>
            <li>Calidad premium</li>
            <li>Acceso anticipado a nuevos lanzamientos</li>
          </ul>
          <form method="POST" action="">
            <input type="hidden" name="plan" value="anual">
            <button type="submit" name="select_plan" class="btn btn-lg w-100" style="background: var(--primary-color); color: var(--bg-color);">Seleccionar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <div class="subscription-features">
    <h3>Características de la suscripción</h3>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="feature-card">
          <i class="fas fa-music" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
          <h4>Música ilimitada</h4>
          <p>Accede a millones de canciones sin restricciones.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card">
          <i class="fas fa-ban" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
          <h4>Sin anuncios</h4>
          <p>Disfruta de tu música sin interrupciones publicitarias.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="feature-card">
          <i class="fas fa-download" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
          <h4>Descargas</h4>
          <p>Descarga tus canciones favoritas para escuchar sin conexión.</p>
        </div>
      </div>
    </div>
  </div>
  
  <div class="faq-section">
    <h3>Preguntas frecuentes</h3>
    <div class="accordion" id="faqAccordion">
      <div class="accordion-item" style="background: var(--bg-card); border: 1px solid var(--border-color); margin-bottom: 10px; border-radius: 8px; overflow: hidden;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: var(--bg-secondary); color: var(--text-color);">
            ¿Puedo cancelar mi suscripción en cualquier momento?
          </button>
        </h2>
        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Sí, puedes cancelar tu suscripción en cualquier momento. Tu suscripción seguirá activa hasta el final del período de facturación.
          </div>
        </div>
      </div>
      <div class="accordion-item" style="background: var(--bg-card); border: 1px solid var(--border-color); margin-bottom: 10px; border-radius: 8px; overflow: hidden;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: var(--bg-secondary); color: var(--text-color);">
            ¿Cómo se renueva mi suscripción?
          </button>
        </h2>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Tu suscripción se renovará automáticamente al final de cada período de facturación. Recibirás una notificación por correo electrónico antes de la renovación.
          </div>
        </div>
      </div>
      <div class="accordion-item" style="background: var(--bg-card); border: 1px solid var(--border-color); margin-bottom: 10px; border-radius: 8px; overflow: hidden;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: var(--bg-secondary); color: var(--text-color);">
            ¿Puedo cambiar de plan en cualquier momento?
          </button>
        </h2>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Sí, puedes cambiar entre el plan mensual y anual en cualquier momento. El nuevo plan entrará en vigor inmediatamente y se ajustará el precio según corresponda.
          </div>
        </div>
      </div>
      <div class="accordion-item" style="background: var(--bg-card); border: 1px solid var(--border-color); margin-bottom: 10px; border-radius: 8px; overflow: hidden;">
        <h2 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: var(--bg-secondary); color: var(--text-color);">
            ¿Qué métodos de pago aceptan?
          </button>
        </h2>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Aceptamos tarjetas de crédito/débito, PayPal, transferencias bancarias y algunos métodos de pago locales según tu ubicación.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Efectos para las tarjetas de suscripción
  const subscriptionCards = document.querySelectorAll('.subscription-card');
  
  subscriptionCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      if (!this.classList.contains('featured')) {
        this.style.transform = 'translateY(-10px)';
      } else {
        this.style.transform = 'scale(1.08)';
      }
      this.style.boxShadow = '0 15px 30px rgba(29, 185, 84, 0.3)';
    });
    
    card.addEventListener('mouseleave', function() {
      if (!this.classList.contains('featured')) {
        this.style.transform = 'scale(1)';
      } else {
        this.style.transform = 'scale(1.05)';
      }
      this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    });
  });
});
</script>
