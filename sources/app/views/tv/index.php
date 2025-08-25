<?php
ob_start();
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

<div class="tv-interface">
    <!-- Sidebar Navigation -->
    <div class="tv-sidebar">
      
        
        <nav class="tv-nav">
            <div class="tv-nav-item active" data-section="home" tabindex="0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/>
                </svg>
                <span>Accueil</span>
            </div>
            
            
            
            <div class="tv-nav-item" data-section="search" tabindex="0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/>
                </svg>
                <span>Rechercher</span>
            </div>
            
            
            
            <div class="tv-nav-item tv-nav-logout" tabindex="0" onclick="logout()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                </svg>
                <span>Déconnexion</span>
            </div>
        </nav>
        
        <div class="tv-user-info">
            <div class="tv-user-avatar">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <div class="tv-user-details">
                <div class="tv-user-role">Connecté en tant que:</div>
                <div class="tv-user-name">TV Viewer</div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="tv-main-content">
        <div class="tv-content-header">
            <h1 class="tv-content-title" id="pageTitle">Mes Galeries</h1>
            <div id="breadcrumb">
                
            </div>
        </div>
        
        <div class="tv-content-area" id="contentArea">
            <div class="tv-loading-state" id="loadingState">
                <div class="tv-spinner"></div>
                <p>Chargement des galeries...</p>
            </div>
            
            <div class="tv-grid" id="contentGrid" style="display: none;">
                
            </div>
        </div>
    </div>
</div>

<!-- Lecteur vidéo full-screen style Netflix -->
<div class="tv-video-player" id="videoPlayer" style="display: none;">
    <!-- Overlay pour les contrôles -->
    <div class="tv-video-overlay" id="videoOverlay">
        <!-- Header avec titre et bouton fermer -->
        <div class="tv-video-header">
            <div class="tv-video-back-btn" id="backBtn">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                </svg>
            </div>
            <div class="tv-video-title-area">
                <h3 class="tv-video-title" id="videoTitle">Titre de la vidéo</h3>
                <div class="tv-video-episode-info" id="videoEpisodeInfo">S6:E2 "Cake Week"</div>
            </div>
            <div class="tv-video-actions">
                <button class="tv-action-btn" id="castBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1,18V16H3A2,2 0 0,1 5,18H1M1,14V12A4,4 0 0,1 5,16H3A2,2 0 0,0 1,14M1,10V8A6,6 0 0,1 7,14H5A4,4 0 0,0 1,10M21,3H3C1.89,3 1,3.89 1,5V8H3V5H21V19H14V21H21A2,2 0 0,0 23,19V5C23,3.89 22.1,3 21,3Z"/>
                    </svg>
                </button>
                <button class="tv-action-btn" id="fullscreenBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5,5H10V7H7V10H5V5M14,5H19V10H17V7H14V5M17,14H19V19H14V17H17V14M10,17V19H5V14H7V17H10Z"/>
                    </svg>
                </button>
                <button class="tv-close-btn" id="closeVideoBtn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Contrôles de lecture centraux -->
        <div class="tv-video-center-controls" id="centerControls">
            <button class="tv-center-btn tv-rewind-btn" id="rewindBtn">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,5V1L7,6L12,11V7A6,6 0 0,1 18,13A6,6 0 0,1 12,19A6,6 0 0,1 6,13H4A8,8 0 0,0 12,21A8,8 0 0,0 20,13A8,8 0 0,0 12,5Z"/>
                </svg>
                
            </button>
            
            <button class="tv-center-btn tv-play-pause-btn" id="playPauseBtn">
                <svg class="tv-play-icon" width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                </svg>
                <svg class="tv-pause-icon" width="64" height="64" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                    <path d="M14,19H18V5H14M6,19H10V5H6V19Z"/>
                </svg>
            </button>
            
            <button class="tv-center-btn tv-forward-btn" id="forwardBtn">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,5V1L17,6L12,11V7A6,6 0 0,0 6,13A6,6 0 0,0 12,19A6,6 0 0,0 18,13H20A8,8 0 0,1 12,21A8,8 0 0,1 4,13A8,8 0 0,1 12,5Z"/>
                </svg>
               
            </button>
        </div>

        <!-- Barre de progression et contrôles du bas -->
        <div class="tv-video-bottom-controls" id="bottomControls">
            <div class="tv-progress-container">
                <div class="tv-progress-bar" id="progressBar">
                    <div class="tv-progress-buffer" id="progressBuffer"></div>
                    <div class="tv-progress-played" id="progressPlayed"></div>
                    <div class="tv-progress-handle" id="progressHandle"></div>
                </div>
            </div>
            
            <div class="tv-bottom-row">
                <div class="tv-left-controls">
                    <button class="tv-control-btn tv-play-btn" id="bottomPlayBtn">
                        <svg class="tv-play-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                        </svg>
                        <svg class="tv-pause-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
                            <path d="M14,19H18V5H14M6,19H10V5H6V19Z"/>
                        </svg>
                    </button>
                    <div class="tv-volume-container">
                        <button class="tv-control-btn tv-volume-btn" id="volumeBtn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,3.23V5.29C16.89,6.15 19,8.83 19,12C19,15.17 16.89,17.85 14,18.71V20.77C18.01,19.86 21,16.28 21,12C21,7.72 18.01,4.14 14,3.23M16.5,12C16.5,10.23 15.5,8.71 14,7.97V16C15.5,15.29 16.5,13.76 16.5,12M3,9V15H7L12,20V4L7,9H3Z"/>
                            </svg>
                        </button>
                        <div class="tv-volume-control" id="volumeControl">
                            <div class="tv-volume-bar" id="volumeBar">
                                <div class="tv-volume-fill" id="volumeFill"></div>
                                <div class="tv-volume-handle" id="volumeHandle"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tv-time-display" id="timeDisplay">
                        <span id="currentTime">0:00</span>
                        <span class="tv-time-separator">/</span>
                        <span id="totalTime">0:00</span>
                    </div>
                </div>
                
                <div class="tv-right-controls">
                    <button class="tv-control-btn tv-subtitle-btn" id="subtitleBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20,4H4A2,2 0 0,0 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6A2,2 0 0,0 20,4M20,18H4V6H20V18M6,10H8V12H6V10M6,14H14V16H6V14M16,14H18V16H16V14M10,10H18V12H10V10Z"/>
                        </svg>
                    </button>
                 
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Élément vidéo -->
    <video id="videoElement" preload="metadata" playsinline>
        Votre navigateur ne supporte pas la lecture vidéo.
    </video>
</div>

<style>
/* Base TV Interface */
.tv-interface {
    display: flex;
    height: 100vh;
    background: #0f0f23;
    color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    overflow: hidden;
}

/* Sidebar */
.tv-sidebar {
    width: 280px;
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
    display: flex;
    flex-direction: column;
    border-right: 1px solid #333;
}

.tv-logo {
    padding: 24px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid #333;
    color: #4facfe;
    font-weight: 700;
    font-size: 1.2rem;
}

.tv-nav {
    flex: 1;
    padding: 16px 0;
}

.tv-nav-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    outline: none;
}

.tv-nav-item:hover,
.tv-nav-item:focus {
    background: rgba(79, 172, 254, 0.1);
    border-left-color: #4facfe;
}

.tv-nav-item.active {
    background: rgba(79, 172, 254, 0.2);
    border-left-color: #4facfe;
    color: #4facfe;
}

.tv-nav-item.focused {
    background: rgba(79, 172, 254, 0.3);
    border-left-color: #4facfe;
    box-shadow: inset 0 0 0 2px #4facfe;
}

.tv-nav-logout {
    margin-top: auto;
    color: #ff6b6b;
}

.tv-nav-logout:hover,
.tv-nav-logout:focus {
    background: rgba(255, 107, 107, 0.1);
    border-left-color: #ff6b6b;
}

.tv-user-info {
    padding: 20px;
    border-top: 1px solid #333;
    display: flex;
    align-items: center;
    gap: 12px;
}

.tv-user-avatar {
    width: 40px;
    height: 40px;
    background: #4facfe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tv-user-role {
    font-size: 0.75rem;
    color: #999;
}

.tv-user-name {
    font-weight: 600;
}

/* Main Content */
.tv-main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.tv-content-header {
    padding: 24px 32px;
    border-bottom: 1px solid #333;
}

.tv-content-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 8px 0;
    color: #ffffff;
}

.tv-breadcrumb {
    font-size: 0.9rem;
    color: #999;
}

.tv-breadcrumb .separator {
    margin: 0 8px;
}

.tv-breadcrumb .current {
    color: #4facfe;
}

.tv-content-area {
    flex: 1;
    padding: 24px 32px;
    overflow-y: auto;
}

/* Loading State */
.tv-loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    gap: 16px;
}

.tv-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #333;
    border-top: 4px solid #4facfe;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Grid Layout */
.tv-grid {
    display: grid;
    gap: 20px;
}

/* Gallery Cards */
.gallery-card {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 16px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    outline: none;
}

.gallery-card:hover,
.gallery-card:focus {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    border-color: #4facfe;
}

.gallery-card.focused {
    border-color: #4facfe;
    box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.3);
}

.gallery-thumbnail {
    height: 160px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    position: relative;
}

.gallery-info h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: #ffffff;
}

.gallery-meta {
    font-size: 0.85rem;
    color: #999;
}

.video-count-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Video Cards */
.video-card {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    outline: none;
}

.video-card:hover,
.video-card:focus {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    border-color: #4facfe;
}

.video-card.focused {
    border-color: #4facfe;
    box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.3);
}

.video-thumbnail {
    height: 180px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.video-info {
    padding: 16px;
}

.video-info h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 8px 0;
    color: #ffffff;
}

.video-meta {
    font-size: 0.85rem;
    color: #999;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.video-duration {
    background: rgba(79, 172, 254, 0.2);
    color: #4facfe;
    padding: 4px 8px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.75rem;
}

/* Back Button */
.back-button {
    background: rgba(79, 172, 254, 0.2);
    color: #4facfe;
    border: 2px solid #4facfe;
    padding: 12px 20px;
    border-radius: 12px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    text-decoration: none;
    outline: none;
}

.back-button:hover,
.back-button:focus {
    background: #4facfe;
    color: #ffffff;
    transform: translateY(-2px);
}

.back-button.focused {
    box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.3);
}

/* Video Player - Style Netflix */
.tv-video-player {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #000000;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: none;
}

.tv-video-player.show-cursor {
    cursor: default;
}

#videoElement {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #000000;
}

/* Overlay pour tous les contrôles */
.tv-video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0.7) 0%,
        transparent 20%,
        transparent 80%,
        rgba(0, 0, 0, 0.7) 100%
    );
}

.tv-video-overlay.visible {
    opacity: 1;
}

/* Header avec titre et actions */
.tv-video-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 40px;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), transparent);
}

.tv-video-back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: rgba(42, 42, 42, 0.8);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tv-video-back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.tv-video-title-area {
    flex: 1;
    margin: 0 20px;
}

.tv-video-title {
    color: #ffffff;
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 4px 0;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

.tv-video-episode-info {
    color: #cccccc;
    font-size: 1.1rem;
    font-weight: 400;
    opacity: 0.9;
}

.tv-video-actions {
    display: flex;
    gap: 12px;
}

.tv-action-btn,
.tv-close-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: rgba(42, 42, 42, 0.8);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
}

.tv-action-btn:hover,
.tv-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

/* Contrôles centraux */
.tv-video-center-controls {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    align-items: center;
    gap: 60px;
}

.tv-center-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(42, 42, 42, 0.9);
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: all 0.2s ease;
    outline: none;
}

.tv-play-pause-btn {
    width: 80px;
    height: 80px;
}

.tv-rewind-btn,
.tv-forward-btn {
    width: 64px;
    height: 64px;
}



.tv-center-btn-text {
    position: absolute;
    bottom: -8px;
    right: -8px;
    background: #e50914;
    color: white;
    font-size: 12px;
    font-weight: bold;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Contrôles du bas */
.tv-video-bottom-controls {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px 40px 40px;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
}

.tv-progress-container {
    margin-bottom: 16px;
    height: 8px;
    position: relative;
}

.tv-progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    position: relative;
    cursor: pointer;
    transition: height 0.2s ease;
}

.tv-progress-bar:hover {
    height: 8px;
}

.tv-progress-buffer {
    height: 100%;
    background: rgba(255, 255, 255, 0.4);
    border-radius: 2px;
    width: 0%;
    transition: width 0.1s ease;
}

.tv-progress-played {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: #e50914;
    border-radius: 2px;
    width: 0%;
    transition: width 0.1s ease;
}

.tv-progress-handle {
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 16px;
    height: 16px;
    background: #e50914;
    border: 2px solid white;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.2s ease;
    left: 0%;
}

.tv-progress-bar:hover .tv-progress-handle {
    opacity: 1;
}

.tv-bottom-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.tv-left-controls,
.tv-right-controls {
    display: flex;
    align-items: center;
    gap: 16px;
}

.tv-control-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: transparent;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.tv-control-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.tv-time-display {
    display: flex;
    align-items: center;
    gap: 4px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    position: relative;
    left: 7rem;
    min-width: 100px;
}

.tv-time-separator {
    opacity: 0.7;
}

/* Conteneur de volume */
.tv-volume-container {
    position: relative;
    display: inline-flex;
    align-items: center;
}

/* Contrôle de volume */
.tv-volume-control {
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(42, 42, 42, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 8px;
    padding: 8px 12px;
    margin-left: 8px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    white-space: nowrap;
    z-index: 1000;
}
}

.tv-volume-container:hover .tv-volume-control,
.tv-volume-control:hover,
.tv-volume-control {
    opacity: 1 !important;
    visibility: visible !important;
}

.tv-volume-bar {
    width: 80px;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 2px;
    position: relative;
    cursor: pointer;
    margin: 0 auto;
    border: 1px solid rgba(255, 255, 255, 0.5); /* Debug: bordure visible */
}

.tv-volume-fill {
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    background: #e50914;
    border-radius: 2px;
    width: 0%;
    transition: width 0.1s ease;
}

.tv-volume-handle {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 12px;
    background: #e50914;
    border: 2px solid white;
    border-radius: 50%;
    left: 0%;
    transition: left 0.1s ease;
    cursor: pointer;
}
    cursor: pointer;
}

.tv-volume-bar:hover .tv-volume-handle {
    width: 14px;
    height: 14px;
}

.tv-volume-control:hover .tv-volume-bar {
    width: 100px;
    transition: width 0.3s ease;
}

/* Animation au hover du bouton volume */
.tv-volume-btn {
    position: relative;
}

.tv-volume-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: scale(1.1);
}

/* Responsive pour petits écrans */
@media (max-width: 768px) {
    .tv-video-header {
        padding: 16px 20px;
    }
    
    .tv-video-title {
        font-size: 1.5rem;
    }
    
    .tv-video-episode-info {
        font-size: 1rem;
    }
    
    .tv-video-center-controls {
        gap: 40px;
    }
    
    .tv-play-pause-btn {
        width: 64px;
        height: 64px;
    }
    
    .tv-rewind-btn,
    .tv-forward-btn {
        width: 48px;
        height: 48px;
    }
    
    .tv-video-bottom-controls {
        padding: 16px 20px 32px;
    }
}

/* Animation pour les icônes */
.tv-play-icon,
.tv-pause-icon {
    transition: opacity 0.2s ease;
}



/* Masquer les contrôles natifs du navigateur */
#videoElement::-webkit-media-controls {
    display: none !important;
}

#videoElement::-webkit-media-controls-panel {
    display: none !important;
}

#videoElement::-webkit-media-controls-play-button {
    display: none !important;
}

#videoElement::-webkit-media-controls-start-playback-button {
    display: none !important;
}

/* Styles pour les notifications/indicateurs */
.tv-volume-indicator,
.tv-buffer-indicator,
.tv-video-notification {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 600;
    z-index: 100;
    opacity: 0;
    transition: opacity 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.tv-volume-indicator.show,
.tv-buffer-indicator.show {
    opacity: 1;
}

/* Animation de chargement */
.tv-loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid #e50914;
    border-radius: 50%;
    animation: tv-spin 1s linear infinite;
    z-index: 100;
}

@keyframes tv-spin {
    0% { transform: translate(-50%, -50%) rotate(0deg); }
    100% { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Amélioration du handle de la barre de progression */
.tv-progress-bar:hover .tv-progress-handle,
.tv-progress-bar.dragging .tv-progress-handle {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.2);
}

.tv-progress-bar.dragging {
    height: 8px;
}

/* Style pour les tooltips de temps */
.tv-time-tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
    margin-bottom: 8px;
}

.tv-progress-bar:hover .tv-time-tooltip {
    opacity: 1;
}

/* States */
.tv-no-content,
.tv-error {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.tv-error {
    color: #ff6b6b;
}

/* Responsive */
@media (max-width: 1200px) {
    .tv-grid {
        gap: 16px;
    }
}

@media (max-width: 768px) {
    .tv-sidebar {
        width: 240px;
    }
    
    .tv-content-area {
        padding: 16px 20px;
    }
    
    .tv-grid {
        gap: 12px;
    }
}

/* Search Interface */
.tv-search-container {
    padding: 30px 40px;
    width: 100%;
    min-height: 100vh;
    box-sizing: border-box;
}

.grid-node{
    grid-template-columns: auto !important;

}

.tv-search-header {
    text-align: left;
    margin-bottom: 30px;
    padding-left: 10px;
}

.tv-search-header h2 {
    color: #ffffff;
    margin-bottom: 8px;
    font-size: 36px;
    font-weight: 600;
}

.tv-search-header p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 18px;
    margin: 0;
}

.tv-search-input-container {
    position: relative;
    width: 100%;
    margin-bottom: 40px;
    max-width: none;
}

.tv-search-icon {
    position: absolute;
    left: 24px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.6);
    z-index: 2;
    width: 28px;
    height: 28px;
}

.tv-search-input {
    width: 100%;
    padding: 24px 70px;
    font-size: 20px;
    background: rgba(42, 42, 42, 0.9);
    border: 3px solid transparent;
    border-radius: 12px;
    color: white;
    outline: none;
    transition: all 0.3s ease;
    box-sizing: border-box;
    backdrop-filter: blur(10px);
}



.tv-search-results {
    width: 100%;
    margin-top: 0;
}

.tv-search-results-header h3 {
    color: #ffffff;
    margin-bottom: 30px;
    font-size: 26px;
    font-weight: 500;
    padding-left: 10px;
    border-bottom: 2px solid rgba(229, 9, 20, 0.3);
    padding-bottom: 15px;
}

/* Grille spécifique pour les résultats de recherche */
.tv-search-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 0;
    margin-top: 20px;
    width: 100%;
}

.tv-search-grid .video-card {
    display: flex;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    outline: none;
    height: 120px;
}

.tv-search-grid .video-card:hover,
.tv-search-grid .video-card:focus {
    transform: translateX(8px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    border-color: #4facfe;
}

.tv-search-grid .video-card.focused {
    border-color: #4facfe;
    box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.3);
}

.tv-search-grid .video-thumbnail {
    width: 200px;
    height: 120px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    flex-shrink: 0;
}

.tv-search-grid .video-info {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.tv-search-grid .video-info h3 {
    color: #ffffff;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px 0;
}



.tv-search-grid .video-card .gallery-tag {
    font-size: 14px;
    font-weight: 500;
}

.tv-search-grid .video-card .file-size {
    color: rgba(255, 255, 255, 0.6);
    font-size: 12px;
}.tv-search-placeholder,
.tv-no-results {
    text-align: center;
    padding: 100px 20px;
    color: rgba(255, 255, 255, 0.6);
    width: 100%;
}

.tv-search-placeholder svg,
.tv-no-results svg {
    margin-bottom: 25px;
    width: 80px;
    height: 80px;
}

.tv-search-placeholder p,
.tv-no-results p {
    font-size: 22px;
    margin: 15px 0;
    font-weight: 300;
}

.tv-no-results small {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.4);
}

        /* Loading state for search */
        .tv-search-loading {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .tv-search-loading p {
            font-size: 16px;
            margin: 15px 0 0 0;
        }

        .tv-search-loading svg {
            margin-bottom: 15px;
        }

        .tv-search-error {
            text-align: center;
            padding: 60px 20px;
            color: rgba(229, 9, 20, 0.8);
        }

        .tv-search-error p {
            font-size: 18px;
            margin: 15px 0 5px 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .tv-search-error small {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
        }

        .tv-search-error svg {
            margin-bottom: 15px;
        }/* Settings Interface */
.tv-settings-container {
    padding: 40px;
    max-width: 800px;
    margin: 0 auto;
}

.tv-settings-header h2 {
    color: #e5e5e5;
    margin-bottom: 10px;
}

.tv-settings-content {
    background: rgba(42, 42, 42, 0.8);
    border-radius: 8px;
    padding: 30px;
    margin-top: 20px;
}

.tv-settings-section {
    margin-bottom: 30px;
}

.tv-settings-section:last-child {
    margin-bottom: 0;
}

.tv-settings-section h3 {
    color: #ffffff;
    margin-bottom: 20px;
    font-size: 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding-bottom: 10px;
}

.tv-settings-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
}

.tv-settings-option label {
    color: #e5e5e5;
    font-size: 16px;
}

.tv-settings-select {
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 8px 15px;
    border-radius: 4px;
    font-size: 14px;
}

.tv-settings-select:focus {
    outline: none;
    border-color: #e50914;
    box-shadow: 0 0 0 2px rgba(229, 9, 20, 0.2);
}
</style>

<script>
class TVNavigationController {
    constructor() {
        this.currentSection = 'galleries';
        this.currentView = 'galleries'; // 'galleries' or 'videos'
        this.currentGallery = null;
        this.focusedItem = null;
        this.focusableItems = [];
        this.galleries = [];
        this.videos = [];
        
        this.init();
    }

    init() {
        this.setupKeyboardNavigation();
        this.setupSidebarNavigation();
        this.setupMouseNavigation(); // Ajouter la gestion des clics souris
        this.loadGalleries();
    }

    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Ne pas intercepter les événements si on est dans un champ de saisie
            const activeElement = document.activeElement;
            if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA' || activeElement.isContentEditable)) {
                return;
            }
            
            e.preventDefault();
            
            switch(e.key) {
                case 'ArrowUp':
                    this.navigateUp();
                    break;
                case 'ArrowDown':
                    this.navigateDown();
                    break;
                case 'ArrowLeft':
                    this.navigateLeft();
                    break;
                case 'ArrowRight':
                    this.navigateRight();
                    break;
                case 'Enter':
                case ' ':
                    this.selectCurrentItem();
                    break;
                case 'Escape':
                    this.handleEscape();
                    break;
                case 'Backspace':
                    this.goBack();
                    break;
            }
        });
    }

    setupSidebarNavigation() {
        const navItems = document.querySelectorAll('.tv-nav-item');
        navItems.forEach((item, index) => {
            item.addEventListener('focus', () => this.updateSidebarFocus(index));
            item.addEventListener('click', () => this.handleSidebarClick(item));
        });
    }

    setupMouseNavigation() {
        // Gestion des clics sur les cartes
        document.addEventListener('click', (event) => {
            const galleryCard = event.target.closest('.gallery-card');
            const videoCard = event.target.closest('.video-card');
            const backButton = event.target.closest('.back-button');
            
            if (galleryCard) {
                const galleryId = galleryCard.dataset.galleryId;
                const galleryName = galleryCard.dataset.galleryName;
                this.loadGalleryVideos(galleryId, galleryName);
            } else if (videoCard) {
                const videoPath = videoCard.dataset.videoPath;
                const videoTitle = videoCard.dataset.videoTitle;
                this.playVideo(videoPath, videoTitle);
            } else if (backButton) {
                this.showGalleries();
            }
        });
    }

    updateFocusableItems() {
        if (this.currentView === 'galleries') {
            this.focusableItems = Array.from(document.querySelectorAll('.gallery-card'));
        } else if (this.currentView === 'videos') {
            const backBtn = document.querySelector('.back-button');
            const videoCards = Array.from(document.querySelectorAll('.video-card'));
            this.focusableItems = backBtn ? [backBtn, ...videoCards] : videoCards;
        } else if (this.currentView === 'search') {
            // Dans la vue recherche, on peut naviguer sur les résultats trouvés
            const videoCards = Array.from(document.querySelectorAll('.video-card'));
            this.focusableItems = videoCards;
        } else {
            this.focusableItems = [];
        }
        
        // Set focus on first item if none focused
        if (this.focusableItems.length > 0 && !this.focusedItem) {
            this.setFocusedItem(0);
        }
    }

    setFocusedItem(index) {
        // Remove previous focus
        this.focusableItems.forEach(item => item.classList.remove('focused'));
        
        // Set new focus
        if (index >= 0 && index < this.focusableItems.length) {
            this.focusedItem = index;
            this.focusableItems[index].classList.add('focused');
            this.focusableItems[index].scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest' 
            });
        }
    }

    navigateUp() {
        if (this.focusableItems.length === 0) return;
        
        const cols = this.getColumnsCount();
        const newIndex = Math.max(0, this.focusedItem - cols);
        this.setFocusedItem(newIndex);
    }

    navigateDown() {
        if (this.focusableItems.length === 0) return;
        
        const cols = this.getColumnsCount();
        const newIndex = Math.min(this.focusableItems.length - 1, this.focusedItem + cols);
        this.setFocusedItem(newIndex);
    }

    navigateLeft() {
        if (this.focusableItems.length === 0) return;
        
        const newIndex = Math.max(0, this.focusedItem - 1);
        this.setFocusedItem(newIndex);
    }

    navigateRight() {
        if (this.focusableItems.length === 0) return;
        
        const newIndex = Math.min(this.focusableItems.length - 1, this.focusedItem + 1);
        this.setFocusedItem(newIndex);
    }

    getColumnsCount() {
        const grid = document.getElementById('contentGrid');
        if (!grid) return 3;
        
        const gridStyles = window.getComputedStyle(grid);
        const templateColumns = gridStyles.getPropertyValue('grid-template-columns');
        return templateColumns.split(' ').length;
    }

    selectCurrentItem() {
        if (this.focusableItems.length === 0 || this.focusedItem === null) return;
        
        const currentItem = this.focusableItems[this.focusedItem];
        
        if (currentItem.classList.contains('gallery-card')) {
            // Get gallery data
            const galleryId = currentItem.dataset.galleryId;
            const galleryName = currentItem.dataset.galleryName;
            this.loadGalleryVideos(galleryId, galleryName);
        } else if (currentItem.classList.contains('video-card')) {
            // Get video data
            const videoPath = currentItem.dataset.videoPath;
            const videoTitle = currentItem.dataset.videoTitle;
            this.playVideo(videoPath, videoTitle);
        } else if (currentItem.classList.contains('back-button')) {
            this.showGalleries();
        }
    }

    handleEscape() {
        const videoPlayer = document.getElementById('videoPlayer');
        
        if (videoPlayer.style.display !== 'none') {
            this.closeVideo();
        } else if (this.currentView === 'videos') {
            this.showGalleries();
        } else if (this.currentView === 'search') {
            // Depuis la recherche, on revient aux galeries
            this.switchSection('galleries');
        }
    }

    goBack() {
        if (this.currentView === 'videos') {
            this.showGalleries();
        } else if (this.currentView === 'search') {
            this.switchSection('galleries');
        }
    }

    updateSidebarFocus(index) {
        // Mettre à jour le focus visuel sur la sidebar
        const navItems = document.querySelectorAll('.tv-nav-item');
        navItems.forEach(item => item.classList.remove('focused'));
        if (navItems[index]) {
            navItems[index].classList.add('focused');
        }
    }

    handleSidebarClick(item) {
        const section = item.dataset.section;
        this.switchSection(section);
    }

    switchSection(section) {
        // Mettre à jour la section active
        const navItems = document.querySelectorAll('.tv-nav-item');
        navItems.forEach(item => item.classList.remove('active'));
        
        const activeItem = document.querySelector(`[data-section="${section}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
        }
        
        this.currentSection = section;
        
        switch(section) {
            case 'home':
            case 'galleries':
                this.showGalleries();
                break;
            case 'search':
                this.showSearchInterface();
                break;
            case 'settings':
                this.showSettings();
                break;
        }
    }

    showSearchInterface() {
        this.currentView = 'search';
        this.updatePageTitle('Recherche de vidéos');
        
        const html = `
            <div class="tv-search-container">
                <div class="tv-search-header">
                    <h2>Rechercher des vidéos</h2>
                    <p>Tapez le nom d'une vidéo pour la rechercher dans toutes vos galeries</p>
                </div>
                
                <div class="tv-search-input-container">
                    <svg class="tv-search-icon" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"/>
                    </svg>
                    <input type="text" 
                           class="tv-search-input" 
                           placeholder="Rechercher des vidéos par titre..."
                           id="searchInput"
                           autocomplete="off"
                           autocapitalize="off"
                           autocorrect="off"
                           spellcheck="false"
                           tabindex="0">
                </div>
                
                <div class="tv-search-results" id="searchResults">
                    <div class="tv-search-placeholder">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="rgba(255,255,255,0.3)">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <p>Commencez à taper pour rechercher des vidéos</p>
                    </div>
                </div>
            </div>
        `;
        
        // Utiliser une méthode spéciale pour la recherche qui ne force pas le grid
        const loadingState = document.getElementById('loadingState');
        const contentGrid = document.getElementById('contentGrid');
        
        contentGrid.innerHTML = html;
        loadingState.style.display = 'none';
        contentGrid.style.display = 'block'; // Block au lieu de grid pour la recherche
        
        this.setupSearchFunctionality();
    }

    setupSearchFunctionality() {
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;
        
        if (searchInput) {
            // Gestionnaire pour la saisie de texte
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                
                // Debounce search
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Permettre la recherche dès 1 caractère pour les nombres, 2+ pour le texte
                    const isNumeric = /^\d+$/.test(query);
                    const shouldSearch = (isNumeric && query.length >= 1) || (!isNumeric && query.length >= 2);
                    
                    if (shouldSearch) {
                        this.performSearch(query);
                    } else {
                        this.showSearchPlaceholder();
                    }
                }, 300);
            });
            
            // Gestionnaire pour les touches spéciales dans le champ de recherche
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    e.stopPropagation();
                    searchInput.value = '';
                    this.showSearchPlaceholder();
                    searchInput.blur();
                }
                // Empêcher la navigation TV quand on tape dans le champ
                e.stopPropagation();
            });

            // Gestionnaire pour éviter la perte de focus
            searchInput.addEventListener('blur', (e) => {
                // Re-focus si c'est un blur involontaire et qu'on est toujours en mode recherche
                setTimeout(() => {
                    if (this.currentView === 'search' && document.activeElement !== searchInput) {
                        const searchContainer = document.querySelector('.tv-search-container');
                        if (searchContainer && !searchContainer.contains(document.activeElement)) {
                            searchInput.focus();
                        }
                    }
                }, 100);
            });
            
            // Focus sur l'input de recherche avec gestion d'erreur
            this.focusSearchInput();
        }
    }

    focusSearchInput() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            // Retarder le focus pour éviter les conflits
            setTimeout(() => {
                try {
                    searchInput.focus();
                    // Sélectionner le texte existant s'il y en a
                    if (searchInput.value) {
                        searchInput.select();
                    }
                } catch (error) {
                    console.log('Focus automatique bloqué, l\'utilisateur peut cliquer manuellement');
                }
            }, 200);
        }
    }

    async performSearch(query) {
        try {
            this.showSearchLoadingState('Recherche en cours...');
            
            // Récupérer toutes les galeries avec leurs vidéos
            const response = await fetch('/test_api.php?endpoint=galleries');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Debug : afficher les données récupérées dans la console
            console.log('Données de recherche récupérées:', data);
            if (data.success && data.galleries) {
                console.log('Nombre de galeries:', data.galleries.length);
                data.galleries.forEach((gallery, i) => {
                    console.log(`Galerie ${i + 1}: ${gallery.gallery_name} - ${gallery.videos ? gallery.videos.length : 0} vidéos`);
                    if (gallery.videos) {
                        gallery.videos.forEach((video, j) => {
                            console.log(`  Video ${j + 1}: "${video.caption}" (${video.video_path})`);
                        });
                    }
                });
                
                const searchResults = this.searchInGalleries(data.galleries, query);
                console.log(`Résultats de recherche pour "${query}":`, searchResults);
                this.displaySearchResults(searchResults, query);
            } else {
                this.showNoSearchResults('Aucune galerie disponible pour la recherche');
            }
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
            this.showSearchError('Erreur lors de la recherche');
        }
    }

    showSearchLoadingState(message) {
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="tv-search-loading">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="rgba(255,255,255,0.6)">
                        <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z">
                            <animateTransform attributeName="transform"
                                            attributeType="XML"
                                            type="rotate"
                                            from="0 12 12"
                                            to="360 12 12"
                                            dur="1s"
                                            repeatCount="indefinite"/>
                        </path>
                    </svg>
                    <p>${message}</p>
                </div>
            `;
        }
    }

    showSearchError(message) {
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="tv-search-error">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="rgba(229, 9, 20, 0.6)">
                        <path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z"/>
                    </svg>
                    <p>${message}</p>
                    <small>Vérifiez votre connexion et réessayez</small>
                </div>
            `;
        }
    }

    showNoSearchResults(message) {
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="tv-no-results">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="rgba(255,255,255,0.3)">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <p>${message}</p>
                </div>
            `;
        }
    }

    searchInGalleries(galleries, query) {
        const results = [];
        const searchTerms = this.normalizeString(query).split(' ').filter(term => term.length > 0);
        
        console.log(`Recherche pour: "${query}"`);
        console.log('Termes de recherche normalisés:', searchTerms);
        
        galleries.forEach(gallery => {
            if (gallery.videos && gallery.videos.length > 0) {
                gallery.videos.forEach(video => {
                    const videoTitle = this.normalizeString(video.caption || 'Vidéo sans titre');
                    const galleryName = this.normalizeString(gallery.gallery_name);
                    const combinedText = videoTitle + ' ' + galleryName;
                    
                    // Debug pour chaque vidéo
                    console.log(`Vérification vidéo: "${video.caption}" -> normalisé: "${videoTitle}"`);
                    
                    // Recherche: au moins un des termes doit être trouvé
                    let isMatch = false;
                    let matchType = '';
                    
                    for (const term of searchTerms) {
                        console.log(`  Test du terme "${term}" dans "${videoTitle}"`);
                        
                        // Recherche exacte dans le titre (pour les noms de fichiers comme 1.mp4)
                        if (videoTitle.includes(term)) {
                            console.log(`    ✓ Correspondance trouvée dans le titre`);
                            isMatch = true;
                            matchType = 'title';
                            break;
                        }
                        // Recherche dans le nom de la galerie
                        else if (galleryName.includes(term)) {
                            console.log(`    ✓ Correspondance trouvée dans la galerie`);
                            isMatch = true;
                            matchType = 'gallery';
                            break;
                        }
                        // Recherche spéciale pour les nombres dans les noms de fichiers
                        else if (this.isNumeric(term) && this.searchNumericInTitle(video.caption || '', term)) {
                            console.log(`    ✓ Correspondance numérique trouvée`);
                            isMatch = true;
                            matchType = 'numeric';
                            break;
                        }
                        // Recherche flexible pour les nombres (dans l'autre sens aussi)
                        else if (this.isNumeric(term) && this.searchNumericFlexible(videoTitle, galleryName, term)) {
                            console.log(`    ✓ Correspondance numérique flexible trouvée`);
                            isMatch = true;
                            matchType = 'numeric-flexible';
                            break;
                        }
                        else {
                            console.log(`    ✗ Pas de correspondance`);
                        }
                    }
                    
                    // Si pas de correspondance directe, essayer la recherche fuzzy pour les mots courts
                    if (!isMatch && query.length <= 3) {
                        for (const term of searchTerms) {
                            if (this.fuzzyMatch(combinedText, term)) {
                                console.log(`    ✓ Correspondance floue trouvée`);
                                isMatch = true;
                                matchType = 'fuzzy';
                                break;
                            }
                        }
                    }
                    
                    if (isMatch) {
                        console.log(`  → AJOUTÉ aux résultats (type: ${matchType})`);
                        results.push({
                            video: video,
                            gallery: gallery,
                            matchType: matchType
                        });
                    }
                });
            }
        });
        
        console.log(`Résultats totaux: ${results.length}`);
        return results;
    }

    // Vérifier si une chaîne est numérique
    isNumeric(str) {
        return /^\d+$/.test(str);
    }

    // Recherche spécialisée pour les nombres dans les noms de fichiers
    searchNumericInTitle(title, numericTerm) {
        if (!title || !numericTerm) return false;
        
        // Recherche directe du nombre dans le titre (sans normalisation excessive)
        const lowerTitle = title.toLowerCase();
        
        // Recherche exacte
        if (lowerTitle.includes(numericTerm)) return true;
        
        // Recherche avec des séparateurs communs (., -, _, espace)
        const patterns = [
            numericTerm + '.',  // 1.mp4
            numericTerm + '-',  // video1-hd
            numericTerm + '_',  // file1_final
            '.' + numericTerm,  // v.1
            '-' + numericTerm,  // video-1
            '_' + numericTerm,  // file_1
            ' ' + numericTerm,  // video 1
            numericTerm + ' '   // 1 video
        ];
        
        if (patterns.some(pattern => lowerTitle.includes(pattern))) return true;
        
        // Recherche bi-directionnelle pour les nombres
        // Si on cherche "22" et qu'on a "2", ou si on cherche "2" et qu'on a "22"
        const titleNumbers = title.match(/\d+/g) || [];
        
        for (const titleNum of titleNumbers) {
            // Correspondance exacte
            if (titleNum === numericTerm) return true;
            
            // Si le terme de recherche commence par ce nombre ou vice-versa
            if (numericTerm.startsWith(titleNum) || titleNum.startsWith(numericTerm)) {
                return true;
            }
        }
        
        return false;
    }

    // Recherche numérique flexible - trouve les correspondances partielles
    searchNumericFlexible(videoTitle, galleryName, numericTerm) {
        const allText = (videoTitle + ' ' + galleryName).toLowerCase();
        
        // Extraire tous les nombres du texte
        const foundNumbers = allText.match(/\d+/g) || [];
        
        console.log(`    Recherche flexible: terme="${numericTerm}", nombres trouvés: [${foundNumbers.join(', ')}]`);
        
        for (const foundNum of foundNumbers) {
            // Correspondance exacte
            if (foundNum === numericTerm) {
                console.log(`      → Correspondance exacte: ${foundNum}`);
                return true;
            }
            
            // Si le terme de recherche est contenu dans le nombre trouvé
            // Ex: chercher "2" et trouver "22", "23", "123"
            if (foundNum.includes(numericTerm)) {
                console.log(`      → Le nombre ${foundNum} contient ${numericTerm}`);
                return true;
            }
            
            // Si le nombre trouvé est contenu dans le terme de recherche
            // Ex: chercher "22" et trouver "2"
            if (numericTerm.includes(foundNum)) {
                console.log(`      → Le terme ${numericTerm} contient ${foundNum}`);
                return true;
            }
        }
        
        return false;
    }

    // Fonction pour la recherche floue (pour les mots courts et erreurs de frappe)
    fuzzyMatch(text, term) {
        if (term.length < 2) return false;
        
        // Recherche de sous-séquences
        let termIndex = 0;
        for (let i = 0; i < text.length && termIndex < term.length; i++) {
            if (text[i] === term[termIndex]) {
                termIndex++;
            }
        }
        
        return termIndex === term.length;
    }

    // Fonction pour normaliser les chaînes de caractères (gère les accents et la casse)
    normalizeString(str) {
        if (!str) return '';
        
        // Convertir en minuscules et normaliser les accents
        return str.toLowerCase()
                  .normalize('NFD') // Décompose les caractères accentués
                  .replace(/[\u0300-\u036f]/g, '') // Supprime les diacritiques (accents)
                  .replace(/[^\w\s\d.-]/gi, ' ') // Remplace les caractères spéciaux par des espaces (garde les chiffres, points, tirets)
                  .replace(/\s+/g, ' ') // Remplace les espaces multiples par un seul
                  .trim(); // Supprime les espaces en début/fin
    }

    displaySearchResults(results, query) {
        const searchResultsContainer = document.getElementById('searchResults');
        
        if (results.length === 0) {
            searchResultsContainer.innerHTML = `
                <div class="tv-no-results">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="rgba(255,255,255,0.3)">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <p>Aucune vidéo trouvée pour "${query}"</p>
                    <small>Essayez avec d'autres mots-clés</small>
                </div>
            `;
            return;
        }
        
        let html = `
            <div class="tv-search-results-header">
                <h3>${results.length} résultat${results.length > 1 ? 's' : ''} trouvé${results.length > 1 ? 's' : ''} pour "${query}"</h3>
            </div>
            <div class="tv-search-grid" id="contentGrid">
        `;
        
        results.forEach((result, index) => {
            const video = result.video;
            const gallery = result.gallery;
            const duration = video.duration ? this.formatDuration(video.duration) : '';
            
            html += `
                <div class="video-card" 
                     data-video-path="${video.video_path}"
                     data-video-title="${video.caption || 'Vidéo sans titre'}"
                     data-gallery-name="${gallery.gallery_name}"
                     tabindex="0">
                    <div class="video-thumbnail">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)">
                            <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                        </svg>
                        ${duration ? `<div class="video-duration">${duration}</div>` : ''}
                        
                    </div>
                    <div class="video-info">
                        <h3>${video.caption || 'Vidéo sans titre'}</h3>
                        <div class="video-meta">
                            <span class="gallery-tag">📁 ${gallery.gallery_name}</span>
                            ${video.file_size ? `<span class="file-size">${(video.file_size / 1024 / 1024).toFixed(1)} MB</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        searchResultsContainer.innerHTML = html;
        
        // Mettre à jour les éléments focusables pour la navigation TV
        this.updateFocusableItems();
        this.setFocusedItem(0);
    }

    showSearchPlaceholder() {
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.innerHTML = `
                <div class="tv-search-placeholder">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="rgba(255,255,255,0.3)">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <p>Commencez à taper pour rechercher des vidéos</p>
                </div>
            `;
        }
    }

    showSettings() {
        this.currentView = 'settings';
        this.updatePageTitle('Paramètres TV');
        
        const html = `
            <div class="tv-settings-container">
                <div class="tv-settings-header">
                    <h2>Paramètres de l'interface TV</h2>
                </div>
                
                <div class="tv-settings-content">
                    <div class="tv-settings-section">
                        <h3>Lecture vidéo</h3>
                        <div class="tv-settings-option">
                            <label>Qualité par défaut</label>
                            <select class="tv-settings-select">
                                <option value="auto">Automatique</option>
                                <option value="1080p">1080p</option>
                                <option value="720p">720p</option>
                                <option value="480p">480p</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="tv-settings-section">
                        <h3>Interface</h3>
                        <div class="tv-settings-option">
                            <label>Mode sombre</label>
                            <input type="checkbox" checked disabled>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        this.showContent(html);
        this.focusableItems = [];
    }

    async loadGalleries() {
        try {
            this.showLoadingState('Chargement des galeries...');
            
            const response = await fetch('/test_api.php?endpoint=galleries');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Galeries chargées:', data);

            if (data.success && data.galleries && data.galleries.length > 0) {
                this.galleries = data.galleries;
                this.displayGalleries();
            } else {
                this.showNoContent('Aucune galerie disponible');
            }
        } catch (error) {
            console.error('Erreur lors du chargement des galeries:', error);
            this.showError('Erreur lors du chargement des galeries');
        }
    }

    displayGalleries() {
        this.currentView = 'galleries';
        this.updatePageTitle('Mes Galeries');
        
        let html = '';
        
        this.galleries.forEach((gallery, index) => {
            const videoCount = gallery.videos ? gallery.videos.length : 0;
            
            html += `
                <div class="gallery-card" 
                     data-gallery-id="${gallery.gallery_id}"
                     data-gallery-name="${gallery.gallery_name}"
                     tabindex="0">
                    <div class="gallery-thumbnail">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="rgba(255,255,255,0.8)">
                            <path d="M19,7H22V9H19V7M19,10H22V12H19V10M19,13H22V15H19V13M18,7V15H14L12,13V15H2V5H12V7H18M4,7V13H10V11L12,13H16V9H4Z"/>
                        </svg>
                        <div class="video-count-badge">${videoCount} vidéo${videoCount !== 1 ? 's' : ''}</div>
                    </div>
                    <div class="gallery-info">
                        <h3>${gallery.gallery_name}</h3>
                        <div class="gallery-meta">
                            ${videoCount} vidéo${videoCount !== 1 ? 's' : ''} • 
                            ${gallery.gallery_created_at ? new Date(gallery.gallery_created_at).toLocaleDateString('fr-FR') : 'Date inconnue'}
                        </div>
                    </div>
                </div>
            `;
        });
        
        this.showContent(html);
        this.updateFocusableItems();
    }

    async loadGalleryVideos(galleryId, galleryName) {
        try {
            this.currentGallery = { id: galleryId, name: galleryName };
            this.showLoadingState('Chargement des vidéos...');
            
            const response = await fetch(`/test_api.php?endpoint=videos&gallery_id=${galleryId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Vidéos chargées:', data);

            if (data.success && data.videos && data.videos.length > 0) {
                this.videos = data.videos;
                this.displayVideos();
            } else {
                this.showNoContent('Aucune vidéo dans cette galerie');
            }
        } catch (error) {
            console.error('Erreur lors du chargement des vidéos:', error);
            this.showError('Erreur lors du chargement des vidéos');
        }
    }

    displayVideos() {
        this.currentView = 'videos';
        this.updatePageTitle(this.currentGallery.name);
        
        let html = `
            <div class="back-button" tabindex="0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20,11H7.83L13.42,5.41L12,4L4,12L12,20L13.42,18.59L7.83,13H20V11Z"/>
                </svg>
                Retour aux galeries
            </div>
        `;
        
        this.videos.forEach((video, index) => {
            const duration = this.formatDuration(video.duration);
            const size = this.formatFileSize(video.file_size);
            
            html += `
                <div class="video-card" 
                     data-video-path="${video.video_path}"
                     data-video-title="${video.video_title || 'Vidéo sans titre'}"
                     tabindex="0">
                    <div class="video-thumbnail">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="rgba(255,255,255,0.9)">
                            <path d="M8,5.14V19.14L19,12.14L8,5.14Z"/>
                        </svg>
                    </div>
                    <div class="video-info">
                        <h4>${video.video_title || 'Vidéo sans titre'}</h4>
                        <div class="video-meta">
                            <span>${size}</span>
                            <span class="video-duration">${duration}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        this.showContent(html);
        this.updateFocusableItems();
    }

    showGalleries() {
        this.currentView = 'galleries';
        this.currentGallery = null;
        this.displayGalleries();
    }

    playVideo(videoPath, videoTitle) {
        const videoPlayer = document.getElementById('videoPlayer');
        const videoElement = document.getElementById('videoElement');
        const videoTitleElement = document.getElementById('videoTitle');
        const videoEpisodeInfo = document.getElementById('videoEpisodeInfo');
        const videoOverlay = document.getElementById('videoOverlay');
        
        // Configuration initiale
        videoTitleElement.textContent = videoTitle;
        videoEpisodeInfo.textContent = 'Épisode 1'; // Vous pouvez passer cette info en paramètre
        videoElement.src = videoPath;
        videoPlayer.style.display = 'flex';
        
        // Variables pour les contrôles
        let isPlaying = false;
        let controlsVisible = true;
        let hideControlsTimeout;
        let isDragging = false;
        
        // Éléments de contrôle
        const overlay = videoOverlay;
        const playPauseBtn = document.getElementById('playPauseBtn');
        const bottomPlayBtn = document.getElementById('bottomPlayBtn');
        const rewindBtn = document.getElementById('rewindBtn');
        const forwardBtn = document.getElementById('forwardBtn');
        const progressBar = document.getElementById('progressBar');
        const progressPlayed = document.getElementById('progressPlayed');
        const progressBuffer = document.getElementById('progressBuffer');
        const progressHandle = document.getElementById('progressHandle');
        const currentTimeEl = document.getElementById('currentTime');
        const totalTimeEl = document.getElementById('totalTime');
        const volumeBtn = document.getElementById('volumeBtn');
        const volumeControl = document.getElementById('volumeControl');
        const volumeBar = document.getElementById('volumeBar');
        const volumeFill = document.getElementById('volumeFill');
        const volumeHandle = document.getElementById('volumeHandle');
        
        // Debug: vérifier si les éléments sont trouvés
        console.log('Elements volume trouvés:', {
            volumeBtn: !!volumeBtn,
            volumeControl: !!volumeControl,
            volumeBar: !!volumeBar,
            volumeFill: !!volumeFill,
            volumeHandle: !!volumeHandle
        });
        const subtitleBtn = document.getElementById('subtitleBtn');
        const castBtn = document.getElementById('castBtn');
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const fullscreenBtn2 = document.getElementById('fullscreenBtn2');
        const closeBtn = document.getElementById('closeVideoBtn');
        const backBtn = document.getElementById('backBtn');
        
        // Fonction pour formater le temps
        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);
            
            if (hours > 0) {
                return `${hours}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
        
        // Fonction pour afficher/masquer les contrôles
        function showControls() {
            controlsVisible = true;
            overlay.classList.add('visible');
            videoPlayer.classList.add('show-cursor');
            
            clearTimeout(hideControlsTimeout);
            hideControlsTimeout = setTimeout(() => {
                if (isPlaying && !isDragging) {
                    hideControls();
                }
            }, 3000);
        }
        
        function hideControls() {
            if (!isDragging) {
                controlsVisible = false;
                overlay.classList.remove('visible');
                videoPlayer.classList.remove('show-cursor');
            }
        }
        
        // Fonction de lecture/pause améliorée
        function togglePlayPause() {
            try {
                if (videoElement.paused || videoElement.ended) {
                    const playPromise = videoElement.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            isPlaying = true;
                            updatePlayPauseIcons(true);
                            console.log('Lecture démarrée');
                        }).catch(error => {
                            console.error('Erreur lors de la lecture:', error);
                            isPlaying = false;
                            updatePlayPauseIcons(false);
                        });
                    }
                } else {
                    videoElement.pause();
                    isPlaying = false;
                    updatePlayPauseIcons(false);
                    console.log('Lecture pausée');
                }
                showControls();
            } catch (error) {
                console.error('Erreur dans togglePlayPause:', error);
            }
        }
        
        // Fonction pour mettre à jour les icônes play/pause
        function updatePlayPauseIcons(playing) {
            const playIcons = document.querySelectorAll('.tv-play-icon');
            const pauseIcons = document.querySelectorAll('.tv-pause-icon');
            
            playIcons.forEach(icon => {
                icon.style.display = playing ? 'none' : 'block';
            });
            
            pauseIcons.forEach(icon => {
                icon.style.display = playing ? 'block' : 'none';
            });
        }
        
        // Variable pour sauvegarder le volume précédent
        let previousVolume = 0.5;
        
        // Fonction pour gérer le volume
        function toggleVolume() {
            if (videoElement.muted || videoElement.volume === 0) {
                // Activer le volume
                videoElement.muted = false;
                videoElement.volume = previousVolume;
                updateVolumeDisplay();
                showNotification(`Volume: ${Math.round(videoElement.volume * 100)}%`);
                console.log('Volume activé');
            } else {
                // Désactiver le volume
                previousVolume = videoElement.volume; // Sauvegarder le volume actuel
                videoElement.muted = true;
                updateVolumeDisplay();
                showNotification('Volume désactivé');
                console.log('Volume désactivé');
            }
            showControls();
        }
        
        // Fonction pour mettre à jour l'affichage du volume (icône + barre)
        function updateVolumeDisplay() {
            updateVolumeIcon();
            updateVolumeBar();
        }
        
        // Fonction pour mettre à jour la barre de volume
        function updateVolumeBar() {
            if (!volumeFill || !volumeHandle) {
                console.log('Elements volume manquants:', {volumeFill, volumeHandle});
                return;
            }
            
            const volume = videoElement.muted ? 0 : videoElement.volume;
            const percentage = volume * 100;
            
            console.log('Mise à jour volume:', {volume, percentage});
            volumeFill.style.width = `${percentage}%`;
            volumeHandle.style.left = `${percentage}%`;
        }
        
        // Fonction pour définir le volume depuis la barre
        function setVolumeFromBar(event) {
            if (!volumeBar) return;
            
            const rect = volumeBar.getBoundingClientRect();
            const x = event.clientX;
            const barLeft = rect.left;
            const barWidth = rect.width;
            
            // Calculer le pourcentage (de gauche à droite)
            let percentage = (x - barLeft) / barWidth;
            percentage = Math.max(0, Math.min(1, percentage));
            
            videoElement.volume = percentage;
            if (percentage > 0) {
                videoElement.muted = false;
            }
            
            updateVolumeDisplay();
            showNotification(`Volume: ${Math.round(percentage * 100)}%`);
            showControls();
        }
        
        // Fonction pour mettre à jour l'icône de volume
        function updateVolumeIcon() {
            if (!volumeBtn) return;
            
            const volume = videoElement.muted ? 0 : videoElement.volume;
            
            if (volume === 0) {
                volumeBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,4L9.91,6.09L12,8.18M4.27,3L3,4.27L7.73,9H3V15H7L12,20V13.27L16.25,17.53C15.58,18.04 14.83,18.46 14,18.7V20.77C15.38,20.45 16.63,19.82 17.68,18.96L19.73,21L21,19.73L12,10.73M19,12C19,12.94 18.8,13.82 18.46,14.64L19.97,16.15C20.62,14.91 21,13.5 21,12C21,7.72 18,4.14 14,3.23V5.29C16.89,6.15 19,8.83 19,12M16.5,12C16.5,10.23 15.5,8.71 14,7.97V10.18L16.45,12.63C16.5,12.43 16.5,12.21 16.5,12Z"/>
                    </svg>
                `;
            } else if (volume < 0.3) {
                volumeBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5,9V15H9L14,20V4L9,9M18.5,12C18.5,10.23 17.5,8.71 16,7.97V16C17.5,15.29 18.5,13.76 18.5,12Z"/>
                    </svg>
                `;
            } else if (volume < 0.7) {
                volumeBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14,3.23V5.29C16.89,6.15 19,8.83 19,12C19,15.17 16.89,17.85 14,18.71V20.77C18.01,19.86 21,16.28 21,12C21,7.72 18.01,4.14 14,3.23M16.5,12C16.5,10.23 15.5,8.71 14,7.97V16C15.5,15.29 16.5,13.76 16.5,12M3,9V15H7L12,20V4L7,9H3Z"/>
                    </svg>
                `;
            } else {
                volumeBtn.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14,3.23V5.29C16.89,6.15 19,8.83 19,12C19,15.17 16.89,17.85 14,18.71V20.77C18.01,19.86 21,16.28 21,12C21,7.72 18.01,4.14 14,3.23M16.5,12C16.5,10.23 15.5,8.71 14,7.97V16C15.5,15.29 16.5,13.76 16.5,12M3,9V15H7L12,20V4L7,9H3Z"/>
                    </svg>
                `;
            }
        }
        
        // Fonction pour les sous-titres
        function toggleSubtitles() {
            const tracks = videoElement.textTracks;
            let subtitlesEnabled = false;
            
            for (let i = 0; i < tracks.length; i++) {
                if (tracks[i].kind === 'subtitles' || tracks[i].kind === 'captions') {
                    if (tracks[i].mode === 'showing') {
                        tracks[i].mode = 'disabled';
                        subtitlesEnabled = false;
                    } else {
                        tracks[i].mode = 'showing';
                        subtitlesEnabled = true;
                    }
                    break;
                }
            }
            
            // Mettre à jour l'apparence du bouton
            if (subtitlesEnabled) {
                subtitleBtn.style.backgroundColor = 'rgba(229, 9, 20, 0.8)';
                console.log('Sous-titres activés');
            } else {
                subtitleBtn.style.backgroundColor = 'transparent';
                console.log('Sous-titres désactivés');
            }
            
            showControls();
            
            // Si pas de pistes de sous-titres, afficher un message
            if (tracks.length === 0) {
                showNotification('Aucun sous-titre disponible');
            }
        }
        
        // Fonction pour le Cast (Chromecast/AirPlay)
        function handleCast() {
            // Vérifier si l'API Presentation est disponible (pour Chromecast)
            if ('presentation' in navigator && 'PresentationRequest' in window) {
                const presentationRequest = new PresentationRequest(['https://www.example.com/tv-app']);
                
                presentationRequest.start().then(connection => {
                    console.log('Connected to presentation:', connection.url);
                    showNotification('Connexion au dispositif de diffusion réussie');
                }).catch(error => {
                    console.error('Erreur de diffusion:', error);
                    showNotification('Impossible de se connecter au dispositif de diffusion');
                });
            } 
            // Vérifier pour AirPlay (Safari uniquement)
            else if (videoElement.webkitShowPlaybackTargetPicker) {
                videoElement.webkitShowPlaybackTargetPicker();
            }
            // Fallback - afficher une notification
            else {
                showNotification('Diffusion non prise en charge par ce navigateur');
                console.log('Cast non supporté');
            }
            
            showControls();
        }
        
        // Fonction pour afficher les notifications
        function showNotification(message) {
            // Créer ou réutiliser l'élément de notification
            let notification = document.getElementById('videoNotification');
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'videoNotification';
                notification.className = 'tv-video-notification';
                videoPlayer.appendChild(notification);
            }
            
            notification.textContent = message;
            notification.style.display = 'block';
            notification.style.opacity = '1';
            
            // Masquer après 3 secondes
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 300);
            }, 3000);
        }
        
        // Fonction pour reculer/avancer
        function rewind() {
            videoElement.currentTime = Math.max(0, videoElement.currentTime - 10);
            showControls();
        }
        
        function forward() {
            videoElement.currentTime = Math.min(videoElement.duration, videoElement.currentTime + 10);
            showControls();
        }
        
        // Mettre à jour la barre de progression
        function updateProgress() {
            if (!isDragging && videoElement.duration) {
                const percent = (videoElement.currentTime / videoElement.duration) * 100;
                progressPlayed.style.width = percent + '%';
                progressHandle.style.left = percent + '%';
                currentTimeEl.textContent = formatTime(videoElement.currentTime);
            }
        }
        
        // Mettre à jour le buffer
        function updateBuffer() {
            if (videoElement.buffered.length > 0 && videoElement.duration) {
                const bufferedEnd = videoElement.buffered.end(videoElement.buffered.length - 1);
                const percent = (bufferedEnd / videoElement.duration) * 100;
                progressBuffer.style.width = percent + '%';
            }
        }
        
        // Gestion du plein écran
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                videoPlayer.requestFullscreen().catch(err => {
                    console.log('Erreur plein écran:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        // Event listeners pour les contrôles
        if (playPauseBtn) playPauseBtn.addEventListener('click', togglePlayPause);
        if (bottomPlayBtn) bottomPlayBtn.addEventListener('click', togglePlayPause);
        if (rewindBtn) rewindBtn.addEventListener('click', rewind);
        if (forwardBtn) forwardBtn.addEventListener('click', forward);
        if (volumeBtn) volumeBtn.addEventListener('click', toggleVolume);
        if (subtitleBtn) subtitleBtn.addEventListener('click', toggleSubtitles);
        if (castBtn) castBtn.addEventListener('click', handleCast);
        if (fullscreenBtn) fullscreenBtn.addEventListener('click', toggleFullscreen);
        if (fullscreenBtn2) fullscreenBtn2.addEventListener('click', toggleFullscreen);
        if (closeBtn) closeBtn.addEventListener('click', () => this.closeVideo());
        if (backBtn) backBtn.addEventListener('click', () => this.closeVideo());
        
        // Événements pour la barre de volume
        if (volumeBar) {
            volumeBar.addEventListener('click', setVolumeFromBar);
            
            let isDragging = false;
            
            volumeBar.addEventListener('mousedown', (e) => {
                e.preventDefault();
                isDragging = true;
                setVolumeFromBar(e);
            });
            
            document.addEventListener('mousemove', (e) => {
                if (isDragging) {
                    e.preventDefault();
                    setVolumeFromBar(e);
                }
            });
            
            document.addEventListener('mouseup', () => {
                isDragging = false;
            });
            
            // Support tactile pour appareils mobiles
            volumeBar.addEventListener('touchstart', (e) => {
                e.preventDefault();
                isDragging = true;
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent('click', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                setVolumeFromBar(mouseEvent);
            });
            
            volumeBar.addEventListener('touchmove', (e) => {
                if (isDragging) {
                    e.preventDefault();
                    const touch = e.touches[0];
                    const mouseEvent = new MouseEvent('click', {
                        clientX: touch.clientX,
                        clientY: touch.clientY
                    });
                    setVolumeFromBar(mouseEvent);
                }
            });
            
            volumeBar.addEventListener('touchend', () => {
                isDragging = false;
            });
        }
        
        // Double-clic pour plein écran
        videoElement.addEventListener('dblclick', toggleFullscreen);
        
        // Clic simple sur la vidéo pour lecture/pause
        videoElement.addEventListener('click', (e) => {
            e.preventDefault();
            togglePlayPause();
            showControls();
        });
        
        // Mouvement de souris pour afficher les contrôles
        videoPlayer.addEventListener('mousemove', showControls);
        videoPlayer.addEventListener('mouseenter', showControls);
        
        // Gestion de la barre de progression
        progressBar.addEventListener('click', (e) => {
            if (videoElement.duration) {
                const rect = progressBar.getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                videoElement.currentTime = percent * videoElement.duration;
                showControls();
            }
        });
        
        // Drag sur la barre de progression
        let startDrag = (e) => {
            isDragging = true;
            showControls();
        };
        
        let duringDrag = (e) => {
            if (isDragging && videoElement.duration) {
                const rect = progressBar.getBoundingClientRect();
                const percent = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                progressPlayed.style.width = (percent * 100) + '%';
                progressHandle.style.left = (percent * 100) + '%';
                currentTimeEl.textContent = formatTime(percent * videoElement.duration);
            }
        };
        
        let endDrag = (e) => {
            if (isDragging && videoElement.duration) {
                const rect = progressBar.getBoundingClientRect();
                const percent = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                videoElement.currentTime = percent * videoElement.duration;
            }
            isDragging = false;
        };
        
        progressBar.addEventListener('mousedown', startDrag);
        document.addEventListener('mousemove', duringDrag);
        document.addEventListener('mouseup', endDrag);
        
        // Event listeners pour la vidéo
        videoElement.addEventListener('loadedmetadata', () => {
            totalTimeEl.textContent = formatTime(videoElement.duration);
            // Définir un volume par défaut s'il n'est pas défini
            if (videoElement.volume === 1) {
                videoElement.volume = 0.5; // 50% par défaut
            }
            updateVolumeIcon(); // Initialiser l'icône de volume
            updateVolumeDisplay(); // Initialiser la barre de volume
            
            // Debug: forcer l'affichage de la barre de volume
            if (volumeControl) {
                volumeControl.style.cssText = `
                    position: absolute !important;
                    left: 100% !important;
                    top: 50% !important;
                    transform: translateY(-50%) !important;
                    
                    padding: 10px !important;
                    opacity: 1 !important;
                    visibility: visible !important;
                    z-index: 9999 !important;
                   
                `;
                console.log('Style forcé appliqué à volumeControl');
            }
            
            console.log(`Vidéo chargée: ${videoElement.videoWidth}x${videoElement.videoHeight}`);
        });
        
        videoElement.addEventListener('timeupdate', updateProgress);
        videoElement.addEventListener('progress', updateBuffer);
        
        // Event listeners pour les changements de volume
        videoElement.addEventListener('volumechange', () => {
            updateVolumeIcon();
            updateVolumeDisplay();
        });
        
        videoElement.addEventListener('play', () => {
            isPlaying = true;
            updatePlayPauseIcons(true);
        });
        
        videoElement.addEventListener('pause', () => {
            isPlaying = false;
            updatePlayPauseIcons(false);
            showControls();
        });
        
        videoElement.addEventListener('ended', () => {
            isPlaying = false;
            updatePlayPauseIcons(false);
            showControls();
            // Optionnel: passer à la vidéo suivante
        });
        
        // Gestion du clavier
        const handleVideoKeydown = (e) => {
            switch(e.key) {
                case ' ':
                case 'k':
                    e.preventDefault();
                    togglePlayPause();
                    showControls();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    rewind();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    forward();
                    break;
                case 'f':
                    e.preventDefault();
                    toggleFullscreen();
                    break;
                case 'Escape':
                    if (document.fullscreenElement) {
                        document.exitFullscreen();
                    } else {
                        this.closeVideo();
                    }
                    break;
                case 'm':
                    e.preventDefault();
                    toggleVolume();
                    break;
                case 'c':
                    e.preventDefault();
                    toggleSubtitles();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    videoElement.volume = Math.min(1, videoElement.volume + 0.1);
                    showNotification(`Volume: ${Math.round(videoElement.volume * 100)}%`);
                    showControls();
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    videoElement.volume = Math.max(0, videoElement.volume - 0.1);
                    showNotification(`Volume: ${Math.round(videoElement.volume * 100)}%`);
                    showControls();
                    break;
            }
        };
        
        document.addEventListener('keydown', handleVideoKeydown);
        
        // Sauvegarder la fonction pour le nettoyage
        videoElement._handleVideoKeydown = handleVideoKeydown;
        videoElement._startDrag = startDrag;
        videoElement._duringDrag = duringDrag;
        videoElement._endDrag = endDrag;
        
        // Démarrer la vidéo
        showControls();
        videoElement.play().then(() => {
            console.log('Lecture démarrée');
        }).catch(e => {
            console.log('Autoplay échoué, interaction requise:', e);
            showControls();
        });
    }

    closeVideo() {
        const videoPlayer = document.getElementById('videoPlayer');
        const videoElement = document.getElementById('videoElement');
        
        // Nettoyer tous les event listeners
        if (videoElement._handleVideoKeydown) {
            document.removeEventListener('keydown', videoElement._handleVideoKeydown);
            videoElement._handleVideoKeydown = null;
        }
        
        if (videoElement._startDrag) {
            document.removeEventListener('mousedown', videoElement._startDrag);
            document.removeEventListener('mousemove', videoElement._duringDrag);
            document.removeEventListener('mouseup', videoElement._endDrag);
            videoElement._startDrag = null;
            videoElement._duringDrag = null;
            videoElement._endDrag = null;
        }
        
        // Sortir du plein écran si nécessaire
        if (document.fullscreenElement) {
            document.exitFullscreen();
        }
        
        // Arrêter et nettoyer la vidéo
        videoElement.pause();
        videoElement.src = '';
        videoElement.currentTime = 0;
        videoPlayer.style.display = 'none';
        
        // Nettoyer les styles
        const overlay = document.getElementById('videoOverlay');
        if (overlay) {
            overlay.classList.remove('visible');
        }
        videoPlayer.classList.remove('show-cursor');
        
        // Return focus to the content area
        this.updateFocusableItems();
        if (this.focusableItems.length > 0) {
            this.setFocusedItem(0);
        }
    }

    // Helper methods
    showLoadingState(message) {
        const loadingState = document.getElementById('loadingState');
        const contentGrid = document.getElementById('contentGrid');
        
        loadingState.querySelector('p').textContent = message;
        loadingState.style.display = 'flex';
        contentGrid.style.display = 'none';
        
        this.focusableItems = [];
        this.focusedItem = null;
    }

    showContent(html) {
        const loadingState = document.getElementById('loadingState');
        const contentGrid = document.getElementById('contentGrid');
        
        contentGrid.innerHTML = html;
        loadingState.style.display = 'none';
        contentGrid.style.display = 'grid';
    }

    showNoContent(message) {
        this.showContent(`
            <div class="tv-no-content">
                <p>${message}</p>
            </div>
        `);
    }

    showError(message) {
        this.showContent(`
            <div class="tv-error">
                <p>${message}</p>
            </div>
        `);
    }

    updatePageTitle(title) {
        document.getElementById('pageTitle').textContent = title;
    }

    updateBreadcrumb(breadcrumb) {
        document.getElementById('breadcrumb').innerHTML = breadcrumb.replace(/>/g, '<span class="separator">></span>');
    }

    formatDuration(seconds) {
        if (!seconds || seconds === '00:00:00') return 'N/A';
        
        if (typeof seconds === 'string') {
            return seconds.substring(0, 8); // HH:MM:SS format
        }
        
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        
        if (hrs > 0) {
            return `${hrs}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        } else {
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
    }

    formatFileSize(bytes) {
        if (!bytes) return 'N/A';
        
        const sizes = ['B', 'KB', 'MB', 'GB'];
        if (bytes === 0) return '0 B';
        
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
    }
}

// Global functions
function logout() {
    if (confirm('Voulez-vous vraiment vous déconnecter ?')) {
        window.location.href = '/logout';
    }
}

// Initialize TV Navigation
const tvNav = new TVNavigationController();
console.log('Interface TV avec navigation directionnelle initialisée');
</script>

<?php
$content = ob_get_clean();
require __DIR__ . "/../layouts/base.php";
?>
