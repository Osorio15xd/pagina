<?php
// Verificar si el usuario está logueado
$is_logged_in = isset($_SESSION['user_id']);

// Obtener canciones recientes para mostrar en el reproductor
$recent_songs = [];
if ($is_logged_in) {
    $stmt = $conn->prepare("
        SELECT c.id, c.titulo, c.portada, c.archivo_audio, a.nombre as artista
        FROM estadisticas_reproduccion er
        JOIN canciones c ON er.cancion_id = c.id
        JOIN artistas a ON c.artista_id = a.id
        WHERE er.usuario_id = ?
        ORDER BY er.fecha_reproduccion DESC
        LIMIT 10
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recent_songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener canciones populares
$popular_songs = [];
$stmt = $conn->query("
    SELECT c.id, c.titulo, c.portada, c.archivo_audio, a.nombre as artista, COUNT(er.id) as reproducciones
    FROM canciones c
    JOIN artistas a ON c.artista_id = a.id
    LEFT JOIN estadisticas_reproduccion er ON c.id = er.cancion_id
    GROUP BY c.id
    ORDER BY reproducciones DESC
    LIMIT 10
");
$popular_songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="page-enhanced-player" class="page active" aria-label="Reproductor Mejorado">
  <h1>Reproductor Avanzado</h1>
  <p>Disfruta de una experiencia de reproducción mejorada con más controles y funcionalidades</p>
  
  <div class="enhanced-player-container">
    <div class="player-main">
      <div class="player-visualization">
        <div class="album-cover-container">
          <img id="enhanced-cover" src="assets/img/placeholder.svg" alt="Portada del álbum" />
          <div class="album-reflection"></div>
        </div>
        
        <div class="visualization-container">
          <canvas id="audio-visualization" width="500" height="100"></canvas>
        </div>
      </div>
      
      <div class="player-controls">
        <div class="track-info">
          <h3 id="enhanced-track-title">Selecciona una canción</h3>
          <p id="enhanced-track-artist">-</p>
        </div>
        
        <div class="progress-container">
          <span id="current-time">0:00</span>
          <div class="progress-bar">
            <div class="progress-background"></div>
            <div id="progress-fill"></div>
            <div id="progress-handle"></div>
          </div>
          <span id="total-time">0:00</span>
        </div>
        
        <div class="control-buttons">
          <button id="shuffle-btn" class="control-btn" title="Aleatorio">
            <i class="fas fa-random"></i>
          </button>
          <button id="prev-track-btn" class="control-btn" title="Anterior">
            <i class="fas fa-step-backward"></i>
          </button>
          <button id="enhanced-play-pause-btn" class="control-btn play-btn" title="Reproducir">
            <i class="fas fa-play"></i>
          </button>
          <button id="next-track-btn" class="control-btn" title="Siguiente">
            <i class="fas fa-step-forward"></i>
          </button>
          <button id="repeat-btn" class="control-btn" title="Repetir">
            <i class="fas fa-redo"></i>
          </button>
        </div>
        
        <div class="extra-controls">
          <div class="volume-control">
            <button id="mute-btn" class="control-btn" title="Silenciar">
              <i class="fas fa-volume-up"></i>
            </button>
            <div class="volume-slider">
              <div class="volume-background"></div>
              <div id="volume-fill"></div>
              <div id="volume-handle"></div>
            </div>
          </div>
          
          <div class="right-controls">
            <button id="queue-btn" class="control-btn" title="Cola de reproducción">
              <i class="fas fa-list"></i>
            </button>
            <button id="eq-btn" class="control-btn" title="Ecualizador">
              <i class="fas fa-sliders-h"></i>
            </button>
            <button id="fullscreen-btn" class="control-btn" title="Pantalla completa">
              <i class="fas fa-expand"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div class="player-sidebar">
      <div class="sidebar-section">
        <h3>Cola de reproducción</h3>
        <div id="queue-list" class="track-list">
          <!-- La cola se llenará dinámicamente -->
          <div class="empty-queue-message">La cola está vacía. Añade canciones para reproducir.</div>
        </div>
      </div>
      
      <div class="sidebar-section">
        <h3>Escuchado recientemente</h3>
        <div class="track-list">
          <?php if (count($recent_songs) > 0): ?>
            <?php foreach ($recent_songs as $song): ?>
              <div class="track-item" data-id="<?php echo $song['id']; ?>" data-audio="<?php echo $song['archivo_audio']; ?>" data-title="<?php echo $song['titulo']; ?>" data-artist="<?php echo $song['artista']; ?>" data-cover="<?php echo $song['portada']; ?>">
                <img src="<?php echo $song['portada']; ?>" alt="<?php echo $song['titulo']; ?>" class="track-thumbnail">
                <div class="track-details">
                  <div class="track-title"><?php echo $song['titulo']; ?></div>
                  <div class="track-artist"><?php echo $song['artista']; ?></div>
                </div>
                <button class="track-play-btn"><i class="fas fa-play"></i></button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <p>Aún no has escuchado ninguna canción.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="sidebar-section">
        <h3>Populares</h3>
        <div class="track-list">
          <?php if (count($popular_songs) > 0): ?>
            <?php foreach ($popular_songs as $song): ?>
              <div class="track-item" data-id="<?php echo $song['id']; ?>" data-audio="<?php echo $song['archivo_audio']; ?>" data-title="<?php echo $song['titulo']; ?>" data-artist="<?php echo $song['artista']; ?>" data-cover="<?php echo $song['portada']; ?>">
                <img src="<?php echo $song['portada']; ?>" alt="<?php echo $song['titulo']; ?>" class="track-thumbnail">
                <div class="track-details">
                  <div class="track-title"><?php echo $song['titulo']; ?></div>
                  <div class="track-artist"><?php echo $song['artista']; ?></div>
                </div>
                <button class="track-play-btn"><i class="fas fa-play"></i></button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <p>No hay canciones populares disponibles.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Ecualizador Modal -->
  <div id="eq-modal" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <h3 class="modal-title">Ecualizador</h3>
        <button class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="eq-presets">
          <button class="eq-preset-btn active" data-preset="flat">Plano</button>
          <button class="eq-preset-btn" data-preset="rock">Rock</button>
          <button class="eq-preset-btn" data-preset="pop">Pop</button>
          <button class="eq-preset-btn" data-preset="jazz">Jazz</button>
          <button class="eq-preset-btn" data-preset="classical">Clásica</button>
          <button class="eq-preset-btn" data-preset="bass">Bajo</button>
        </div>
        
        <div class="eq-sliders">
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">60Hz</span>
          </div>
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">150Hz</span>
          </div>
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">400Hz</span>
          </div>
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">1kHz</span>
          </div>
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">2.4kHz</span>
          </div>
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">6kHz</span>
          </div>
          <div class="eq-slider">
            <div class="eq-slider-container">
              <div class="eq-slider-fill"></div>
              <div class="eq-slider-handle"></div>
            </div>
            <span class="eq-freq">15kHz</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary" id="reset-eq">Restablecer</button>
        <button class="btn-primary" id="save-eq">Guardar</button>
      </div>
    </div>
  </div>
</section>

<style>
.enhanced-player-container {
  display: flex;
  gap: 20px;
  margin-top: 20px;
}

.player-main {
  flex: 1;
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  border: 1px solid var(--border-color);
}

.player-sidebar {
  width: 300px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.sidebar-section {
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  border: 1px solid var(--border-color);
}

.sidebar-section h3 {
  margin-top: 0;
  margin-bottom: 15px;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 10px;
}

.player-visualization {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}

.album-cover-container {
  position: relative;
  width: 250px;
  height: 250px;
  margin-bottom: 20px;
}

#enhanced-cover {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.album-reflection {
  position: absolute;
  bottom: -20px;
  left: 10%;
  width: 80%;
  height: 20px;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), transparent);
  filter: blur(5px);
  border-radius: 50%;
}

.visualization-container {
  width: 100%;
  height: 100px;
  margin-top: 20px;
}

#audio-visualization {
  width: 100%;
  height: 100%;
  border-radius: 8px;
  background: var(--bg-card);
}

.player-controls {
  padding: 20px;
  background: var(--bg-card);
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.track-info {
  text-align: center;
  margin-bottom: 20px;
}

.track-info h3 {
  margin: 0;
  font-size: 1.5rem;
  margin-bottom: 5px;
}

.track-info p {
  margin: 0;
  color: var(--text-secondary);
}

.progress-container {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
}

.progress-bar {
  flex: 1;
  height: 6px;
  background: var(--bg-secondary);
  border-radius: 3px;
  position: relative;
  cursor: pointer;
}

.progress-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: var(--bg-secondary);
  border-radius: 3px;
}

#progress-fill {
  position: absolute;
  top: 0;
  left: 0;
  width: 0%;
  height: 100%;
  background: var(--primary-color);
  border-radius: 3px;
  transition: width 0.1s linear;
}

#progress-handle {
  position: absolute;
  top: 50%;
  left: 0%;
  width: 12px;
  height: 12px;
  background: var(--primary-color);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
  cursor: grab;
}

#current-time, #total-time {
  font-size: 0.8rem;
  color: var(--text-secondary);
  width: 40px;
  text-align: center;
}

.control-buttons {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-bottom: 20px;
}

.control-btn {
  background: transparent;
  border: none;
  color: var(--text-color);
  cursor: pointer;
  font-size: 1.2rem;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.control-btn:hover {
  color: var(--primary-color);
  background: rgba(255, 255, 255, 0.1);
}

.control-btn.active {
  color: var(--primary-color);
}

.play-btn {
  width: 60px;
  height: 60px;
  background: var(--primary-color);
  color: var(--bg-color);
  font-size: 1.5rem;
}

.play-btn:hover {
  background: var(--primary-hover);
  color: var(--bg-color);
  transform: scale(1.1);
}

.extra-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.volume-control {
  display: flex;
  align-items: center;
  gap: 10px;
}

.volume-slider {
  width: 100px;
  height: 6px;
  background: var(--bg-secondary);
  border-radius: 3px;
  position: relative;
  cursor: pointer;
}

.volume-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: var(--bg-secondary);
  border-radius: 3px;
}

#volume-fill {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: var(--primary-color);
  border-radius: 3px;
}

#volume-handle {
  position: absolute;
  top: 50%;
  right: 0;
  width: 12px;
  height: 12px;
  background: var(--primary-color);
  border-radius: 50%;
  transform: translate(50%, -50%);
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
  cursor: grab;
}

.right-controls {
  display: flex;
  gap: 10px;
}

.track-list {
  max-height: 300px;
  overflow-y: auto;
  padding-right: 5px;
}

.track-item {
  display: flex;
  align-items: center;
  padding: 10px;
  background: var(--bg-card);
  border-radius: 8px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.track-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.track-thumbnail {
  width: 40px;
  height: 40px;
  border-radius: 5px;
  object-fit: cover;
  margin-right: 10px;
}

.track-details {
  flex: 1;
}

.track-title {
  font-weight: 600;
  margin-bottom: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.track-artist {
  color: var(--text-secondary);
  font-size: 0.8rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.track-play-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  background: var(--primary-color);
  color: var(--bg-color);
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.track-play-btn:hover {
  transform: scale(1.1);
}

.empty-state {
  text-align: center;
  padding: 20px;
  color: var(--text-secondary);
}

.empty-queue-message {
  text-align: center;
  padding: 20px;
  color: var(--text-secondary);
  font-style: italic;
}

/* Ecualizador Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.modal-overlay.active {
  opacity: 1;
  visibility: visible;
}

.modal-container {
  background: var(--bg-color);
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid var(--border-color);
}

.modal-title {
  margin: 0;
  font-size: 1.2rem;
}

.modal-close {
  background: transparent;
  border: none;
  color: var(--text-secondary);
  font-size: 1.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.modal-close:hover {
  color: var(--primary-color);
}

.modal-body {
  padding: 20px;
}

.modal-footer {
  padding: 15px 20px;
  border-top: 1px solid var(--border-color);
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.eq-presets {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.eq-preset-btn {
  padding: 8px 15px;
  background: var(--bg-card);
  color: var(--text-color);
  border: 1px solid var(--border-color);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.eq-preset-btn:hover {
  background: var(--bg-secondary);
}

.eq-preset-btn.active {
  background: var(--primary-color);
  color: var(--bg-color);
  border-color: var(--primary-color);
}

.eq-sliders {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  height: 200px;
}

.eq-slider {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 40px;
}

.eq-slider-container {
  width: 10px;
  height: 150px;
  background: var(--bg-secondary);
  border-radius: 5px;
  position: relative;
  margin-bottom: 10px;
}

.eq-slider-fill {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 50%;
  background: var(--primary-color);
  border-radius: 5px;
}

.eq-slider-handle {
  position: absolute;
  left: 50%;
  top: 50%;
  width: 20px;
  height: 20px;
  background: var(--primary-color);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  cursor: grab;
}

.eq-freq {
  font-size: 0.8rem;
  color: var(--text-secondary);
}

@media (max-width: 768px) {
  .enhanced-player-container {
    flex-direction: column;
  }
  
  .player-sidebar {
    width: 100%;
  }
  
  .album-cover-container {
    width: 200px;
    height: 200px;
  }
  
  .control-buttons {
    gap: 10px;
  }
  
  .control-btn {
    width: 35px;
    height: 35px;
    font-size: 1rem;
  }
  
  .play-btn {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
  
  .volume-slider {
    width: 60px;
  }
  
  .eq-sliders {
    height: 150px;
  }
  
  .eq-slider-container {
    height: 100px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Variables globales
  const enhancedAudio = new Audio();
  let currentTrackId = null;
  let isPlaying = false;
  let isMuted = false;
  let volume = 1;
  let isShuffleActive = false;
  let repeatMode = 'none'; // none, one, all
  let playQueue = [];
  let currentQueueIndex = -1;
  let audioContext = null;
  let analyser = null;
  let dataArray = null;
  let canvasContext = null;
  let isVisualizationActive = false;
  let animationFrameId = null;
  let eqSettings = {
    flat: [0, 0, 0, 0, 0, 0, 0],
    rock: [4, 3, -2, -3, 2, 5, 6],
    pop: [-1, 2, 5, 4, 2, 0, -2],
    jazz: [4, 2, -2, -2, 0, 4, 5],
    classical: [5, 4, 3, 0, -2, 0, 3],
    bass: [6, 5, 3, 1, 0, -2, -3]
  };
  let currentEqPreset = 'flat';
  
  // Elementos DOM
  const enhancedCover = document.getElementById('enhanced-cover');
  const trackTitle = document.getElementById('enhanced-track-title');
  const trackArtist = document.getElementById('enhanced-track-artist');
  const playPauseBtn = document.getElementById('enhanced-play-pause-btn');
  const prevBtn = document.getElementById('prev-track-btn');
  const nextBtn = document.getElementById('next-track-btn');
  const shuffleBtn = document.getElementById('shuffle-btn');
  const repeatBtn = document.getElementById('repeat-btn');
  const muteBtn = document.getElementById('mute-btn');
  const queueBtn = document.getElementById('queue-btn');
  const eqBtn = document.getElementById('eq-btn');
  const fullscreenBtn = document.getElementById('fullscreen-btn');
  const progressFill = document.getElementById('progress-fill');
  const progressHandle = document.getElementById('progress-handle');
  const progressBar = document.querySelector('.progress-bar');
  const currentTimeDisplay = document.getElementById('current-time');
  const totalTimeDisplay = document.getElementById('total-time');
  const volumeFill = document.getElementById('volume-fill');
  const volumeHandle = document.getElementById('volume-handle');
  const volumeSlider = document.querySelector('.volume-slider');
  const queueList = document.getElementById('queue-list');
  const trackItems = document.querySelectorAll('.track-item');
  const canvas = document.getElementById('audio-visualization');
  const eqModal = document.getElementById('eq-modal');
  const eqPresetBtns = document.querySelectorAll('.eq-preset-btn');
  const eqSliders = document.querySelectorAll('.eq-slider-handle');
  const resetEqBtn = document.getElementById('reset-eq');
  const saveEqBtn = document.getElementById('save-eq');
  
  // Inicializar reproductor
  initPlayer();
  
  // Función para inicializar el reproductor
  function initPlayer() {
    // Configurar eventos de audio
    enhancedAudio.addEventListener('timeupdate', updateProgress);
    enhancedAudio.addEventListener('ended', handleTrackEnd);
    enhancedAudio.addEventListener('loadedmetadata', updateTotalTime);
    enhancedAudio.addEventListener('play', () => {
      isPlaying = true;
      updatePlayPauseButton();
      startVisualization();
    });
    enhancedAudio.addEventListener('pause', () => {
      isPlaying = false;
      updatePlayPauseButton();
      stopVisualization();
    });
    
    // Configurar eventos de botones
    playPauseBtn.addEventListener('click', togglePlay);
    prevBtn.addEventListener('click', playPrevious);
    nextBtn.addEventListener('click', playNext);
    shuffleBtn.addEventListener('click', toggleShuffle);
    repeatBtn.addEventListener('click', toggleRepeat);
    muteBtn.addEventListener('click', toggleMute);
    queueBtn.addEventListener('click', toggleQueueView);
    eqBtn.addEventListener('click', toggleEqModal);
    fullscreenBtn.addEventListener('click', toggleFullscreen);
    
    // Configurar eventos de progreso
    progressBar.addEventListener('click', seekToPosition);
    progressHandle.addEventListener('mousedown', startDraggingProgress);
    
    // Configurar eventos de volumen
    volumeSlider.addEventListener('click', changeVolume);
    volumeHandle.addEventListener('mousedown', startDraggingVolume);
    
    // Configurar eventos de pistas
    trackItems.forEach(item => {
      item.addEventListener('click', function() {
        const trackId = this.dataset.id;
        const audioSrc = this.dataset.audio;
        const title = this.dataset.title;
        const artist = this.dataset.artist;
        const cover = this.dataset.cover;
        
        playTrack(trackId, audioSrc, title, artist, cover);
      });
    });
    
    // Configurar eventos de ecualizador
    eqPresetBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const preset = this.dataset.preset;
        applyEqPreset(preset);
        
        // Actualizar botones activos
        eqPresetBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
      });
    });
    
    eqSliders.forEach((slider, index) => {
      slider.addEventListener('mousedown', function(e) {
        startDraggingEqSlider(e, index);
      });
    });
    
    resetEqBtn.addEventListener('click', resetEqualizer);
    saveEqBtn.addEventListener('click', saveEqualizer);
    
    // Cerrar modal de ecualizador
    document.querySelector('#eq-modal .modal-close').addEventListener('click', () => {
      eqModal.classList.remove('active');
    });
    
    // Inicializar visualización
    initVisualization();
  }
  
  // Función para reproducir una pista
  function playTrack(id, src, title, artist, cover) {
    // Si es la misma pista, solo alternar reproducción/pausa
    if (id === currentTrackId) {
      togglePlay();
      return;
    }
    
    // Actualizar información de la pista actual
    currentTrackId = id;
    enhancedAudio.src = src;
    trackTitle.textContent = title;
    trackArtist.textContent = artist;
    enhancedCover.src = cover || 'assets/img/placeholder.svg';
    
    // Reproducir la pista
    enhancedAudio.play().catch(error => {
      console.error('Error al reproducir:', error);
      showToast('Error al reproducir la pista. Inténtalo de nuevo.', 'error');
    });
    
    // Añadir a la cola si no está ya
    if (!playQueue.some(track => track.id === id)) {
      playQueue.push({ id, src, title, artist, cover });
      currentQueueIndex = playQueue.length - 1;
      updateQueueDisplay();
    } else {
      // Actualizar índice en la cola
      currentQueueIndex = playQueue.findIndex(track => track.id === id);
    }
    
    // Registrar reproducción en el servidor
    if (id) {
      fetch('api/music.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=record_play&id=${id}`
      }).catch(error => {
        console.error('Error al registrar reproducción:', error);
      });
    }
  }
  
  // Función para alternar reproducción/pausa
  function togglePlay() {
    if (!enhancedAudio.src) {
      showToast('Selecciona una canción primero', 'info');
      return;
    }
    
    if (enhancedAudio.paused) {
      enhancedAudio.play().catch(error => {
        console.error('Error al reproducir:', error);
        showToast('Error al reproducir la pista. Inténtalo de nuevo.', 'error');
      });
    } else {
      enhancedAudio.pause();
    }
  }
  
  // Función para actualizar el botón de reproducción/pausa
  function updatePlayPauseButton() {
    if (isPlaying) {
      playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
    } else {
      playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
    }
  }
  
  // Función para reproducir la pista anterior
  function playPrevious() {
    if (playQueue.length === 0) {
      showToast('No hay canciones en la cola', 'info');
      return;
    }
    
    // Si la reproducción lleva más de 3 segundos, reiniciar la pista actual
    if (enhancedAudio.currentTime > 3) {
      enhancedAudio.currentTime = 0;
      return;
    }
    
    // Reproducir la pista anterior en la cola
    let prevIndex = currentQueueIndex - 1;
    if (prevIndex < 0) {
      prevIndex = isShuffleActive ? Math.floor(Math.random() * playQueue.length) : playQueue.length - 1;
    }
    
    currentQueueIndex = prevIndex;
    const track = playQueue[currentQueueIndex];
    playTrack(track.id, track.src, track.title, track.artist, track.cover);
  }
  
  // Función para reproducir la siguiente pista
  function playNext() {
    if (playQueue.length === 0) {
      showToast('No hay canciones en la cola', 'info');
      return;
    }
    
    // Determinar la siguiente pista según el modo de repetición y aleatorio
    let nextIndex;
    
    if (repeatMode === 'one') {
      // Repetir la pista actual
      enhancedAudio.currentTime = 0;
      enhancedAudio.play().catch(error => {
        console.error('Error al reproducir:', error);
      });
      return;
    } else if (isShuffleActive) {
      // Modo aleatorio
      nextIndex = Math.floor(Math.random() * playQueue.length);
      while (nextIndex === currentQueueIndex && playQueue.length > 1) {
        nextIndex = Math.floor(Math.random() * playQueue.length);
      }
    } else {
      // Modo normal
      nextIndex = currentQueueIndex + 1;
      if (nextIndex >= playQueue.length) {
        if (repeatMode === 'all') {
          nextIndex = 0;
        } else {
          showToast('Fin de la cola de reproducción', 'info');
          return;
        }
      }
    }
    
    currentQueueIndex = nextIndex;
    const track = playQueue[currentQueueIndex];
    playTrack(track.id, track.src, track.title, track.artist, track.cover);
  }
  
  // Función para manejar el final de una pista
  function handleTrackEnd() {
    playNext();
  }
  
  // Función para alternar el modo aleatorio
  function toggleShuffle() {
    isShuffleActive = !isShuffleActive;
    shuffleBtn.classList.toggle('active', isShuffleActive);
    
    if (isShuffleActive) {
      showToast('Modo aleatorio activado', 'success');
    } else {
      showToast('Modo aleatorio desactivado', 'info');
    }
  }
  
  // Función para alternar el modo de repetición
  function toggleRepeat() {
    if (repeatMode === 'none') {
      repeatMode = 'all';
      repeatBtn.innerHTML = '<i class="fas fa-redo"></i>';
      repeatBtn.classList.add('active');
      showToast('Repetir todas las canciones', 'success');
    } else if (repeatMode === 'all') {
      repeatMode = 'one';
      repeatBtn.innerHTML = '<i class="fas fa-redo-alt"></i>';
      repeatBtn.classList.add('active');
      showToast('Repetir canción actual', 'success');
    } else {
      repeatMode = 'none';
      repeatBtn.innerHTML = '<i class="fas fa-redo"></i>';
      repeatBtn.classList.remove('active');
      showToast('Repetición desactivada', 'info');
    }
  }
  
  // Función para alternar silencio
  function toggleMute() {
    isMuted = !isMuted;
    enhancedAudio.muted = isMuted;
    
    if (isMuted) {
      muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
      volumeFill.style.width = '0%';
      volumeHandle.style.left = '0%';
      showToast('Sonido silenciado', 'info');
    } else {
      muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
      volumeFill.style.width = (volume * 100) + '%';
      volumeHandle.style.left = (volume * 100) + '%';
      showToast('Sonido activado', 'info');
    }
  }
  
  // Función para cambiar el volumen
  function changeVolume(e) {
    const rect = volumeSlider.getBoundingClientRect();
    const pos = (e.clientX - rect.left) / rect.width;
    volume = Math.max(0, Math.min(1, pos));
    
    enhancedAudio.volume = volume;
    volumeFill.style.width = (volume * 100) + '%';
    volumeHandle.style.left = (volume * 100) + '%';
    
    // Si el volumen es 0, silenciar; de lo contrario, quitar silencio
    if (volume === 0) {
      isMuted = true;
      muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
    } else if (isMuted) {
      isMuted = false;
      enhancedAudio.muted = false;
      muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
    }
  }
  
  // Función para iniciar arrastre del control de volumen
  function startDraggingVolume(e) {
    e.preventDefault();
    
    function moveHandler(e) {
      const rect = volumeSlider.getBoundingClientRect();
      const pos = (e.clientX - rect.left) / rect.width;
      volume = Math.max(0, Math.min(1, pos));
      
      enhancedAudio.volume = volume;
      volumeFill.style.width = (volume * 100) + '%';
      volumeHandle.style.left = (volume * 100) + '%';
      
      // Si el volumen es 0, silenciar; de lo contrario, quitar silencio
      if (volume === 0) {
        isMuted = true;
        muteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
      } else if (isMuted) {
        isMuted = false;
        enhancedAudio.muted = false;
        muteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
      }
    }
    
    function upHandler() {
      document.removeEventListener('mousemove', moveHandler);
      document.removeEventListener('mouseup', upHandler);
    }
    
    document.addEventListener('mousemove', moveHandler);
    document.addEventListener('mouseup', upHandler);
  }
  
  // Función para actualizar la barra de progreso
  function updateProgress() {
    if (isNaN(enhancedAudio.duration)) return;
    
    const progress = (enhancedAudio.currentTime / enhancedAudio.duration) * 100;
    progressFill.style.width = progress + '%';
    progressHandle.style.left = progress + '%';
    
    // Actualizar tiempo actual
    currentTimeDisplay.textContent = formatTime(enhancedAudio.currentTime);
  }
  
  // Función para actualizar el tiempo total
  function updateTotalTime() {
    if (isNaN(enhancedAudio.duration)) return;
    
    totalTimeDisplay.textContent = formatTime(enhancedAudio.duration);
  }
  
  // Función para formatear tiempo en minutos:segundos
  function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  }
  
  // Función para buscar una posición en la pista
  function seekToPosition(e) {
    if (!enhancedAudio.src) return;
    
    const rect = progressBar.getBoundingClientRect();
    const pos = (e.clientX - rect.left) / rect.width;
    enhancedAudio.currentTime = pos * enhancedAudio.duration;
  }
  
  // Función para iniciar arrastre de la barra de progreso
  function startDraggingProgress(e) {
    e.preventDefault();
    
    function moveHandler(e) {
      const rect = progressBar.getBoundingClientRect();
      const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
      progressFill.style.width = (pos * 100) + '%';
      progressHandle.style.left = (pos * 100) + '%';
      currentTimeDisplay.textContent = formatTime(pos * enhancedAudio.duration);
    }
    
    function upHandler(e) {
      const rect = progressBar.getBoundingClientRect();
      const pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
      enhancedAudio.currentTime = pos * enhancedAudio.duration;
      
      document.removeEventListener('mousemove', moveHandler);
      document.removeEventListener('mouseup', upHandler);
    }
    
    document.addEventListener('mousemove', moveHandler);
    document.addEventListener('mouseup', upHandler);
  }
  
  // Función para alternar la vista de la cola
  function toggleQueueView() {
    const sidebar = document.querySelector('.player-sidebar');
    sidebar.classList.toggle('show-queue');
    
    if (sidebar.classList.contains('show-queue')) {
      queueBtn.classList.add('active');
    } else {
      queueBtn.classList.remove('active');
    }
  }
  
  // Función para actualizar la visualización de la cola
  function updateQueueDisplay() {
    if (playQueue.length === 0) {
      queueList.innerHTML = '<div class="empty-queue-message">La cola está vacía. Añade canciones para reproducir.</div>';
      return;
    }
    
    let queueHTML = '';
    playQueue.forEach((track, index) => {
      const isActive = index === currentQueueIndex;
      queueHTML += `
        <div class="track-item ${isActive ? 'active' : ''}" data-index="${index}">
          <img src="${track.cover || 'assets/img/placeholder.svg'}" alt="${track.title}" class="track-thumbnail">
          <div class="track-details">
            <div class="track-title">${track.title}</div>
            <div class="track-artist">${track.artist}</div>
          </div>
          <button class="track-action-btn" data-action="remove" title="Eliminar de la cola"><i class="fas fa-times"></i></button>
        </div>
      `;
    });
    
    queueList.innerHTML = queueHTML;
    
    // Añadir eventos a los elementos de la cola
    const queueItems = queueList.querySelectorAll('.track-item');
    queueItems.forEach(item => {
      item.addEventListener('click', function(e) {
        if (e.target.closest('.track-action-btn')) {
          const action = e.target.closest('.track-action-btn').dataset.action;
          const index = parseInt(this.dataset.index);
          
          if (action === 'remove') {
            removeFromQueue(index);
          }
          return;
        }
        
        const index = parseInt(this.dataset.index);
        playQueueItem(index);
      });
    });
  }
  
  // Función para reproducir un elemento de la cola
  function playQueueItem(index) {
    if (index < 0 || index >= playQueue.length) return;
    
    currentQueueIndex = index;
    const track = playQueue[index];
    playTrack(track.id, track.src, track.title, track.artist, track.cover);
  }
  
  // Función para eliminar un elemento de la cola
  function removeFromQueue(index) {
    if (index < 0 || index >= playQueue.length) return;
    
    // Si es la pista actual, reproducir la siguiente
    if (index === currentQueueIndex) {
      playQueue.splice(index, 1);
      
      if (playQueue.length === 0) {
        // La cola está vacía, detener reproducción
        enhancedAudio.pause();
        enhancedAudio.src = '';
        currentTrackId = null;
        currentQueueIndex = -1;
        trackTitle.textContent = 'Selecciona una canción';
        trackArtist.textContent = '-';
        enhancedCover.src = 'assets/img/placeholder.svg';
      } else {
        // Ajustar el índice actual
        if (currentQueueIndex >= playQueue.length) {
          currentQueueIndex = 0;
        }
        
        // Reproducir la nueva pista actual
        const track = playQueue[currentQueueIndex];
        playTrack(track.id, track.src, track.title, track.artist, track.cover);
      }
    } else {
      // No es la pista actual, solo eliminarla
      playQueue.splice(index, 1);
      
      // Ajustar el índice actual si es necesario
      if (index < currentQueueIndex) {
        currentQueueIndex--;
      }
    }
    
    // Actualizar la visualización de la cola
    updateQueueDisplay();
  }
  
  // Función para alternar pantalla completa
  function toggleFullscreen() {
    const playerContainer = document.querySelector('.enhanced-player-container');
    
    if (!document.fullscreenElement) {
      if (playerContainer.requestFullscreen) {
        playerContainer.requestFullscreen();
      } else if (playerContainer.mozRequestFullScreen) {
        playerContainer.mozRequestFullScreen();
      } else if (playerContainer.webkitRequestFullscreen) {
        playerContainer.webkitRequestFullscreen();
      } else if (playerContainer.msRequestFullscreen) {
        playerContainer.msRequestFullscreen();
      }
      fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }
      fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
    }
  }
  
  // Función para inicializar la visualización de audio
  function initVisualization() {
    if (!canvas) return;
    
    canvasContext = canvas.getContext('2d');
    
    try {
      // Crear contexto de audio
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      analyser = audioContext.createAnalyser();
      
      // Conectar el elemento de audio al analizador
      const source = audioContext.createMediaElementSource(enhancedAudio);
      source.connect(analyser);
      analyser.connect(audioContext.destination);
      
      // Configurar analizador
      analyser.fftSize = 256;
      const bufferLength = analyser.frequencyBinCount;
      dataArray = new Uint8Array(bufferLength);
      
      // Dibujar visualización inicial
      canvasContext.fillStyle = 'rgba(40, 40, 40, 1)';
      canvasContext.fillRect(0, 0, canvas.width, canvas.height);
    } catch (error) {
      console.error('Error al inicializar la visualización de audio:', error);
    }
  }
  
  // Función para iniciar la visualización
  function startVisualization() {
    if (!analyser || !canvasContext || isVisualizationActive) return;
    
    isVisualizationActive = true;
    visualize();
  }
  
  // Función para detener la visualización
  function stopVisualization() {
    isVisualizationActive = false;
    if (animationFrameId) {
      cancelAnimationFrame(animationFrameId);
      animationFrameId = null;
    }
  }
  
  // Función para visualizar el audio
  function visualize() {
    if (!isVisualizationActive) return;
    
    animationFrameId = requestAnimationFrame(visualize);
    
    analyser.getByteFrequencyData(dataArray);
    
    canvasContext.fillStyle = 'rgba(40, 40, 40, 0.2)';
    canvasContext.fillRect(0, 0, canvas.width, canvas.height);
    
    const barWidth = (canvas.width / dataArray.length) * 2.5;
    let x = 0;
    
    for (let i = 0; i < dataArray.length; i++) {
      const barHeight = dataArray[i] / 2;
      
      const gradient = canvasContext.createLinearGradient(0, canvas.height - barHeight, 0, canvas.height);
      gradient.addColorStop(0, getComputedStyle(document.documentElement).getPropertyValue('--primary-color'));
      gradient.addColorStop(1, 'rgba(0, 0, 0, 0.5)');
      
      canvasContext.fillStyle = gradient;
      canvasContext.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
      
      x += barWidth + 1;
    }
  }
  
  // Función para alternar el modal del ecualizador
  function toggleEqModal() {
    eqModal.classList.toggle('active');
  }
  
  // Función para aplicar un preset de ecualizador
  function applyEqPreset(preset) {
    if (!eqSettings[preset]) return;
    
    currentEqPreset = preset;
    
    // Actualizar posición de los sliders
    eqSettings[preset].forEach((value, index) => {
      const slider = document.querySelectorAll('.eq-slider-fill')[index];
      const handle = document.querySelectorAll('.eq-slider-handle')[index];
      
      // Convertir el valor (-6 a 6) a porcentaje (0 a 100)
      const percent = ((value + 6) / 12) * 100;
      
      slider.style.height = percent + '%';
      handle.style.top = (100 - percent) + '%';
    });
    
    // Aquí se aplicaría el ecualizador real si tuviéramos acceso a los filtros de audio
    showToast(`Preset de ecualizador "${preset}" aplicado`, 'success');
  }
  
  // Función para iniciar arrastre de un slider del ecualizador
  function startDraggingEqSlider(e, index) {
    e.preventDefault();
    
    const sliderContainer = document.querySelectorAll('.eq-slider-container')[index];
    const sliderFill = document.querySelectorAll('.eq-slider-fill')[index];
    const sliderHandle = document.querySelectorAll('.eq-slider-handle')[index];
    
    function moveHandler(e) {
      const rect = sliderContainer.getBoundingClientRect();
      const pos = 1 - Math.max(0, Math.min(1, (e.clientY - rect.top) / rect.height));
      
      sliderFill.style.height = (pos * 100) + '%';
      sliderHandle.style.top = ((1 - pos) * 100) + '%';
      
      // Convertir posición (0-1) a valor de ganancia (-6 a 6 dB)
      const gain = (pos * 12) - 6;
      
      // Aquí se aplicaría el valor al filtro correspondiente
      // Por ahora, solo actualizamos el preset personalizado
      if (currentEqPreset !== 'custom') {
        currentEqPreset = 'custom';
        eqPresetBtns.forEach(btn => btn.classList.remove('active'));
      }
    }
    
    function upHandler() {
      document.removeEventListener('mousemove', moveHandler);
      document.removeEventListener('mouseup', upHandler);
    }
    
    document.addEventListener('mousemove', moveHandler);
    document.addEventListener('mouseup', upHandler);
  }
  
  // Función para restablecer el ecualizador
  function resetEqualizer() {
    applyEqPreset('flat');
    
    // Actualizar botones activos
    eqPresetBtns.forEach(btn => {
      btn.classList.toggle('active', btn.dataset.preset === 'flat');
    });
    
    showToast('Ecualizador restablecido', 'info');
  }
  
  // Función para guardar la configuración del ecualizador
  function saveEqualizer() {
    // Aquí se guardaría la configuración actual
    showToast('Configuración de ecualizador guardada', 'success');
    eqModal.classList.remove('active');
  }
  
  // Función para mostrar un mensaje toast
  function showToast(message, type = 'info') {
    // Crear el elemento toast
    const toast = document.createElement('div');
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.left = '50%';
    toast.style.transform = 'translateX(-50%) translateY(-100px)';
    toast.style.padding = '10px 20px';
    toast.style.borderRadius = '5px';
    toast.style.boxShadow = '0 3px 10px rgba(0,0,0,0.3)';
    toast.style.zIndex = '1000';
    toast.style.transition = 'all 0.3s ease';
    
    // Establecer colores según el tipo
    if (type === 'error') {
      toast.style.background = '#e74c3c';
      toast.style.color = '#fff';
      toast.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    } else if (type === 'success') {
      toast.style.background = 'var(--primary-color)';
      toast.style.color = 'var(--bg-color)';
      toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
    } else {
      toast.style.background = '#3498db';
      toast.style.color = '#fff';
      toast.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
    }
    
    document.body.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
      toast.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
      toast.style.transform = 'translateX(-50%) translateY(-100px)';
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, 3000);
  }
});
</script>
