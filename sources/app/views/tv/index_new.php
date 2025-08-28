<?php
ob_start();
?>
<link rel="stylesheet" href="/assets/css/tv-streaming-2025.css">

<div class="tv-streaming-app">
    <!-- Header de navigation TV -->
    <header class="tv-header">
        <div class="tv-brand">
            <svg class="tv-brand-icon" width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21,3H3C1.89,3 1,3.89 1,5V19A2,2 0 0,0 3,21H21A2,2 0 0,0 23,19V5C23,3.89 22.1,3 21,3M21,19H3V5H21M16,10V15H14V10M13,10V15H11V10M10,10V15H8V10"/>
            </svg>
            <span class="tv-brand-text">SERIES TV</span>
        </div>
        
        <nav class="tv-main-nav">
            <a href="#" class="tv-nav-link active" data-section="home">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                </svg>
                Accueil
            </a>
            <a href="#" class="tv-nav-link" data-section="series">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18,4V3A1,1 0 0,0 17,2H7A1,1 0 0,0 6,3V4H4A1,1 0 0,0 3,5V19A1,1 0 0,0 4,20H20A1,1 0 0,0 21,19V5A1,1 0 0,0 20,4H18M8,4H16V15L12,13L8,15V4Z"/>
                </svg>
                Mes Séries
            </a>
            <a href="#" class="tv-nav-link" data-section="search">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/>
                </svg>
                Rechercher
            </a>
        </nav>
        
        <div class="tv-user-area">
            <div class="tv-user-profile">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <button class="tv-logout-btn" onclick="logout()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                </svg>
                Déconnexion
            </button>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="tv-main">
        <!-- Section Hero -->
        <section class="tv-hero" id="heroSection" style="display: none;">
            <div class="tv-hero-background">
                <div class="tv-hero-gradient"></div>
            </div>
            <div class="tv-hero-content">
                <h1 class="tv-hero-title">Découvrez vos séries préférées</h1>
                <p class="tv-hero-description">Plongez dans un univers de divertissement sans limite avec notre collection de séries exclusives.</p>
                <button class="tv-hero-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                    </svg>
                    Commencer à regarder
                </button>
            </div>
        </section>

        <!-- Navigation fil d'Ariane -->
        <div class="tv-breadcrumb" id="breadcrumb">
            <!-- Sera rempli dynamiquement -->
        </div>

        <!-- Zone de contenu -->
        <div class="tv-content" id="contentArea">
            <!-- État de chargement -->
            <div class="tv-loading" id="loadingState">
                <div class="tv-loading-spinner"></div>
                <p>Chargement en cours...</p>
            </div>

            <!-- Grille de galeries principales -->
            <div class="tv-galleries-grid" id="contentGrid" style="display: none;">
                <!-- Sera rempli dynamiquement -->
            </div>
        </div>
    </main>
</div>

<!-- Lecteur vidéo moderne style Netflix -->
<div class="tv-video-player" id="videoPlayer" style="display: none;">
    <div class="tv-video-container">
        <!-- Header vidéo -->
        <div class="tv-video-header" id="videoHeader">
            <button class="tv-video-back" id="backToGallery">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                </svg>
                Retour
            </button>
            
            <div class="tv-video-info">
                <h2 class="tv-video-title" id="videoTitle">Titre de la vidéo</h2>
                <div class="tv-video-meta">
                    <span class="tv-video-episode" id="videoEpisode">S1E1</span>
                    <span class="tv-video-duration" id="videoDuration">42 min</span>
                </div>
            </div>
        </div>

        <!-- Élément vidéo -->
        <video id="videoElement" preload="metadata" playsinline>
            Votre navigateur ne supporte pas la lecture vidéo.
        </video>

        <!-- Contrôles vidéo -->
        <div class="tv-video-controls" id="videoControls">
            <!-- Contrôles centraux -->
            <div class="tv-video-center-controls">
                <button class="tv-control-btn tv-rewind" id="rewindBtn">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.5,12L20,18V6M11,18V6L2.5,12L11,18Z"/>
                    </svg>
                    <span>10s</span>
                </button>
                
                <button class="tv-control-btn tv-play-pause" id="playPauseBtn">
                    <svg class="tv-play-icon" width="60" height="60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                    </svg>
                    <svg class="tv-pause-icon" width="60" height="60" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                        <path d="M14,19H18V5H14M6,19H10V5H6V19Z"/>
                    </svg>
                </button>
                
                <button class="tv-control-btn tv-forward" id="forwardBtn">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13,6V18L21.5,12M4,18L12.5,12L4,6V18Z"/>
                    </svg>
                    <span>10s</span>
                </button>
            </div>

            <!-- Barre de progression -->
            <div class="tv-video-progress-container">
                <div class="tv-video-progress" id="videoProgress">
                    <div class="tv-progress-buffer" id="progressBuffer"></div>
                    <div class="tv-progress-played" id="progressPlayed"></div>
                    <div class="tv-progress-handle" id="progressHandle"></div>
                </div>
            </div>

            <!-- Contrôles inférieurs -->
            <div class="tv-video-bottom-controls">
                <div class="tv-controls-left">
                    <button class="tv-control-btn" id="bottomPlayBtn">
                        <svg class="tv-play-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                        </svg>
                        <svg class="tv-pause-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                            <path d="M14,19H18V5H14M6,19H10V5H6V19Z"/>
                        </svg>
                    </button>
                    
                    <div class="tv-volume-controls">
                        <button class="tv-control-btn" id="volumeBtn">
                            <svg class="tv-volume-high" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,3.23V5.29C16.89,6.15 19,8.83 19,12C19,15.17 16.89,17.85 14,18.71V20.77C18.01,19.86 21,16.28 21,12C21,7.72 18.01,4.14 14,3.23M16.5,12C16.5,10.23 15.5,8.71 14,7.97V16C15.5,15.29 16.5,13.76 16.5,12M3,9V15H7L12,20V4L7,9H3Z"/>
                            </svg>
                            <svg class="tv-volume-mute" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                                <path d="M12,4L9.91,6.09L12,8.18M4.27,3L3,4.27L7.73,9H3V15H7L12,20V13.27L16.25,17.53C15.58,18.04 14.83,18.46 14,18.7V20.77C15.38,20.45 16.63,19.82 17.68,18.96L19.73,21L21,19.73L12,10.73M19,12C19,12.94 18.8,13.82 18.46,14.64L19.97,16.15C20.62,14.91 21,13.5 21,12C21,7.72 18,4.14 14,3.23V5.29C16.89,6.15 19,8.83 19,12M16.5,12C16.5,10.23 15.5,8.71 14,7.97V10.18L16.45,12.63C16.5,12.43 16.5,12.21 16.5,12Z"/>
                            </svg>
                        </button>
                        <div class="tv-volume-slider" id="volumeSlider">
                            <div class="tv-volume-track">
                                <div class="tv-volume-fill" id="volumeFill"></div>
                            </div>
                        </div>
                    </div>
                    
                    <span class="tv-time-display">
                        <span id="currentTime">0:00</span> / <span id="totalTime">0:00</span>
                    </span>
                </div>
                
                <div class="tv-controls-right">
                    <button class="tv-control-btn" id="subtitlesBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,4H4A2,2 0 0,0 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6A2,2 0 0,0 20,4M20,18H4V6H20V18M6,10H8V12H6V10M6,14H14V16H6V14M16,14H18V16H16V14M10,10H18V12H10V10Z"/>
                        </svg>
                    </button>
                    
                    <button class="tv-control-btn" id="fullscreenBtn">
                        <svg class="tv-fullscreen-enter" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5,5H10V7H7V10H5V5M14,5H19V10H17V7H14V5M17,14H19V19H14V17H17V14M10,17V19H5V14H7V17H10Z"/>
                        </svg>
                        <svg class="tv-fullscreen-exit" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                            <path d="M14,14H19V16H16V19H14V14M5,14H10V19H8V16H5V14M8,5H10V10H5V8H8V5M19,8V10H14V5H16V8H19Z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Overlay pour gérer les clics -->
        <div class="tv-video-overlay" id="videoOverlay"></div>
    </div>
</div>

<script>
// TV Streaming App 2025 - JavaScript moderne pour interface TV
class TVStreamingApp {
    constructor() {
        this.currentGallery = null;
        this.currentPath = [];
        this.focusedElement = null;
        this.videoPlayer = null;
        this.isVideoPlayerOpen = false;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupVideoPlayer();
        this.loadMainGalleries();
        
        // Gestion de la navigation au clavier
        this.setupKeyboardNavigation();
        
        console.log('🎬 TV Streaming App 2025 initialisée');
    }

    setupEventListeners() {
        // Navigation principale
        document.querySelectorAll('.tv-nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleNavigation(link.dataset.section);
            });
        });

        // Gestionnaire de redimensionnement
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    }

    setupVideoPlayer() {
        this.videoPlayer = document.getElementById('videoElement');
        const playerContainer = document.getElementById('videoPlayer');
        const backBtn = document.getElementById('backToGallery');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const overlay = document.getElementById('videoOverlay');
        
        if (backBtn) {
            backBtn.addEventListener('click', () => this.closeVideoPlayer());
        }

        if (playPauseBtn) {
            playPauseBtn.addEventListener('click', () => this.togglePlayPause());
        }

        if (overlay) {
            overlay.addEventListener('click', () => this.toggleVideoControls());
        }

        // Gestion du plein écran
        document.addEventListener('fullscreenchange', () => {
            this.updateFullscreenButton();
        });
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            this.handleKeyboardInput(e);
        });
    }

    handleKeyboardInput(e) {
        if (this.isVideoPlayerOpen) {
            this.handleVideoPlayerKeys(e);
            return;
        }

        switch(e.key) {
            case 'ArrowUp':
            case 'ArrowDown':
            case 'ArrowLeft':
            case 'ArrowRight':
                e.preventDefault();
                this.navigateGrid(e.key);
                break;
            case 'Enter':
                e.preventDefault();
                this.selectFocusedElement();
                break;
            case 'Escape':
                this.goBack();
                break;
        }
    }

    handleVideoPlayerKeys(e) {
        switch(e.key) {
            case 'Escape':
                this.closeVideoPlayer();
                break;
            case ' ':
                e.preventDefault();
                this.togglePlayPause();
                break;
            case 'ArrowLeft':
                this.seekBackward();
                break;
            case 'ArrowRight':
                this.seekForward();
                break;
            case 'ArrowUp':
                this.volumeUp();
                break;
            case 'ArrowDown':
                this.volumeDown();
                break;
        }
    }

    handleNavigation(section) {
        // Mise à jour de la navigation active
        document.querySelectorAll('.tv-nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        document.querySelector(`[data-section="${section}"]`).classList.add('active');

        switch(section) {
            case 'home':
                this.loadMainGalleries();
                break;
            case 'series':
                this.loadSeriesView();
                break;
            case 'search':
                this.openSearchInterface();
                break;
        }
    }

    async loadMainGalleries() {
        this.showLoading();
        this.updateBreadcrumb('Accueil');
        
        try {
            const response = await fetch('/api/galleries');
            const data = await response.json();
            
            if (data.success) {
                this.renderMainGalleries(data.galleries);
                this.showHeroSection();
            } else {
                this.showError('Erreur lors du chargement des galeries');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur de connexion');
        }
    }

    renderMainGalleries(galleries) {
        const grid = document.getElementById('contentGrid');
        
        if (!galleries || galleries.length === 0) {
            grid.innerHTML = `
                <div class="tv-empty-state">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19M13.96,12.71L11.21,15.46L9.25,13.5L6.5,16.25H17.5L13.96,12.71Z"/>
                    </svg>
                    <h3>Aucune galerie disponible</h3>
                    <p>Aucune série n'est disponible pour le moment.</p>
                </div>
            `;
            return;
        }

        const galleriesHTML = galleries.map(gallery => `
            <div class="tv-gallery-card" tabindex="0" data-gallery-id="${gallery.id}" data-type="gallery">
                <div class="tv-gallery-cover">
                    <svg class="tv-gallery-icon" width="60" height="60" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19M13.96,12.71L11.21,15.46L9.25,13.5L6.5,16.25H17.5L13.96,12.71Z"/>
                    </svg>
                </div>
                <div class="tv-gallery-info">
                    <h3 class="tv-gallery-title">${this.escapeHtml(gallery.name)}</h3>
                    <div class="tv-gallery-meta">
                        <span class="tv-gallery-count">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19Z"/>
                            </svg>
                            ${gallery.video_count || 0} ${gallery.video_count > 1 ? 'épisodes' : 'épisode'}
                        </span>
                        <span class="tv-gallery-type">Série</span>
                    </div>
                </div>
            </div>
        `).join('');

        grid.innerHTML = galleriesHTML;
        this.hideLoading();
        
        // Ajouter les gestionnaires d'événements
        this.setupGalleryCards();
        
        // Focus sur le premier élément
        this.focusFirstElement();
    }

    async loadGalleryContent(galleryId, galleryName) {
        this.showLoading();
        this.updateBreadcrumb(`Accueil > ${galleryName}`);
        
        try {
            const response = await fetch(`/api/galleries/${galleryId}/videos`);
            const data = await response.json();
            
            if (data.success) {
                if (data.hasSubGalleries) {
                    this.renderSubGalleries(data.subGalleries, galleryName);
                } else {
                    this.renderVideos(data.videos, galleryName);
                }
            } else {
                this.showError('Erreur lors du chargement du contenu');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur de connexion');
        }
    }

    renderSubGalleries(subGalleries, parentName) {
        const grid = document.getElementById('contentGrid');
        
        const subGalleriesHTML = subGalleries.map(subGallery => `
            <div class="tv-gallery-card" tabindex="0" data-gallery-id="${subGallery.id}" data-type="subgallery">
                <div class="tv-gallery-cover">
                    <svg class="tv-gallery-icon" width="50" height="50" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18,4V3A1,1 0 0,0 17,2H7A1,1 0 0,0 6,3V4H4A1,1 0 0,0 3,5V19A1,1 0 0,0 4,20H20A1,1 0 0,0 21,19V5A1,1 0 0,0 20,4H18M8,4H16V15L12,13L8,15V4Z"/>
                    </svg>
                </div>
                <div class="tv-gallery-info">
                    <h3 class="tv-gallery-title">${this.escapeHtml(subGallery.name)}</h3>
                    <div class="tv-gallery-meta">
                        <span class="tv-gallery-count">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                            </svg>
                            ${subGallery.video_count || 0} vidéos
                        </span>
                        <span class="tv-gallery-type">Saison</span>
                    </div>
                </div>
            </div>
        `).join('');

        grid.innerHTML = subGalleriesHTML;
        this.hideLoading();
        this.setupGalleryCards();
        this.focusFirstElement();
        this.hideHeroSection();
    }

    renderVideos(videos, galleryName) {
        const grid = document.getElementById('contentGrid');
        
        if (!videos || videos.length === 0) {
            grid.innerHTML = `
                <div class="tv-empty-state">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V17A1,1 0 0,0 4,18H16A1,1 0 0,0 17,17V13.5L21,17.5V6.5L17,10.5Z"/>
                    </svg>
                    <h3>Aucune vidéo disponible</h3>
                    <p>Cette galerie ne contient aucune vidéo pour le moment.</p>
                </div>
            `;
            return;
        }

        const videosHTML = videos.map(video => `
            <div class="tv-video-card" tabindex="0" data-video-id="${video.id}" data-type="video">
                <div class="tv-video-thumbnail">
                    <div class="tv-video-play-btn">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                        </svg>
                    </div>
                </div>
                <div class="tv-video-info">
                    <h4 class="tv-video-title">${this.escapeHtml(video.original_filename || video.filename)}</h4>
                    <div class="tv-video-duration">${this.formatDuration(video.duration)}</div>
                </div>
            </div>
        `).join('');

        grid.innerHTML = videosHTML;
        this.hideLoading();
        this.setupVideoCards();
        this.focusFirstElement();
        this.hideHeroSection();
    }

    setupGalleryCards() {
        document.querySelectorAll('.tv-gallery-card').forEach(card => {
            card.addEventListener('click', () => {
                const galleryId = card.dataset.galleryId;
                const galleryName = card.querySelector('.tv-gallery-title').textContent;
                this.loadGalleryContent(galleryId, galleryName);
            });
        });
    }

    setupVideoCards() {
        document.querySelectorAll('.tv-video-card').forEach(card => {
            card.addEventListener('click', () => {
                const videoId = card.dataset.videoId;
                const videoTitle = card.querySelector('.tv-video-title').textContent;
                this.playVideo(videoId, videoTitle);
            });
        });
    }

    async playVideo(videoId, title) {
        try {
            const response = await fetch(`/api/videos/${videoId}/stream-url`);
            const data = await response.json();
            
            if (data.success && data.streamUrl) {
                this.openVideoPlayer(data.streamUrl, title);
            } else {
                this.showError('Impossible de lire cette vidéo');
            }
        } catch (error) {
            console.error('Erreur:', error);
            this.showError('Erreur lors du chargement de la vidéo');
        }
    }

    openVideoPlayer(streamUrl, title) {
        const player = document.getElementById('videoPlayer');
        const video = document.getElementById('videoElement');
        const titleElement = document.getElementById('videoTitle');
        
        if (titleElement) {
            titleElement.textContent = title;
        }
        
        video.src = streamUrl;
        player.style.display = 'block';
        this.isVideoPlayerOpen = true;
        
        // Auto-play
        video.play().catch(e => console.log('Auto-play prevented:', e));
    }

    closeVideoPlayer() {
        const player = document.getElementById('videoPlayer');
        const video = document.getElementById('videoElement');
        
        video.pause();
        video.src = '';
        player.style.display = 'none';
        this.isVideoPlayerOpen = false;
    }

    togglePlayPause() {
        const video = document.getElementById('videoElement');
        
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    }

    seekBackward() {
        const video = document.getElementById('videoElement');
        video.currentTime = Math.max(0, video.currentTime - 10);
    }

    seekForward() {
        const video = document.getElementById('videoElement');
        video.currentTime = Math.min(video.duration, video.currentTime + 10);
    }

    volumeUp() {
        const video = document.getElementById('videoElement');
        video.volume = Math.min(1, video.volume + 0.1);
    }

    volumeDown() {
        const video = document.getElementById('videoElement');
        video.volume = Math.max(0, video.volume - 0.1);
    }

    toggleVideoControls() {
        // Toggle des contrôles vidéo
        const player = document.getElementById('videoPlayer');
        player.classList.toggle('paused');
    }

    updateFullscreenButton() {
        const enterIcon = document.querySelector('.tv-fullscreen-enter');
        const exitIcon = document.querySelector('.tv-fullscreen-exit');
        
        if (document.fullscreenElement) {
            enterIcon.style.display = 'none';
            exitIcon.style.display = 'block';
        } else {
            enterIcon.style.display = 'block';
            exitIcon.style.display = 'none';
        }
    }

    navigateGrid(direction) {
        const grid = document.getElementById('contentGrid');
        const cards = grid.querySelectorAll('.tv-gallery-card, .tv-video-card');
        
        if (cards.length === 0) return;
        
        let currentIndex = Array.from(cards).findIndex(card => card === this.focusedElement);
        
        if (currentIndex === -1) {
            currentIndex = 0;
        } else {
            switch(direction) {
                case 'ArrowUp':
                    currentIndex = Math.max(0, currentIndex - 3);
                    break;
                case 'ArrowDown':
                    currentIndex = Math.min(cards.length - 1, currentIndex + 3);
                    break;
                case 'ArrowLeft':
                    currentIndex = Math.max(0, currentIndex - 1);
                    break;
                case 'ArrowRight':
                    currentIndex = Math.min(cards.length - 1, currentIndex + 1);
                    break;
            }
        }
        
        this.setFocus(cards[currentIndex]);
    }

    setFocus(element) {
        if (this.focusedElement) {
            this.focusedElement.classList.remove('tv-nav-focus');
        }
        
        this.focusedElement = element;
        
        if (element) {
            element.classList.add('tv-nav-focus');
            element.focus();
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    focusFirstElement() {
        const grid = document.getElementById('contentGrid');
        const firstCard = grid.querySelector('.tv-gallery-card, .tv-video-card');
        
        if (firstCard) {
            this.setFocus(firstCard);
        }
    }

    selectFocusedElement() {
        if (this.focusedElement) {
            this.focusedElement.click();
        }
    }

    goBack() {
        if (this.currentPath.length > 0) {
            this.currentPath.pop();
            // Logique de retour en arrière
            this.loadMainGalleries();
        }
    }

    showLoading() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('contentGrid').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('contentGrid').style.display = 'grid';
    }

    showHeroSection() {
        const hero = document.getElementById('heroSection');
        if (hero) {
            hero.style.display = 'block';
        }
    }

    hideHeroSection() {
        const hero = document.getElementById('heroSection');
        if (hero) {
            hero.style.display = 'none';
        }
    }

    showError(message) {
        const grid = document.getElementById('contentGrid');
        grid.innerHTML = `
            <div class="tv-error-state">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                </svg>
                <h3>Une erreur est survenue</h3>
                <p>${message}</p>
            </div>
        `;
        this.hideLoading();
    }

    updateBreadcrumb(path) {
        const breadcrumb = document.getElementById('breadcrumb');
        const parts = path.split(' > ');
        
        const breadcrumbHTML = parts.map((part, index) => {
            if (index === parts.length - 1) {
                return `<span class="tv-breadcrumb-current">${part}</span>`;
            }
            return `<a href="#" class="tv-breadcrumb-link">${part}</a>`;
        }).join('<span class="tv-breadcrumb-separator"> > </span>');
        
        breadcrumb.innerHTML = breadcrumbHTML;
    }

    formatDuration(duration) {
        if (!duration) return 'N/A';
        
        if (typeof duration === 'string') {
            return duration.substring(0, 5); // HH:MM format
        }
        
        const hours = Math.floor(duration / 3600);
        const minutes = Math.floor((duration % 3600) / 60);
        
        if (hours > 0) {
            return `${hours}h ${minutes}min`;
        }
        return `${minutes}min`;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    handleResize() {
        // Gestion du redimensionnement
        if (this.focusedElement) {
            this.focusedElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
}

// Fonction globale de déconnexion
function logout() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        window.location.href = '/logout';
    }
}

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', () => {
    window.tvApp = new TVStreamingApp();
});

console.log('🎬 TV Streaming App 2025 chargée et prête');
</script>

<?php
$content = ob_get_clean();
require __DIR__ . "/../layouts/base.php";
?>
