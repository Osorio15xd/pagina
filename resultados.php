<?php
require_once 'conexion.php';
require_once 'encabezado.php';

function buscar($termino) {
    global $conexion;
    $termino = "%$termino%";
    
    $sql = "SELECT 'artista' as tipo, a.id_artista as id, u.nombre_usuario as nombre, '' as artista, a.foto_path as imagen
            FROM artista a
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE u.nombre_usuario LIKE ?
            UNION
            SELECT 'album' as tipo, al.id_album as id, al.nombre_album as nombre, u.nombre_usuario as artista, al.imagen_album_path as imagen
            FROM album al
            JOIN artista a ON al.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE al.nombre_album LIKE ?
            UNION
            SELECT 'cancion' as tipo, c.id_cancion as id, c.nombre_cancion as nombre, u.nombre_usuario as artista, al.imagen_album_path as imagen
            FROM canciones c
            JOIN album al ON c.id_album = al.id_album
            JOIN artista a ON c.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE c.nombre_cancion LIKE ?
            UNION
            SELECT 'sencillo' as tipo, s.id_sencillo as id, s.nombre_sencillo as nombre, u.nombre_usuario as artista, s.imagen_sencillo_path as imagen
            FROM sencillos s
            JOIN artista a ON s.id_artista = a.id_artista
            JOIN usuario u ON a.usuario = u.id_usuario
            WHERE s.nombre_sencillo LIKE ?
            LIMIT 20";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $termino, $termino, $termino, $termino);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
$searchResults = $searchTerm ? buscar($searchTerm) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de búsqueda - BassCulture</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .result-card {
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .result-card:hover {
            transform: translateY(-5px);
        }
        .result-image {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Resultados de búsqueda para "<?php echo htmlspecialchars($searchTerm); ?>"</h1>
        
        <?php if (empty($searchResults)): ?>
            <p class="text-center">No se encontraron resultados para tu búsqueda.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($searchResults as $result): ?>
                    <div class="col-md-4 mb-4">
                        <div class="result-card">
                            <img src="<?php echo htmlspecialchars($result['imagen'] ?? 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($result['nombre']); ?>" class="result-image">
                            <div class="p-3">
                                <h3><?php echo htmlspecialchars($result['nombre']); ?></h3>
                                <p><?php echo ucfirst($result['tipo']); ?></p>
                                <?php if ($result['artista']): ?>
                                    <p>Por: <?php echo htmlspecialchars($result['artista']); ?></p>
                                <?php endif; ?>
                                <?php
                                $link = '#';
                                switch ($result['tipo']) {
                                    case 'artista':
                                        $link = "artista.php?id=" . $result['id'];
                                        break;
                                    case 'album':
                                        $link = "Album.php?id_album=" . $result['id'];
                                        break;
                                    case 'cancion':
                                    case 'sencillo':
                                        // Assuming you have a page to display individual songs/singles
                                        $link = "cancion.php?id=" . $result['id'] . "&tipo=" . $result['tipo'];
                                        break;
                                }
                                ?>
                                <a href="<?php echo $link; ?>" class="btn btn-primary">Ver más</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

