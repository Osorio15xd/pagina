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

// Procesar la acción
switch ($action) {
    case 'get_profile':
        getProfile();
        break;
    case 'update_profile':
        updateProfile();
        break;
    case 'add_to_library':
        addToLibrary();
        break;
    case 'remove_from_library':
        removeFromLibrary();
        break;
    case 'get_library':
        getLibrary();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

/**
 * Obtiene el perfil del usuario
 */
function getProfile() {
    global $conn;
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    try {
        // Obtener información del usuario
        $stmt = $conn->prepare("
            SELECT id_usuario, nombre_usuario, correo, foto_perfil 
            FROM usuario
            WHERE id_usuario = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
            exit;
        }
        
        // Devolver la información del usuario
        echo json_encode([
            'success' => true, 
            'user' => $user
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener perfil: ' . $e->getMessage()]);
    }
}

/**
 * Actualiza el perfil del usuario
 */
function updateProfile() {
    global $conn;
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Verificar si se enviaron datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no válido']);
        exit;
    }
    
    // Obtener datos del formulario
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : null;
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : null;
    
    try {
        // Verificar si el usuario existe
        $stmt = $conn->prepare("SELECT contraseña FROM usuario WHERE id_usuario = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
            exit;
        }
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        // Actualizar nombre de usuario si se proporcionó
        if ($username) {
            // Verificar si el nombre de usuario ya está en uso
            $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE nombre_usuario = ? AND id_usuario != ?");
            $stmt->execute([$username, $userId]);
            
            if ($stmt->rowCount() > 0) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE usuario SET nombre_usuario = ? WHERE id_usuario = ?");
            $stmt->execute([$username, $userId]);
            
            // Actualizar sesión
            $_SESSION['username'] = $username;
        }
        
        // Actualizar email si se proporcionó
        if ($email) {
            // Verificar si el email ya está en uso
            $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = ? AND id_usuario != ?");
            $stmt->execute([$email, $userId]);
            
            if ($stmt->rowCount() > 0) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'message' => 'El email ya está en uso']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE usuario SET correo = ? WHERE id_usuario = ?");
            $stmt->execute([$email, $userId]);
            
            // Actualizar sesión
            $_SESSION['user_email'] = $email;
        }
        
        // Actualizar contraseña si se proporcionó
        if ($currentPassword && $newPassword) {
            // Verificar contraseña actual
            if (!password_verify($currentPassword, $user['contraseña'])) {
                $conn->rollBack();
                echo json_encode(['success' => false, 'message' => 'Contraseña actual incorrecta']);
                exit;
            }
            
            // Actualizar contraseña
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuario SET contraseña = ? WHERE id_usuario = ?");
            $stmt->execute([$hashedPassword, $userId]);
        }
        
        // Procesar imagen de perfil si se proporcionó
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/profile/';
            
            // Crear directorio si no existe
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generar nombre único para el archivo
            $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $fileName = 'profile_' . $userId . '_' . time() . '.' . $fileExtension;
            $targetFile = $uploadDir . $fileName;
            
            // Mover archivo
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                // Actualizar ruta de imagen en la base de datos
                $imagePath = 'uploads/profile/' . $fileName;
                $stmt = $conn->prepare("UPDATE usuario SET foto_perfil = ? WHERE id_usuario = ?");
                $stmt->execute([$imagePath, $userId]);
                
                // Actualizar sesión
                $_SESSION['user_photo'] = $imagePath;
            } else {
                $conn->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error al subir la imagen de perfil']);
                exit;
            }
        }
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Perfil actualizado correctamente']);
        
    } catch (PDOException $e) {
        // Revertir transacción en caso de error
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        
        echo json_encode(['success' => false, 'message' => 'Error al actualizar perfil: ' . $e->getMessage()]);
    }
}

/**
 * Añade una canción a la biblioteca del usuario
 */
function addToLibrary() {
    global $conn;
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Verificar si se enviaron datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no válido']);
        exit;
    }
    
    // Obtener datos
    $songId = isset($_POST['song_id']) ? $_POST['song_id'] : null;
    $songType = isset($_POST['type']) ? $_POST['type'] : null;
    
    if (!$songId || !$songType) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    try {
        // Verificar si la canción ya está en la biblioteca
        $stmt = $conn->prepare("
            SELECT id_biblioteca 
            FROM biblioteca_usuario 
            WHERE id_usuario = ? AND 
                  " . ($songType == 'album_song' ? "id_cancion = ? AND id_sencillo IS NULL" : "id_sencillo = ? AND id_cancion IS NULL") . " AND 
                  tipo = ?
        ");
        $stmt->execute([$userId, $songId, $songType]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'La canción ya está en tu biblioteca']);
            exit;
        }
        
        // Añadir a biblioteca
        $stmt = $conn->prepare("
            INSERT INTO biblioteca_usuario 
            (id_usuario, " . ($songType == 'album_song' ? "id_cancion, id_sencillo" : "id_sencillo, id_cancion") . ", tipo) 
            VALUES (?, ?, NULL, ?)
        ");
        $stmt->execute([$userId, $songId, $songType]);
        
        echo json_encode(['success' => true, 'message' => 'Canción añadida a tu biblioteca']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al añadir a biblioteca: ' . $e->getMessage()]);
    }
}

/**
 * Elimina una canción de la biblioteca del usuario
 */
function removeFromLibrary() {
    global $conn;
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Verificar si se enviaron datos
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Método no válido']);
        exit;
    }
    
    // Obtener datos
    $songId = isset($_POST['song_id']) ? $_POST['song_id'] : null;
    $songType = isset($_POST['type']) ? $_POST['type'] : null;
    
    if (!$songId || !$songType) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    
    try {
        // Eliminar de biblioteca
        $stmt = $conn->prepare("
            DELETE FROM biblioteca_usuario 
            WHERE id_usuario = ? AND 
                  " . ($songType == 'album_song' ? "id_cancion = ? AND id_sencillo IS NULL" : "id_sencillo = ? AND id_cancion IS NULL") . " AND 
                  tipo = ?
        ");
        $stmt->execute([$userId, $songId, $songType]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'La canción no está en tu biblioteca']);
            exit;
        }
        
        echo json_encode(['success' => true, 'message' => 'Canción eliminada de tu biblioteca']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar de biblioteca: ' . $e->getMessage()]);
    }
}

/**
 * Obtiene la biblioteca del usuario
 */
function getLibrary() {
    global $conn;
    
    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no logueado']);
        exit;
    }
    
    $userId = $_SESSION['user_id'];
    
    try {
        // Obtener canciones de álbumes en la biblioteca
        $stmt = $conn->prepare("
            SELECT 
                c.id_cancion as id, 
                c.nombre_cancion as titulo, 
                a.imagen_album_path as portada, 
                u.nombre_usuario as artista, 
                g.nombre_genero as genero,
                'album_song' as tipo,
                b.fecha_agregado
            FROM biblioteca_usuario b
            JOIN canciones c ON b.id_cancion = c.id_cancion
            JOIN album a ON c.id_album = a.id_album
            JOIN artista art ON c.id_artista = art.id_artista
            JOIN usuario u ON art.usuario = u.id_usuario
            JOIN genero g ON a.id_genero = g.id_genero
            WHERE b.id_usuario = ? AND b.tipo = 'album_song'
        ");
        $stmt->execute([$userId]);
        $canciones_album = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener sencillos en la biblioteca
        $stmt = $conn->prepare("
            SELECT 
                s.id_sencillo as id, 
                s.nombre_sencillo as titulo, 
                s.imagen_sencillo_path as portada, 
                u.nombre_usuario as artista, 
                g.nombre_genero as genero,
                'sencillo' as tipo,
                b.fecha_agregado
            FROM biblioteca_usuario b
            JOIN sencillos s ON b.id_sencillo = s.id_sencillo
            JOIN artista a ON s.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            JOIN genero g ON s.id_genero = g.id_genero
            WHERE b.id_usuario = ? AND b.tipo = 'sencillo'
        ");
        $stmt->execute([$userId]);
        $sencillos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combinar resultados y ordenar por fecha de agregado
        $biblioteca = array_merge($canciones_album, $sencillos);
        usort($biblioteca, function($a, $b) {
            return strtotime($b['fecha_agregado']) - strtotime($a['fecha_agregado']);
        });
        
        echo json_encode(['success' => true, 'library' => $biblioteca]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener biblioteca: ' . $e->getMessage()]);
    }
}
?>
