// Variables globales
let currentSong = null
let isPlaying = false
const playQueue = []
const audio = new Audio()

// Inicializar la aplicación cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  // Aplicar tema y color guardados
  applyThemeAndColor()

  // Inicializar el reproductor de música
  initPlayer()

  // Inicializar el menú desplegable de usuario
  initUserDropdown()

  // Inicializar el cambio de tema
  initThemeToggle()

  // Inicializar los eventos de las tarjetas de música
  initMusicCards()

  // Inicializar modales
  initModals()

  // Cargar el número de elementos en el carrito
  updateCartCount()
})

// Aplicar tema y color guardados
function applyThemeAndColor() {
  const savedTheme = localStorage.getItem("theme") || "dark"
  const savedColor = localStorage.getItem("primaryColor") || "#1db954"

  // Aplicar color primario
  document.documentElement.style.setProperty("--primary-color", savedColor)
  document.documentElement.style.setProperty("--primary-hover", adjustColor(savedColor, 20))

  // Aplicar tema
  if (savedTheme === "dark") {
    document.documentElement.style.setProperty("--bg-color", "#121212")
    document.documentElement.style.setProperty("--bg-secondary", "#181818")
    document.documentElement.style.setProperty("--bg-card", "#282828")
    document.documentElement.style.setProperty("--text-color", "#eee")
    document.documentElement.style.setProperty("--text-secondary", "#b3b3b3")
    document.documentElement.style.setProperty("--border-color", "#333")

    const themeToggle = document.getElementById("theme-toggle")
    if (themeToggle) {
      themeToggle.innerHTML = '<i class="fas fa-sun"></i>'
    }
  } else if (savedTheme === "light") {
    document.documentElement.style.setProperty("--bg-color", "#f5f5f5")
    document.documentElement.style.setProperty("--bg-secondary", "#ffffff")
    document.documentElement.style.setProperty("--bg-card", "#e9e9e9")
    document.documentElement.style.setProperty("--text-color", "#333")
    document.documentElement.style.setProperty("--text-secondary", "#666")
    document.documentElement.style.setProperty("--border-color", "#ddd")

    const themeToggle = document.getElementById("theme-toggle")
    if (themeToggle) {
      themeToggle.innerHTML = '<i class="fas fa-moon"></i>'
    }
  }
}

// Inicializar el reproductor de música
function initPlayer() {
  const playPauseBtn = document.getElementById("play-pause-btn")
  const prevBtn = document.getElementById("prev-btn")
  const nextBtn = document.getElementById("next-btn")
  const progressBar = document.getElementById("progress-bar")
  const progress = document.getElementById("progress")
  const currentTimeEl = document.getElementById("current-time")
  const totalTimeEl = document.getElementById("total-time")
  const volumeBtn = document.getElementById("volume-btn")
  const volumeSlider = document.getElementById("volume-slider")

  if (playPauseBtn) {
    playPauseBtn.addEventListener("click", togglePlay)
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", playPrevious)
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", playNext)
  }

  if (progressBar) {
    progressBar.addEventListener("click", (e) => {
      const percent = e.offsetX / progressBar.offsetWidth
      audio.currentTime = percent * audio.duration
    })
  }

  if (volumeBtn) {
    volumeBtn.addEventListener("click", toggleMute)
  }

  if (volumeSlider) {
    volumeSlider.addEventListener("input", () => {
      const volume = volumeSlider.value / 100
      audio.volume = volume
      updateVolumeIcon(volume)
    })
  }

  // Configurar eventos de audio
  audio.addEventListener("timeupdate", updateProgress)
  audio.addEventListener("ended", playNext)
  audio.addEventListener("play", updatePlayButton)
  audio.addEventListener("pause", updatePlayButton)
  audio.addEventListener("loadedmetadata", updateDuration)

  // Mostrar u ocultar el reproductor flotante según si hay una canción reproduciéndose
  updateFloatingPlayer()
}

// Actualizar el reproductor flotante
function updateFloatingPlayer() {
  const floatingPlayer = document.getElementById("floating-player")
  if (floatingPlayer) {
    if (audio.src) {
      floatingPlayer.classList.add("active")
    } else {
      floatingPlayer.classList.remove("active")
    }
  }
}

// Reproducir/pausar la canción actual
function togglePlay() {
  if (!audio.src) return

  if (audio.paused) {
    audio.play()
  } else {
    audio.pause()
  }
}

// Actualizar el botón de reproducción/pausa
function updatePlayButton() {
  const playPauseBtn = document.getElementById("play-pause-btn")
  if (!playPauseBtn) return

  if (audio.paused) {
    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>'
    isPlaying = false
  } else {
    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>'
    isPlaying = true
  }

  // Actualizar el reproductor flotante
  updateFloatingPlayer()
}

// Actualizar la barra de progreso
function updateProgress() {
  const progressBar = document.getElementById("progress")
  const currentTimeEl = document.getElementById("current-time")

  if (progressBar) {
    const percent = (audio.currentTime / audio.duration) * 100
    progressBar.style.width = `${percent}%`
  }

  if (currentTimeEl) {
    currentTimeEl.textContent = formatTime(audio.currentTime)
  }
}

// Actualizar la duración total
function updateDuration() {
  const totalTimeEl = document.getElementById("total-time")
  if (totalTimeEl) {
    totalTimeEl.textContent = formatTime(audio.duration)
  }
}

// Formatear tiempo en formato mm:ss
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60)
  const secs = Math.floor(seconds % 60)
  return `${minutes}:${secs < 10 ? "0" : ""}${secs}`
}

// Alternar silencio
function toggleMute() {
  audio.muted = !audio.muted
  updateVolumeIcon(audio.muted ? 0 : audio.volume)
}

// Actualizar icono de volumen
function updateVolumeIcon(volume) {
  const volumeBtn = document.getElementById("volume-btn")
  if (!volumeBtn) return

  if (volume === 0 || audio.muted) {
    volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>'
  } else if (volume < 0.5) {
    volumeBtn.innerHTML = '<i class="fas fa-volume-down"></i>'
  } else {
    volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>'
  }
}

// Reproducir la canción anterior
function playPrevious() {
  if (audio.currentTime > 3) {
    // Si la canción actual ha reproducido más de 3 segundos, reiniciarla
    audio.currentTime = 0
    return
  }

  if (playQueue.length === 0) {
    showToast("No hay canciones anteriores en la cola", "info")
    return
  }

  // Obtener la canción anterior de la cola
  const previousSong = playQueue.pop()
  if (previousSong) {
    playSong(previousSong.id, previousSong.type)
  } else {
    showToast("No hay canciones anteriores en la cola", "info")
  }
}

// Reproducir la siguiente canción
function playNext() {
  if (playQueue.length === 0) {
    showToast("No hay más canciones en la cola", "info")
    return
  }

  // Obtener la siguiente canción de la cola
  const nextSong = playQueue.shift()
  if (nextSong) {
    playSong(nextSong.id, nextSong.type)
  } else {
    showToast("No hay más canciones en la cola", "info")
  }
}

// Reproducir una canción específica
function playSong(songId, songType = "album_cancion") {
  // Mostrar un mensaje de carga
  showToast("Cargando canción...", "info")

  // Enviar solicitud AJAX para reproducir la canción
  fetch(`api/music.php?action=get_song&id=${songId}&type=${songType}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`)
      }
      return response.json()
    })
    .then((data) => {
      if (data.success) {
        // Si hay una canción reproduciéndose, añadirla a la cola
        if (currentSong && currentSong.id) {
          playQueue.push(currentSong)
        }

        const song = data.song
        currentSong = {
          id: song.id,
          title: song.titulo || song.nombre_cancion || "Canción sin título",
          artist: song.artista || "Artista desconocido",
          type: songType,
        }

        // Verificar si el archivo de audio existe y es accesible
        const audioSrc = song.archivo_audio || song.cancion_path

        if (!audioSrc) {
          showToast("La canción no tiene un archivo de audio asociado", "error")
          return
        }

        // Configurar el audio
        audio.src = audioSrc

        // Intentar reproducir
        audio
          .play()
          .then(() => {
            console.log("Reproducción iniciada con éxito")
            showToast(`Reproduciendo: ${currentSong.title}`, "success")
          })
          .catch((error) => {
            console.error("Error al reproducir:", error)
            showToast(
              "No se pudo reproducir el archivo de audio. Puede que el formato no sea compatible o el archivo no exista.",
              "error",
            )
          })

        // Actualizar la interfaz del reproductor
        const playerCover = document.getElementById("floating-player-cover")
        const trackName = document.getElementById("floating-track-name")

        if (playerCover) {
          playerCover.src =
            song.portada || song.imagen_album_path || song.imagen_sencillo_path || "assets/img/default-cover.png"
        }

        if (trackName) {
          trackName.textContent = `${currentSong.title} - ${currentSong.artist}`
        }

        // Mostrar el reproductor flotante
        const floatingPlayer = document.getElementById("floating-player")
        if (floatingPlayer) {
          floatingPlayer.classList.add("active")
        }

        // Registrar la reproducción
        fetch(`api/music.php?action=record_play&id=${songId}&type=${songType}`).catch((error) => {
          console.error("Error al registrar reproducción:", error)
        })
      } else {
        showToast(data.message || "Error al reproducir la canción", "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showToast("Error al comunicarse con el servidor", "error")
    })
}

// Añadir canción a la cola de reproducción
function addToQueue(songId, songType = "album_cancion") {
  playQueue.push({ id: songId, type: songType })
  showToast("Canción añadida a la cola de reproducción", "success")
}

// Añadir canción al carrito
function addToCart(songId, songType = "album_cancion") {
  fetch("api/cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=add_to_cart&id_producto=${songId}&tipo_producto=${songType === "album_cancion" ? "cancion" : songType}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast("Canción añadida al carrito", "success")
        updateCartCount()
      } else {
        showToast(data.message || "Error al añadir la canción al carrito", "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showToast("Error al comunicarse con el servidor", "error")
    })
}

// Actualizar contador del carrito
function updateCartCount() {
  const cartCount = document.getElementById("cart-count")
  if (!cartCount) return

  fetch("api/cart.php?action=get_count")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        cartCount.textContent = data.count
      }
    })
    .catch((error) => {
      console.error("Error al obtener el contador del carrito:", error)
    })
}

// Mostrar modal para añadir a playlist
function showAddToPlaylistModal(songId, songType = "album_cancion") {
  // Obtener las playlists del usuario
  fetch("api/playlist.php?action=get_playlists")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const playlists = data.playlists

        // Crear el contenido del modal
        let modalContent = `
          <div class="modal-header">
            <h3 class="modal-title">Añadir a Playlist</h3>
            <button class="modal-close" id="close-playlist-modal"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
        `

        if (playlists && playlists.length > 0) {
          modalContent += `<p>Selecciona una playlist:</p><ul class="playlist-select-list">`

          playlists.forEach((playlist) => {
            modalContent += `
              <li data-id="${playlist.id_playlist}" data-song-id="${songId}" data-song-type="${songType}">
                <i class="fas fa-list"></i> ${playlist.nombre_playlist}
              </li>
            `
          })

          modalContent += `</ul>`
        } else {
          modalContent += `
            <div class="empty-state">
              <p>No tienes playlists. Crea una nueva playlist primero.</p>
              <button id="create-new-playlist-btn" class="btn-primary">Crear Playlist</button>
            </div>
          `
        }

        modalContent += `
          </div>
          <div class="modal-footer">
            <button class="btn-secondary" id="cancel-playlist-modal">Cancelar</button>
          </div>
        `

        // Mostrar el modal
        const playlistModal = document.getElementById("playlist-modal")
        if (playlistModal) {
          playlistModal.querySelector(".modal-container").innerHTML = modalContent
          playlistModal.classList.add("active")

          // Eventos para los elementos del modal
          const closeBtn = playlistModal.querySelector("#close-playlist-modal")
          const cancelBtn = playlistModal.querySelector("#cancel-playlist-modal")
          const createNewPlaylistBtn = playlistModal.querySelector("#create-new-playlist-btn")
          const playlistItems = playlistModal.querySelectorAll(".playlist-select-list li")

          if (closeBtn) {
            closeBtn.addEventListener("click", () => {
              playlistModal.classList.remove("active")
            })
          }

          if (cancelBtn) {
            cancelBtn.addEventListener("click", () => {
              playlistModal.classList.remove("active")
            })
          }

          if (createNewPlaylistBtn) {
            createNewPlaylistBtn.addEventListener("click", () => {
              playlistModal.classList.remove("active")
              // Mostrar modal para crear playlist
              showCreatePlaylistModal()
            })
          }

          playlistItems.forEach((item) => {
            item.addEventListener("click", () => {
              const playlistId = item.dataset.id
              const songId = item.dataset.songId
              const songType = item.dataset.songType

              // Añadir canción a la playlist
              addSongToPlaylist(playlistId, songId, songType)

              // Cerrar el modal
              playlistModal.classList.remove("active")
            })
          })
        }
      } else {
        showToast(data.message || "Error al obtener las playlists", "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showToast("Error al comunicarse con el servidor", "error")
    })
}

// Añadir canción a una playlist
function addSongToPlaylist(playlistId, songId, songType) {
  fetch("api/playlist.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `action=add_song&id_playlist=${playlistId}&id_cancion=${songId}&tipo_cancion=${songType}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast("Canción añadida a la playlist", "success")
      } else {
        showToast(data.message || "Error al añadir la canción a la playlist", "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showToast("Error al comunicarse con el servidor", "error")
    })
}

// Mostrar modal para crear playlist
function showCreatePlaylistModal() {
  const playlistModal = document.getElementById("playlist-modal")
  if (playlistModal) {
    const modalContent = `
      <div class="modal-header">
        <h3 class="modal-title">Nueva Playlist</h3>
        <button class="modal-close" id="close-create-playlist-modal"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <form id="create-playlist-form">
          <div class="form-group">
            <label for="playlist-name">Nombre de la playlist</label>
            <input type="text" id="playlist-name" required minlength="3" maxlength="100">
          </div>
          <div class="form-group">
            <label for="playlist-description">Descripción (opcional)</label>
            <textarea id="playlist-description" rows="3" maxlength="500"></textarea>
          </div>
          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" id="playlist-public" class="form-check-input">
              <label for="playlist-public" class="form-check-label">Playlist pública</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary" id="cancel-create-playlist-modal">Cancelar</button>
        <button class="btn-primary" id="save-playlist">Guardar</button>
      </div>
    `

    playlistModal.querySelector(".modal-container").innerHTML = modalContent
    playlistModal.classList.add("active")

    // Eventos para los elementos del modal
    const closeBtn = playlistModal.querySelector("#close-create-playlist-modal")
    const cancelBtn = playlistModal.querySelector("#cancel-create-playlist-modal")
    const saveBtn = playlistModal.querySelector("#save-playlist")
    const form = playlistModal.querySelector("#create-playlist-form")

    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        playlistModal.classList.remove("active")
      })
    }

    if (cancelBtn) {
      cancelBtn.addEventListener("click", () => {
        playlistModal.classList.remove("active")
      })
    }

    if (saveBtn && form) {
      saveBtn.addEventListener("click", () => {
        const playlistName = document.getElementById("playlist-name").value.trim()
        const playlistDescription = document.getElementById("playlist-description").value.trim()
        const isPublic = document.getElementById("playlist-public").checked ? 1 : 0

        if (playlistName.length < 3) {
          showToast("El nombre de la playlist debe tener al menos 3 caracteres", "error")
          return
        }

        // Crear playlist
        fetch("api/playlist.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `action=create_playlist&nombre_playlist=${encodeURIComponent(playlistName)}&descripcion=${encodeURIComponent(playlistDescription)}&es_publica=${isPublic}`,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              showToast("Playlist creada correctamente", "success")
              playlistModal.classList.remove("active")

              // Redirigir a la página de playlist
              window.location.href = `index.php?page=playlist&id=${data.playlist_id}`
            } else {
              showToast(data.message || "Error al crear la playlist", "error")
            }
          })
          .catch((error) => {
            console.error("Error:", error)
            showToast("Error al comunicarse con el servidor", "error")
          })
      })
    }
  }
}

// Inicializar el menú desplegable de usuario
function initUserDropdown() {
  const userProfileContainer = document.querySelector(".user-profile-container")
  const dropdown = document.getElementById("user-dropdown")

  if (userProfileContainer && dropdown) {
    userProfileContainer.addEventListener("click", (e) => {
      e.preventDefault()
      e.stopPropagation()
      dropdown.classList.toggle("show")
    })
  }

  // Cerrar el dropdown al hacer clic fuera
  document.addEventListener("click", (event) => {
    if (dropdown && dropdown.classList.contains("show") && !event.target.closest(".user-profile-container")) {
      dropdown.classList.remove("show")
    }
  })
}

// Inicializar el cambio de tema
function initThemeToggle() {
  const themeToggle = document.getElementById("theme-toggle")

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      const currentTheme = localStorage.getItem("theme") || "dark"
      const newTheme = currentTheme === "dark" ? "light" : "dark"

      localStorage.setItem("theme", newTheme)
      applyThemeAndColor()
    })
  }
}

// Inicializar los eventos de las tarjetas de música
function initMusicCards() {
  const cards = document.querySelectorAll(".card[data-id]")

  cards.forEach((card) => {
    card.addEventListener("click", function (e) {
      // Solo reproducir si no se hizo clic en un botón de acción
      if (!e.target.closest(".song-action-btn")) {
        const songId = this.dataset.id
        const songType = this.dataset.type || "album_cancion" // Valor por defecto

        try {
          playSong(songId, songType)
        } catch (error) {
          console.error("Error al reproducir:", error)
          showToast("Error al reproducir la canción", "error")
        }
      }
    })

    card.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        const songId = this.dataset.id
        const songType = this.dataset.type || "album_cancion" // Valor por defecto

        try {
          playSong(songId, songType)
        } catch (error) {
          console.error("Error al reproducir:", error)
          showToast("Error al reproducir la canción", "error")
        }
      }
    })

    // Añadir botones de acción a las tarjetas si no existen
    if (!card.querySelector(".card-actions")) {
      try {
        const songId = card.dataset.id
        const songType = card.dataset.type || "album_cancion"
        const actionsDiv = document.createElement("div")
        actionsDiv.className = "card-actions"
        actionsDiv.innerHTML = `
          <button class="song-action-btn add-to-queue-btn" data-id="${songId}" data-type="${songType}" title="Añadir a la cola">
            <i class="fas fa-list"></i>
          </button>
          <button class="song-action-btn add-to-playlist-btn" data-id="${songId}" data-type="${songType}" title="Añadir a playlist">
            <i class="fas fa-plus"></i>
          </button>
          <button class="song-action-btn add-to-cart-btn" data-id="${songId}" data-type="${songType}" title="Añadir al carrito">
            <i class="fas fa-shopping-cart"></i>
          </button>
        `
        card.appendChild(actionsDiv)

        // Añadir eventos a los botones
        const addToQueueBtn = actionsDiv.querySelector(".add-to-queue-btn")
        const addToPlaylistBtn = actionsDiv.querySelector(".add-to-playlist-btn")
        const addToCartBtn = actionsDiv.querySelector(".add-to-cart-btn")

        if (addToQueueBtn) {
          addToQueueBtn.addEventListener("click", function (e) {
            e.stopPropagation() // Evitar que se reproduzca la canción
            const songId = this.dataset.id
            const songType = this.dataset.type
            addToQueue(songId, songType)
          })
        }

        if (addToPlaylistBtn) {
          addToPlaylistBtn.addEventListener("click", function (e) {
            e.stopPropagation() // Evitar que se reproduzca la canción
            const songId = this.dataset.id
            const songType = this.dataset.type
            showAddToPlaylistModal(songId, songType)
          })
        }

        if (addToCartBtn) {
          addToCartBtn.addEventListener("click", function (e) {
            e.stopPropagation() // Evitar que se reproduzca la canción
            const songId = this.dataset.id
            const songType = this.dataset.type
            addToCart(songId, songType)
          })
        }
      } catch (error) {
        console.error("Error al añadir botones de acción:", error)
      }
    }
  })
}

// Inicializar modales
function initModals() {
  const modalOverlays = document.querySelectorAll(".modal-overlay")
  const modalCloseButtons = document.querySelectorAll('.modal-close, .btn-secondary[id*="cancel"]')

  modalCloseButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const modalId = this.closest(".modal-overlay").id
      document.getElementById(modalId).classList.remove("active")
    })
  })
}

// Función para ajustar color
function adjustColor(color, amount) {
  return (
    "#" +
    color
      .replace(/^#/, "")
      .replace(/../g, (color) =>
        ("0" + Math.min(255, Math.max(0, Number.parseInt(color, 16) + amount)).toString(16)).substr(-2),
      )
  )
}

// Mostrar un mensaje toast
function showToast(message, type = "info") {
  // Crear el elemento toast
  const toast = document.createElement("div")
  toast.style.position = "fixed"
  toast.style.top = "20px"
  toast.style.left = "50%"
  toast.style.transform = "translateX(-50%) translateY(-100px)"
  toast.style.padding = "10px 20px"
  toast.style.borderRadius = "5px"
  toast.style.boxShadow = "0 3px 10px rgba(0,0,0,0.3)"
  toast.style.zIndex = "1000"
  toast.style.transition = "all 0.3s ease"

  // Establecer colores según el tipo
  if (type === "error") {
    toast.style.background = "#e74c3c"
    toast.style.color = "#fff"
    toast.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`
  } else if (type === "success") {
    toast.style.background = "var(--primary-color)"
    toast.style.color = "var(--bg-color)"
    toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`
  } else {
    toast.style.background = "#3498db"
    toast.style.color = "#fff"
    toast.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`
  }

  document.body.appendChild(toast)

  // Animar entrada
  setTimeout(() => {
    toast.style.transform = "translateX(-50%) translateY(0)"
  }, 10)

  // Eliminar después de 3 segundos
  setTimeout(() => {
    toast.style.transform = "translateX(-50%) translateY(-100px)"
    setTimeout(() => {
      document.body.removeChild(toast)
    }, 300)
  }, 3000)
}
