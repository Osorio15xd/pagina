<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
require_once '../config/db_connect.php';

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar si se proporcionó un mensaje
if (!isset($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó un mensaje']);
    exit;
}

$message = trim($_POST['message']);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';

// Obtener respuesta del chatbot
try {
    // Buscar respuestas que coincidan con el mensaje usando keywords
    $stmt = $pdo->prepare("
        SELECT r.id, r.response, r.priority, k.weight
        FROM ai_responses r
        JOIN ai_keywords k ON r.id = k.response_id
        WHERE LOWER(:message) LIKE CONCAT('%', LOWER(k.keyword), '%')
        AND r.active = 1
        ORDER BY r.priority DESC, k.weight DESC
        LIMIT 1
    ");
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
    
    $result = $stmt->fetch();
    
    // Si no se encontró una respuesta, usar respuesta por defecto
    if (!$result) {
        $response = "Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.";
    } else {
        $response = $result['response'];
        
        // Reemplazar variables en la respuesta
        $response = str_replace('{greeting}', $username ? "Hola $username, " : "", $response);
    }
    
    // Guardar la conversación en el historial
    if ($userId > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO ai_chat_history (user_id, message, response)
            VALUES (:user_id, :message, :response)
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':response', $response, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'response' => $response]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}
?>
