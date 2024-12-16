<?php
require_once 'encabezado.php';
require_once 'funcioncarrito.php';

if ($user_logged_in) {
    $_SESSION['items_carrito'] = obtenerItemsCarrito($conexion, $id_usuario);
} else {
    $_SESSION['items_carrito'] = [];
}

$items_carrito = $_SESSION['items_carrito'];

function calcularTotal($items) {
    $total = 0;
    foreach ($items as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    return $total;
}

$total = calcularTotal($items_carrito);

// Función para vaciar el carrito
function vaciarCarrito($conexion, $id_usuario) {
    $query = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    return $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulación de Pago - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
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
        .checkout-container {
            background-color: #1e1e1e;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }
        .checkout-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-submit {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 1.2em;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #218838;
        }
        .product-image {
            width: 50px;
            height: auto;
            border-radius: 4px;
        }
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        #confirmationMessage {
            margin-top: 20px;
        }
        .alert-success {
            background-color: #28a745;
            color: #fff;
            border-color: #28a745;
        }
    </style>
</head>
<body>

<div class="container checkout-container">
    <h2 class="checkout-header">Simulación de Pago</h2>
    <form id="payment-form">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <h4>Productos en el carrito:</h4>
        <table class="table table-dark table-striped" id="cart-table">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items_carrito as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($item['imagen_producto']); ?>" alt="<?php echo htmlspecialchars($item['nombre_producto']); ?>" class="product-image">
                        </td>
                        <td><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                        <td>$<?php echo htmlspecialchars($item['precio']); ?></td>
                        <td><?php echo htmlspecialchars($item['cantidad']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 id="total-amount">Total a Pagar: $<?php echo htmlspecialchars($total); ?></h4>

        <button type="submit" class="btn btn-submit">Confirmar Pago</button>
    </form>
</div>

<script>
    document.getElementById('payment-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const nombre = document.getElementById('nombre').value.trim();
        const email = document.getElementById('email').value.trim();

        if (!nombre || !email) {
            alert('Por favor, completa todos los campos requeridos.');
        } else if (!validateEmail(email)) {
            alert('Por favor, introduce un email válido.');
        } else {
            showLoadingOverlay();
            setTimeout(() => {
                hideLoadingOverlay();
                showConfirmation();
                initiateDownloads();
                emptyCart();
            }, 2000);
        }
    });

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Procesando pago...</span></div>';
        document.body.appendChild(overlay);
    }

    function hideLoadingOverlay() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    }

    function showConfirmation() {
        const confirmationDiv = document.createElement('div');
        confirmationDiv.id = 'confirmationMessage';
        confirmationDiv.innerHTML = '<div class="alert alert-success" role="alert"><h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Pago Confirmado</h4><p>Espere a que se descargue su música. Su carrito ha sido vaciado.</p></div>';
        document.querySelector('.checkout-container').prepend(confirmationDiv);
    }

    function initiateDownloads() {
        <?php foreach ($items_carrito as $item): ?>
        if ('<?php echo $item['tipo_producto']; ?>' === 'album') {
            fetch('descargar.php?tipo=album&id=<?php echo $item['id_producto']; ?>')
                .then(response => response.json())
                .then(data => {
                    data.canciones.forEach(cancion => {
                        downloadFile(cancion.ruta, cancion.nombre);
                    });
                    downloadFile(data.imagen, '<?php echo $item['nombre_producto']; ?>_cover.jpg');
                })
                .catch(error => console.error('Error al descargar el álbum:', error));
        } else {
            downloadFile('<?php echo $item['ruta_descarga']; ?>', '<?php echo $item['nombre_producto']; ?>.mp3');
        }
        <?php endforeach; ?>
    }

    function downloadFile(url, fileName) {
        fetch(url)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error('Error al descargar:', error));
    }

    function emptyCart() {
        fetch('vaciar_carrito.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: 'empty_cart' }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la UI para reflejar el carrito vacío
                document.getElementById('cart-table').querySelector('tbody').innerHTML = '';
                document.getElementById('total-amount').textContent = 'Total a Pagar: $0.00';
            } else {
                console.error('Error al vaciar el carrito:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
<script src="js/bootstrap.bundle.min.js"></script>
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

