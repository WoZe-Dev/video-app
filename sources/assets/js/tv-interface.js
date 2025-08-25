// Interface TV pour streaming vidéo
class TVInterface {
    constructor() {
        console.log('TVInterface constructor appelé - Version avec test_api.php');
        this.currentFocus = null;
        this.focusIndex = 0;
        this.focusableElements = [];
        this.isPlayerOpen = false;
        this.currentVideo = null;
        this.hideControlsTimeout = null;
        this.isControlsVisible = true;
        
        // Nouvelles propriétés pour la gestion des galeries
        this.galleries = [];
        this.currentGallery = null;
        this.currentView = 'galleries'; // 'galleries' ou 'videos'
        this.currentGalleryIndex = 0;
        this.currentVideoIndex = 0;
        
        this.init();
    }
    
    init() {
        this.createTVInterface();
        this.setupEventListeners();
        this.updateFocusableElements();
        this.setInitialFocus();
    }
    
    createTVInterface() {
        // Créer l'interface TV complète
        const tvMode = document.createElement('div');
        tvMode.className = 'tv-mode';
        tvMode.id = 'tvMode';
        tvMode.innerHTML = this.getTVHTML();
        
        document.body.appendChild(tvMode);
        
        // Charger les galeries
        console.log('Appel de loadGalleries() depuis init()');
        this.loadGalleries();
    }
    
    getTVHTML() {
        return `
            <div class="tv-container">
                <!-- Sidebar Navigation -->
                <div class="tv-sidebar" id="tvSidebar">
                    <div class="tv-logo">
                        <div class="tv-logo-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <span class="tv-logo-text">BetweenUs</span>
                    </div>
                    
                    <nav class="tv-nav">
                        <div class="tv-nav-item active tv-focusable" data-action="home">
                            <svg class="tv-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                            </svg>
                            <span>Accueil</span>
                        </div>
                        <div class="tv-nav-item tv-focusable" data-action="galleries">
                            <svg class="tv-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8 12.5v-9l6 4.5-6 4.5z"/>
                            </svg>
                            <span>Mes Galeries</span>
                        </div>
                        <div class="tv-nav-item tv-focusable" data-action="recent">
                            <svg class="tv-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                            </svg>
                            <span>Récentes</span>
                        </div>
                        <div class="tv-nav-item tv-focusable" data-action="settings">
                            <svg class="tv-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                            </svg>
                            <span>Paramètres</span>
                        </div>
                    </nav>
                    
                    <div style="margin-top: auto; padding-top: 20px; border-top: 1px solid #333;">
                        <div class="tv-nav-item tv-focusable" data-action="exit">
                            <svg class="tv-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5z"/>
                            </svg>
                            <span>Quitter TV</span>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="tv-main">
                    <div class="tv-header">
                        <h1 class="tv-title" id="tvTitle">Mes Vidéos</h1>
                        <div class="tv-controls">
                            <button class="tv-button tv-focusable" data-action="search">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                </svg>
                                Rechercher
                            </button>
                            <button class="tv-button primary tv-focusable" data-action="upload">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16h6v-6h4l-7-7-7 7h4zm-4 2h14v2H5z"/>
                                </svg>
                                Ajouter Vidéo
                            </button>
                        </div>
                    </div>
                    
                    <div class="tv-content">
                        <div class="tv-grid" id="tvGrid">
                            <!-- Contenu dynamique -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lecteur Vidéo TV -->
            <div class="tv-player" id="tvPlayer">
                <video class="tv-video" id="tvVideo">
                    <source id="tvVideoSource" src="" type="video/mp4">
                </video>
                
                <div class="tv-player-controls" id="tvPlayerControls">
                    <div class="tv-controls-main">
                        <button class="tv-play-btn tv-focusable" id="tvPlayBtn">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </button>
                        
                        <div class="tv-progress-container tv-focusable" id="tvProgressContainer">
                            <div class="tv-progress-bar" id="tvProgressBar"></div>
                        </div>
                        
                        <div class="tv-time" id="tvTime">00:00 / 00:00</div>
                        
                        <div class="tv-volume-container">
                            <button class="tv-volume-btn tv-focusable" id="tvVolumeBtn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/>
                                </svg>
                            </button>
                            <input type="range" class="tv-volume-slider tv-focusable" id="tvVolumeSlider" min="0" max="1" step="0.1" value="0.8">
                        </div>
                    </div>
                    
                    <div class="tv-controls-secondary">
                        <div class="tv-seek-controls">
                            <button class="tv-seek-btn tv-focusable" data-seek="-30">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11 18V6l-8.5 6 8.5 6zm.5-6l8.5 6V6l-8.5 6z"/>
                                </svg>
                                -30s
                            </button>
                            <button class="tv-seek-btn tv-focusable" data-seek="-10">-10s</button>
                            <button class="tv-seek-btn tv-focusable" data-seek="10">+10s</button>
                            <button class="tv-seek-btn tv-focusable" data-seek="30">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 18l8.5-6L4 6v12zm9-12v12l8.5-6L13 6z"/>
                                </svg>
                                +30s
                            </button>
                        </div>
                        
                        <div class="tv-speed-controls">
                            <span class="tv-speed-label">Vitesse:</span>
                            <button class="tv-speed-btn tv-focusable" data-speed="0.5">0.5x</button>
                            <button class="tv-speed-btn active tv-focusable" data-speed="1">1x</button>
                            <button class="tv-speed-btn tv-focusable" data-speed="1.25">1.25x</button>
                            <button class="tv-speed-btn tv-focusable" data-speed="1.5">1.5x</button>
                            <button class="tv-speed-btn tv-focusable" data-speed="2">2x</button>
                        </div>
                        
                        <div class="tv-player-actions">
                            <button class="tv-fullscreen-btn tv-focusable">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                                </svg>
                                Plein écran
                            </button>
                            <button class="tv-exit-btn tv-focusable" id="tvExitBtn">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="tv-notification" id="tvNotification"></div>
        `;
    }
    
    setupEventListeners() {
        // Navigation clavier/télécommande
        document.addEventListener('keydown', (e) => this.handleKeyDown(e));
        
        // Événements de clic
        document.addEventListener('click', (e) => this.handleClick(e));
        
        // Contrôles vidéo
        this.setupVideoControls();
        
        // Gestion de la souris pour montrer les contrôles
        document.addEventListener('mousemove', () => this.showPlayerControls());
    }
    
    handleKeyDown(e) {
        if (!document.getElementById('tvMode')) return;
        
        switch(e.code) {
            case 'ArrowUp':
                e.preventDefault();
                this.navigateUp();
                break;
            case 'ArrowDown':
                e.preventDefault();
                this.navigateDown();
                break;
            case 'ArrowLeft':
                e.preventDefault();
                this.navigateLeft();
                break;
            case 'ArrowRight':
                e.preventDefault();
                this.navigateRight();
                break;
            case 'Enter':
            case 'Space':
                e.preventDefault();
                this.activateElement();
                break;
            case 'Escape':
                e.preventDefault();
                this.handleEscape();
                break;
            case 'Backspace':
                e.preventDefault();
                this.handleBack();
                break;
        }
    }
    
    handleClick(e) {
        const focusable = e.target.closest('.tv-focusable');
        if (focusable) {
            this.setFocus(focusable);
            this.activateElement();
        }
    }
    
    updateFocusableElements() {
        this.focusableElements = Array.from(document.querySelectorAll('.tv-focusable:not([disabled])'));
    }
    
    setInitialFocus() {
        if (this.focusableElements.length > 0) {
            this.setFocus(this.focusableElements[0]);
        }
    }
    
    setFocus(element) {
        // Retirer le focus précédent
        if (this.currentFocus) {
            this.currentFocus.classList.remove('focused');
        }
        
        // Définir le nouveau focus
        this.currentFocus = element;
        element.classList.add('focused');
        this.focusIndex = this.focusableElements.indexOf(element);
        
        // Faire défiler si nécessaire
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    navigateUp() {
        if (!this.currentFocus) return;
        
        const rect = this.currentFocus.getBoundingClientRect();
        const candidates = this.focusableElements.filter(el => {
            const elRect = el.getBoundingClientRect();
            return elRect.bottom <= rect.top && Math.abs(elRect.left - rect.left) < 200;
        });
        
        if (candidates.length > 0) {
            const closest = candidates.reduce((prev, curr) => {
                const prevRect = prev.getBoundingClientRect();
                const currRect = curr.getBoundingClientRect();
                return (rect.top - currRect.bottom) < (rect.top - prevRect.bottom) ? curr : prev;
            });
            this.setFocus(closest);
        }
    }
    
    navigateDown() {
        if (!this.currentFocus) return;
        
        const rect = this.currentFocus.getBoundingClientRect();
        const candidates = this.focusableElements.filter(el => {
            const elRect = el.getBoundingClientRect();
            return elRect.top >= rect.bottom && Math.abs(elRect.left - rect.left) < 200;
        });
        
        if (candidates.length > 0) {
            const closest = candidates.reduce((prev, curr) => {
                const prevRect = prev.getBoundingClientRect();
                const currRect = curr.getBoundingClientRect();
                return (currRect.top - rect.bottom) < (prevRect.top - rect.bottom) ? curr : prev;
            });
            this.setFocus(closest);
        }
    }
    
    navigateLeft() {
        if (!this.currentFocus) return;
        
        const rect = this.currentFocus.getBoundingClientRect();
        const candidates = this.focusableElements.filter(el => {
            const elRect = el.getBoundingClientRect();
            return elRect.right <= rect.left && Math.abs(elRect.top - rect.top) < 100;
        });
        
        if (candidates.length > 0) {
            const closest = candidates.reduce((prev, curr) => {
                const prevRect = prev.getBoundingClientRect();
                const currRect = curr.getBoundingClientRect();
                return (rect.left - currRect.right) < (rect.left - prevRect.right) ? curr : prev;
            });
            this.setFocus(closest);
        }
    }
    
    navigateRight() {
        if (!this.currentFocus) return;
        
        const rect = this.currentFocus.getBoundingClientRect();
        const candidates = this.focusableElements.filter(el => {
            const elRect = el.getBoundingClientRect();
            return elRect.left >= rect.right && Math.abs(elRect.top - rect.top) < 100;
        });
        
        if (candidates.length > 0) {
            const closest = candidates.reduce((prev, curr) => {
                const prevRect = prev.getBoundingClientRect();
                const currRect = curr.getBoundingClientRect();
                return (currRect.left - rect.right) < (prevRect.left - rect.right) ? curr : prev;
            });
            this.setFocus(closest);
        }
    }
    
    activateElement() {
        if (!this.currentFocus) return;
        
        // Vérifier si c'est une carte de galerie
        if (this.currentFocus.classList.contains('tv-gallery-card')) {
            const galleryId = this.currentFocus.dataset.galleryId;
            if (galleryId) {
                this.openGallery(galleryId);
                return;
            }
        }
        
        // Vérifier si c'est un bouton retour
        if (this.currentFocus.classList.contains('tv-back-card')) {
            this.backToGalleries();
            return;
        }
        
        // Vérifier si c'est une carte vidéo
        if (this.currentFocus.classList.contains('tv-video-card')) {
            const videoId = this.currentFocus.dataset.videoId;
            if (videoId) {
                this.playVideo(videoId);
                return;
            }
        }
        
        const action = this.currentFocus.dataset.action;
        const videoId = this.currentFocus.dataset.videoId;
        const seek = this.currentFocus.dataset.seek;
        const speed = this.currentFocus.dataset.speed;
        
        if (action) {
            this.handleAction(action);
        } else if (videoId) {
            this.playVideo(videoId);
        } else if (seek) {
            this.seekVideo(parseInt(seek));
        } else if (speed) {
            this.setVideoSpeed(parseFloat(speed));
        } else if (this.currentFocus.id === 'tvPlayBtn') {
            this.togglePlay();
        } else if (this.currentFocus.id === 'tvVolumeBtn') {
            this.toggleMute();
        } else if (this.currentFocus.id === 'tvExitBtn') {
            this.closePlayer();
        }
    }

    backToGalleries() {
        this.currentView = 'galleries';
        this.currentGallery = null;
        this.loadGalleries();
    }

    handleEscape() {
        if (this.isPlayerOpen) {
            this.closePlayer();
        } else if (this.currentView === 'videos') {
            this.backToGalleries();
        } else {
            this.exitTVMode();
        }
    }

    handleBack() {
        if (this.currentView === 'videos') {
            this.backToGalleries();
        } else {
            this.exitTVMode();
        }
    }
    
    handleAction(action) {
        switch(action) {
            case 'exit':
                this.exitTVMode();
                break;
            case 'home':
                this.showHome();
                break;
            case 'galleries':
                this.showGalleries();
                break;
            case 'recent':
                this.showRecent();
                break;
            case 'settings':
                this.showSettings();
                break;
            case 'search':
                this.showSearch();
                break;
            case 'upload':
                this.showUpload();
                break;
        }
    }
    
    handleEscape() {
        if (this.isPlayerOpen) {
            this.closePlayer();
        } else {
            this.exitTVMode();
        }
    }
    
    handleBack() {
        if (this.isPlayerOpen) {
            this.closePlayer();
        }
    }
    
    async loadGalleries() {
        try {
            console.log('Début du chargement des galeries...');
            // Afficher le loading
            this.showLoading();
            
            console.log('Appel à /test_api.php');
            const response = await fetch('/test_api.php');
            
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.text();
            console.log('Data received:', data.substring(0, 200) + '...');
            
            // Vérifier si la réponse est du JSON valide
            let galleries;
            try {
                galleries = JSON.parse(data);
            } catch (jsonError) {
                console.error('Réponse non-JSON reçue:', data);
                throw new Error('Réponse serveur invalide');
            }
            
            // Vérifier si c'est une erreur API
            if (galleries.error) {
                throw new Error(galleries.error);
            }
            
            this.displayGalleries(galleries);
        } catch (error) {
            console.error('Erreur lors du chargement des galeries:', error);
            this.displayError('Impossible de charger les galeries: ' + error.message);
        }
    }
    
    showLoading() {
        const grid = document.getElementById('tvGrid');
        grid.innerHTML = `
            <div class="tv-loading">
                <div class="tv-loading-spinner"></div>
                <div class="tv-loading-text">Chargement des vidéos...</div>
            </div>
        `;
    }
    
    displayGalleries(galleriesResponse) {
        console.log('Affichage des galeries:', galleriesResponse);
        
        // Extraire les galeries de la réponse
        const galleries = galleriesResponse.galleries || [];
        
        if (galleries.length === 0) {
            this.displayError('Aucune galerie trouvée');
            return;
        }
        
        this.galleries = galleries;
        this.currentView = 'galleries';
        this.currentGalleryIndex = 0;
        
        const grid = document.getElementById('tvGrid');
        grid.innerHTML = '';
        
        galleries.forEach((gallery, index) => {
            const card = this.createGalleryCard(gallery, index);
            grid.appendChild(card);
        });
        
        this.updateFocusableElements();
        this.showNotification(`${galleries.length} galerie${galleries.length > 1 ? 's' : ''} chargée${galleries.length > 1 ? 's' : ''}`);
    }
    
    createGalleryCard(gallery, index) {
        const card = document.createElement('div');
        card.className = 'tv-card tv-focusable tv-gallery-card';
        card.dataset.galleryId = gallery.gallery_id;
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
            <div class="tv-thumbnail tv-gallery-thumbnail">
                ${gallery.thumbnail_path ? 
                    `<img src="${gallery.thumbnail_path}" alt="${gallery.gallery_name}">` : 
                    `<div class="tv-gallery-placeholder">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/>
                        </svg>
                    </div>`
                }
                <div class="tv-gallery-count">${gallery.video_count} vidéo${gallery.video_count > 1 ? 's' : ''}</div>
                <div class="tv-play-overlay">
                    <button class="tv-play-button">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="tv-card-info">
                <h3 class="tv-card-title">${gallery.gallery_name}</h3>
                <div class="tv-card-meta">
                    <span>${gallery.video_count} vidéo${gallery.video_count > 1 ? 's' : ''}</span>
                    ${gallery.gallery_description ? `<span>${gallery.gallery_description}</span>` : ''}
                </div>
            </div>
        `;
        
        return card;
    }

    async openGallery(galleryId) {
        try {
            this.showNotification('Chargement de la galerie...');
            
            const response = await fetch(`/api/gallery/${galleryId}/videos`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const galleryData = await response.json();
            
            if (galleryData.error) {
                throw new Error(galleryData.error);
            }
            
            this.currentGallery = galleryData;
            this.currentView = 'videos';
            this.currentVideoIndex = 0;
            
            this.displayGalleryVideos(galleryData);
            
        } catch (error) {
            console.error('Erreur lors du chargement de la galerie:', error);
            this.showNotification('Erreur: ' + error.message);
        }
    }

    displayGalleryVideos(galleryData) {
        const videos = galleryData.videos || [];
        
        if (videos.length === 0) {
            this.displayError(`Aucune vidéo dans la galerie "${galleryData.gallery_name}"`);
            return;
        }
        
        const grid = document.getElementById('tvGrid');
        grid.innerHTML = '';
        
        // Ajouter un bouton retour
        const backButton = document.createElement('div');
        backButton.className = 'tv-card tv-focusable tv-back-card';
        backButton.innerHTML = `
            <div class="tv-thumbnail">
                <div class="tv-back-icon">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </div>
            </div>
            <div class="tv-card-info">
                <h3 class="tv-card-title">← Retour aux galeries</h3>
            </div>
        `;
        grid.appendChild(backButton);
        
        // Ajouter les vidéos
        videos.forEach((video, index) => {
            const card = this.createVideoCard(video, index + 1); // +1 pour tenir compte du bouton retour
            grid.appendChild(card);
        });
        
        this.updateFocusableElements();
        this.showNotification(`Galerie "${galleryData.gallery_name}" - ${videos.length} vidéo${videos.length > 1 ? 's' : ''}`);
    }

    createVideoCard(video, index) {
        const card = document.createElement('div');
        card.className = 'tv-card tv-focusable tv-video-card';
        card.dataset.videoId = video.id;
        card.style.animationDelay = `${index * 0.1}s`;
        
        card.innerHTML = `
            <div class="tv-thumbnail">
                <video muted preload="metadata">
                    <source src="${video.video_path}" type="${video.mime_type || 'video/mp4'}">
                </video>
                <div class="tv-play-overlay">
                    <button class="tv-play-button">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </button>
                </div>
                ${video.duration ? `<div class="tv-duration">${this.formatDuration(video.duration)}</div>` : ''}
            </div>
            <div class="tv-card-info">
                <h3 class="tv-card-title">${video.caption || 'Vidéo sans titre'}</h3>
                <div class="tv-card-meta">
                    ${video.file_size ? `<span>${this.formatFileSize(video.file_size)}</span>` : ''}
                    ${video.duration ? `<span>${this.formatDuration(video.duration)}</span>` : ''}
                </div>
            </div>
        `;
        
        return card;
    }
    
    async playVideo(videoId) {
        try {
            // Afficher notification de chargement
            this.showNotification('Chargement de la vidéo...');
            
            // Récupérer les données de la vidéo
            const response = await fetch(`/api/video/${videoId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const videoData = await response.json();
            
            if (videoData.error) {
                throw new Error(videoData.error);
            }
            
            this.currentVideo = videoData;
            
            const player = document.getElementById('tvPlayer');
            const video = document.getElementById('tvVideo');
            const source = document.getElementById('tvVideoSource');
            
            source.src = videoData.video_path;
            video.load();
            player.style.display = 'block';
            this.isPlayerOpen = true;
            
            this.showNotification(`Lecture: ${videoData.caption}`);
            this.setupPlayerFocus();
            
        } catch (error) {
            console.error('Erreur lors du chargement de la vidéo:', error);
            this.showNotification('Erreur: ' + error.message);
        }
    }
    
    setupPlayerFocus() {
        setTimeout(() => {
            this.updateFocusableElements();
            const playBtn = document.getElementById('tvPlayBtn');
            if (playBtn) {
                this.setFocus(playBtn);
            }
        }, 100);
    }
    
    setupVideoControls() {
        const video = document.getElementById('tvVideo');
        if (!video) return;
        
        video.addEventListener('timeupdate', () => this.updateProgress());
        video.addEventListener('loadedmetadata', () => this.updateTimeDisplay());
        video.addEventListener('ended', () => this.onVideoEnded());
    }
    
    togglePlay() {
        const video = document.getElementById('tvVideo');
        const playBtn = document.getElementById('tvPlayBtn');
        
        if (video.paused) {
            video.play();
            playBtn.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
            this.hidePlayerControlsWithDelay();
        } else {
            video.pause();
            playBtn.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
            this.showPlayerControls();
        }
    }
    
    updateProgress() {
        const video = document.getElementById('tvVideo');
        const progressBar = document.getElementById('tvProgressBar');
        
        if (video.duration) {
            const percent = (video.currentTime / video.duration) * 100;
            progressBar.style.width = `${percent}%`;
        }
        
        this.updateTimeDisplay();
    }
    
    updateTimeDisplay() {
        const video = document.getElementById('tvVideo');
        const timeDisplay = document.getElementById('tvTime');
        
        const current = this.formatTime(video.currentTime);
        const duration = this.formatTime(video.duration);
        timeDisplay.textContent = `${current} / ${duration}`;
    }
    
    seekVideo(seconds) {
        const video = document.getElementById('tvVideo');
        video.currentTime += seconds;
        this.showNotification(`${seconds > 0 ? '+' : ''}${seconds}s`);
    }
    
    setVideoSpeed(speed) {
        const video = document.getElementById('tvVideo');
        video.playbackRate = speed;
        
        // Mettre à jour les boutons de vitesse
        document.querySelectorAll('.tv-speed-btn').forEach(btn => {
            btn.classList.toggle('active', parseFloat(btn.dataset.speed) === speed);
        });
        
        this.showNotification(`Vitesse: ${speed}x`);
    }
    
    toggleMute() {
        const video = document.getElementById('tvVideo');
        const volumeSlider = document.getElementById('tvVolumeSlider');
        
        video.muted = !video.muted;
        volumeSlider.value = video.muted ? 0 : video.volume;
        
        this.showNotification(video.muted ? 'Son coupé' : 'Son rétabli');
    }
    
    closePlayer() {
        const player = document.getElementById('tvPlayer');
        const video = document.getElementById('tvVideo');
        
        video.pause();
        player.style.display = 'none';
        this.isPlayerOpen = false;
        
        this.updateFocusableElements();
        this.setInitialFocus();
    }
    
    showPlayerControls() {
        const controls = document.getElementById('tvPlayerControls');
        controls.classList.add('active');
        this.isControlsVisible = true;
        
        clearTimeout(this.hideControlsTimeout);
    }
    
    hidePlayerControlsWithDelay() {
        clearTimeout(this.hideControlsTimeout);
        this.hideControlsTimeout = setTimeout(() => {
            const controls = document.getElementById('tvPlayerControls');
            controls.classList.remove('active');
            this.isControlsVisible = false;
        }, 5000);
    }
    
    showNotification(message) {
        const notification = document.getElementById('tvNotification');
        notification.textContent = message;
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 2000);
    }
    
    exitTVMode() {
        const tvMode = document.getElementById('tvMode');
        if (tvMode) {
            tvMode.remove();
        }
    }
    
    // Méthodes d'affichage des différentes sections
    showHome() {
        document.getElementById('tvTitle').textContent = 'Accueil';
        this.loadGalleries();
        this.updateNavigation('home');
    }
    
    showGalleries() {
        document.getElementById('tvTitle').textContent = 'Mes Galeries';
        this.loadGalleries();
        this.updateNavigation('galleries');
    }
    
    showRecent() {
        document.getElementById('tvTitle').textContent = 'Vidéos Récentes';
        this.updateNavigation('recent');
    }
    
    showSettings() {
        document.getElementById('tvTitle').textContent = 'Paramètres';
        this.updateNavigation('settings');
    }
    
    showSearch() {
        this.showNotification('Fonction de recherche à venir');
    }
    
    showUpload() {
        this.showNotification('Upload depuis l\'interface web');
    }
    
    updateNavigation(activeSection) {
        document.querySelectorAll('.tv-nav-item').forEach(item => {
            item.classList.toggle('active', item.dataset.action === activeSection);
        });
    }
    
    // Utilitaires
    formatTime(seconds) {
        if (isNaN(seconds)) return '00:00';
        
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        
        if (hours > 0) {
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
    }
    
    formatDuration(seconds) {
        return this.formatTime(seconds);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
    
    displayError(message) {
        const grid = document.getElementById('tvGrid');
        grid.innerHTML = `
            <div class="tv-loading">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="#e53e3e">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <div class="tv-loading-text">${message}</div>
            </div>
        `;
    }
}

// Fonction pour activer le mode TV
function activateTVMode() {
    new TVInterface();
}

// Fonction pour sortir du mode TV
function exitTVMode() {
    const tvMode = document.getElementById('tvMode');
    if (tvMode) {
        tvMode.remove();
    }
}

// Export pour utilisation globale
window.activateTVMode = activateTVMode;
window.exitTVMode = exitTVMode;
