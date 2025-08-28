<?php
ob_start();
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

<div class="streaming-gallery">
    <!-- Gallery header -->
    <div class="streaming-header">
        <div class="header-left">
            <?php if (!empty($breadcrumb)): ?>
                <?php 
                $parentUrl = '/gallery';
                if (count($breadcrumb) > 1) {
                    $parentIndex = count($breadcrumb) - 2;
                    $parentUrl = '/gallery/' . $breadcrumb[$parentIndex]['id'];
                }
                ?>
                <a href="<?php echo $parentUrl; ?>" class="back-button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </a>
            <?php else: ?>
                <a href="/gallery" class="back-button">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </a>
            <?php endif; ?>
            <h1 class="gallery-title"><?php echo $title; ?></h1>
        </div>
        
        <!-- Fil d'Ariane -->
        <?php if (!empty($breadcrumb)): ?>
            <nav class="breadcrumb">
                <a href="/gallery">Accueil</a>
                <?php foreach ($breadcrumb as $item): ?>
                    <span class="breadcrumb-separator">/</span>
                    <a href="/gallery/<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>
        
        <div class="header-right">
            <div class="gallery-actions">
                <?php 
                use App\Middlewares\AuthMiddleware;
                if (AuthMiddleware::isAdmin()): ?>
                    <!-- Bouton Créer sous-galerie -->
                    <a href="/gallery/create?parent=<?php echo $galleryId; ?>" class="action-button upload-button-empty">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z"/>
                        </svg>
                        Nouvelle Sous-galerie
                    </a>
                    
                    <!-- Bouton Importer vidéo -->
                    <button id="uploadButton" type="button" class="action-button upload-button-empty">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                        </svg>
                        Importer Vidéo
                    </button>
                    
                    <!-- Menu dropdown pour actions dangereuses -->
                    <div class="dropdown-menu">
                        <button class="action-button dropdown-button">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" class="dropdown-item delete-gallery" onclick="showDeleteConfirmation(<?php echo $galleryId; ?>, '<?php echo htmlspecialchars($gallery->name); ?>', <?php echo $hasChildren ? 'true' : 'false'; ?>)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                </svg>
                                Supprimer la galerie
                            </a>
                        </div>
                    </div>
                    
                    <a href="/logout" class="action-button logout-button" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                        </svg>
                        Déconnexion
                    </a>
                <?php else: ?>
                    <a href="/logout" class="action-button logout-button" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                        </svg>
                        Déconnexion
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenu de la galerie -->
    <div class="gallery-content">
        <!-- Sous-galeries -->
        <?php if (!empty($subgalleries)): ?>
            <div class="content-section">
                <h2 class="section-title">Sous-galeries</h2>
                <div class="galleries-grid">
                    <?php foreach ($subgalleries as $subgallery): ?>
                        <a href="/gallery/<?php echo $subgallery->gallery_id; ?>" class="gallery-card">
                            <div class="gallery-thumbnail">
                                <div class="gallery-placeholder">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="#666">
                                        <path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z"/>
                                    </svg>
                                    <p><?php echo $subgallery->total_count; ?> élément<?php echo $subgallery->total_count > 1 ? 's' : ''; ?></p>
                                </div>
                            </div>
                            <div class="gallery-info">
                                <h3 class="gallery-name"><?php echo htmlspecialchars($subgallery->gallery_name); ?></h3>
                                <div class="gallery-meta">
                                    <?php echo $subgallery->videos_count; ?> vidéo<?php echo $subgallery->videos_count > 1 ? 's' : ''; ?>
                                    <br>
                                    Créée le <?php echo date('d/m/Y', strtotime($subgallery->gallery_created_at)); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Vidéos -->
        <?php if (!empty($videos)): ?>
            
                
                <div class="videos-grid">
                    <?php foreach ($videos as $video): ?>
                        <div class="video-card" data-video-id="<?php echo $video->id; ?>">
                            <div class="video-thumbnail">
                                <video preload="metadata" poster="" muted>
                                    <source src="<?php echo $video->video_path; ?>" type="<?php echo $video->mime_type ?? 'video/mp4'; ?>">
                                    Votre navigateur ne supporte pas la lecture de cette vidéo.
                                </video>
                                <div class="video-overlay">
                                    <button class="play-button" onclick="openVideoModal('<?php echo $video->video_path; ?>', '<?php echo htmlspecialchars($video->caption); ?>')">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="white">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="video-fallback-info">
                                    <div class="video-icon">📹</div>
                                    <div class="video-format">MP4</div>
                                </div>
                                <?php if ($video->duration): ?>
                                    <div class="video-duration"><?php echo gmdate("i:s", $video->duration); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="video-info">
                                <h3 class="video-title"><?php echo htmlspecialchars($video->caption ?: 'Vidéo sans titre'); ?></h3>
                                <div class="video-meta">
                                    
                                    <?php if ($video->file_size): ?>
                                        <span class="video-size"><?php echo round($video->file_size / 1024 / 1024, 1); ?> MB</span> • 
                                    <?php endif; ?>
                                    <span class="video-date">Ajoutée le <?php echo date('d/m/Y', strtotime($video->created_at)); ?></span>
                                    <br>
                                    
                                    
                                    <?php if ($isAdmin): ?>
                                        <br>
                                        <a href="/gallery/delete-video/<?php echo $video->id; ?>" 
                                           class="delete-video-link" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')">
                                            Supprimer
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Message si galerie vide -->
        <?php if (empty($subgalleries) && empty($videos)): ?>
            <div class="empty-gallery">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="#666">
                    <path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z"/>
                </svg>
                <h3>Cette galerie est vide</h3>
                <p>Ajoutez des sous-galeries ou des vidéos pour commencer</p>
                <?php if ($isAdmin): ?>
                    <div class="empty-actions">
                        <a href="/gallery/create?parent=<?php echo $galleryId; ?>" class="upload-button-empty">
                            Créer une sous-galerie
                        </a>
                        <button class="button upload-button-empty" id="uploadButtonEmpty">
                            Importer une vidéo
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de lecture vidéo -->
<div id="videoModal" class="video-modal">
    <div class="video-modal-content">
        <span class="video-modal-close">&times;</span>
        <h3 id="videoModalTitle"></h3>
        <video id="videoModalPlayer" controls>
            <source id="videoModalSource" src="" type="video/mp4">
            Votre navigateur ne supporte pas la lecture vidéo.
        </video>
    </div>
</div>

<!-- Upload form (hidden) -->
<form id="uploadForm" enctype="multipart/form-data" style="display: none;">
    <input type="file" id="videoFile" name="files[]" multiple accept=".mp4,.mov,.avi">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</form>

<!-- Modal de progression d'upload -->
<div id="uploadProgressModal" class="upload-progress-modal" style="display: none;">
    <div class="upload-progress-content">
        <div class="upload-progress-header">
            <h3>📤 Upload des vidéos</h3>
            <button class="upload-close-btn" id="uploadCloseBtn" title="Fermer">&times;</button>
        </div>
        
        <div class="upload-progress-body">
            <div class="upload-stats">
                <div class="stat">
                    <span class="stat-label">File d'attente:</span>
                    <span id="queueCount">0</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Terminées:</span>
                    <span id="completedCount">0</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Échecs:</span>
                    <span id="failedCount">0</span>
                </div>
            </div>
            
            <div class="upload-queue" id="uploadQueue">
                <!-- Les items de la file d'attente seront ajoutés ici -->
            </div>
            
            <div class="upload-actions">
                <button id="cancelAllBtn" class="btn-secondary" style="display: none;">Annuler tout</button>
                <button id="retryFailedBtn" class="btn-primary" style="display: none;">Réessayer les échecs</button>
            </div>
        </div>
    </div>
</div>

<style>
.breadcrumb {
    margin: 10px 0;
    font-size: 14px;
    color: #666;
}

.breadcrumb a {
    color: #0066cc;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb-separator {
    margin: 0 8px;
    color: #999;
}

.content-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 20px;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.gallery-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 240px;
    background: #f5f5f5;
 
}

.video-path {
    font-style: italic;
    color: #666;
    font-size: 12px;
}

.delete-video-link {
    color: #dc3545;
    text-decoration: none;
    font-size: 12px;
}

.delete-video-link:hover {
    text-decoration: underline;
}

.empty-actions {
    margin-top: 20px;
}

.empty-actions .button {
    margin: 0 10px;
}

.create-subgallery-button {
    background: #28a745;
    margin-right: 10px;
}

.create-subgallery-button:hover {
    background: #218838;
}

.dropdown-menu {
    position: relative;
    display: inline-block;
    margin-left: 10px;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 200px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.dropdown-menu:hover .dropdown-content {
    display: block;
}

.dropdown-item {
    color: #333;
    padding: 12px 16px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dropdown-item:hover {
    background-color: #f1f1f1;
}

.delete-gallery {
    color: #dc3545 !important;
}

/* Styles pour la modal de progression d'upload */
.upload-progress-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-progress-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.upload-progress-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.upload-progress-header h3 {
    margin: 0;
    font-size: 1.2em;
}

.upload-close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.upload-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
}

.upload-progress-body {
    padding: 20px;
    max-height: 60vh;
    overflow-y: auto;
}

.upload-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
}

.stat-label {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.stat span:last-child {
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.upload-queue {
    max-height: 300px;
    overflow-y: auto;
}

.upload-item {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.upload-item.uploading {
    border-color: #007bff;
    background: #f8f9ff;
}

.upload-item.completed {
    border-color: #28a745;
    background: #f8fff8;
}

.upload-item.failed {
    border-color: #dc3545;
    background: #fff8f8;
}

.upload-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.upload-filename {
    font-weight: 500;
    color: #333;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.upload-status {
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.upload-status.waiting {
    background: #fff3cd;
    color: #856404;
}

.upload-status.uploading {
    background: #cce5ff;
    color: #004085;
}

.upload-status.completed {
    background: #d4edda;
    color: #155724;
}

.upload-status.failed {
    background: #f8d7da;
    color: #721c24;
}

.upload-progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
    position: relative;
}

.upload-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #0056b3);
    border-radius: 4px;
    transition: width 0.3s ease;
    width: 0%;
    position: relative;
    overflow: hidden;
}

.upload-progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.upload-progress-text {
    font-size: 12px;
    color: #666;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.upload-percentage {
    font-weight: bold;
    color: #007bff;
    min-width: 35px;
    text-align: center;
}

.upload-speed {
    font-size: 11px;
    color: #28a745;
    min-width: 60px;
    text-align: right;
}

.upload-actions {
    margin-top: 20px;
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-primary, .btn-secondary {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}
</style>

<script>
// Gestion de l'upload de fichiers avec file d'attente
document.addEventListener('DOMContentLoaded', function() {
    const uploadButton = document.getElementById('uploadButton');
    const uploadButtonEmpty = document.getElementById('uploadButtonEmpty');
    const uploadForm = document.getElementById('uploadForm');
    const videoFile = document.getElementById('videoFile');
    const uploadModal = document.getElementById('uploadProgressModal');
    const uploadQueue = document.getElementById('uploadQueue');
    const queueCountEl = document.getElementById('queueCount');
    const completedCountEl = document.getElementById('completedCount');
    const failedCountEl = document.getElementById('failedCount');
    const cancelAllBtn = document.getElementById('cancelAllBtn');
    const retryFailedBtn = document.getElementById('retryFailedBtn');
    const uploadCloseBtn = document.getElementById('uploadCloseBtn');

    let uploadManager = {
        queue: [],
        currentUpload: null,
        isUploading: false,
        completed: 0,
        failed: 0,
        abortControllers: new Map()
    };

    function triggerUpload() {
        videoFile.click();
    }

    if (uploadButton) {
        uploadButton.addEventListener('click', triggerUpload);
    }
    
    if (uploadButtonEmpty) {
        uploadButtonEmpty.addEventListener('click', triggerUpload);
    }

    // Gestionnaire de sélection de fichiers
    videoFile.addEventListener('change', function() {
        if (this.files.length > 0) {
            // Ajouter les fichiers à la file d'attente
            for (let i = 0; i < this.files.length; i++) {
                addToQueue(this.files[i]);
            }
            
            // Ouvrir la modal et commencer les uploads
            showUploadModal();
            startUploading();
            
            // Réinitialiser l'input
            this.value = '';
        }
    });

    function addToQueue(file) {
        const uploadItem = {
            id: Date.now() + Math.random(),
            file: file,
            status: 'waiting',
            progress: 0,
            error: null
        };
        
        uploadManager.queue.push(uploadItem);
        createUploadItemElement(uploadItem);
        updateStats();
    }

    function createUploadItemElement(uploadItem) {
        const itemEl = document.createElement('div');
        itemEl.className = 'upload-item';
        itemEl.id = `upload-item-${uploadItem.id}`;
        
        itemEl.innerHTML = `
            <div class="upload-item-header">
                <div class="upload-filename" title="${uploadItem.file.name}">${uploadItem.file.name}</div>
                <div class="upload-status waiting">En attente</div>
            </div>
            <div class="upload-progress-bar">
                <div class="upload-progress-fill"></div>
            </div>
            <div class="upload-progress-text">
                <span>Taille: ${formatFileSize(uploadItem.file.size)}</span>
                <span class="upload-percentage">0%</span>
                <span class="upload-speed"></span>
            </div>
        `;
        
        uploadQueue.appendChild(itemEl);
    }

    function updateUploadItemStatus(uploadItem, status, progress = null, error = null) {
        const itemEl = document.getElementById(`upload-item-${uploadItem.id}`);
        if (!itemEl) return;

        uploadItem.status = status;
        if (progress !== null) uploadItem.progress = progress;
        if (error !== null) uploadItem.error = error;

        // Mettre à jour les classes CSS
        itemEl.className = `upload-item ${status}`;
        
        // Mettre à jour le statut
        const statusEl = itemEl.querySelector('.upload-status');
        statusEl.className = `upload-status ${status}`;
        
        switch (status) {
            case 'waiting':
                statusEl.textContent = 'En attente';
                break;
            case 'uploading':
                statusEl.textContent = `Upload... ${progress}%`;
                break;
            case 'completed':
                statusEl.textContent = 'Terminé ✓';
                break;
            case 'failed':
                statusEl.textContent = 'Échec ✗';
                break;
        }
        
        // Mettre à jour la barre de progression
        const progressFill = itemEl.querySelector('.upload-progress-fill');
        const percentageEl = itemEl.querySelector('.upload-percentage');
        
        if (progressFill && progress !== null) {
            progressFill.style.width = `${progress}%`;
            
            // Animation de la barre de progression
            if (status === 'uploading') {
                progressFill.style.background = 'linear-gradient(90deg, #007bff, #0056b3)';
            } else if (status === 'completed') {
                progressFill.style.background = 'linear-gradient(90deg, #28a745, #1e7e34)';
            } else if (status === 'failed') {
                progressFill.style.background = 'linear-gradient(90deg, #dc3545, #c82333)';
            }
        }
        
        if (percentageEl) {
            percentageEl.textContent = `${progress || 0}%`;
        }
        
        // Afficher l'erreur si applicable
        if (error) {
            const speedEl = itemEl.querySelector('.upload-speed');
            if (speedEl) {
                speedEl.textContent = error;
                speedEl.style.color = '#dc3545';
            }
        }
    }

    function showUploadModal() {
        uploadModal.style.display = 'flex';
        cancelAllBtn.style.display = 'inline-block';
    }

    function hideUploadModal() {
        uploadModal.style.display = 'none';
    }

    function updateStats() {
        queueCountEl.textContent = uploadManager.queue.filter(item => item.status === 'waiting' || item.status === 'uploading').length;
        completedCountEl.textContent = uploadManager.completed;
        failedCountEl.textContent = uploadManager.failed;
        
        // Afficher le bouton de retry si il y a des échecs
        retryFailedBtn.style.display = uploadManager.failed > 0 ? 'inline-block' : 'none';
        
        // Masquer le bouton d'annulation si plus rien en cours
        if (uploadManager.queue.filter(item => item.status === 'waiting' || item.status === 'uploading').length === 0) {
            cancelAllBtn.style.display = 'none';
        }
    }

    async function startUploading() {
        if (uploadManager.isUploading) return;
        
        uploadManager.isUploading = true;
        
        while (uploadManager.queue.length > 0) {
            const waitingItem = uploadManager.queue.find(item => item.status === 'waiting');
            if (!waitingItem) break;
            
            uploadManager.currentUpload = waitingItem;
            await uploadFile(waitingItem);
        }
        
        uploadManager.isUploading = false;
        uploadManager.currentUpload = null;
        
        // Auto-fermeture après quelques secondes si tout est terminé
        if (uploadManager.failed === 0) {
            setTimeout(() => {
                if (uploadManager.queue.filter(item => item.status === 'waiting' || item.status === 'uploading').length === 0) {
                    location.reload(); // Recharger pour voir les nouvelles vidéos
                }
            }, 2000);
        }
    }

    async function uploadFile(uploadItem) {
        updateUploadItemStatus(uploadItem, 'uploading', 0);
        updateStats();
        
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            
            formData.append('files[]', uploadItem.file);
            formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
            
            // Gestionnaire de progression
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = Math.round((e.loaded / e.total) * 100);
                    updateUploadItemStatus(uploadItem, 'uploading', percentComplete);
                    
                    // Mettre à jour la vitesse d'upload
                    const itemEl = document.getElementById(`upload-item-${uploadItem.id}`);
                    if (itemEl) {
                        const speedEl = itemEl.querySelector('.upload-speed');
                        if (speedEl && uploadItem.startTime) {
                            const elapsed = (Date.now() - uploadItem.startTime) / 1000; // en secondes
                            const bytesPerSecond = e.loaded / elapsed;
                            speedEl.textContent = formatSpeed(bytesPerSecond);
                        }
                    }
                }
            });
            
            // Gestionnaire de réussite
            xhr.addEventListener('load', function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            updateUploadItemStatus(uploadItem, 'completed', 100);
                            uploadManager.completed++;
                            
                            // Retirer de la queue
                            uploadManager.queue = uploadManager.queue.filter(item => item.id !== uploadItem.id);
                            resolve();
                        } else {
                            throw new Error(data.message || 'Erreur inconnue du serveur');
                        }
                    } catch (e) {
                        reject(new Error('Réponse serveur invalide'));
                    }
                } else {
                    reject(new Error(`HTTP ${xhr.status}: ${xhr.statusText}`));
                }
            });
            
            // Gestionnaire d'erreur
            xhr.addEventListener('error', function() {
                reject(new Error('Erreur de connexion'));
            });
            
            // Gestionnaire d'annulation
            xhr.addEventListener('abort', function() {
                reject(new Error('Upload annulé'));
            });
            
            // Enregistrer le timestamp de début
            uploadItem.startTime = Date.now();
            
            // Stocker xhr pour pouvoir l'annuler
            uploadManager.abortControllers.set(uploadItem.id, xhr);
            
            // Démarrer l'upload
            xhr.open('POST', '/gallery/upload/<?php echo $galleryId; ?>');
            xhr.send(formData);
        }).catch(error => {
            if (error.message === 'Upload annulé') {
                updateUploadItemStatus(uploadItem, 'failed', 0, 'Annulé');
            } else {
                updateUploadItemStatus(uploadItem, 'failed', uploadItem.progress || 0, error.message);
            }
            uploadManager.failed++;
            uploadManager.abortControllers.delete(uploadItem.id);
            updateStats();
            throw error;
        });
    }

    function cancelAllUploads() {
        // Annuler tous les uploads en cours
        uploadManager.abortControllers.forEach((xhr, id) => {
            if (xhr && xhr.abort) {
                xhr.abort();
            }
        });
        uploadManager.abortControllers.clear();
        
        // Marquer tous les items en attente comme annulés
        uploadManager.queue.forEach(item => {
            if (item.status === 'waiting' || item.status === 'uploading') {
                updateUploadItemStatus(item, 'failed', 0, 'Annulé par l\'utilisateur');
                uploadManager.failed++;
            }
        });
        
        uploadManager.isUploading = false;
        uploadManager.currentUpload = null;
        updateStats();
    }

    function retryFailedUploads() {
        const failedItems = uploadManager.queue.filter(item => item.status === 'failed');
        
        failedItems.forEach(item => {
            updateUploadItemStatus(item, 'waiting', 0);
            uploadManager.failed--;
        });
        
        updateStats();
        startUploading();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function formatSpeed(bytesPerSecond) {
        if (bytesPerSecond === 0) return '0 KB/s';
        const k = 1024;
        const sizes = ['B/s', 'KB/s', 'MB/s', 'GB/s'];
        const i = Math.floor(Math.log(bytesPerSecond) / Math.log(k));
        return parseFloat((bytesPerSecond / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    // Event listeners pour les boutons de la modal
    uploadCloseBtn.addEventListener('click', hideUploadModal);
    cancelAllBtn.addEventListener('click', cancelAllUploads);
    retryFailedBtn.addEventListener('click', retryFailedUploads);

    // Fermer la modal en cliquant à l'extérieur
    uploadModal.addEventListener('click', function(e) {
        if (e.target === uploadModal) {
            hideUploadModal();
        }
    });
});

// Fonction pour ouvrir la modal vidéo
function openVideoModal(videoPath, title) {
    console.log('openVideoModal appelée avec:', videoPath, title);
    
    const modal = document.getElementById('videoModal');
    const modalTitle = document.getElementById('videoModalTitle');
    const modalSource = document.getElementById('videoModalSource');
    const modalPlayer = document.getElementById('videoModalPlayer');
    
    console.log('Éléments trouvés:', {
        modal: !!modal,
        modalTitle: !!modalTitle,
        modalSource: !!modalSource,
        modalPlayer: !!modalPlayer
    });
    
    if (!modal) {
        console.error('Modal videoModal non trouvée!');
        alert('Erreur: Modal non trouvée');
        return;
    }
    
    if (!modalTitle || !modalSource || !modalPlayer) {
        console.error('Éléments de la modal manquants');
        alert('Erreur: Éléments de la modal manquants');
        return;
    }
    
    modalTitle.textContent = title;
    modalSource.src = videoPath;
    modalPlayer.load();
    modal.classList.add('show');
    
    console.log('Modal ouverte avec succès');
}

// Fonction de test simplifié
function testVideoAccess() {
    console.log('Test d\'accès vidéo lancé');
    const testPath = '/uploads/gallery_videos/1/gallery_13/20250826212253_88bdbe9b8f38353d.mp4';
    openVideoModal(testPath, 'TEST - Vidéo de diagnostic');
}

// Fonction de debug avancé
function debugModal() {
    const modal = document.getElementById('videoModal');
    const modalTitle = document.getElementById('videoModalTitle');
    const modalSource = document.getElementById('videoModalSource');
    const modalPlayer = document.getElementById('videoModalPlayer');
    
    let debugInfo = 'DIAGNOSTIC MODAL:\n\n';
    debugInfo += `Modal element: ${modal ? 'TROUVÉ' : 'MANQUANT'}\n`;
    debugInfo += `Modal Title: ${modalTitle ? 'TROUVÉ' : 'MANQUANT'}\n`;
    debugInfo += `Modal Source: ${modalSource ? 'TROUVÉ' : 'MANQUANT'}\n`;
    debugInfo += `Modal Player: ${modalPlayer ? 'TROUVÉ' : 'MANQUANT'}\n\n`;
    
    if (modal) {
        debugInfo += `Modal classList: ${modal.classList.toString()}\n`;
        debugInfo += `Modal style.display: ${modal.style.display}\n`;
        debugInfo += `Modal computed display: ${window.getComputedStyle(modal).display}\n`;
    }
    
    alert(debugInfo);
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('videoModal');
    const closeBtn = document.querySelector('.video-modal-close');
    const modalPlayer = document.getElementById('videoModalPlayer');
    
    if (closeBtn) {
        closeBtn.onclick = function() {
            modal.classList.remove('show');
            modalPlayer.pause();
        }
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.classList.remove('show');
            modalPlayer.pause();
        }
    }
    
    // Gestion des miniatures de vidéo
    const videoThumbnails = document.querySelectorAll('.video-thumbnail video');
    videoThumbnails.forEach(video => {
        const fallbackInfo = video.parentElement.querySelector('.video-fallback-info');
        
        // Tenter de charger la vidéo pour générer une miniature
        video.addEventListener('loadedmetadata', function() {
            // Cacher le fallback si la vidéo se charge
            if (fallbackInfo) {
                fallbackInfo.style.display = 'none';
            }
            
            // Essayer de capturer une frame à 1 seconde
            try {
                video.currentTime = 1;
            } catch (e) {
                console.log('Impossible de définir currentTime pour la miniature');
            }
        });
        
        // En cas d'erreur de chargement, afficher le fallback
        video.addEventListener('error', function() {
            if (fallbackInfo) {
                fallbackInfo.style.display = 'block';
            }
        });
        
        // Si aucune miniature n'est générée après 3 secondes, afficher le fallback
        setTimeout(() => {
            if (video.readyState < 2 && fallbackInfo) {
                fallbackInfo.style.display = 'block';
            }
        }, 3000);
    });
});

// Fonction pour afficher la confirmation de suppression
function showDeleteConfirmation(galleryId, galleryName, hasChildren) {
    if (hasChildren) {
        // Galerie avec des enfants - confirmation avec avertissement
        const message = `⚠️ ATTENTION ⚠️\n\nLa galerie "${galleryName}" contient des sous-galeries ou des vidéos. Voulez-vous vraiment continuer ?`;
        
        if (confirm(message)) {
            window.location.href = `/gallery/delete/${galleryId}?force=true`;
        }
    } else {
        // Galerie vide - confirmation simple
        const message = `Êtes-vous sûr de vouloir supprimer la galerie "${galleryName}" ?\n\nCette action est irréversible.`;
        
        if (confirm(message)) {
            window.location.href = `/gallery/delete/${galleryId}`;
        }
    }
}
</script>

<!-- Modal de confirmation de suppression -->
<div id="deleteConfirmModal" class="delete-confirm-modal" style="display: none;">
    <div class="delete-confirm-content">
        <div class="delete-confirm-header">
            <h3>⚠️ Confirmation de suppression</h3>
        </div>
        <div class="delete-confirm-body">
            <p id="deleteConfirmMessage"></p>
        </div>
        <div class="delete-confirm-footer">
            <button class="btn btn-danger" id="confirmDeleteBtn">Supprimer définitivement</button>
            <button class="btn btn-secondary" id="cancelDeleteBtn">Annuler</button>
        </div>
    </div>
</div>

<style>
.delete-confirm-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.delete-confirm-content {
    background: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.delete-confirm-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.delete-confirm-header h3 {
    margin: 0;
    color: #dc3545;
    font-size: 1.2em;
}

.delete-confirm-body {
    padding: 20px;
    line-height: 1.6;
}

.delete-confirm-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 0 0 8px 8px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.delete-confirm-footer .btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . "/../layouts/base.php";
?>
