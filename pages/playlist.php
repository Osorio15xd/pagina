<?php
// Obtener las playlists del usuario si está logueado
$playlists = [];
$playlist_actual = null;
$canciones_playlist = [];

if (isset($_SESSION['user_id'])) {
    // Obtener todas las playlists del usuario
    $stmt = $conn->prepare("SELECT id, nombre, fecha_creacion FROM playlists WHERE usuario_id = ? ORDER BY fecha_creacion DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener la playlist seleccionada o la primera por defecto
    $playlist_id = isset($_GET['id']) ? intval($_GET['id']) : (count($playlists) > 0 ? $playlists[0]['id'] : 0);
    
    if ($playlist_id > 0) {
        // Obtener información de la playlist
        $stmt = $conn->prepare("SELECT id, nombre FROM playlists WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$playlist_id, $_SESSION['user_id']]);
        $playlist_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($playlist_actual) {
            // Obtener canciones de la playlist
            $stmt = $conn->prepare("SELECT c.id, c.titulo, c.portada, c.archivo_audio, a.nombre as artista, pc.orden
                                   FROM playlist_canciones pc
                                   JOIN canciones c ON pc.cancion_id = c.id
                                   JOIN artistas a ON c.artista_id = a.id
                                   WHERE pc.playlist_id = ?
                                   ORDER BY pc.orden");
            $stmt->execute([$playlist_id]);
            $canciones_playlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<section id="page-playlist" class="page active" aria-label="Playlist">
  <h1>Playlist</h1>
  
  <?php if (isset($_SESSION['user_id'])): ?>
    <?php if (count($playlists) > 0): ?>
      <div class="playlist-header">
        <div class="playlist-selector">
          <label for="playlist-select">Seleccionar playlist:</label>
          <select id="playlist-select" onchange="window.location.href='index.php?page=playlist&id='+this.value">
            <?php foreach ($playlists as $playlist): ?>
              <option value="<?php echo $playlist['id']; ?>" <?php echo ($playlist_actual && $playlist['id'] == $playlist_actual['id']) ? 'selected' : ''; ?>>
                <?php echo $playlist['nombre']; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="playlist-actions">
          <button id="create-playlist-btn"><i class="fas fa-plus"></i> Nueva Playlist</button>
          <?php if ($playlist_actual): ?>
            <button id="rename-playlist-btn" data-id="<?php echo $playlist_actual['id']; ?>"><i class="fas fa-edit"></i> Renombrar</button>
            <button id="delete-playlist-btn" data-id="<?php echo $playlist_actual['id']; ?>"><i class="fas fa-trash"></i> Eliminar</button>
          <?php endif; ?>
        </div>
      </div>
      
      <?php if ($playlist_actual): ?>
        <?php if (count($canciones_playlist) > 0): ?>
          <ul id="playlist-list" aria-live="polite" aria-relevant="all">
            <?php foreach ($canciones_playlist as $index => $cancion): ?>
              <li data-id="<?php echo $cancion['id']; ?>" tabindex="0">
                <div class="song-info">
                  <img src="<?php echo $cancion['portada']; ?>" alt="Portada" class="song-thumbnail">
                  <div>
                    <span class="song-title"><?php echo $cancion['titulo']; ?></span>
                    <span class="song-artist"><?php echo $cancion['artista']; ?></span>
                  </div>
                </div>
                <div class="song-actions">
                  <button class="move-up-btn" title="Mover arriba" <?php echo $index === 0 ? 'disabled' : ''; ?>>
                    <i class="fas fa-arrow-up"></i>
                  </button>
                  <button class="move-down-btn" title="Mover abajo" <?php echo $index === count($canciones_playlist) - 1 ? 'disabled' : ''; ?>>
                    <i class="fas fa-arrow-down"></i>
                  </button>
                  <button class="remove-from-playlist-btn" title="Eliminar de la playlist">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
          
          <div id="visualizer-container">
            <div id="music-visualizer">
              <!-- Visualizer bars will be added dynamically -->
            </div>
          </div>
          <div id="progress-container">
            <div id="progress-bar"></div>
            <div id="progress-handle"></div>
          </div>
          <div id="time-display">
            <span id="current-time">0:00</span>
            <span id="total-time">0:00</span>
          </div>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-list-music" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
            <h3>Esta playlist está vacía</h3>
            <p>Agrega canciones desde la página de inicio o tu biblioteca.</p>
            <a href="index.php?page=inicio" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Explorar música</a>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-list-music" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
        <h3>No tienes playlists</h3>
        <p>Crea tu primera playlist para organizar tu música favorita.</p>
        <button id="create-first-playlist-btn" class="btn-primary" style="margin-top: 1rem;">Crear Playlist</button>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <div class="login-prompt">
      <i class="fas fa-user-lock" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
      <h3>Inicia sesión para crear playlists</h3>
      <p>Necesitas una cuenta para crear y gestionar tus playlists personalizadas.</p>
      <a href="index.php?page=login" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Iniciar Sesión</a>
    </div>
  <?php endif; ?>
</section>

<!-- Modal para crear/renombrar playlist -->
<div class="modal-overlay" id="playlist-modal">
  <div class="modal-container">
    <div class="modal-header">
      <h3 class="modal-title" id="playlist-modal-title">Nueva Playlist</h3>
      <button class="modal-close" id="close-playlist-modal"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <form id="playlist-form">
        <input type="hidden" id="playlist-id" value="0">
        <div class="form-group">
          <label for="playlist-name">Nombre de la playlist</label>
          <input type="text" id="playlist-name" required minlength="3" maxlength="100">
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" id="cancel-playlist-modal">Cancelar</button>
      <button class="btn-primary" id="save-playlist">Guardar</button>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar visualizador
  initializeVisualizer();
  
  // Eventos para crear/renombrar/eliminar playlist
  const createPlaylistBtn = document.getElementById('create-playlist-btn');
  const createFirstPlaylistBtn = document.getElementById('create-first-playlist-btn');
  const renamePlaylistBtn = document.getElementById('rename-playlist-btn');
  const deletePlaylistBtn = document.getElementById('delete-playlist-btn');
  const playlistModal = document.getElementById('playlist-modal');
  const closePlaylistModal = document.getElementById('close-playlist-modal');
  const cancelPlaylistModal = document.getElementById('cancel-playlist-modal');
  const savePlaylistBtn = document.getElementById('save-playlist');
  const playlistForm = document.getElementById('playlist-form');
  const playlistIdInput = document.getElementById('playlist-id');
  const playlistNameInput = document.getElementById('playlist-name');
  const playlistModalTitle = document.getElementById('playlist-modal-title');
  
  // Función para mostrar el modal de playlist
  function showPlaylistModal(id = 0, name = '') {
    playlistIdInput.value = id;
    playlistNameInput.value = name;
    playlistModalTitle.textContent = id > 0 ? 'Renombrar Playlist' : 'Nueva Playlist';
    playlistModal.classList.add('active');
    playlistNameInput.focus();
  }
  
  // Evento para crear nueva playlist
  if (createPlaylistBtn) {
    createPlaylistBtn.addEventListener('click', function() {
      showPlaylistModal();
    });
  }
  
  // Evento para crear primera playlist
  if (createFirstPlaylistBtn) {
    createFirstPlaylistBtn.addEventListener('click', function() {
      showPlaylistModal();
    });
  }
  
  // Evento para renombrar playlist
  if (renamePlaylistBtn) {
    renamePlaylistBtn.addEventListener('click', function() {
      const playlistId = this.dataset.id;
      const playlistName = document.getElementById('playlist-select').options[document.getElementById('playlist-select').selectedIndex].text;
      showPlaylistModal(playlistId, playlistName);
    });
  }
  
  // Evento para eliminar playlist
  if (deletePlaylistBtn) {
    deletePlaylistBtn.addEventListener('click', function() {
      const playlistId = this.dataset.id;
      if (confirm('¿Estás seguro de que deseas eliminar esta playlist?')) {
        // Enviar solicitud para eliminar playlist
        fetch('api/playlist.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=delete_playlist&playlist_id=${playlistId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.href = 'index.php?page=playlist';
          } else {
            showToast(data.message || 'Error al eliminar la playlist', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Error al comunicarse con el servidor', 'error');
        });
      }
    });
  }
  
  // Cerrar modal
  if (closePlaylistModal) {
    closePlaylistModal.addEventListener('click', function() {
      playlistModal.classList.remove('active');
    });
  }
  
  if (cancelPlaylistModal) {
    cancelPlaylistModal.addEventListener('click', function() {
      playlistModal.classList.remove('active');
    });
  }
  
  // Guardar playlist
  if (savePlaylistBtn && playlistForm) {
    playlistForm.addEventListener('submit', function(e) {
      e.preventDefault();
      savePlaylist();
    });
    
    savePlaylistBtn.addEventListener('click', function() {
      savePlaylist();
    });
  }
  
  function savePlaylist() {
    const playlistId = playlistIdInput.value;
    const playlistName = playlistNameInput.value.trim();
    
    if (playlistName.length < 3) {
      showToast('El nombre de la playlist debe tener al menos 3 caracteres', 'error');
      return;
    }
    
    // Determinar la acción (crear o renombrar)
    const action = playlistId > 0 ? 'rename_playlist' : 'create_playlist';
    
    // Enviar solicitud
    fetch('api/playlist.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=${action}&playlist_id=${playlistId}&name=${encodeURIComponent(playlistName)}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        playlistModal.classList.remove('active');
        if (action === 'create_playlist') {
          window.location.href = `index.php?page=playlist&id=${data.playlist_id}`;
        } else {
          // Actualizar el nombre en el selector
          const select = document.getElementById('playlist-select');
          if (select && select.selectedIndex >= 0) {
            select.options[select.selectedIndex].text = playlistName;
          }
          showToast('Playlist renombrada correctamente', 'success');
        }
      } else {
        showToast(data.message || 'Error al guardar la playlist', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('Error al comunicarse con el servidor', 'error');
    });
  }
  
  // Eventos para las canciones de la playlist
  const playlistItems = document.querySelectorAll('#playlist-list li');
  
  playlistItems.forEach(item => {
    // Reproducir canción al hacer clic
    item.addEventListener('click', function(e) {
      if (!e.target.closest('button')) {
        const songId = this.dataset.id;
        playSong(songId);
      }
    });
    
    // Mover canción hacia arriba
    const moveUpBtn = item.querySelector('.move-up-btn');
    if (moveUpBtn) {
      moveUpBtn.addEventListener('click', function() {
        const li = this.closest('li');
        const prevLi = li.previousElementSibling;
        if (prevLi) {
          // Intercambiar visualmente
          li.parentNode.insertBefore(li, prevLi);
          
          // Actualizar orden en la base de datos
          updatePlaylistOrder();
        }
      });
    }
    
    // Mover canción hacia abajo
    const moveDownBtn = item.querySelector('.move-down-btn');
    if (moveDownBtn) {
      moveDownBtn.addEventListener('click', function() {
        const li = this.closest('li');
        const nextLi = li.nextElementSibling;
        if (nextLi) {
          // Intercambiar visualmente
          li.parentNode.insertBefore(nextLi, li);
          
          // Actualizar orden en la base de datos
          updatePlaylistOrder();
        }
      });
    }
    
    // Eliminar canción de la playlist
    const removeBtn = item.querySelector('.remove-from-playlist-btn');
    if (removeBtn) {
      removeBtn.addEventListener('click', function() {
        const li = this.closest('li');
        const songId = li.dataset.id;
        const playlistId = document.getElementById('playlist-select').value;
        
        // Enviar solicitud para eliminar de la playlist
        fetch('api/playlist.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=remove_song&playlist_id=${playlistId}&song_id=${songId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Eliminar visualmente
            li.remove();
            showToast('Canción eliminada de la playlist', 'success');
            
            // Actualizar botones de mover
            updateMoveButtons();
            
            // Si no quedan canciones, mostrar estado vacío
            if (document.querySelectorAll('#playlist-list li').length === 0) {
              document.getElementById('playlist-list').innerHTML = `
                <div class="empty-state">
                  <i class="fas fa-list-music" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 1rem;"></i>
                  <h3>Esta playlist está vacía</h3>
                  <p>Agrega canciones desde la página de inicio o tu biblioteca.</p>
                  <a href="index.php?page=inicio" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Explorar música</a>
                </div>
              `;
            }
          } else {
            showToast(data.message || 'Error al eliminar la canción', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('Error al comunicarse con el servidor', 'error');
        });
      });
    }
  });
  
  // Función para actualizar el orden de las canciones en la playlist
  function updatePlaylistOrder() {
    const playlistId = document.getElementById('playlist-select').value;
    const items = document.querySelectorAll('#playlist-list li');
    const songIds = Array.from(items).map(item => item.dataset.id);
    
    // Enviar solicitud para actualizar orden
    fetch('api/playlist.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=update_order&playlist_id=${playlistId}&song_ids=${songIds.join(',')}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        updateMoveButtons();
        showToast('Orden actualizado', 'success');
      } else {
        showToast(data.message || 'Error al actualizar el orden', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('Error al comunicarse con el servidor', 'error');
    });
  }
  
  // Función para actualizar los botones de mover
  function updateMoveButtons() {
    const items = document.querySelectorAll('#playlist-list li');
    
    items.forEach((item, index) => {
      const moveUpBtn = item.querySelector('.move-up-btn');
      const moveDownBtn = item.querySelector('.move-down-btn');
      
      if (moveUpBtn) {
        moveUpBtn.disabled = index === 0;
      }
      
      if (moveDownBtn) {
        moveDownBtn.disabled = index === items.length - 1;
      }
    });
  }
  
  // Inicializar visualizador
  function initializeVisualizer() {
    const visualizer = document.getElementById('music-visualizer');
    if (!visualizer) return;
    
    visualizer.innerHTML = '';
    
    // Crear barras del visualizador
    const barCount = 30;
    for (let i = 0; i < barCount; i++) {
      const bar = document.createElement('div');
      bar.classList.add('visualizer-bar');
      bar.style.left = `${(i / barCount) * 100}%`;
      bar.style.width = `${90 / barCount}%`;
      visualizer.appendChild(bar);
    }
  }
});
</script>
