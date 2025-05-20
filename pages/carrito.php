<?php
// Obtener el carrito del usuario si está logueado
$carrito = [];
$subtotal = 0;
$impuestos = 0;
$descuento = 0;
$total = 0;

if (isset($_SESSION['user_id'])) {
    // Obtener artículos en el carrito
    $stmt = $conn->prepare("SELECT c.id, c.titulo, c.portada, c.precio, a.nombre as artista, ca.fecha_agregado 
                           FROM carrito ca 
                           JOIN canciones c ON ca.cancion_id = c.id 
                           JOIN artistas a ON c.artista_id = a.id 
                           WHERE ca.usuario_id = ? 
                           ORDER BY ca.fecha_agregado DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular totales
    foreach ($carrito as $item) {
        $subtotal += $item['precio'];
    }
    
    $impuestos = $subtotal * 0.16; // 16% de impuestos
    $total = $subtotal + $impuestos - $descuento;
}

// Procesar código promocional (simulado)
$mensaje_promo = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_promo'])) {
    $codigo = trim($_POST['promo_code']);
    
    if ($codigo === 'PROMO10') {
        $descuento = $subtotal * 0.10; // 10% de descuento
        $total = $subtotal + $impuestos - $descuento;
        $mensaje_promo = '<div class="alert alert-success">Código promocional aplicado: 10% de descuento.</div>';
    } else {
        $mensaje_promo = '<div class="alert alert-danger">Código promocional inválido.</div>';
    }
}
?>

<section id="page-carrito" class="page active" aria-label="Carrito de compras">
  <h1>Carrito</h1>
  
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="row">
      <div class="col-md-8">
        <div class="card" style="background: var(--bg-secondary); border: 1px solid var(--border-color); margin-bottom: 20px;">
          <div class="card-body">
            <h3>Artículos en tu carrito</h3>
            
            <?php if (count($carrito) > 0): ?>
              <ul id="cart-list" class="list-group">
                <?php foreach ($carrito as $item): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center animate-fade-up" style="background: var(--bg-card); color: var(--text-color); border: 1px solid var(--border-color); margin-bottom: 10px; border-radius: 8px; padding: 15px;">
                    <div>
                      <div class="d-flex align-items-center">
                        <img src="<?php echo $item['portada']; ?>" alt="Portada" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                        <div>
                          <h5 style="margin: 0;"><?php echo $item['titulo']; ?></h5>
                          <small><?php echo $item['artista']; ?></small>
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-center">
                      <span class="badge bg-primary rounded-pill me-3">$<?php echo number_format($item['precio'], 2); ?></span>
                      <button class="btn btn-sm btn-danger remove-cart-item" data-id="<?php echo $item['id']; ?>">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
              
              <div class="d-flex justify-content-between align-items-center mt-4">
                <button id="continue-shopping-btn" class="btn" style="background: var(--bg-card); color: var(--text-color);" onclick="window.location.href='index.php?page=inicio'">
                  <i class="fas fa-arrow-left"></i> Seguir Comprando
                </button>
                <button id="proceed-checkout-btn" class="btn" style="background: var(--primary-color); color: var(--bg-color);" onclick="window.location.href='index.php?page=checkout'">
                  Proceder al Pago <i class="fas fa-arrow-right"></i>
                </button>
              </div>
            <?php else: ?>
              <div class="empty-state">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                <h3>Tu carrito está vacío</h3>
                <p>Explora nuestra colección y agrega canciones a tu carrito.</p>
                <a href="index.php?page=inicio" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Explorar música</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card" style="background: var(--bg-secondary); border: 1px solid var(--border-color);">
          <div class="card-body">
            <h3>Resumen</h3>
            
            <?php echo $mensaje_promo; ?>
            
            <div class="d-flex justify-content-between mb-2">
              <span>Subtotal:</span>
              <span id="cart-subtotal">$<?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Impuestos:</span>
              <span id="cart-taxes">$<?php echo number_format($impuestos, 2); ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Descuento:</span>
              <span id="cart-discount">$<?php echo number_format($descuento, 2); ?></span>
            </div>
            <hr style="border-color: var(--border-color);">
            <div class="d-flex justify-content-between">
              <strong>Total:</strong>
              <strong id="cart-total">$<?php echo number_format($total, 2); ?></strong>
            </div>
            
            <form method="POST" action="" class="mt-3">
              <div class="input-group">
                <input type="text" id="promo-code" name="promo_code" placeholder="Código promocional" style="width: 100%; padding: 8px; background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 5px;">
                <button type="submit" name="apply_promo" id="apply-promo-btn" class="btn mt-2 w-100" style="background: var(--primary-color); color: var(--bg-color);">
                  Aplicar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="login-prompt">
      <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
      <h3>Inicia sesión para usar el carrito</h3>
      <p>Necesitas una cuenta para agregar canciones a tu carrito y realizar compras.</p>
      <a href="index.php?page=login" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Iniciar Sesión</a>
    </div>
  <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Eventos para eliminar artículos del carrito
  const removeButtons = document.querySelectorAll('.remove-cart-item');
  
  removeButtons.forEach(button => {
    button.addEventListener('click', function() {
      const itemId = this.dataset.id;
      
      if (confirm('¿Estás seguro de que deseas eliminar este artículo del carrito?')) {
        // Enviar solicitud para eliminar del carrito
        fetch('api/cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=remove_from_cart&item_id=${itemId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Eliminar el elemento visualmente
            this.closest('.list-group-item').remove();
            
            // Actualizar totales
            document.getElementById('cart-subtotal').textContent = `$${data.subtotal.toFixed(2)}`;
            document.getElementById('cart-taxes').textContent = `$${data.taxes.toFixed(2)}`;
            document.getElementById('cart-discount').textContent = `$${data.discount.toFixed(2)}`;
            document.getElementById('cart-total').textContent = `$${data.total.toFixed(2)}`;
            
            showToast('Artículo eliminado del carrito', 'success');
            
            // Si no quedan artículos, mostrar estado vacío
            if (document.querySelectorAll('.list-group-item').length === 0) {
              document.getElementById('cart-list').innerHTML = `
                <div class="empty-state">
                  <i class="fas fa-shopping-cart" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                  <h3>Tu carrito está vacío</h3>
                  <p>Explora nuestra colección y agrega canciones a tu carrito.</p>
                  <a href="index.php?page=inicio" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Explorar música</a>
                </div>
              `;
            }
          } else {
            showToast(data.message || 'Error al eliminar el artículo', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Error al comunicarse con el servidor', 'error');
        });
      }
    });
  });
});
</script>
