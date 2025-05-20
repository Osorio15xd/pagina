<?php
// Iniciar sesión si no está iniciada

include_once '../includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Incluir la conexión a la base de datos
require_once __DIR__ . '/../config/db_connect.php';

// Verificar si el usuario está logueado
$is_logged_in = isset($_SESSION['user_id']);
$userId = $is_logged_in ? $_SESSION['user_id'] : 0;

// Obtener géneros musicales para filtrar
$stmt = $pdo->query("SELECT id_genero as id, nombre_genero as nombre FROM genero ORDER BY nombre_genero");
$generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener artistas para filtrar
$stmt = $pdo->query("
    SELECT a.id_artista as id, u.nombre_usuario as nombre 
    FROM artista a 
    JOIN usuario u ON a.usuario = u.id_usuario 
    ORDER BY u.nombre_usuario
");
$artistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar búsqueda y filtros
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre_filter = isset($_GET['genre']) ? intval($_GET['genre']) : 0;
$artist_filter = isset($_GET['artist']) ? intval($_GET['artist']) : 0;
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$content_type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Función para obtener canciones
function getSongs($pdo, $params = []) {
    $sql = "
        SELECT c.id_cancion as id, c.nombre_cancion as titulo, a.imagen_album_path as portada, 
               c.precio, u.nombre_usuario as artista, g.nombre_genero as genero,
               'cancion' as tipo, c.cancion_path as audio_path, c.fecha_lanzamiento
        FROM canciones c
        JOIN album a ON c.id_album = a.id_album
        JOIN artista art ON c.id_artista = art.id_artista
        JOIN usuario u ON art.usuario = u.id_usuario
        JOIN genero g ON a.id_genero = g.id_genero
        WHERE 1=1
    ";
    
    if (!empty($params['search'])) {
        $sql .= " AND (c.nombre_cancion LIKE :search OR u.nombre_usuario LIKE :search)";
    }
    
    if (!empty($params['genre']) && $params['genre'] > 0) {
        $sql .= " AND a.id_genero = :genre";
    }
    
    if (!empty($params['artist']) && $params['artist'] > 0) {
        $sql .= " AND c.id_artista = :artist";
    }
    
    if (!empty($params['price'])) {
        if ($params['price'] === 'free') {
            $sql .= " AND c.precio = 0";
        } else if ($params['price'] === 'under5') {
            $sql .= " AND c.precio < 5";
        } else if ($params['price'] === 'under10') {
            $sql .= " AND c.precio < 10";
        } else if ($params['price'] === 'over10') {
            $sql .= " AND c.precio >= 10";
        }
    }
    
    // Ordenar resultados
    if ($params['sort'] === 'price_asc') {
        $sql .= " ORDER BY c.precio ASC";
    } else if ($params['sort'] === 'price_desc') {
        $sql .= " ORDER BY c.precio DESC";
    } else if ($params['sort'] === 'name_asc') {
        $sql .= " ORDER BY c.nombre_cancion ASC";
    } else if ($params['sort'] === 'name_desc') {
        $sql .= " ORDER BY c.nombre_cancion DESC";
    } else {
        $sql .= " ORDER BY c.fecha_lanzamiento DESC";
    }
    
    if (isset($params['limit'])) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if (!empty($params['search'])) {
        $search = '%' . $params['search'] . '%';
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    }
    
    if (!empty($params['genre']) && $params['genre'] > 0) {
        $stmt->bindParam(':genre', $params['genre'], PDO::PARAM_INT);
    }
    
    if (!empty($params['artist']) && $params['artist'] > 0) {
        $stmt->bindParam(':artist', $params['artist'], PDO::PARAM_INT);
    }
    
    if (isset($params['limit'])) {
        $stmt->bindParam(':limit', $params['limit'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener sencillos
function getSingles($pdo, $params = []) {
    $sql = "
        SELECT s.id_sencillo as id, s.nombre_sencillo as titulo, s.imagen_sencillo_path as portada, 
               s.precio, u.nombre_usuario as artista, g.nombre_genero as genero,
               'sencillo' as tipo, s.cancion_path as audio_path, s.fecha_lanzamiento
        FROM sencillos s
        JOIN artista a ON s.id_artista = a.id_artista
        JOIN usuario u ON a.usuario = u.id_usuario
        JOIN genero g ON s.id_genero = g.id_genero
        WHERE 1=1
    ";
    
    if (!empty($params['search'])) {
        $sql .= " AND (s.nombre_sencillo LIKE :search OR u.nombre_usuario LIKE :search)";
    }
    
    if (!empty($params['genre']) && $params['genre'] > 0) {
        $sql .= " AND s.id_genero = :genre";
    }
    
    if (!empty($params['artist']) && $params['artist'] > 0) {
        $sql .= " AND s.id_artista = :artist";
    }
    
    if (!empty($params['price'])) {
        if ($params['price'] === 'free') {
            $sql .= " AND s.precio = 0";
        } else if ($params['price'] === 'under5') {
            $sql .= " AND s.precio < 5";
        } else if ($params['price'] === 'under10') {
            $sql .= " AND s.precio < 10";
        } else if ($params['price'] === 'over10') {
            $sql .= " AND s.precio >= 10";
        }
    }
    
    // Ordenar resultados
    if ($params['sort'] === 'price_asc') {
        $sql .= " ORDER BY s.precio ASC";
    } else if ($params['sort'] === 'price_desc') {
        $sql .= " ORDER BY s.precio DESC";
    } else if ($params['sort'] === 'name_asc') {
        $sql .= " ORDER BY s.nombre_sencillo ASC";
    } else if ($params['sort'] === 'name_desc') {
        $sql .= " ORDER BY s.nombre_sencillo DESC";
    } else {
        $sql .= " ORDER BY s.fecha_lanzamiento DESC";
    }
    
    if (isset($params['limit'])) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if (!empty($params['search'])) {
        $search = '%' . $params['search'] . '%';
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    }
    
    if (!empty($params['genre']) && $params['genre'] > 0) {
        $stmt->bindParam(':genre', $params['genre'], PDO::PARAM_INT);
    }
    
    if (!empty($params['artist']) && $params['artist'] > 0) {
        $stmt->bindParam(':artist', $params['artist'], PDO::PARAM_INT);
    }
    
    if (isset($params['limit'])) {
        $stmt->bindParam(':limit', $params['limit'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener álbumes
function getAlbums($pdo, $params = []) {
    $sql = "
        SELECT a.id_album as id, a.nombre_album as titulo, a.imagen_album_path as portada, 
               a.precio, u.nombre_usuario as artista, g.nombre_genero as genero,
               'album' as tipo, NULL as audio_path, a.fecha_lanzamiento
        FROM album a
        JOIN artista art ON a.id_artista = art.id_artista
        JOIN usuario u ON art.usuario = u.id_usuario
        JOIN genero g ON a.id_genero = g.id_genero
        WHERE 1=1
    ";
    
    if (!empty($params['search'])) {
        $sql .= " AND (a.nombre_album LIKE :search OR u.nombre_usuario LIKE :search)";
    }
    
    if (!empty($params['genre']) && $params['genre'] > 0) {
        $sql .= " AND a.id_genero = :genre";
    }
    
    if (!empty($params['artist']) && $params['artist'] > 0) {
        $sql .= " AND a.id_artista = :artist";
    }
    
    if (!empty($params['price'])) {
        if ($params['price'] === 'free') {
            $sql .= " AND a.precio = 0";
        } else if ($params['price'] === 'under5') {
            $sql .= " AND a.precio < 5";
        } else if ($params['price'] === 'under10') {
            $sql .= " AND a.precio < 10";
        } else if ($params['price'] === 'over10') {
            $sql .= " AND a.precio >= 10";
        }
    }
    
    // Ordenar resultados
    if ($params['sort'] === 'price_asc') {
        $sql .= " ORDER BY a.precio ASC";
    } else if ($params['sort'] === 'price_desc') {
        $sql .= " ORDER BY a.precio DESC";
    } else if ($params['sort'] === 'name_asc') {
        $sql .= " ORDER BY a.nombre_album ASC";
    } else if ($params['sort'] === 'name_desc') {
        $sql .= " ORDER BY a.nombre_album DESC";
    } else {
        $sql .= " ORDER BY a.fecha_lanzamiento DESC";
    }
    
    if (isset($params['limit'])) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    
    if (!empty($params['search'])) {
        $search = '%' . $params['search'] . '%';
        $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    }
    
    if (!empty($params['genre']) && $params['genre'] > 0) {
        $stmt->bindParam(':genre', $params['genre'], PDO::PARAM_INT);
    }
    
    if (!empty($params['artist']) && $params['artist'] > 0) {
        $stmt->bindParam(':artist', $params['artist'], PDO::PARAM_INT);
    }
    
    if (isset($params['limit'])) {
        $stmt->bindParam(':limit', $params['limit'], PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener canciones destacadas
$params = [
    'limit' => 6,
    'sort' => 'newest'
];
$featured_songs = getSongs($pdo, $params);

// Obtener sencillos destacados
$featured_singles = getSingles($pdo, $params);

// Obtener nuevos lanzamientos (combinación de canciones y sencillos)
$new_releases = array_merge(
    getSongs($pdo, ['limit' => 4, 'sort' => 'newest']),
    getSingles($pdo, ['limit' => 4, 'sort' => 'newest'])
);
// Ordenar por fecha de lanzamiento
usort($new_releases, function($a, $b) {
    return strtotime($b['fecha_lanzamiento']) - strtotime($a['fecha_lanzamiento']);
});
// Limitar a 8 elementos
$new_releases = array_slice($new_releases, 0, 8);

// Obtener álbumes destacados
$featured_albums = getAlbums($pdo, ['limit' => 4, 'sort' => 'newest']);

// Procesar búsqueda y filtros
$filtered_items = [];
if (!empty($search_query) || $genre_filter > 0 || $artist_filter > 0 || !empty($price_filter) || isset($_GET['filter'])) {
    $filter_params = [
        'search' => $search_query,
        'genre' => $genre_filter,
        'artist' => $artist_filter,
        'price' => $price_filter,
        'sort' => $sort_by
    ];
    
    if ($content_type === 'all' || $content_type === 'songs') {
        $filtered_songs = getSongs($pdo, $filter_params);
        $filtered_items = array_merge($filtered_items, $filtered_songs);
    }
    
    if ($content_type === 'all' || $content_type === 'singles') {
        $filtered_singles = getSingles($pdo, $filter_params);
        $filtered_items = array_merge($filtered_items, $filtered_singles);
    }
    
    if ($content_type === 'all' || $content_type === 'albums') {
        $filtered_albums = getAlbums($pdo, $filter_params);
        $filtered_items = array_merge($filtered_items, $filtered_albums);
    }
    
    // Ordenar resultados combinados
    if ($sort_by === 'price_asc') {
        usort($filtered_items, function($a, $b) {
            return $a['precio'] - $b['precio'];
        });
    } else if ($sort_by === 'price_desc') {
        usort($filtered_items, function($a, $b) {
            return $b['precio'] - $a['precio'];
        });
    } else if ($sort_by === 'name_asc') {
        usort($filtered_items, function($a, $b) {
            return strcmp($a['titulo'], $b['titulo']);
        });
    } else if ($sort_by === 'name_desc') {
        usort($filtered_items, function($a, $b) {
            return strcmp($b['titulo'], $a['titulo']);
        });
    } else {
        usort($filtered_items, function($a, $b) {
            return strtotime($b['fecha_lanzamiento']) - strtotime($a['fecha_lanzamiento']);
        });
    }
}

// Obtener el número de elementos en el carrito
$cart_count = 0;
$cart_total = 0;
if ($is_logged_in) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM carrito WHERE id_usuario = ?");
        $stmt->execute([$userId]);
        $cart_count = $stmt->fetchColumn();
        
        // Obtener el total del carrito
        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE 
                    WHEN c.tipo_producto = 'album' THEN (SELECT precio FROM album WHERE id_album = c.id_producto)
                    WHEN c.tipo_producto = 'sencillo' THEN (SELECT precio FROM sencillos WHERE id_sencillo = c.id_producto)
                    WHEN c.tipo_producto = 'cancion' THEN (SELECT precio FROM canciones WHERE id_cancion = c.id_producto)
                    ELSE 0
                END) as total
            FROM carrito c
            WHERE c.id_usuario = ?
        ");
        $stmt->execute([$userId]);
        $cart_total = $stmt->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        // Manejar error
        $cart_count = 0;
        $cart_total = 0;
    }
}
?>

<section id="page-music-store" class="page active" aria-label="Tienda de Música">
  <h1>Tienda de Música</h1>
  <p>Descubre y compra música de tus artistas favoritos</p>
  
  <div class="store-container">
    <div class="store-sidebar">
      <div class="search-filters">
        <h3>Filtros</h3>
        <form id="filter-form" action="" method="GET">
          <input type="hidden" name="page" value="music-store">
          <input type="hidden" name="filter" value="1">
          
          <div class="filter-group">
            <label for="search-input">Buscar</label>
            <input type="text" id="search-input" name="search" placeholder="Título o artista..." value="<?php echo htmlspecialchars($search_query); ?>">
          </div>
          
          <div class="filter-group">
            <label for="content-type">Tipo de contenido</label>
            <select id="content-type" name="type">
              <option value="all" <?php echo $content_type === 'all' ? 'selected' : ''; ?>>Todo</option>
              <option value="songs" <?php echo $content_type === 'songs' ? 'selected' : ''; ?>>Canciones</option>
              <option value="singles" <?php echo $content_type === 'singles' ? 'selected' : ''; ?>>Sencillos</option>
              <option value="albums" <?php echo $content_type === 'albums' ? 'selected' : ''; ?>>Álbumes</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="genre-filter">Género</label>
            <select id="genre-filter" name="genre">
              <option value="0">Todos los géneros</option>
              <?php foreach ($generos as $genero): ?>
                <option value="<?php echo $genero['id']; ?>" <?php echo $genre_filter == $genero['id'] ? 'selected' : ''; ?>><?php echo $genero['nombre']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="artist-filter">Artista</label>
            <select id="artist-filter" name="artist">
              <option value="0">Todos los artistas</option>
              <?php foreach ($artistas as $artista): ?>
                <option value="<?php echo $artista['id']; ?>" <?php echo $artist_filter == $artista['id'] ? 'selected' : ''; ?>><?php echo $artista['nombre']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="price-filter">Precio</label>
            <select id="price-filter" name="price">
              <option value="" <?php echo $price_filter === '' ? 'selected' : ''; ?>>Cualquier precio</option>
              <option value="free" <?php echo $price_filter === 'free' ? 'selected' : ''; ?>>Gratis</option>
              <option value="under5" <?php echo $price_filter === 'under5' ? 'selected' : ''; ?>>Menos de $5</option>
              <option value="under10" <?php echo $price_filter === 'under10' ? 'selected' : ''; ?>>Menos de $10</option>
              <option value="over10" <?php echo $price_filter === 'over10' ? 'selected' : ''; ?>>$10 o más</option>
            </select>
          </div>
          
          <div class="filter-group">
            <label for="sort-by">Ordenar por</label>
            <select id="sort-by" name="sort">
              <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Más recientes</option>
              <option value="price_asc" <?php echo $sort_by === 'price_asc' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
              <option value="price_desc" <?php echo $sort_by === 'price_desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
              <option value="name_asc" <?php echo $sort_by === 'name_asc' ? 'selected' : ''; ?>>Nombre: A-Z</option>
              <option value="name_desc" <?php echo $sort_by === 'name_desc' ? 'selected' : ''; ?>>Nombre: Z-A</option>
            </select>
          </div>
          
          <button type="submit" class="btn-primary filter-btn">
            <i class="fas fa-filter"></i> Aplicar Filtros
          </button>
          
          <button type="button" id="clear-filters" class="btn-secondary">
            <i class="fas fa-times"></i> Limpiar Filtros
          </button>
        </form>
      </div>
      
      <div class="store-categories">
        <h3>Categorías</h3>
        <ul>
          <li><a href="#featured-songs">Canciones Destacadas</a></li>
          <li><a href="#featured-singles">Sencillos Destacados</a></li>
          <li><a href="#featured-albums">Álbumes Destacados</a></li>
          <li><a href="#new-releases">Nuevos Lanzamientos</a></li>
        </ul>
      </div>
      
      <div class="store-cart-summary">
        <h3>Carrito</h3>
        <div id="cart-summary">
          <p><?php echo $cart_count; ?> item<?php echo $cart_count !== 1 ? 's' : ''; ?> en el carrito</p>
          <p>Total: $<?php echo number_format($cart_total, 2); ?></p>
        </div>
        <a href="index.php?page=carrito" class="btn-primary view-cart-btn">
          <i class="fas fa-shopping-cart"></i> Ver Carrito
        </a>
      </div>
    </div>
    
    <div class="store-content">
      <?php if (!empty($search_query) || $genre_filter > 0 || $artist_filter > 0 || !empty($price_filter) || isset($_GET['filter'])): ?>
        <div class="search-results">
          <h2>Resultados de búsqueda</h2>
          <?php if (count($filtered_items) > 0): ?>
            <p><?php echo count($filtered_items); ?> resultados encontrados</p>
            <div class="song-grid">
              <?php foreach ($filtered_items as $item): ?>
                <div class="song-card" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['tipo']; ?>">
                  <div class="song-cover">
                    <img src="<?php echo file_exists($item['portada']) ? $item['portada'] : 'assets/img/default-cover.png'; ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                    <div class="song-actions">
                      <?php if ($item['tipo'] !== 'album'): ?>
                        <button class="preview-btn" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['tipo']; ?>"><i class="fas fa-play"></i></button>
                      <?php endif; ?>
                      <button class="add-to-cart-btn" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['tipo']; ?>"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                  </div>
                  <div class="song-info">
                    <h3 class="song-title"><?php echo htmlspecialchars($item['titulo']); ?></h3>
                    <p class="song-artist"><?php echo htmlspecialchars($item['artista']); ?></p>
                    <p class="song-genre"><?php echo htmlspecialchars($item['genero']); ?></p>
                    <div class="song-price">
                      <span class="regular-price">$<?php echo number_format($item['precio'], 2); ?></span>
                    </div>
                    <div class="song-type-badge <?php echo $item['tipo']; ?>">
                      <?php echo ucfirst($item['tipo']); ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <p>No se encontraron resultados para tu búsqueda.</p>
              <button id="clear-search" class="btn-primary">Limpiar búsqueda</button>
            </div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div id="featured-songs" class="store-section">
          <h2>Canciones Destacadas</h2>
          <div class="song-grid">
            <?php foreach ($featured_songs as $song): ?>
              <div class="song-card" data-id="<?php echo $song['id']; ?>" data-type="<?php echo $song['tipo']; ?>">
                <div class="song-cover">
                  <img src="<?php echo file_exists($song['portada']) ? $song['portada'] : 'assets/img/default-cover.png'; ?>" alt="<?php echo htmlspecialchars($song['titulo']); ?>">
                  <div class="song-actions">
                    <button class="preview-btn" data-id="<?php echo $song['id']; ?>" data-type="<?php echo $song['tipo']; ?>"><i class="fas fa-play"></i></button>
                    <button class="add-to-cart-btn" data-id="<?php echo $song['id']; ?>" data-type="<?php echo $song['tipo']; ?>"><i class="fas fa-shopping-cart"></i></button>
                  </div>
                </div>
                <div class="song-info">
                  <h3 class="song-title"><?php echo htmlspecialchars($song['titulo']); ?></h3>
                  <p class="song-artist"><?php echo htmlspecialchars($song['artista']); ?></p>
                  <p class="song-genre"><?php echo htmlspecialchars($song['genero']); ?></p>
                  <div class="song-price">
                    <span class="regular-price">$<?php echo number_format($song['precio'], 2); ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div id="featured-singles" class="store-section">
          <h2>Sencillos Destacados</h2>
          <div class="song-grid">
            <?php foreach ($featured_singles as $single): ?>
              <div class="song-card" data-id="<?php echo $single['id']; ?>" data-type="<?php echo $single['tipo']; ?>">
                <div class="song-cover">
                  <img src="<?php echo file_exists($single['portada']) ? $single['portada'] : 'assets/img/default-cover.png'; ?>" alt="<?php echo htmlspecialchars($single['titulo']); ?>">
                  <div class="song-actions">
                    <button class="preview-btn" data-id="<?php echo $single['id']; ?>" data-type="<?php echo $single['tipo']; ?>"><i class="fas fa-play"></i></button>
                    <button class="add-to-cart-btn" data-id="<?php echo $single['id']; ?>" data-type="<?php echo $single['tipo']; ?>"><i class="fas fa-shopping-cart"></i></button>
                  </div>
                </div>
                <div class="song-info">
                  <h3 class="song-title"><?php echo htmlspecialchars($single['titulo']); ?></h3>
                  <p class="song-artist"><?php echo htmlspecialchars($single['artista']); ?></p>
                  <p class="song-genre"><?php echo htmlspecialchars($single['genero']); ?></p>
                  <div class="song-price">
                    <span class="regular-price">$<?php echo number_format($single['precio'], 2); ?></span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div id="featured-albums" class="store-section">
          <h2>Álbumes Destacados</h2>
          <div class="song-grid">
            <?php foreach ($featured_albums as $album): ?>
              <div class="song-card" data-id="<?php echo $album['id']; ?>" data-type="<?php echo $album['tipo']; ?>">
                <div class="song-cover">
                  <img src="<?php echo file_exists($album['portada']) ? $album['portada'] : 'assets/img/default-cover.png'; ?>" alt="<?php echo htmlspecialchars($album['titulo']); ?>">
                  <div class="song-actions">
                    <button class="add-to-cart-btn" data-id="<?php echo $album['id']; ?>" data-type="<?php echo $album['tipo']; ?>"><i class="fas fa-shopping-cart"></i></button>
                  </div>
                </div>
                <div class="song-info">
                  <h3 class="song-title"><?php echo htmlspecialchars($album['titulo']); ?></h3>
                  <p class="song-artist"><?php echo htmlspecialchars($album['artista']); ?></p>
                  <p class="song-genre"><?php echo htmlspecialchars($album['genero']); ?></p>
                  <div class="song-price">
                    <span class="regular-price">$<?php echo number_format($album['precio'], 2); ?></span>
                  </div>
                  <div class="song-type-badge album">Álbum</div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        
        <div id="new-releases" class="store-section">
          <h2>Nuevos Lanzamientos</h2>
          <div class="song-grid">
            <?php foreach ($new_releases as $item): ?>
              <div class="song-card" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['tipo']; ?>">
                <div class="song-cover">
                  <img src="<?php echo file_exists($item['portada']) ? $item['portada'] : 'assets/img/default-cover.png'; ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                  <div class="song-actions">
                    <?php if ($item['tipo'] !== 'album'): ?>
                      <button class="preview-btn" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['tipo']; ?>"><i class="fas fa-play"></i></button>
                    <?php endif; ?>
                    <button class="add-to-cart-btn" data-id="<?php echo $item['id']; ?>" data-type="<?php echo $item['tipo']; ?>"><i class="fas fa-shopping-cart"></i></button>
                  </div>
                </div>
                <div class="song-info">
                  <h3 class="song-title"><?php echo htmlspecialchars($item['titulo']); ?></h3>
                  <p class="song-artist"><?php echo htmlspecialchars($item['artista']); ?></p>
                  <p class="song-genre"><?php echo htmlspecialchars($item['genero']); ?></p>
                  <div class="song-price">
                    <span class="regular-price">$<?php echo number_format($item['precio'], 2); ?></span>
                  </div>
                  <div class="song-type-badge <?php echo $item['tipo']; ?>">
                    <?php echo ucfirst($item['tipo']); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Preview Modal -->
  <div id="preview-modal" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <h3 class="modal-title">Previsualización</h3>
        <button class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="preview-content">
          <div class="preview-cover">
            <img id="preview-cover-img" src="assets/img/default-cover.png" alt="Portada">
          </div>
          <div class="preview-info">
            <h3 id="preview-title">Título de la canción</h3>
            <p id="preview-artist">Artista</p>
            <p id="preview-genre">Género</p>
            <div class="preview-player">
              <audio id="preview-audio" controls></audio>
            </div>
            <div class="preview-price">
              <span id="preview-price-display">$0.00</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-secondary" id="close-preview">Cerrar</button>
        <button class="btn-primary" id="add-to-cart-preview"><i class="fas fa-shopping-cart"></i> Añadir al Carrito</button>
      </div>
    </div>
  </div>
  
  <!-- Checkout Modal -->
  <div id="checkout-modal" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <h3 class="modal-title">Finalizar Compra</h3>
        <button class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="payment-methods">
          <button type="button" class="payment-method active" id="creditCardBtn">
            <i class="fas fa-credit-card"></i>
            <span>Tarjeta de Crédito</span>
          </button>
          <button type="button" class="payment-method" id="paypalBtn">
            <i class="fab fa-paypal"></i>
            <span>PayPal</span>
          </button>
        </div>
        
        <!-- Formulario de Tarjeta de Crédito -->
        <form id="creditCardForm" class="payment-form">
          <div class="form-group">
            <label for="cardNumber">Número de Tarjeta</label>
            <div class="input-icon">
              <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
              <i class="fas fa-credit-card"></i>
            </div>
            <small class="error-message" id="cardNumberError"></small>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="expiryDate">Fecha de Expiración</label>
              <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/AA" maxlength="5">
              <small class="error-message" id="expiryDateError"></small>
            </div>
            
            <div class="form-group">
              <label for="cvv">CVV</label>
              <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
              <small class="error-message" id="cvvError"></small>
            </div>
          </div>
          
          <div class="form-group">
            <label for="cardName">Nombre en la Tarjeta</label>
            <input type="text" id="cardName" name="cardName" placeholder="Juan Pérez">
            <small class="error-message" id="cardNameError"></small>
          </div>
          
          <div class="checkout-summary">
            <h4>Resumen de compra</h4>
            <div id="checkout-items">
              <!-- Los items se cargarán dinámicamente -->
            </div>
            <div class="checkout-total">
              <p>Total: <span id="checkout-total-amount">$0.00</span></p>
            </div>
          </div>
          
          <button type="submit" class="submit-btn">
            <i class="fas fa-lock"></i> Pagar Ahora
          </button>
        </form>
        
        <!-- Formulario de PayPal -->
        <div id="paypalForm" class="payment-form hidden">
          <div class="paypal-info">
            <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" alt="PayPal Logo" class="paypal-logo">
            <p>Serás redirigido a PayPal para completar tu pago de forma segura.</p>
          </div>
          
          <div class="checkout-summary">
            <h4>Resumen de compra</h4>
            <div id="checkout-items-paypal">
              <!-- Los items se cargarán dinámicamente -->
            </div>
            <div class="checkout-total">
              <p>Total: <span id="checkout-total-amount-paypal">$0.00</span></p>
            </div>
          </div>
          
          <button type="button" id="paypalRedirectBtn" class="submit-btn paypal-btn">
            <i class="fab fa-paypal"></i> Pagar con PayPal
          </button>
        </div>
      </div>
      
      <div class="modal-footer checkout-footer">
        <p><i class="fas fa-shield-alt"></i> Tus datos están protegidos con encriptación SSL de 256 bits</p>
      </div>
    </div>
  </div>
  
  <!-- Modal de Éxito -->
  <div class="modal-overlay" id="successModal">
    <div class="modal-container">
      <div class="modal-header">
        <h3 class="modal-title">¡Compra Exitosa!</h3>
        <button class="modal-close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body">
        <div class="success-content">
          <div class="success-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <h2>¡Pago Exitoso!</h2>
          <p>Tu transacción ha sido procesada correctamente.</p>
          <p>Ahora puedes disfrutar de tu música en la sección "Mi Biblioteca".</p>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-primary" id="continue-shopping">Seguir Comprando</button>
        <a href="index.php?page=biblioteca" class="btn-secondary">Ir a Mi Biblioteca</a>
      </div>
    </div>
  </div>
</section>

<style>
.store-container {
  display: flex;
  gap: 30px;
  margin-top: 20px;
}

.store-sidebar {
  width: 300px;
  flex-shrink: 0;
}

.store-content {
  flex: 1;
}

.search-filters, .store-categories, .store-cart-summary {
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  border: 1px solid var(--border-color);
}

.search-filters h3, .store-categories h3, .store-cart-summary h3 {
  margin-top: 0;
  margin-bottom: 15px;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 10px;
}

.filter-group {
  margin-bottom: 15px;
}

.filter-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}

.filter-group input, .filter-group select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  background: var(--bg-card);
  color: var(--text-color);
}

.filter-btn {
  width: 100%;
  margin-bottom: 10px;
}

.store-categories ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.store-categories li {
  margin-bottom: 10px;
}

.store-categories a {
  display: block;
  padding: 10px;
  border-radius: 8px;
  background: var(--bg-card);
  color: var(--text-color);
  text-decoration: none;
  transition: all 0.3s ease;
}

.store-categories a:hover {
  background: var(--primary-color);
  color: var(--bg-color);
  transform: translateY(-3px);
}

.store-cart-summary {
  text-align: center;
}

#cart-summary {
  margin-bottom: 15px;
}

.view-cart-btn {
  width: 100%;
}

.store-section {
  margin-bottom: 40px;
}

.store-section h2 {
  margin-top: 0;
  margin-bottom: 20px;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 10px;
}

.song-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
}

.song-card {
  background: var(--bg-secondary);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  border: 1px solid var(--border-color);
  position: relative;
}

.song-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.song-cover {
  position: relative;
  width: 100%;
  padding-top: 100%; /* 1:1 Aspect Ratio */
  overflow: hidden;
}

.song-cover img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.song-actions {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 15px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.song-cover:hover .song-actions {
  opacity: 1;
}

.preview-btn, .add-to-cart-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--primary-color);
  color: var(--bg-color);
  border: none;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.preview-btn:hover, .add-to-cart-btn:hover {
  transform: scale(1.1);
  background: var(--primary-hover);
}

.song-info {
  padding: 15px;
}

.song-title {
  margin: 0 0 5px 0;
  font-size: 1rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.song-artist, .song-genre {
  margin: 0 0 5px 0;
  font-size: 0.9rem;
  color: var(--text-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.song-price {
  display: flex;
  align-items: center;
  gap: 10px;
}

.regular-price {
  font-weight: 700;
  color: var(--primary-color);
}

.song-type-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: bold;
  text-transform: uppercase;
  color: white;
}

.song-type-badge.cancion {
  background-color: #1db954;
}

.song-type-badge.sencillo {
  background-color: #ff7700;
}

.song-type-badge.album {
  background-color: #e91e63;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  background: var(--bg-secondary);
  border-radius: 12px;
  border: 1px solid var(--border-color);
}

.search-results {
  margin-bottom: 40px;
}

.search-results h2 {
  margin-top: 0;
  margin-bottom: 10px;
}

.search-results p {
  margin-top: 0;
  margin-bottom: 20px;
  color: var(--text-secondary);
}

/* Modal Styles */
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

.preview-content {
  display: flex;
  gap: 20px;
}

.preview-cover {
  width: 200px;
  height: 200px;
  border-radius: 10px;
  overflow: hidden;
}

.preview-cover img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.preview-info {
  flex: 1;
}

.preview-info h3 {
  margin-top: 0;
  margin-bottom: 10px;
}

.preview-info p {
  margin: 0 0 10px 0;
  color: var(--text-secondary);
}

.preview-player {
  margin: 20px 0;
}

.preview-player audio {
  width: 100%;
}

.preview-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-top: 20px;
}

/* Payment Methods */
.payment-methods {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
}

.payment-method {
  flex: 1;
  padding: 15px;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  background: var(--bg-secondary);
  color: var(--text-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.payment-method i {
  font-size: 1.5rem;
}

.payment-method.active {
  border-color: var(--primary-color);
  background: var(--primary-color);
  color: white;
}

.payment-form {
  margin-top: 20px;
}

.payment-form.hidden {
  display: none;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 600;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid var(--border-color);
  background: var(--bg-card);
  color: var(--text-color);
}

.form-row {
  display: flex;
  gap: 15px;
}

.form-row .form-group {
  flex: 1;
}

.input-icon {
  position: relative;
}

.input-icon i {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

.error-message {
  color: #e74c3c;
  font-size: 0.8rem;
  margin-top: 5px;
  display: block;
}

.submit-btn {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  border: none;
  background: var(--primary-color);
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 20px;
}

.submit-btn:hover {
  background: var(--primary-hover);
}

.paypal-logo {
  max-width: 150px;
  margin-bottom: 15px;
}

.paypal-info {
  text-align: center;
  margin-bottom: 20px;
}

.checkout-footer {
  justify-content: center;
}

.checkout-footer p {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.checkout-summary {
  background: var(--bg-card);
  border-radius: 8px;
  padding: 15px;
  margin: 20px 0;
}

.checkout-summary h4 {
  margin-top: 0;
  margin-bottom: 15px;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 10px;
}

.checkout-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
  padding-bottom: 10px;
  border-bottom: 1px solid var(--border-color);
}

.checkout-item:last-child {
  border-bottom: none;
}

.checkout-item-name {
  flex: 1;
}

.checkout-item-price {
  font-weight: 600;
}

.checkout-total {
  display: flex;
  justify-content: space-between;
  font-weight: 700;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid var(--border-color);
}

/* Success Modal */
.success-content {
  text-align: center;
  padding: 20px 0;
}

.success-icon {
  font-size: 4rem;
  color: var(--primary-color);
  margin-bottom: 20px;
}

.success-content h2 {
  margin-top: 0;
  margin-bottom: 15px;
}

.success-content p {
  margin-bottom: 10px;
  color: var(--text-secondary);
}

@media (max-width: 768px) {
  .store-container {
    flex-direction: column;
  }
  
  .store-sidebar {
    width: 100%;
  }
  
  .preview-content {
    flex-direction: column;
    align-items: center;
  }
  
  .preview-info {
    width: 100%;
    text-align: center;
  }
  
  .form-row {
    flex-direction: column;
    gap: 0;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Variables globales
  let cartItems = [];
  let cartTotal = 0;
  let previewItemId = null;
  let previewItemType = null;
  
  // Elementos DOM
  const clearFiltersBtn = document.getElementById('clear-filters');
  const clearSearchBtn = document.getElementById('clear-search');
  const previewBtns = document.querySelectorAll('.preview-btn');
  const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
  const previewModal = document.getElementById('preview-modal');
  const checkoutModal = document.getElementById('checkout-modal');
  const successModal = document.getElementById('successModal');
  const closePreviewBtn = document.getElementById('close-preview');
  const addToCartPreviewBtn = document.getElementById('add-to-cart-preview');
  const previewAudio = document.getElementById('preview-audio');
  const cartSummary = document.getElementById('cart-summary');
  const creditCardBtn = document.getElementById('creditCardBtn');
  const paypalBtn = document.getElementById('paypalBtn');
  const creditCardForm = document.getElementById('creditCardForm');
  const paypalForm = document.getElementById('paypalForm');
  const paypalRedirectBtn = document.getElementById('paypalRedirectBtn');
  const continueShoppingBtn = document.getElementById('continue-shopping');
  
  // Inicializar
  initStore();
  
  // Función para inicializar la tienda
  function initStore() {
    // Cargar carrito desde el servidor
    loadCart();
    
    // Actualizar resumen del carrito
    updateCartSummary();
    
    // Configurar eventos
    if (clearFiltersBtn) {
      clearFiltersBtn.addEventListener('click', clearFilters);
    }
    
    if (clearSearchBtn) {
      clearSearchBtn.addEventListener('click', clearSearch);
    }
    
    previewBtns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const songId = this.dataset.id;
        const songType = this.dataset.type;
        previewItem(songId, songType);
      });
    });
    
    addToCartBtns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const songId = this.dataset.id;
        const songType = this.dataset.type;
        addToCart(songId, songType);
      });
    });
    
    if (closePreviewBtn) {
      closePreviewBtn.addEventListener('click', closePreview);
    }
    
    if (previewModal) {
      previewModal.querySelector('.modal-close').addEventListener('click', closePreview);
    }
    
    if (addToCartPreviewBtn) {
      addToCartPreviewBtn.addEventListener('click', function() {
        if (previewItemId && previewItemType) {
          addToCart(previewItemId, previewItemType);
          closePreview();
        }
      });
    }
    
    // Configurar eventos para el checkout
    if (creditCardBtn) {
      creditCardBtn.addEventListener('click', function() {
        creditCardBtn.classList.add('active');
        paypalBtn.classList.remove('active');
        creditCardForm.classList.remove('hidden');
        paypalForm.classList.add('hidden');
      });
    }
    
    if (paypalBtn) {
      paypalBtn.addEventListener('click', function() {
        paypalBtn.classList.add('active');
        creditCardBtn.classList.remove('active');
        paypalForm.classList.remove('hidden');
        creditCardForm.classList.add('hidden');
      });
    }
    
    // Configurar formulario de tarjeta de crédito
    if (creditCardForm) {
      creditCardForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateCreditCardForm()) {
          processPayment('credit_card');
        }
      });
    }
    
    // Configurar botón de PayPal
    if (paypalRedirectBtn) {
      paypalRedirectBtn.addEventListener('click', function() {
        processPayment('paypal');
      });
    }
    
    // Configurar modal de éxito
    if (continueShoppingBtn) {
      continueShoppingBtn.addEventListener('click', function() {
        successModal.classList.remove('active');
      });
    }
    
    if (successModal) {
      successModal.querySelector('.modal-close').addEventListener('click', function() {
        successModal.classList.remove('active');
      });
    }
    
    // Detener reproducción al cerrar el modal
    if (previewModal) {
      previewModal.addEventListener('click', function(e) {
        if (e.target === previewModal) {
          closePreview();
        }
      });
    }
    
    // Formatear campos de tarjeta
    const cardNumber = document.getElementById('cardNumber');
    const expiryDate = document.getElementById('expiryDate');
    const cvv = document.getElementById('cvv');
    
    if (cardNumber) {
      cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = '';
        
        for (let i = 0; i < value.length; i++) {
          if (i > 0 && i % 4 === 0) {
            formattedValue += ' ';
          }
          formattedValue += value[i];
        }
        
        e.target.value = formattedValue;
      });
    }
    
    if (expiryDate) {
      expiryDate.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length > 0) {
          if (value.length <= 2) {
            e.target.value = value;
          } else {
            e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4);
          }
        }
      });
    }
    
    if (cvv) {
      cvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
      });
    }
  }
  
  // Función para limpiar filtros
  function clearFilters() {
    window.location.href = 'index.php?page=music-store';
  }
  
  // Función para limpiar búsqueda
  function clearSearch() {
    window.location.href = 'index.php?page=music-store';
  }
  
  // Función para previsualizar un elemento
  function previewItem(itemId, itemType) {
    // Guardar ID y tipo para añadir al carrito
    previewItemId = itemId;
    previewItemType = itemType;
    
    // Obtener información del elemento mediante AJAX
    let endpoint = '';
    if (itemType === 'cancion') {
      endpoint = `api/music.php?action=get_song&id=${itemId}`;
    } else if (itemType === 'sencillo') {
      endpoint = `api/music.php?action=get_sencillo&id=${itemId}`;
    }
    
    fetch(endpoint)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const item = itemType === 'cancion' ? data.song : data.sencillo;
          
          // Actualizar modal con información del elemento
          document.getElementById('preview-title').textContent = item.titulo || item.nombre_cancion || item.nombre_sencillo;
          document.getElementById('preview-artist').textContent = item.artista || item.artista_nombre;
          document.getElementById('preview-genre').textContent = item.genero || item.nombre_genero;
          document.getElementById('preview-cover-img').src = item.portada || item.imagen_album_path || item.imagen_sencillo_path || 'assets/img/default-cover.png';
          
          // Actualizar precio
          document.getElementById('preview-price-display').textContent = `$${parseFloat(item.precio).toFixed(2)}`;
          
          // Configurar audio
          previewAudio.src = item.audio_path || item.cancion_path;
          previewAudio.play().catch(error => {
            console.error('Error al reproducir:', error);
            showToast('No se pudo reproducir la previsualización', 'error');
          });
          
          // Mostrar modal
          previewModal.classList.add('active');
        } else {
          showToast(data.message || 'Error al obtener información del elemento', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error al comunicarse con el servidor', 'error');
      });
  }
  
  // Función para cerrar la previsualización
  function closePreview() {
    previewModal.classList.remove('active');
    previewAudio.pause();
    previewAudio.src = '';
    previewItemId = null;
    previewItemType = null;
  }
  
  // Función para añadir al carrito
  function addToCart(itemId, itemType) {
    // Verificar si el usuario está logueado
    <?php if (!$is_logged_in): ?>
      showToast('Debes iniciar sesión para añadir elementos al carrito', 'error');
      return;
    <?php endif; ?>
    
    // Enviar solicitud AJAX para añadir al carrito
    fetch('api/cart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=add_to_cart&id_producto=${itemId}&tipo_producto=${itemType}`,
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('Elemento añadido al carrito', 'success');
          
          // Actualizar carrito
          loadCart();
        } else {
          showToast(data.message || 'Error al añadir el elemento al carrito', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error al comunicarse con el servidor', 'error');
      });
  }
  
  // Función para cargar el carrito
  function loadCart() {
    <?php if (!$is_logged_in): ?>
      return;
    <?php endif; ?>
    
    fetch('api/cart.php?action=get_cart')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          cartItems = data.items || [];
          cartTotal = data.total || 0;
          updateCartSummary();
        }
      })
      .catch(error => {
        console.error('Error al cargar el carrito:', error);
      });
  }
  
  // Función para actualizar el resumen del carrito
  function updateCartSummary() {
    if (cartSummary) {
      cartSummary.innerHTML = `
        <p>${cartItems.length} item${cartItems.length !== 1 ? 's' : ''} en el carrito</p>
        <p>Total: $${parseFloat(cartTotal).toFixed(2)}</p>
      `;
    }
  }
  
  // Función para mostrar el modal de checkout
  function showCheckout() {
    // Cargar items del carrito en el resumen
    const checkoutItems = document.getElementById('checkout-items');
    const checkoutItemsPaypal = document.getElementById('checkout-items-paypal');
    const checkoutTotalAmount = document.getElementById('checkout-total-amount');
    const checkoutTotalAmountPaypal = document.getElementById('checkout-total-amount-paypal');
    
    if (checkoutItems && checkoutItemsPaypal) {
      let itemsHtml = '';
      
      cartItems.forEach(item => {
        itemsHtml += `
          <div class="checkout-item">
            <div class="checkout-item-name">${item.nombre}</div>
            <div class="checkout-item-price">$${parseFloat(item.precio).toFixed(2)}</div>
          </div>
        `;
      });
      
      checkoutItems.innerHTML = itemsHtml;
      checkoutItemsPaypal.innerHTML = itemsHtml;
      
      if (checkoutTotalAmount) {
        checkoutTotalAmount.textContent = `$${parseFloat(cartTotal).toFixed(2)}`;
      }
      
      if (checkoutTotalAmountPaypal) {
        checkoutTotalAmountPaypal.textContent = `$${parseFloat(cartTotal).toFixed(2)}`;
      }
    }
    
    // Mostrar modal
    checkoutModal.classList.add('active');
  }
  
  // Función para validar el formulario de tarjeta de crédito
  function validateCreditCardForm() {
    let isValid = true;
    
    // Validar número de tarjeta
    const cardNumber = document.getElementById('cardNumber');
    const cardNumberError = document.getElementById('cardNumberError');
    
    if (cardNumber && cardNumberError) {
      const cardNumberValue = cardNumber.value.replace(/\s+/g, '');
      if (!/^\d{16}$/.test(cardNumberValue)) {
        cardNumberError.textContent = 'Número de tarjeta inválido. Debe tener 16 dígitos.';
        isValid = false;
      } else {
        cardNumberError.textContent = '';
      }
    }
    
    // Validar fecha de expiración
    const expiryDate = document.getElementById('expiryDate');
    const expiryDateError = document.getElementById('expiryDateError');
    
    if (expiryDate && expiryDateError) {
      if (!/^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(expiryDate.value)) {
        expiryDateError.textContent = 'Formato inválido. Use MM/AA.';
        isValid = false;
      } else {
        const [month, year] = expiryDate.value.split('/');
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;
        
        if (
          Number.parseInt(year) < currentYear ||
          (Number.parseInt(year) === currentYear && Number.parseInt(month) < currentMonth)
        ) {
          expiryDateError.textContent = 'La tarjeta ha expirado.';
          isValid = false;
        } else {
          expiryDateError.textContent = '';
        }
      }
    }
    
    // Validar CVV
    const cvv = document.getElementById('cvv');
    const cvvError = document.getElementById('cvvError');
    
    if (cvv && cvvError) {
      if (!/^\d{3}$/.test(cvv.value)) {
        cvvError.textContent = 'CVV inválido. Debe tener 3 dígitos.';
        isValid = false;
      } else {
        cvvError.textContent = '';
      }
    }
    
    // Validar nombre en la tarjeta
    const cardName = document.getElementById('cardName');
    const cardNameError = document.getElementById('cardNameError');
    
    if (cardName && cardNameError) {
      if (cardName.value.trim().length < 3) {
        cardNameError.textContent = 'Por favor, ingrese el nombre completo.';
        isValid = false;
      } else {
        cardNameError.textContent = '';
      }
    }
    
    return isValid;
  }
  
  // Función para procesar el pago
  function processPayment(paymentMethod) {
    // Simular procesamiento de pago
    showToast('Procesando pago...', 'info');
    
    // En un caso real, aquí se enviaría la información a un procesador de pagos
    setTimeout(() => {
      // Cerrar modal de checkout
      checkoutModal.classList.remove('active');
      
      // Registrar la compra en la base de datos
      registerPurchase(paymentMethod);
    }, 2000);
  }
  
  // Función para registrar la compra
  function registerPurchase(paymentMethod) {
    fetch('api/cart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=checkout&payment_method=${paymentMethod}`,
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Mostrar modal de éxito
          successModal.classList.add('active');
          
          // Actualizar carrito
          loadCart();
        } else {
          showToast(data.message || 'Error al procesar la compra', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Error al comunicarse con el servidor', 'error');
      });
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

<?php include_once '../includes/footer.php'; ?>
