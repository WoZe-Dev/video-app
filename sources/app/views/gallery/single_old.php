<?php
ob_start();
$videoCount = count($galleryVideos);
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

<style>
/* CSS pour modal vidéo simple */
.simple-video-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(5px);
}

.simple-modal-content {
    background: white;
    padding: 20px;
    border-radius: 15px;
    max-width: 90%;
    max-height: 90%;
    width: 800px;
    position: relative;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
}

.simple-modal-close {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    background: none;
    border: none;
    color: #999;
    z-index: 10;
}

.simple-modal-close:hover {
    color: #000;
}

.simple-modal-video {
    width: 100%;
    max-width: 100%;
    height: auto;
    max-height: 70vh;
    border-radius: 10px;
    margin-bottom: 15px;
}

.simple-modal-info {
    text-align: center;
}

.simple-modal-info h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.2rem;
}

.simple-modal-controls-info {
    color: #666;
    font-size: 0.9rem;
    margin: 10px 0;
    line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
    .simple-modal-content {
        margin: 10px;
        padding: 15px;
        width: calc(100% - 20px);
    }
    
    .simple-modal-video {
        max-height: 60vh;
    }
}
</style>

<!-- Script pour forcer la redirection de openVideoModal -->
<script>
// S'assurer que openVideoModal redirige vers notre fonction simple
window.addEventListener('DOMContentLoaded', function() {
    // Redéfinir openVideoModal dès que possible
    window.openVideoModal = function(videoPath, title) {
        console.log('openVideoModal redirection forcée vers openSimpleVideoModal');
        if (typeof openSimpleVideoModal === 'function') {
            openSimpleVideoModal(videoPath, title);
        } else {
            console.error('openSimpleVideoModal non disponible');
        }
    };
});
</script>

<div class="streaming-gallery">
    <!-- Gallery header -->
    <div class="streaming-header">
        <div class="header-left">
            <a href="/gallery" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
            </a>
            <h1 class="gallery-title"><?php echo $title; ?></h1>
        </div>
        
        <div class="header-right">
            
            
            <div class="gallery-actions">
                
                <?php 
                use App\Middlewares\AuthMiddleware;
                if (AuthMiddleware::isAdmin()): ?>
                    <a href="/logout" class="action-button logout-button" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')" style="margin-right: 10px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                        </svg>
                    </a>
                <?php endif; ?>

                <!-- Dropdown Menu -->
                <div class="dropdown-menu">
                    <button class="action-button dropdown-button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                    <div class="dropdown-content">
                        <a href="/gallery/empty/<?php echo $galleryId; ?>" class="dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                            </svg>
                            Vider la galerie
                        </a>
                        <a href="/gallery/deletegallery/<?php echo $galleryId; ?>" class="dropdown-item delete-gallery">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                            </svg>
                            Supprimer la galerie
                        </a>
                    </div>
                </div>

                <button id="uploadButton" type="button" class="action-button upload-button">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                    </svg>
                    Ajouter Vidéo
                </button>
            </div>
        </div>
    </div>

    <?php if ($videoCount == 0) { ?>
        <div class="empty-gallery">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="#666">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <h3>Aucune vidéo dans cette galerie</h3>
            <p>Commencez par ajouter votre première vidéo</p>
            <button class="upload-button-empty" id="uploadButtonEmpty">
                Ajouter une vidéo
            </button>
        </div>
    <?php } else { ?>
        <div class="videos-grid">
            <?php foreach ($galleryVideos as $video) { ?>
                <div class="video-card" data-video-id="<?php echo $video->id; ?>">
                    <div class="video-thumbnail">
                        <video preload="metadata" muted>
                            <source src="<?php echo $video->video_path; ?>" type="<?php echo $video->mime_type ?? 'video/mp4'; ?>">
                            Votre navigateur ne supporte pas la lecture de cette vidéo.
                        </video>
                        <div class="video-overlay">
                            <button class="play-button" onclick="openSimpleVideoModal('<?php echo addslashes($video->video_path); ?>', '<?php echo addslashes(htmlspecialchars($video->caption)); ?>')">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="white">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </button>
                        </div>
                        <?php if ($video->duration): ?>
                            <div class="video-duration"><?php echo gmdate("i:s", $video->duration); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title"><?php echo htmlspecialchars($video->caption ?: 'Vidéo sans titre'); ?></h3>
                        <div class="video-meta">
                            <?php if ($video->file_size): ?>
                                <span class="video-size"><?php echo round($video->file_size / 1024 / 1024, 1); ?> MB</span>
                            <?php endif; ?>
                            <?php if ($video->duration): ?>
                                <span class="video-length"><?php echo gmdate("i:s", $video->duration); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="video-delete" onclick="deleteVideo(<?php echo $video->id; ?>)" title="Supprimer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                        </svg>
                    </button>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- Modal simple pour lecture vidéo -->
    <div id="simpleVideoModal" class="simple-video-modal">
        <div class="simple-modal-content">
            <button class="simple-modal-close" onclick="closeSimpleVideoModal()">&times;</button>
            <video id="simpleModalVideo" class="simple-modal-video" controls>
                <source id="simpleModalVideoSource" src="" type="video/mp4">
                Votre navigateur ne supporte pas la lecture de cette vidéo.
            </video>
            <div class="simple-modal-info">
                <h3 id="simpleModalVideoTitle">Titre de la vidéo</h3>
                <p class="simple-modal-controls-info">
                    <strong>Contrôles :</strong> Utilisez les contrôles natifs du navigateur pour lire, mettre en pause, ajuster le volume et passer en plein écran.
                </p>
            </div>
        </div>
    </div>

    <!-- Upload overlay -->
    <div id="uploadOverlay" class="upload-overlay">
        <div class="upload-popup">
            <div class="upload-header">
                <h3>Upload en cours...</h3>
                <button class="close-upload" onclick="closeUploadPopup()">&times;</button>
            </div>
            <div class="upload-content">
                <div class="upload-file-info">
                    <span id="uploadFileName">fichier.mp4</span>
                    <span id="uploadFileSize">0 MB</span>
                </div>
                <div class="progress-bar">
                    <div id="progressFill" class="progress-fill"></div>
                </div>
                <div class="upload-stats">
                    <span id="uploadProgress">0%</span>
                    <span id="uploadSpeed">0 MB/s</span>
                    <span id="uploadTimeRemaining">--:--</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Champ de fichier caché pour l'upload -->
    <input type="file" id="fileInput" name="files[]" style="display: none;" multiple
        accept="video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/webm,video/ogg"
        data-gallery-id="<?= $galleryId ?>">
</div>

<script>
// Fonctions simples pour le modal vidéo
function openSimpleVideoModal(videoPath, title) {
    console.log('Ouverture de la vidéo:', videoPath, title);
    
    const modal = document.getElementById('simpleVideoModal');
    const video = document.getElementById('simpleModalVideo');
    const source = document.getElementById('simpleModalVideoSource');
    const titleElement = document.getElementById('simpleModalVideoTitle');
    
    if (modal && video && source && titleElement) {
        // Configurer la source vidéo
        source.src = videoPath;
        titleElement.textContent = title || 'Vidéo sans titre';
        
        // Recharger la vidéo et afficher le modal
        video.load();
        modal.style.display = 'flex';
        
        console.log('Modal ouverte avec succès');
        
        // Pause toutes les autres vidéos sur la page
        document.querySelectorAll('video').forEach(v => {
            if (v !== video) {
                v.pause();
            }
        });
        
        // Focus sur la vidéo pour l'accessibilité
        setTimeout(() => {
            video.focus();
        }, 100);
    } else {
        console.error('Éléments manquants pour le modal:', {modal, video, source, titleElement});
        alert('Erreur: Impossible d\'ouvrir la vidéo');
    }
}

function closeSimpleVideoModal() {
    const modal = document.getElementById('simpleVideoModal');
    const video = document.getElementById('simpleModalVideo');
    
    if (modal && video) {
        video.pause();
        video.currentTime = 0;
        modal.style.display = 'none';
        console.log('Modal fermée');
    }
}

// Fermer le modal en cliquant en dehors
document.addEventListener('click', function(event) {
    const modal = document.getElementById('simpleVideoModal');
    if (event.target === modal) {
        closeSimpleVideoModal();
    }
});

// Fermer le modal avec la touche Échap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSimpleVideoModal();
    }
});

// Debug au chargement
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page galerie chargée - Version simple');
    console.log('Fonction openSimpleVideoModal disponible:', typeof openSimpleVideoModal);
    
    const modal = document.getElementById('simpleVideoModal');
    console.log('Modal simple trouvée:', !!modal);
    
    const overlays = document.querySelectorAll('.video-overlay');
    console.log('Nombre d\'overlays trouvés:', overlays.length);
    
    // Vérifier que chaque overlay a un bouton cliquable
    overlays.forEach((overlay, index) => {
        const button = overlay.querySelector('.play-button');
        console.log(`Overlay ${index}: bouton trouvé:`, !!button);
    });
});

// Fonction de suppression de vidéo
function deleteVideo(videoId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')) {
        window.location.href = '/gallery/delete/' + videoId;
    }
}
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/gallery-simple.php';
