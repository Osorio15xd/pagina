</main>
    
    <!-- Reproductor flotante -->
    <div id="floating-player">
        <div class="player-left">
            <img id="floating-player-cover" src="assets/img/default-cover.jpg" alt="Portada">
            <div class="song-info">
                <h6 id="floating-track-name">Nombre de la canción - Artista</h6>
            </div>
        </div>
        <div class="player-center">
            <button id="prev-btn"><i class="fas fa-step-backward"></i></button>
            <button id="play-pause-btn"><i class="fas fa-play"></i></button>
            <button id="next-btn"><i class="fas fa-step-forward"></i></button>
        </div>
        <div class="player-right">
            <div class="progress-container">
                <span id="current-time">0:00</span>
                <div id="progress-bar">
                    <div id="progress"></div>
                </div>
                <span id="total-time">0:00</span>
            </div>
            <button id="volume-btn"><i class="fas fa-volume-up"></i></button>
            <div id="volume-slider-container">
                <input type="range" id="volume-slider" min="0" max="100" value="100">
            </div>
        </div>
    </div>
    
    <!-- Modales -->
    <div class="modal-overlay" id="playlist-modal">
        <div class="modal-container">
            <!-- El contenido del modal se cargará dinámicamente -->
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
    // Inicializar el contador del carrito
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar contador del carrito
        updateCartCount();
        
        // Inicializar el botón de cambio de tema
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                const currentTheme = localStorage.getItem('theme') || 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                // Guardar tema en localStorage
                localStorage.setItem('theme', newTheme);
                
                // Guardar tema en sesión PHP mediante AJAX
                fetch('api/user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=save_theme&theme=${newTheme}`
                });
                
                // Aplicar tema
                applyThemeAndColor();
            });
        }
    });
    </script>
</body>
</html>
