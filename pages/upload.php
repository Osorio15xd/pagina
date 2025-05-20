<?php
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Obtener géneros musicales
$stmt = $conn->query("SELECT id, nombre FROM generos ORDER BY nombre");
$generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener artistas
$stmt = $conn->query("SELECT id, nombre FROM artistas ORDER BY nombre");
$artistas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de subida
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_music'])) {
    $titulo = trim($_POST['titulo']);
    $artista_id = intval($_POST['artista']);
    $genero_id = intval($_POST['genero']);
    $precio = floatval($_POST['precio']);
    
    if (empty($titulo) || $artista_id <= 0 || $genero_id <= 0) {
        $mensaje = '<div class="alert alert-danger">Por favor, completa todos los campos obligatorios.</div>';
    } else {
        // Asegurar que los directorios de uploads existan
        $covers_dir = 'uploads/covers/';
        $audio_dir = 'uploads/audio/';
        
        // Crear directorios si no existen
        if (!file_exists($covers_dir)) {
            mkdir($covers_dir, 0777, true);
        }
        
        if (!file_exists($audio_dir)) {
            mkdir($audio_dir, 0777, true);
        }
        
        // Verificar si se ha subido una portada
        $portada = '';
        if (isset($_FILES['portada']) && $_FILES['portada']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            
            if (!in_array($_FILES['portada']['type'], $allowed_types)) {
                $mensaje = '<div class="alert alert-danger">Tipo de archivo no permitido para la portada. Solo se permiten imágenes JPEG, PNG y GIF.</div>';
            } else {
                // Generar nombre único para el archivo
                $filename = uniqid() . '_' . $_FILES['portada']['name'];
                $upload_path = $covers_dir . $filename;
                
                if (move_uploaded_file($_FILES['portada']['tmp_name'], $upload_path)) {
                    $portada = $upload_path;
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al subir la portada. Verifica los permisos del directorio.</div>';
                }
            }
        } else {
            // Usar portada por defecto
            $portada = 'assets/img/placeholder.svg';
        }
        
        // Verificar si se ha subido un archivo de audio
        $archivo_audio = '';
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg'];
            
            // Permitir más tipos MIME comunes para archivos de audio
            $mime_type = $_FILES['audio']['type'];
            if (!in_array($mime_type, $allowed_types) && 
                !strpos($mime_type, 'audio/') === 0) { // Aceptar cualquier tipo que comience con 'audio/'
                $mensaje = '<div class="alert alert-danger">Tipo de archivo no permitido para el audio. Solo se permiten archivos MP3, WAV y OGG.</div>';
            } else {
                // Generar nombre único para el archivo
                $filename = uniqid() . '_' . $_FILES['audio']['name'];
                $upload_path = $audio_dir . $filename;
                
                if (move_uploaded_file($_FILES['audio']['tmp_name'], $upload_path)) {
                    $archivo_audio = $upload_path;
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al subir el archivo de audio. Verifica los permisos del directorio.</div>';
                }
            }
        } else if (isset($_POST['audio_url']) && !empty($_POST['audio_url'])) {
            // Usar URL de audio si se proporciona
            $archivo_audio = $_POST['audio_url'];
        } else {
            $mensaje = '<div class="alert alert-danger">Por favor, sube un archivo de audio o proporciona una URL.</div>';
            $archivo_audio = '';
        }
        
        // Si no hay errores, insertar la canción
        if (empty($mensaje) && !empty($archivo_audio)) {
            try {
                $stmt = $conn->prepare("INSERT INTO canciones (titulo, artista_id, genero_id, portada, archivo_audio, precio, fecha_subida) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                
                if ($stmt->execute([$titulo, $artista_id, $genero_id, $portada, $archivo_audio, $precio])) {
                    $mensaje = '<div class="alert alert-success">Canción subida correctamente.</div>';
                    
                    // Añadir a la biblioteca del usuario si está logueado
                    if (isset($_SESSION['user_id'])) {
                        $cancion_id = $conn->lastInsertId();
                        $stmt = $conn->prepare("INSERT INTO biblioteca_usuario (usuario_id, cancion_id) VALUES (?, ?)");
                        $stmt->execute([$_SESSION['user_id'], $cancion_id]);
                    }
                    
                    // Limpiar el formulario después de una subida exitosa
                    $_POST = array();
                } else {
                    $mensaje = '<div class="alert alert-danger">Error al subir la canción. Inténtalo de nuevo.</div>';
                }
            } catch (PDOException $e) {
                $mensaje = '<div class="alert alert-danger">Error de base de datos: ' . $e->getMessage() . '</div>';
            }
        }
    }
}
?>

<section id="page-upload-music" class="page active" aria-label="Subir álbum o sencillo">
  <h1>Subir Música</h1>
  
  <?php echo $mensaje; ?>
  
  <div class="upload-container">
    <div class="upload-sidebar">
      <div class="upload-steps">
        <div class="upload-step active">
          <div class="step-number">1</div>
          <div class="step-info">
            <div class="step-title">Información básica</div>
            <div class="step-desc">Título, artista y género</div>
          </div>
        </div>
        <div class="upload-step">
          <div class="step-number">2</div>
          <div class="step-info">
            <div class="step-title">Archivos</div>
            <div class="step-desc">Portada y archivo de audio</div>
          </div>
        </div>
        <div class="upload-step">
          <div class="step-number">3</div>
          <div class="step-info">
            <div class="step-title">Detalles</div>
            <div class="step-desc">Precio y metadatos</div>
          </div>
        </div>
      </div>
      
      <div class="upload-tips">
        <h3><i class="fas fa-lightbulb"></i> Consejos</h3>
        <ul>
          <li>Utiliza archivos de audio de alta calidad (MP3 a 320kbps o superior).</li>
          <li>Las portadas deben tener un tamaño mínimo de 500x500 píxeles.</li>
          <li>Asegúrate de tener los derechos para distribuir la música que subes.</li>
          <li>Proporciona información precisa para ayudar a los usuarios a encontrar tu música.</li>
        </ul>
      </div>
    </div>
    
    <div class="upload-content">
      <form id="upload-music-form" method="POST" action="" enctype="multipart/form-data">
        <div class="upload-section active" id="section-1">
          <h2>Información básica</h2>
          
          <div class="form-group">
            <label for="music-title">Título de la canción <span class="required">*</span></label>
            <input type="text" id="music-title" name="titulo" required placeholder="Ej. Mi canción increíble" />
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="music-artist">Artista <span class="required">*</span></label>
              <select id="music-artist" name="artista" required>
                <option value="">Selecciona un artista</option>
                <?php foreach ($artistas as $artista): ?>
                  <option value="<?php echo $artista['id']; ?>"><?php echo $artista['nombre']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="music-genre">Género <span class="required">*</span></label>
              <select id="music-genre" name="genero" required>
                <option value="">Selecciona un género</option>
                <?php foreach ($generos as $genero): ?>
                  <option value="<?php echo $genero['id']; ?>"><?php echo $genero['nombre']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn-next">Siguiente <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
        
        <div class="upload-section" id="section-2">
          <h2>Archivos</h2>
          
          <div class="form-group">
            <label for="music-cover">Portada (imagen)</label>
            <div class="file-upload-container">
              <div class="file-upload-preview" id="cover-preview-container">
                <img src="assets/img/placeholder.svg" alt="Vista previa de portada" id="cover-preview-img">
              </div>
              <div class="file-upload-input">
                <input type="file" id="music-cover" name="portada" accept="image/*" />
                <label for="music-cover" class="file-upload-label">
                  <i class="fas fa-cloud-upload-alt"></i> Seleccionar imagen
                </label>
                <p class="file-upload-info">JPG, PNG o GIF. Tamaño recomendado: 500x500px.</p>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="music-file">Archivo de audio</label>
            <div class="file-upload-container">
              <div class="file-upload-preview" id="audio-preview-container">
                <i class="fas fa-music" id="audio-icon"></i>
                <div id="audio-preview"></div>
              </div>
              <div class="file-upload-input">
                <input type="file" id="music-file" name="audio" accept="audio/*" />
                <label for="music-file" class="file-upload-label">
                  <i class="fas fa-cloud-upload-alt"></i> Seleccionar audio
                </label>
                <p class="file-upload-info">MP3, WAV u OGG. Calidad recomendada: 320kbps.</p>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="music-url">O URL del archivo de audio</label>
            <input type="text" id="music-url" name="audio_url" placeholder="URL del archivo de audio (opcional)" />
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Anterior</button>
            <button type="button" class="btn-next">Siguiente <i class="fas fa-arrow-right"></i></button>
          </div>
        </div>
        
        <div class="upload-section" id="section-3">
          <h2>Detalles</h2>
          
          <div class="form-group">
            <label for="music-price">Precio <span class="required">*</span></label>
            <div class="price-input-container">
              <span class="price-symbol">$</span>
              <input type="number" id="music-price" name="precio" step="0.01" min="0" value="0.99" required />
            </div>
          </div>
          
          <div class="form-group">
            <label for="music-description">Descripción</label>
            <textarea id="music-description" name="descripcion" rows="3" placeholder="Describe tu canción (opcional)"></textarea>
          </div>
          
          <div class="form-group">
            <label>Etiquetas</label>
            <div class="tags-input-container">
              <input type="text" id="tag-input" placeholder="Añadir etiqueta y presionar Enter" />
              <div id="tags-container"></div>
              <input type="hidden" name="tags" id="tags-hidden" />
            </div>
            <p class="form-hint">Añade etiquetas para ayudar a los usuarios a encontrar tu música.</p>
          </div>
          
          <div class="form-actions">
            <button type="button" class="btn-prev"><i class="fas fa-arrow-left"></i> Anterior</button>
            <button type="submit" name="upload_music"><i class="fas fa-upload"></i> Subir</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

<style>
.upload-container {
  display: flex;
  gap: 30px;
  margin-top: 20px;
}

.upload-sidebar {
  width: 300px;
  flex-shrink: 0;
}

.upload-content {
  flex: 1;
}

.upload-steps {
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  border: 1px solid var(--border-color);
}

.upload-step {
  display: flex;
  align-items: center;
  padding: 15px 0;
  border-bottom: 1px solid var(--border-color);
  opacity: 0.6;
  transition: all 0.3s ease;
}

.upload-step:last-child {
  border-bottom: none;
}

.upload-step.active {
  opacity: 1;
}

.step-number {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: var(--primary-color);
  color: var(--bg-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  margin-right: 15px;
}

.step-title {
  font-weight: 600;
  margin-bottom: 5px;
}

.step-desc {
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.upload-tips {
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  border: 1px solid var(--border-color);
}

.upload-tips h3 {
  margin-top: 0;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.upload-tips ul {
  padding-left: 20px;
  margin: 0;
}

.upload-tips li {
  margin-bottom: 10px;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.upload-section {
  display: none;
  background: var(--bg-secondary);
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  border: 1px solid var(--border-color);
  animation: fadeIn 0.5s ease;
}

.upload-section.active {
  display: block;
}

.upload-section h2 {
  margin-top: 0;
  margin-bottom: 20px;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 10px;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 30px;
}

.btn-prev, .btn-next {
  padding: 10px 20px;
  border-radius: 20px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-prev {
  background: var(--bg-card);
  color: var(--text-color);
  border: 1px solid var(--border-color);
}

.btn-next {
  background: var(--primary-color);
  color: var(--bg-color);
  border: none;
}

.btn-prev:hover, .btn-next:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.required {
  color: var(--danger-color);
}

.file-upload-container {
  display: flex;
  gap: 20px;
  margin-top: 10px;
}

.file-upload-preview {
  width: 150px;
  height: 150px;
  background: var(--bg-card);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  border: 1px solid var(--border-color);
}

.file-upload-preview img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

#audio-icon {
  font-size: 3rem;
  color: var(--primary-color);
}

.file-upload-input {
  flex: 1;
}

.file-upload-input input[type="file"] {
  display: none;
}

.file-upload-label {
  display: inline-block;
  padding: 10px 20px;
  background: var(--primary-color);
  color: var(--bg-color);
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 600;
}

.file-upload-label:hover {
  background: var(--primary-hover);
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.file-upload-info {
  margin-top: 10px;
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.price-input-container {
  position: relative;
}

.price-symbol {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

.price-input-container input {
  padding-left: 30px;
}

.tags-input-container {
  margin-top: 10px;
}

#tags-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 10px;
}

.tag {
  background: var(--primary-color);
  color: var(--bg-color);
  padding: 5px 10px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.9rem;
}

.tag-remove {
  cursor: pointer;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
}

.form-hint {
  margin-top: 5px;
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border-radius: 8px;
}

.alert-danger {
  background-color: rgba(231, 76, 60, 0.2);
  border: 1px solid #e74c3c;
  color: #e74c3c;
}

.alert-success {
  background-color: rgba(46, 204, 113, 0.2);
  border: 1px solid #2ecc71;
  color: #2ecc71;
}

.upload-progress {
  height: 6px;
  width: 100%;
  background: var(--bg-card);
  border-radius: 3px;
  margin-top: 10px;
  overflow: hidden;
  display: none;
}

.upload-progress-bar {
  height: 100%;
  background: var(--primary-color);
  width: 0%;
  transition: width 0.3s ease;
}

.upload-status {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin-top: 5px;
  display: none;
}

@media (max-width: 768px) {
  .upload-container {
    flex-direction: column;
  }
  
  .upload-sidebar {
    width: 100%;
  }
  
  .file-upload-container {
    flex-direction: column;
    align-items: center;
  }
  
  .file-upload-input {
    width: 100%;
    text-align: center;
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Navigation between sections
  const sections = document.querySelectorAll('.upload-section');
  const steps = document.querySelectorAll('.upload-step');
  const nextButtons = document.querySelectorAll('.btn-next');
  const prevButtons = document.querySelectorAll('.btn-prev');
  
  nextButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Find current section
      const currentSection = document.querySelector('.upload-section.active');
      const currentIndex = Array.from(sections).indexOf(currentSection);
      
      // Validate required fields in current section
      const requiredFields = currentSection.querySelectorAll('[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value) {
          field.style.borderColor = 'var(--danger-color)';
          isValid = false;
        } else {
          field.style.borderColor = '';
        }
      });
      
      if (!isValid) {
        showToast('Por favor, completa todos los campos obligatorios.', 'error');
        return;
      }
      
      // Show next section
      if (currentIndex < sections.length - 1) {
        currentSection.classList.remove('active');
        sections[currentIndex + 1].classList.add('active');
        
        // Update steps
        steps[currentIndex].classList.remove('active');
        steps[currentIndex + 1].classList.add('active');
        
        // Scroll to top of the new section
        window.scrollTo({
          top: sections[currentIndex + 1].offsetTop - 100,
          behavior: 'smooth'
        });
      }
    });
  });
  
  prevButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Find current section
      const currentSection = document.querySelector('.upload-section.active');
      const currentIndex = Array.from(sections).indexOf(currentSection);
      
      // Show previous section
      if (currentIndex > 0) {
        currentSection.classList.remove('active');
        sections[currentIndex - 1].classList.add('active');
        
        // Update steps
        steps[currentIndex].classList.remove('active');
        steps[currentIndex - 1].classList.add('active');
        
        // Scroll to top of the new section
        window.scrollTo({
          top: sections[currentIndex - 1].offsetTop - 100,
          behavior: 'smooth'
        });
      }
    });
  });
  
  // Preview cover
  const coverInput = document.getElementById('music-cover');
  const coverPreviewImg = document.getElementById('cover-preview-img');
  
  if (coverInput) {
    coverInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        
        // Validar tamaño (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
          showToast('La imagen es demasiado grande. El tamaño máximo es 5MB.', 'error');
          this.value = '';
          return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
          coverPreviewImg.src = e.target.result;
        };
        
        reader.readAsDataURL(file);
      }
    });
  }
  
  // Preview audio
  const audioInput = document.getElementById('music-file');
  const audioPreview = document.getElementById('audio-preview');
  const audioIcon = document.getElementById('audio-icon');
  
  if (audioInput) {
    audioInput.addEventListener('change', function(e) {
      if (e.target.files.length > 0) {
        const file = e.target.files[0];
        
        // Validar tamaño (máximo 20MB)
        if (file.size > 20 * 1024 * 1024) {
          showToast('El archivo de audio es demasiado grande. El tamaño máximo es 20MB.', 'error');
          this.value = '';
          return;
        }
        
        const url = URL.createObjectURL(file);
        
        // Hide icon
        audioIcon.style.display = 'none';
        
        // Create preview
        audioPreview.innerHTML = `
          <audio controls style="width: 100%;">
            <source src="${url}" type="${file.type}">
            Tu navegador no soporta la reproducción de audio.
          </audio>
          <p style="margin-top: 5px; font-size: 0.8rem; color: var(--text-secondary);">
            ${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)
          </p>
        `;
      }
    });
  }
  
  // Tag management
  const tagInput = document.getElementById('tag-input');
  const tagsContainer = document.getElementById('tags-container');
  const tagsHidden = document.getElementById('tags-hidden');
  let tags = [];
  
  if (tagInput) {
    tagInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        
        const tag = this.value.trim();
        if (tag && !tags.includes(tag)) {
          tags.push(tag);
          updateTags();
          this.value = '';
        }
      }
    });
  }
  
  function updateTags() {
    // Update visual container
    tagsContainer.innerHTML = '';
    tags.forEach((tag, index) => {
      const tagElement = document.createElement('div');
      tagElement.className = 'tag';
      tagElement.innerHTML = `
        ${tag}
        <span class="tag-remove" data-index="${index}">×</span>
      `;
      tagsContainer.appendChild(tagElement);
    });
    
    // Update hidden field
    tagsHidden.value = JSON.stringify(tags);
    
    // Add events to remove tags
    document.querySelectorAll('.tag-remove').forEach(button => {
      button.addEventListener('click', function() {
        const index = parseInt(this.dataset.index);
        tags.splice(index, 1);
        updateTags();
      });
    });
  }
  
  // Submit form with progress
  const uploadForm = document.getElementById('upload-music-form');
  if (uploadForm) {
    uploadForm.addEventListener('submit', function(e) {
      // Form will be submitted normally, but we can add validation here
      const requiredFields = this.querySelectorAll('[required]');
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!field.value) {
          field.style.borderColor = 'var(--danger-color)';
          isValid = false;
        } else {
          field.style.borderColor = '';
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        showToast('Por favor, completa todos los campos obligatorios.', 'error');
        return;
      }
      
      // Check if audio file is selected or URL is provided
      const audioFile = document.getElementById('music-file').files[0];
      const audioUrl = document.getElementById('music-url').value.trim();
      
      if (!audioFile && !audioUrl) {
        e.preventDefault();
        showToast('Por favor, sube un archivo de audio o proporciona una URL.', 'error');
        return;
      }
      
      // Show loading state
      const submitBtn = this.querySelector('button[type="submit"]');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
      }
    });
  }
  
  // Function to show toast
  function showToast(message, type = 'info') {
    // Create toast element
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
    
    // Set colors based on type
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
    
    // Animate entry
    setTimeout(() => {
      toast.style.transform = 'translateX(-50%) translateY(0)';
    }, 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
      toast.style.transform = 'translateX(-50%) translateY(-100px)';
      setTimeout(() => {
        document.body.removeChild(toast);
      }, 300);
    }, 3000);
  }
});
</script>
