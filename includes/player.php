<?php
// Verificar si hay una sesión activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<div id="music-player" class="fixed-bottom bg-dark text-white py-2 d-none">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <img id="player-cover" src="/bassculture/assets/img/default-album.jpg" alt="Portada" class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                    <div>
                        <h6 id="player-title" class="mb-0">Título de la canción</h6>
                        <small id="player-artist">Artista</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex flex-column">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <button id="player-prev" class="btn btn-sm btn-dark me-3"><i class="fas fa-step-backward"></i></button>
                        <button id="player-play" class="btn btn-sm btn-primary me-3"><i class="fas fa-play"></i></button>
                        <button id="player-next" class="btn btn-sm btn-dark"><i class="fas fa-step-forward"></i></button>
                    </div>
                    <div class="d-flex align-items-center">
                        <span id="player-current-time">0:00</span>
                        <div class="progress flex-grow-1 mx-2" style="height: 5px;">
                            <div id="player-progress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span id="player-duration">0:00</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center">
                    <button id="player-volume" class="btn btn-sm btn-dark me-2"><i class="fas fa-volume-up"></i></button>
                    <div class="progress" style="width: 80px; height: 5px;">
                        <div id="player-volume-bar" class="progress-bar" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <button id="player-repeat" class="btn btn-sm btn-dark ms-3"><i class="fas fa-redo"></i></button>
                    <button id="player-shuffle" class="btn btn-sm btn-dark ms-2"><i class="fas fa-random"></i></button>
                </div>
            </div>
        </div>
    </div>
    <audio id="audio-player" src="/placeholder.svg"></audio>
</div>
