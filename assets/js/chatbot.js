document.addEventListener("DOMContentLoaded", () => {
  const chatbotMessages = document.querySelector(".chatbot-messages")
  const chatbotInput = document.getElementById("chatbot-input")
  const chatbotSendBtn = document.getElementById("chatbot-send-btn")
  const suggestionChips = document.querySelectorAll(".suggestion-chip")

  // Función para enviar mensaje
  function sendMessage() {
    const message = chatbotInput.value.trim()
    if (message === "") return

    // Añadir mensaje del usuario al chat
    addMessage(message, "user")

    // Limpiar input
    chatbotInput.value = ""

    // Enviar mensaje al servidor y obtener respuesta
    fetch("api/chatbot.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `message=${encodeURIComponent(message)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Añadir respuesta del bot al chat
          addMessage(data.response, "bot")
        } else {
          addMessage("Lo siento, ha ocurrido un error. Por favor, inténtalo de nuevo más tarde.", "bot")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        addMessage("Lo siento, ha ocurrido un error de conexión. Por favor, inténtalo de nuevo más tarde.", "bot")
      })
  }

  // Función para añadir mensaje al chat
  function addMessage(text, sender) {
    const messageElement = document.createElement("div")
    messageElement.classList.add("message", sender)

    // Escapar HTML para prevenir XSS
    const safeText = text.replace(/</g, "&lt;").replace(/>/g, "&gt;")
    messageElement.innerHTML = safeText

    // Añadir hora
    const timeElement = document.createElement("span")
    timeElement.classList.add("message-time")
    const now = new Date()
    timeElement.textContent = `${now.getHours()}:${now.getMinutes().toString().padStart(2, "0")}`
    messageElement.appendChild(timeElement)

    // Añadir al chat
    chatbotMessages.appendChild(messageElement)

    // Scroll al final
    chatbotMessages.scrollTop = chatbotMessages.scrollHeight
  }

  // Evento para enviar mensaje con botón
  if (chatbotSendBtn) {
    chatbotSendBtn.addEventListener("click", sendMessage)
  }

  // Evento para enviar mensaje con Enter
  if (chatbotInput) {
    chatbotInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault()
        sendMessage()
      }
    })
  }

  // Evento para chips de sugerencias
  suggestionChips.forEach((chip) => {
    chip.addEventListener("click", function () {
      chatbotInput.value = this.textContent
      sendMessage()
    })
  })

  // Mensaje de bienvenida
  setTimeout(() => {
    addMessage("¡Hola! Soy el asistente virtual de BassCulture. ¿En qué puedo ayudarte hoy?", "bot")
  }, 500)
})
