<?php
require_once 'funcioncarrito.php'; // Incluir la lógica del carrito
require_once 'encabezado.php';

// Obtener los items del carrito al cargar la página
$items_carrito = $user_logged_in ? obtenerItemsCarrito($conexion, $id_usuario) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        
        body {
            background-color: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
        }
        .cart-container {
            background-color: #1e1e1e;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .cart-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .cart-item {
            background-color: #2a2a2a;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column; /* Cambiado a columna para diseño vertical */
            transition: background-color 0.3s;
        }
        .cart-item:hover {
            background-color: #3a3a3a;
        }
        .cart-item-info {
            display: flex;
            align-items: center;
        }
        .cart-item-info img {
            width: 80px; 
            height: auto;
            margin-right: 15px;
            border-radius: 4px;
        }
        .cart-item-details {
            flex-grow: 1;
        }
        .cart-item-actions {
            display: flex;
            justify-content: flex-end; /* Alinear el botón a la derecha */
            margin-top: 10px; /* Espacio entre detalles y botón */
        }
        .btn-remove {
            background-color: #ff4136;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-remove:hover {
            background-color: #ff0000;
        }
        .cart-total {
            font-size: 1.5em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        .btn-checkout {
            background-color: #28a745; /* Color verde */
            color: #fff; /* Texto blanco */
            border: none;
            padding: 12px 20px; /* Espaciado */
            border-radius: 50px; /* Bordes redondeados */
            font-size: 1.2em; /* Tamaño de fuente */
            margin-top: 20px;
            display: block;
            width: 100%;
            transition: background-color 0.3s, transform 0.2s; /* Transiciones suaves */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Sombra */
            text-decoration: none; /* Sin subrayado */
        }
        .btn-checkout:hover {
            background-color: #218838; /* Color verde más oscuro al pasar el mouse */
            transform: translateY(-2px); /* Efecto de elevación */
        }
        .cart-item-quantity {
            margin-left: 15px;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="container mt-5 cart-container">
        <h2 class="cart-header">Tu Carrito de Compras</h2>
        <?php if (!$user_logged_in): ?>
            <div class="alert alert-warning">Debes iniciar sesión para poder comprar productos.</div>
        <?php else: ?>
            <?php if (empty($items_carrito)): ?>
                <div class="alert alert-info">Tu carrito está vacío.</div>
            <?php else: ?>
                <div id="cart-items">
                    <?php foreach ($items_carrito as $item): ?>
                        <div class="cart-item" id="item-<?php echo $item['id_carrito']; ?>">
                            <div class="cart-item-info">
                                <img src="<?php echo htmlspecialchars($item['imagen_producto']); ?>" alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>">
                                <div class="cart-item-details">
                                    <strong><?php echo htmlspecialchars($item['nombre_producto']); ?></strong>
                                    <p class="cart-item-quantity">Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?></p>
                                    <p>Precio: $<?php echo htmlspecialchars($item['precio']); ?></p>
                                    <p>Tipo: <?php echo htmlspecialchars($item['tipo_producto']); ?></p>
                                </div>
                            </div>
                            <div class="cart-item-actions">
                                <button class="btn-remove" onclick="eliminarItem(<?php echo $item['id_carrito']; ?>)">Eliminar</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="cart-total">
                    Total: $<span id="total"><?php echo array_sum(array_column($items_carrito, 'precio')); ?></span>
                </div>
                <a href="checkout.php" class="btn-checkout">Proceder al Pago</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
function eliminarItem(idCarrito) {
    if (confirm('¿Estás seguro de que deseas eliminar este producto del carrito?')) {
        fetch('', { // Se envía la solicitud a la misma página
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_carrito=' + idCarrito
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recargar la página para actualizar el carrito
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
        function actualizarCarrito() {
            // Llama a la función obtenerItemsCarrito desde funcioncarrito.php
            fetch('funcioncarrito.php?action=obtenerItemsCarrito') // Asegúrate de que esta URL sea correcta
                .then(response => response.json())
                .then(data => {
                    const cartItemsContainer = document.getElementById('cart-items');
                    cartItemsContainer.innerHTML = ''; // Limpiar la lista actual
                    let total = 0;

                    data.forEach(item => {
                        const itemElement = document.createElement('div');
                        itemElement.className = 'cart-item';
                        itemElement.id = 'item-' + item.id_carrito;

                        itemElement.innerHTML = `
                            <div class="cart-item-info">
                                <img src="${item.imagen_producto}" alt="${item.nombre_producto}">
                                <div class="cart-item-details">
                                    <strong>${item.nombre_producto}</strong>
                                    <p class="cart-item-quantity">Cantidad: ${item.cantidad}</p>
                                    <p>Precio: $${item.precio}</p>
                                    <p>Tipo: ${item.tipo_producto}</p> <!-- Mostrar tipo de producto -->
                                </div>
                            </div>
                            <div class="cart-item-actions">
                                <button class="btn-remove" onclick="eliminarItem(${item.id_carrito})">Eliminar</button>
                            </div>
                        `;
                        cartItemsContainer.appendChild(itemElement);
                        total += item.precio * item.cantidad; // Sumar el precio total
                    });

                    document.getElementById('total').innerText = total.toFixed(2);
                })
                .catch(error => {
                    console.error('Error al actualizar el carrito:', error);
                });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
<footer>
<div id="footer-placeholder"></div>
<script>
        // Cargar el footer
        fetch('footer.html')
            .then(response => response.text())
            .then(data => {
                document.getElementById('footer-placeholder').innerHTML = data;
            })
            .catch(error => console.error('Error al cargar el footer:', error));
    </script>
</footer>
</html>