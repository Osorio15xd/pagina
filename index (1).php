<?php 
session_start();
require_once 'conexion.php';
require_once 'encabezado.php';
?>

<div class="container mt-3">
    <form id="searchForm" class="mb-3">
        <div class="input-group">
            <input type="text" id="searchTerm" class="form-control" placeholder="Buscar canción o artista...">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>
    <div id="searchResults"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = new bootstrap.Carousel(document.getElementById('carouselExampleDark'), {
        interval: 5000,
        wrap: true
    });
});
</script>

<div class="banner">
    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="5000">
                <img src="travis.jpg" class="d-block w-100" alt="Travis Scott Concert">
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <img src="edc.jpg" class="d-block w-100" alt="Electronic Music Festival">
            </div>
            <div class="carousel-item" data-bs-interval="5000">
                <img src="coachela.png" class="d-block w-100" alt="Coachella">
            </div>
            <!-- Agrega más elementos del carrusel aquí -->
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <iframe src="chatbot.html" width="300" height="400" style="border: none; position: fixed; bottom: 20px; right: 20px;"></iframe>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var termino = $('#searchTerm').val();
        $.ajax({
            url: 'buscar.php',
            type: 'POST',
            data: {termino: termino},
            dataType: 'json',
            success: function(response) {
                var html = '<ul class="list-group">';
                response.forEach(function(item) {
                    html += '<li class="list-group-item">';
                    html += item.tipo === 'cancion' ? '🎵 ' : '👤 ';
                    html += item.nombre;
                    if (item.artista) {
                        html += ' - ' + item.artista;
                    }
                    html += '</li>';
                });
                html += '</ul>';
                $('#searchResults').html(html);
            },
            error: function() {
                $('#searchResults').html('<p>Error al buscar. Intente de nuevo.</p>');
            }
        });
    });
});
</script>

