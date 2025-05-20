<?php
session_start();
require_once '../config/db_connect.php';

// Verificar si el usuario está autenticado
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Obtener historial de chat si el usuario está autenticado
$chatHistory = [];
if ($isLoggedIn) {
    $stmt = $pdo->prepare("
        SELECT message, response, timestamp 
        FROM ai_chat_history 
        WHERE user_id = ? 
        ORDER BY timestamp DESC 
        LIMIT 20
    ");
    $stmt->execute([$userId]);
    $chatHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Invertir el orden para mostrar los mensajes más antiguos primero
    $chatHistory = array_reverse($chatHistory);
}

// Incluir el encabezado
include_once '../includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card chatbot-card">
                <div class="card-header bg-primary text-white">
                    <h3><i class="fas fa-robot me-2"></i> Asistente Virtual de BassCulture</h3>
                </div>
                <div class="card-body">
                    <div id="chat-container">
                        <div id="chat-messages">
                            <!-- Mensaje de bienvenida -->
                            <div class="message bot-message">
                                <div class="message-content">
                                    <p>¡Hola<?php echo $isLoggedIn ? ' ' . htmlspecialchars($_SESSION['username']) : ''; ?>! Soy el asistente virtual de BassCulture. ¿En qué puedo ayudarte hoy?</p>
                                </div>
                            </div>
                            
                            <?php if ($isLoggedIn && !empty($chatHistory)): ?>
                                <!-- Historial de chat -->
                                <?php foreach ($chatHistory as $chat): ?>
                                    <div class="message user-message">
                                        <div class="message-content">
                                            <p><?php echo htmlspecialchars($chat['message']); ?></p>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($chat['timestamp'])); ?></small>
                                        </div>
                                    </div>
                                    <div class="message bot-message">
                                        <div class="message-content">
                                            <p><?php echo htmlspecialchars($chat['response']); ?></p>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($chat['timestamp'])); ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div id="chat-input-container">
                            <form id="chat-form">
                                <div class="input-group">
                                    <input type="text" id="chat-input" class="form-control" placeholder="Escribe tu mensaje aquí..." required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <?php if (!$isLoggedIn): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Inicia sesión para guardar tu historial de conversaciones.
                            <a href="../auth/login.php" class="alert-link">Iniciar sesión</a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="chat-suggestions mt-3">
                        <h5>Preguntas frecuentes:</h5>
                        <div class="suggestion-buttons">
                            <button class="btn btn-outline-primary btn-sm suggestion" data-text="¿Cómo funciona BassCulture?">¿Cómo funciona BassCulture?</button>
                            <button class="btn btn-outline-primary btn-sm suggestion" data-text="¿Cómo puedo registrarme?">¿Cómo puedo registrarme?</button>
                            <button class="btn btn-outline-primary btn-sm suggestion" data-text="¿Cómo puedo subir música?">¿Cómo puedo subir música?</button>
                            <button class="btn btn-outline-primary btn-sm suggestion" data-text="¿Cuáles son los planes de suscripción?">¿Cuáles son los planes de suscripción?</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chatbot-card {
        margin-top: 20px;
        margin-bottom: 20px;
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
        padding: 15px 20px;
    }
    
    #chat-container {
        display: flex;
        flex-direction: column;
        height: 500px;
    }
    
    #chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .message {
        display: flex;
        margin-bottom: 10px;
    }
    
    .user-message {
        justify-content: flex-end;
    }
    
    .bot-message {
        justify-content: flex-start;
    }
    
    .message-content {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
    }
    
    .user-message .message-content {
        background-color: #007bff;
        color: white;
        border-radius: 15px 15px 0 15px;
    }
    
    .bot-message .message-content {
        background-color: #f1f1f1;
        color: #333;
        border-radius: 15px 15px 15px 0;
    }
    
    .message-content p {
        margin-bottom: 0;
    }
    
    #chat-input-container {
        padding: 15px;
        border-top: 1px solid #eee;
    }
    
    #chat-form {
        display: flex;
    }
    
    #chat-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 30px;
        margin-right: 10px;
    }
    
    .chat-suggestions {
        padding: 10px 15px;
    }
    
    .suggestion-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    
    .suggestion {
        margin-bottom: 5px;
    }
    
    /* Estilos para pantallas pequeñas */
    @media (max-width: 768px) {
        #chat-container {
            height: 400px;
        }
        
        .message-content {
            max-width: 90%;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    const suggestionButtons = document.querySelectorAll('.suggestion');
    
    // Función para añadir un mensaje al chat
    function addMessage(message, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        const messageParagraph = document.createElement('p');
        messageParagraph.textContent = message;
        
        const timestamp = document.createElement('small');
        timestamp.className = 'text-muted';
        timestamp.textContent = new Date().toLocaleTimeString();
        
        messageContent.appendChild(messageParagraph);
        messageContent.appendChild(timestamp);
        messageDiv.appendChild(messageContent);
        
        chatMessages.appendChild(messageDiv);
        
        // Scroll al final del chat
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Función para enviar mensaje al servidor
    async function sendMessage(message) {
        try {
            const response = await fetch('../chatbot/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    user_id: <?php echo $isLoggedIn ? $_SESSION['user_id'] : 'null'; ?>
                }),
            });
            
            const data = await response.json();
            return data.response;
        } catch (error) {
            console.error('Error:', error);
            return 'Lo siento, ha ocurrido un error al procesar tu mensaje.';
        }
    }
    
    // Manejar envío del formulario
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = chatInput.value.trim();
        if (!message) return;
        
        // Añadir mensaje del usuario al chat
        addMessage(message, true);
        
        // Limpiar input
        chatInput.value = '';
        
        // Mostrar indicador de "escribiendo..."
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot-message typing';
        typingDiv.innerHTML = '<div class="message-content"><p>Escribiendo...</p></div>';
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Enviar mensaje al servidor y obtener respuesta
        const botResponse = await sendMessage(message);
        
        // Eliminar indicador de "escribiendo..."
        chatMessages.removeChild(typingDiv);
        
        // Añadir respuesta del bot
        addMessage(botResponse);
    });
    
    // Manejar clic en sugerencias
    suggestionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const suggestionText = this.getAttribute('data-text');
            chatInput.value = suggestionText;
            chatInput.focus();
        });
    });
    
    // Scroll al final del chat al cargar la página
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
</script>

<?php include_once '../includes/footer.php'; ?>
