<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
require_once '../config/db_connect.php';

// Verificar si hay una acción especificada
if (!isset($_GET['action']) && !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'No se especificó ninguna acción']);
    exit;
}

// Obtener la acción
$action = isset($_GET['action']) ? $_GET['action'] : $_POST['action'];

// Verificar si el usuario está logueado para ciertas acciones
$requiresAuth = ['add_to_cart', 'remove_from_cart', 'update_cart', 'checkout'];
if (in_array($action, $requiresAuth) && !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para realizar esta acción']);
    exit;
}

// Procesar la acción
switch ($action) {
    case 'get_count':
        getCartCount();
        break;
    case 'get_cart':
        getCart();
        break;
    case 'add_to_cart':
        addToCart();
        break;
    case 'remove_from_cart':
        removeFromCart();
        break;
    case 'update_cart':
        updateCart();
        break;
    case 'checkout':
        checkout();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

/**
 * Obtiene el número de elementos en el carrito
 */
function getCartCount() {
    // Si el usuario está logueado, obtener el carrito de la base de datos
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM carrito 
                WHERE id_usuario = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'count' => $result['count']]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el carrito: ' . $e->getMessage()]);
        }
    } else {
        // Si no está logueado, usar el carrito de la sesión
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        echo json_encode(['success' => true, 'count' => count($cart)]);
    }
}

/**
 * Obtiene el contenido del carrito
 */
function getCart() {
    // Si el usuario está logueado, obtener el carrito de la base de datos
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                SELECT c.id_carrito, c.id_producto, c.tipo_producto, c.cantidad, c.precio,
                       CASE 
                           WHEN c.tipo_producto = 'cancion' THEN (SELECT nombre_cancion FROM canciones WHERE id_cancion = c.id_producto)
                           WHEN c.tipo_producto = 'album' THEN (SELECT nombre_album FROM album WHERE id_album = c.id_producto)
                           WHEN c.tipo_producto = 'sencillo' THEN (SELECT nombre_sencillo FROM sencillos WHERE id_sencillo = c.id_producto)
                       END as nombre,
                       CASE 
                           WHEN c.tipo_producto = 'cancion' THEN (
                               SELECT u.nombre_usuario 
                               FROM usuario u 
                               JOIN artista a ON u.id_usuario = a.usuario 
                               JOIN canciones cn ON a.id_artista = cn.id_artista 
                               WHERE cn.id_cancion = c.id_producto
                           )
                           WHEN c.tipo_producto = 'album' THEN (
                               SELECT u.nombre_usuario 
                               FROM usuario u 
                               JOIN artista a ON u.id_usuario = a.usuario 
                               JOIN album al ON a.id_artista = al.id_artista 
                               WHERE al.id_album = c.id_producto
                           )
                           WHEN c.tipo_producto = 'sencillo' THEN (
                               SELECT u.nombre_usuario 
                               FROM usuario u 
                               JOIN artista a ON u.id_usuario = a.usuario 
                               JOIN sencillos s ON a.id_artista = s.id_artista 
                               WHERE s.id_sencillo = c.id_producto
                           )
                       END as artista,
                       CASE 
                           WHEN c.tipo_producto = 'cancion' THEN (
                               SELECT a.imagen_album_path 
                               FROM album a 
                               JOIN canciones cn ON a.id_album = cn.id_album 
                               WHERE cn.id_cancion = c.id_producto
                           )
                           WHEN c.tipo_producto = 'album' THEN (SELECT imagen_album_path FROM album WHERE id_album = c.id_producto)
                           WHEN c.tipo_producto = 'sencillo' THEN (SELECT imagen_sencillo_path FROM sencillos WHERE id_sencillo = c.id_producto)
                       END as imagen
                FROM carrito c
                WHERE c.id_usuario = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calcular total
            $total = 0;
            foreach ($items as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }
            
            echo json_encode([
                'success' => true, 
                'items' => $items,
                'total' => $total
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al obtener el carrito: ' . $e->getMessage()]);
        }
    } else {
        // Si no está logueado, usar el carrito de la sesión
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $total = 0;
        
        foreach ($cart as &$item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        echo json_encode([
            'success' => true, 
            'items' => $cart,
            'total' => $total
        ]);
    }
}

/**
 * Añade un producto al carrito
 */
function addToCart() {
    // Verificar si se enviaron datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no válido']);
        exit;
    }
    
    // Obtener datos
    $idProducto = isset($_POST['id_producto']) ? $_POST['id_producto'] : null;
    $tipoProducto = isset($_POST['tipo_producto']) ? $_POST['tipo_producto'] : null;
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 1;
    
    if (!$idProducto || !$tipoProducto) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    // Obtener precio del producto
    global $pdo;
    
    try {
        switch ($tipoProducto) {
            case 'cancion':
                $stmt = $pdo->prepare("SELECT precio FROM canciones WHERE id_cancion = ?");
                break;
            case 'album':
                $stmt = $pdo->prepare("SELECT precio FROM album WHERE id_album = ?");
                break;
            case 'sencillo':
                $stmt = $pdo->prepare("SELECT precio FROM sencillos WHERE id_sencillo = ?");
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Tipo de producto no válido']);
                exit;
        }
        
        $stmt->execute([$idProducto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$producto) {
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
            exit;
        }
        
        $precio = $producto['precio'];
        
        // Si el usuario está logueado, guardar en la base de datos
        if (isset($_SESSION['user_id'])) {
            // Verificar si el producto ya está en el carrito
            $stmt = $pdo->prepare("
                SELECT id_carrito, cantidad 
                FROM carrito 
                WHERE id_usuario = ? AND id_producto = ? AND tipo_producto = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $idProducto, $tipoProducto]);
            $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cartItem) {
                // Actualizar cantidad
                $newCantidad = $cartItem['cantidad'] + $cantidad;
                $stmt = $pdo->prepare("
                    UPDATE carrito 
                    SET cantidad = ? 
                    WHERE id_carrito = ?
                ");
                $stmt->execute([$newCantidad, $cartItem['id_carrito']]);
            } else {
                // Insertar nuevo item
                $stmt = $pdo->prepare("
                    INSERT INTO carrito (id_usuario, id_producto, tipo_producto, cantidad, precio) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $idProducto, $tipoProducto, $cantidad, $precio]);
            }
        } else {
            // Si no está logueado, guardar en la sesión
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Generar un ID único para el item
            $itemId = uniqid();
            
            // Obtener información adicional del producto
            switch ($tipoProducto) {
                case 'cancion':
                    $stmt = $pdo->prepare("
                        SELECT c.nombre_cancion as nombre, u.nombre_usuario as artista, a.imagen_album_path as imagen
                        FROM canciones c
                        JOIN artista ar ON c.id_artista = ar.id_artista
                        JOIN usuario u ON ar.usuario = u.id_usuario
                        JOIN album a ON c.id_album = a.id_album
                        WHERE c.id_cancion = ?
                    ");
                    break;
                case 'album':
                    $stmt = $pdo->prepare("
                        SELECT a.nombre_album as nombre, u.nombre_usuario as artista, a.imagen_album_path as imagen
                        FROM album a
                        JOIN artista ar ON a.id_artista = ar.id_artista
                        JOIN usuario u ON ar.usuario = u.id_usuario
                        WHERE a.id_album = ?
                    ");
                    break;
                case 'sencillo':
                    $stmt = $pdo->prepare("
                        SELECT s.nombre_sencillo as nombre, u.nombre_usuario as artista, s.imagen_sencillo_path as imagen
                        FROM sencillos s
                        JOIN artista ar ON s.id_artista = ar.id_artista
                        JOIN usuario u ON ar.usuario = u.id_usuario
                        WHERE s.id_sencillo = ?
                    ");
                    break;
            }
            
            $stmt->execute([$idProducto]);
            $productoInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si el producto ya está en el carrito
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id_producto'] == $idProducto && $item['tipo_producto'] == $tipoProducto) {
                    $item['cantidad'] += $cantidad;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // Añadir al carrito
                $_SESSION['cart'][] = [
                    'id_carrito' => $itemId,
                    'id_producto' => $idProducto,
                    'tipo_producto' => $tipoProducto,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'nombre' => $productoInfo['nombre'],
                    'artista' => $productoInfo['artista'],
                    'imagen' => $productoInfo['imagen']
                ];
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Producto añadido al carrito']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al añadir al carrito: ' . $e->getMessage()]);
    }
}

/**
 * Elimina un producto del carrito
 */
function removeFromCart() {
    // Verificar si se enviaron datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no válido']);
        exit;
    }
    
    // Obtener datos
    $idCarrito = isset($_POST['id_carrito']) ? $_POST['id_carrito'] : null;
    
    if (!$idCarrito) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    // Si el usuario está logueado, eliminar de la base de datos
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                DELETE FROM carrito 
                WHERE id_carrito = ? AND id_usuario = ?
            ");
            $stmt->execute([$idCarrito, $_SESSION['user_id']]);
            
            if ($stmt->rowCount() === 0) {
                echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el producto del carrito']);
                exit;
            }
            
            echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar del carrito: ' . $e->getMessage()]);
        }
    } else {
        // Si no está logueado, eliminar de la sesión
        if (!isset($_SESSION['cart'])) {
            echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
            exit;
        }
        
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id_carrito'] == $idCarrito) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexar array
                echo json_encode(['success' => true, 'message' => 'Producto eliminado del carrito']);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
    }
}

/**
 * Actualiza la cantidad de un producto en el carrito
 */
function updateCart() {
    // Verificar si se enviaron datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no válido']);
        exit;
    }
    
    // Obtener datos
    $idCarrito = isset($_POST['id_carrito']) ? $_POST['id_carrito'] : null;
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : null;
    
    if (!$idCarrito || !$cantidad) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    // Si la cantidad es 0 o negativa, eliminar el producto
    if ($cantidad <= 0) {
        $_POST['id_carrito'] = $idCarrito;
        removeFromCart();
        exit;
    }
    
    // Si el usuario está logueado, actualizar en la base de datos
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                UPDATE carrito 
                SET cantidad = ? 
                WHERE id_carrito = ? AND id_usuario = ?
            ");
            $stmt->execute([$cantidad, $idCarrito, $_SESSION['user_id']]);
            
            if ($stmt->rowCount() === 0) {
                echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el carrito']);
                exit;
            }
            
            echo json_encode(['success' => true, 'message' => 'Carrito actualizado']);
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el carrito: ' . $e->getMessage()]);
        }
    } else {
        // Si no está logueado, actualizar en la sesión
        if (!isset($_SESSION['cart'])) {
            echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
            exit;
        }
        
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id_carrito'] == $idCarrito) {
                $item['cantidad'] = $cantidad;
                echo json_encode(['success' => true, 'message' => 'Carrito actualizado']);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado en el carrito']);
    }
}

/**
 * Procesa el pago y finaliza la compra
 */
function checkout() {
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión para realizar esta acción']);
        exit;
    }
    
    global $pdo;
    
    try {
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Obtener items del carrito
        $stmt = $pdo->prepare("
            SELECT * FROM carrito WHERE id_usuario = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($cartItems) === 0) {
            echo json_encode(['success' => false, 'message' => 'Carrito vacío']);
            exit;
        }
        
        // Crear registro de compra
        $stmt = $pdo->prepare("
            INSERT INTO compras (id_usuario, fecha_compra, total) 
            VALUES (?, NOW(), ?)
        ");
        
        // Calcular total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }
        
        $stmt->execute([$_SESSION['user_id'], $total]);
        $compraId = $pdo->lastInsertId();
        
        // Registrar detalles de la compra
        $stmt = $pdo->prepare("
            INSERT INTO detalle_compra (id_compra, id_producto, tipo_producto, cantidad, precio) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($cartItems as $item) {
            $stmt->execute([
                $compraId,
                $item['id_producto'],
                $item['tipo_producto'],
                $item['cantidad'],
                $item['precio']
            ]);
            
            // Añadir a la biblioteca del usuario
            $stmtBiblioteca = $pdo->prepare("
                INSERT INTO biblioteca_usuario (id_usuario, id_cancion, id_sencillo, tipo, fecha_agregado) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            if ($item['tipo_producto'] === 'cancion') {
                $stmtBiblioteca->execute([
                    $_SESSION['user_id'],
                    $item['id_producto'],
                    null,
                    'album_song'
                ]);
            } elseif ($item['tipo_producto'] === 'sencillo') {
                $stmtBiblioteca->execute([
                    $_SESSION['user_id'],
                    null,
                    $item['id_producto'],
                    'sencillo'
                ]);
            } elseif ($item['tipo_producto'] === 'album') {
                // Obtener todas las canciones del álbum
                $stmtCanciones = $pdo->prepare("
                    SELECT id_cancion FROM canciones WHERE id_album = ?
                ");
                $stmtCanciones->execute([$item['id_producto']]);
                $canciones = $stmtCanciones->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($canciones as $cancion) {
                    $stmtBiblioteca->execute([
                        $_SESSION['user_id'],
                        $cancion['id_cancion'],
                        null,
                        'album_song'
                    ]);
                }
            }
        }
        
        // Vaciar carrito
        $stmt = $pdo->prepare("
            DELETE FROM carrito WHERE id_usuario = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        // Confirmar transacción
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Compra realizada con éxito',
            'compra_id' => $compraId
        ]);
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Error al procesar la compra: ' . $e->getMessage()]);
    }
}
?>
