// Variables globales
let currentUpload = null;
let uploadStartTime = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initializeStreaming();
});

function initializeStreaming() {
    // Gestion du bouton upload - laissé à upload.js
    // const uploadButton = document.getElementById('uploadButton');
    // const fileInput = document.getElementById('fileInput');
    
    // if (uploadButton && fileInput) {
    //     uploadButton.addEventListener('click', function() {
    //         fileInput.click();
    //     });
    //     
    //     fileInput.addEventListener('change', handleFileUpload);
    // }
    
    // Gestion du dropdown menu
    initializeDropdown();
    
    // Prévisualisation des vidéos au hover
    initializeVideoPreview();
    
    // Fermeture du modal en cliquant à l'extérieur
    const videoModal = document.getElementById('videoModal');
    if (videoModal) {
        videoModal.addEventListener('click', function(e) {
            if (e.target === videoModal) {
                closeVideoModal();
            }
        });
    }
}

// Gestion du menu dropdown
function initializeDropdown() {
    const dropdownButton = document.querySelector('.dropdown-button');
    const dropdownContent = document.querySelector('.dropdown-content');
    
    if (dropdownButton && dropdownContent) {
        dropdownButton.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownContent.classList.toggle('show');
        });
        
        // Fermer le dropdown si on clique ailleurs
        document.addEventListener('click', function() {
            dropdownContent.classList.remove('show');
        });
    }
}

// Prévisualisation des vidéos
function initializeVideoPreview() {
    const videoCards = document.querySelectorAll('.video-card');
    
    videoCards.forEach(card => {
        const video = card.querySelector('video');
        if (video) {
            card.addEventListener('mouseenter', function() {
                video.currentTime = 1; // Avancer un peu pour avoir une image
                video.play().catch(e => console.log('Autoplay prevented'));
            });
            
            card.addEventListener('mouseleave', function() {
                video.pause();
                video.currentTime = 0;
            });
        }
    });
}

// Gestion de l'upload avec progress
function handleFileUpload(event) {
    const files = event.target.files;
    if (files.length === 0) return;
    
    const galleryId = event.target.getAttribute('data-gallery-id');
    
    // Upload des fichiers un par un
    Array.from(files).forEach((file, index) => {
        setTimeout(() => {
            uploadFile(file, galleryId);
        }, index * 100); // Petit délai entre chaque upload
    });
}

function uploadFile(file, galleryId) {
    // Validation du fichier
    if (!validateVideoFile(file)) {
        alert('Fichier non valide. Veuillez sélectionner une vidéo.');
        return;
    }
    
    // Afficher le popup d'upload
    showUploadPopup(file);
    
    // Créer la requête XMLHttpRequest
    const xhr = new XMLHttpRequest();
    const formData = new FormData();
    
    formData.append('files[]', file);
    formData.append('gallery_id', galleryId);
    
    // Variables pour le calcul de progression
    uploadStartTime = Date.now();
    let lastLoaded = 0;
    let lastTime = uploadStartTime;
    
    // Gestion de la progression
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const now = Date.now();
            const timeDiff = (now - lastTime) / 1000; // en secondes
            const loadedDiff = e.loaded - lastLoaded;
            
            // Calcul de la vitesse
            const speed = loadedDiff / timeDiff; // bytes/sec
            const speedMB = speed / (1024 * 1024); // MB/sec
            
            // Calcul du temps restant
            const remaining = (e.total - e.loaded) / speed; // secondes
            
            // Mise à jour de l'interface
            updateUploadProgress(e.loaded, e.total, speedMB, remaining);
            
            lastLoaded = e.loaded;
            lastTime = now;
        }
    });
    
    // Gestion de la fin d'upload
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    hideUploadPopup();
                    showUploadSuccess();
                    // Recharger la page après un court délai
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showUploadError(response.message || 'Erreur lors de l\'upload');
                }
            } catch (e) {
                showUploadError('Erreur de traitement de la réponse');
            }
        } else {
            showUploadError('Erreur réseau: ' + xhr.status);
        }
    });
    
    // Gestion des erreurs
    xhr.addEventListener('error', function() {
        showUploadError('Erreur de connexion');
    });
    
    // Envoyer la requête
    xhr.open('POST', '/gallery/upload');
    xhr.send(formData);
    
    currentUpload = xhr;
}

function validateVideoFile(file) {
    const allowedTypes = ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo', 'video/webm', 'video/ogg'];
    const maxSize = 10 * 1024 * 1024 * 1024; // 10GB
    
    if (!allowedTypes.includes(file.type)) {
        return false;
    }
    
    if (file.size > maxSize) {
        alert('Le fichier est trop volumineux (max 10GB)');
        return false;
    }
    
    return true;
}

// Gestion du popup d'upload
function showUploadPopup(file) {
    const overlay = document.getElementById('uploadOverlay');
    const fileName = document.getElementById('uploadFileName');
    const fileSize = document.getElementById('uploadFileSize');
    
    if (overlay && fileName && fileSize) {
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        overlay.style.display = 'flex';
    }
}

function hideUploadPopup() {
    const overlay = document.getElementById('uploadOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function updateUploadProgress(loaded, total, speedMB, remainingSeconds) {
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('uploadProgress');
    const speedText = document.getElementById('uploadSpeed');
    const timeText = document.getElementById('uploadTimeRemaining');
    
    const percent = Math.round((loaded / total) * 100);
    
    if (progressFill) {
        progressFill.style.width = percent + '%';
    }
    
    if (progressText) {
        progressText.textContent = percent + '%';
    }
    
    if (speedText) {
        speedText.textContent = speedMB.toFixed(1) + ' MB/s';
    }
    
    if (timeText) {
        if (remainingSeconds > 0 && remainingSeconds < Infinity) {
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = Math.floor(remainingSeconds % 60);
            timeText.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        } else {
            timeText.textContent = '--:--';
        }
    }
}

function closeUploadPopup() {
    if (currentUpload) {
        currentUpload.abort();
        currentUpload = null;
    }
    hideUploadPopup();
}

function showUploadSuccess() {
    // Remplacer le contenu du popup par un message de succès
    const uploadContent = document.querySelector('.upload-content');
    if (uploadContent) {
        uploadContent.innerHTML = `
            <div class="upload-success">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#4CAF50">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <h3>Upload terminé !</h3>
                <p>La vidéo a été ajoutée avec succès</p>
            </div>
        `;
    }
}

function showUploadError(message) {
    const uploadContent = document.querySelector('.upload-content');
    if (uploadContent) {
        uploadContent.innerHTML = `
            <div class="upload-error">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#f44336">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <h3>Erreur d'upload</h3>
                <p>${message}</p>
                <button onclick="hideUploadPopup()" class="retry-button">Fermer</button>
            </div>
        `;
    }
}

// Gestion du modal vidéo avec lecteur screencast
function openVideoModal(videoPath, title) {
    console.log('openVideoModal (legacy) appelée, redirection vers openSimpleVideoModal');
    
    // Essayer d'abord de rediriger vers la nouvelle fonction
    if (typeof window.openSimpleVideoModal === 'function') {
        console.log('Redirection réussie vers openSimpleVideoModal');
        window.openSimpleVideoModal(videoPath, title);
        return;
    }
    
    // Si openSimpleVideoModal n'existe pas, essayer de créer un modal simple
    console.log('openSimpleVideoModal non disponible, création d\'un modal simple');
    
    // Chercher d'abord le modal simple
    let modal = document.getElementById('simpleVideoModal');
    if (modal) {
        const video = document.getElementById('simpleModalVideo');
        const source = document.getElementById('simpleModalVideoSource');
        const titleElement = document.getElementById('simpleModalVideoTitle');
        
        if (video && source && titleElement) {
            source.src = videoPath;
            titleElement.textContent = title || 'Vidéo sans titre';
            video.load();
            modal.style.display = 'flex';
            console.log('Modal simple ouvert avec succès');
            return;
        }
    }
    
    // En dernier recours, créer un modal basique
    console.log('Création d\'un modal de base');
    modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 20px;
        border-radius: 10px;
        max-width: 90%;
        max-height: 90%;
    `;
    
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '&times;';
    closeBtn.style.cssText = `
        float: right;
        font-size: 24px;
        background: none;
        border: none;
        cursor: pointer;
    `;
    closeBtn.onclick = () => document.body.removeChild(modal);
    
    const video = document.createElement('video');
    video.controls = true;
    video.style.cssText = 'width: 100%; max-width: 800px; height: auto;';
    video.src = videoPath;
    
    const titleDiv = document.createElement('h3');
    titleDiv.textContent = title || 'Vidéo';
    titleDiv.style.marginBottom = '10px';
    
    content.appendChild(closeBtn);
    content.appendChild(titleDiv);
    content.appendChild(video);
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    // Fermer en cliquant en dehors
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            document.body.removeChild(modal);
        }
    });
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');
    
    if (modal && video) {
        video.pause();
        modal.style.display = 'none';
        
        // Remettre à zéro le temps de lecture
        video.currentTime = 0;
    }
}

// Suppression de vidéo
function deleteVideo(videoId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')) {
        window.location.href = '/gallery/delete/' + videoId;
    }
}

// Utilitaires
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Gestion des raccourcis clavier
document.addEventListener('keydown', function(e) {
    // Échapper pour fermer le modal
    if (e.key === 'Escape') {
        closeVideoModal();
        closeUploadPopup();
    }
    
    // Barre d'espace pour play/pause dans le modal
    if (e.key === ' ' && document.getElementById('videoModal').style.display === 'flex') {
        e.preventDefault();
        const video = document.getElementById('modalVideo');
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    }
});
