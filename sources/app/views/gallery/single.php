<?php
ob_start();
$videoCount = count($galleryVideos);
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

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
                        <video preload="metadata">
                            <source src="<?php echo $video->video_path; ?>" type="<?php echo $video->mime_type ?? 'video/mp4'; ?>">
                        </video>
                        <div class="video-overlay">
                            <button class="play-button" onclick="openVideoModal('<?php echo $video->video_path; ?>', '<?php echo htmlspecialchars($video->caption); ?>')">
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

    <!-- Modal pour lecture vidéo -->
    <div id="videoModal" class="video-modal">
        <div class="video-modal-content">
            <button class="video-modal-close" onclick="closeVideoModal()">&times;</button>
            <div class="screencast-player" id="modalPlayer">
                <video class="screencast-video" id="modalVideo">
                    <source id="modalVideoSource" src="" type="video/mp4">
                </video>
                
                <!-- Overlay de lecture -->
                <div class="screencast-overlay">
                    <button class="overlay-play-btn">
                        <svg viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Contrôles screencast -->
                <div class="screencast-controls">
                    <div class="controls-row">
                        <button class="play-pause-btn">
                            <svg viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                        
                        <div class="progress-container">
                            <div class="progress-bar"></div>
                        </div>
                        
                        <div class="time-display">00:00 / 00:00</div>
                        
                        <div class="volume-container">
                            <button class="volume-btn">
                                <svg width="20" height="20" viewBox="0 0 24 24">
                                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                                </svg>
                            </button>
                            <input type="range" class="volume-slider" min="0" max="1" step="0.1" value="0.8">
                        </div>
                        
                        <button class="fullscreen-btn" data-tooltip>
                            <svg width="20" height="20" viewBox="0 0 24 24">
                                <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                            </svg>
                            <div class="control-tooltip">Plein écran (F)</div>
                        </button>
                        
                        <button class="pip-btn" data-tooltip>
                            <svg width="20" height="20" viewBox="0 0 24 24">
                                <path d="M19 7h-8v6h8V7zm2-4H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14z"/>
                            </svg>
                            <div class="control-tooltip">Picture-in-Picture</div>
                        </button>
                    </div>
                    
                    <div class="screencast-extras">
                        <div class="seek-controls">
                            <button class="seek-btn" data-seek="-30">-30s</button>
                            <button class="seek-btn" data-seek="-10">-10s</button>
                            <button class="seek-btn" data-seek="10">+10s</button>
                            <button class="seek-btn" data-seek="30">+30s</button>
                        </div>
                        
                        <div class="speed-controls">
                            <span style="color: white; font-size: 0.8rem; margin-right: 10px;">Vitesse:</span>
                            <button class="speed-btn" data-speed="0.5">0.5x</button>
                            <button class="speed-btn active" data-speed="1">1x</button>
                            <button class="speed-btn" data-speed="1.25">1.25x</button>
                            <button class="speed-btn" data-speed="1.5">1.5x</button>
                            <button class="speed-btn" data-speed="2">2x</button>
                        </div>
                        
                        <div class="quality-selector">
                            <button class="quality-btn">Auto</button>
                            <div class="quality-dropdown">
                                <div class="quality-option active">Auto</div>
                                <div class="quality-option">1080p</div>
                                <div class="quality-option">720p</div>
                                <div class="quality-option">480p</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="video-modal-info">
                <h3 id="modalVideoTitle"></h3>
                <div style="margin-top: 10px; color: #718096; font-size: 0.9rem;">
                    <strong>Raccourcis clavier:</strong> 
                    Espace (Play/Pause) • ← → (Saut 10s) • ↑ ↓ (Volume) • F (Plein écran) • M (Muet) • 1-5 (Vitesse)
                </div>
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

<?php
$content = ob_get_clean();
require_once __DIR__ . '../../layouts/base.php';
