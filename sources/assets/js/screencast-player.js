// Lecteur vidéo optimisé pour screencasts
class ScreencastPlayer {
    constructor(container) {
        this.container = container;
        this.video = container.querySelector('.screencast-video');
        this.controls = container.querySelector('.screencast-controls');
        this.playPauseBtn = container.querySelector('.play-pause-btn');
        this.progressContainer = container.querySelector('.progress-container');
        this.progressBar = container.querySelector('.progress-bar');
        this.timeDisplay = container.querySelector('.time-display');
        this.volumeBtn = container.querySelector('.volume-btn');
        this.volumeSlider = container.querySelector('.volume-slider');
        this.speedBtns = container.querySelectorAll('.speed-btn');
        this.fullscreenBtn = container.querySelector('.fullscreen-btn');
        this.seekBtns = container.querySelectorAll('.seek-btn');
        this.qualityBtn = container.querySelector('.quality-btn');
        this.pipBtn = container.querySelector('.pip-btn');
        this.overlay = container.querySelector('.screencast-overlay');
        this.overlayPlayBtn = container.querySelector('.overlay-play-btn');
        
        this.isPlaying = false;
        this.isDragging = false;
        this.currentSpeed = 1;
        this.hideControlsTimeout = null;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.updateTimeDisplay();
        this.setInitialVolume();
        this.setupKeyboardControls();
    }
    
    setupEventListeners() {
        // Play/Pause
        this.playPauseBtn.addEventListener('click', () => this.togglePlayPause());
        this.overlayPlayBtn.addEventListener('click', () => this.togglePlayPause());
        this.video.addEventListener('click', () => this.togglePlayPause());
        
        // Progress bar
        this.progressContainer.addEventListener('click', (e) => this.seek(e));
        this.progressContainer.addEventListener('mousedown', (e) => this.startDrag(e));
        document.addEventListener('mousemove', (e) => this.drag(e));
        document.addEventListener('mouseup', () => this.endDrag());
        
        // Volume
        this.volumeBtn.addEventListener('click', () => this.toggleMute());
        this.volumeSlider.addEventListener('input', (e) => this.setVolume(e.target.value));
        
        // Speed controls
        this.speedBtns.forEach(btn => {
            btn.addEventListener('click', () => this.setSpeed(parseFloat(btn.dataset.speed)));
        });
        
        // Seek controls
        this.seekBtns.forEach(btn => {
            btn.addEventListener('click', () => this.seekBy(parseInt(btn.dataset.seek)));
        });
        
        // Fullscreen
        this.fullscreenBtn.addEventListener('click', () => this.toggleFullscreen());
        
        // Picture-in-picture
        if (this.pipBtn) {
            this.pipBtn.addEventListener('click', () => this.togglePiP());
        }
        
        // Video events
        this.video.addEventListener('loadedmetadata', () => this.onLoadedMetadata());
        this.video.addEventListener('timeupdate', () => this.onTimeUpdate());
        this.video.addEventListener('ended', () => this.onEnded());
        this.video.addEventListener('waiting', () => this.showLoading());
        this.video.addEventListener('canplay', () => this.hideLoading());
        
        // Controls visibility
        this.container.addEventListener('mouseenter', () => this.showControls());
        this.container.addEventListener('mouseleave', () => this.hideControls());
        this.container.addEventListener('mousemove', () => this.resetHideTimer());
    }
    
    setupKeyboardControls() {
        document.addEventListener('keydown', (e) => {
            if (!this.isPlayerFocused()) return;
            
            switch(e.code) {
                case 'Space':
                    e.preventDefault();
                    this.togglePlayPause();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.seekBy(-10);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.seekBy(10);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.changeVolume(0.1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.changeVolume(-0.1);
                    break;
                case 'KeyF':
                    e.preventDefault();
                    this.toggleFullscreen();
                    break;
                case 'KeyM':
                    e.preventDefault();
                    this.toggleMute();
                    break;
                case 'Digit1':
                    this.setSpeed(0.5);
                    break;
                case 'Digit2':
                    this.setSpeed(1);
                    break;
                case 'Digit3':
                    this.setSpeed(1.25);
                    break;
                case 'Digit4':
                    this.setSpeed(1.5);
                    break;
                case 'Digit5':
                    this.setSpeed(2);
                    break;
            }
        });
    }
    
    isPlayerFocused() {
        return this.container.matches(':hover') || document.activeElement === this.video;
    }
    
    togglePlayPause() {
        if (this.video.paused) {
            this.play();
        } else {
            this.pause();
        }
    }
    
    play() {
        this.video.play();
        this.isPlaying = true;
        this.container.classList.remove('paused');
        this.updatePlayButton();
    }
    
    pause() {
        this.video.pause();
        this.isPlaying = false;
        this.container.classList.add('paused');
        this.updatePlayButton();
    }
    
    updatePlayButton() {
        const playIcon = `<svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`;
        const pauseIcon = `<svg viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>`;
        
        this.playPauseBtn.innerHTML = this.isPlaying ? pauseIcon : playIcon;
        this.overlayPlayBtn.innerHTML = playIcon;
    }
    
    seek(e) {
        const rect = this.progressContainer.getBoundingClientRect();
        const percent = (e.clientX - rect.left) / rect.width;
        const time = percent * this.video.duration;
        this.video.currentTime = time;
    }
    
    startDrag(e) {
        this.isDragging = true;
        this.seek(e);
    }
    
    drag(e) {
        if (!this.isDragging) return;
        this.seek(e);
    }
    
    endDrag() {
        this.isDragging = false;
    }
    
    seekBy(seconds) {
        this.video.currentTime += seconds;
        this.showSeekIndicator(seconds);
    }
    
    showSeekIndicator(seconds) {
        // Créer un indicateur visuel de saut temporel
        const indicator = document.createElement('div');
        indicator.className = 'seek-indicator';
        indicator.textContent = `${seconds > 0 ? '+' : ''}${seconds}s`;
        indicator.style.cssText = `
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            z-index: 1000;
            pointer-events: none;
        `;
        
        this.container.appendChild(indicator);
        
        setTimeout(() => {
            indicator.remove();
        }, 1000);
    }
    
    setVolume(volume) {
        this.video.volume = volume;
        this.volumeSlider.value = volume;
        this.updateVolumeIcon();
    }
    
    changeVolume(delta) {
        const newVolume = Math.max(0, Math.min(1, this.video.volume + delta));
        this.setVolume(newVolume);
    }
    
    toggleMute() {
        if (this.video.muted) {
            this.video.muted = false;
            this.volumeSlider.value = this.video.volume;
        } else {
            this.video.muted = true;
            this.volumeSlider.value = 0;
        }
        this.updateVolumeIcon();
    }
    
    updateVolumeIcon() {
        const volume = this.video.muted ? 0 : this.video.volume;
        let icon;
        
        if (volume === 0) {
            icon = `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>`;
        } else if (volume < 0.5) {
            icon = `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M7 9v6h4l5 5V4l-5 5H7z"/></svg>`;
        } else {
            icon = `<svg width="20" height="20" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>`;
        }
        
        this.volumeBtn.innerHTML = icon;
    }
    
    setSpeed(speed) {
        this.video.playbackRate = speed;
        this.currentSpeed = speed;
        
        // Mettre à jour les boutons de vitesse
        this.speedBtns.forEach(btn => {
            btn.classList.toggle('active', parseFloat(btn.dataset.speed) === speed);
        });
    }
    
    toggleFullscreen() {
        if (!document.fullscreenElement) {
            this.container.requestFullscreen().then(() => {
                this.container.classList.add('fullscreen');
            });
        } else {
            document.exitFullscreen().then(() => {
                this.container.classList.remove('fullscreen');
            });
        }
    }
    
    async togglePiP() {
        try {
            if (document.pictureInPictureElement) {
                await document.exitPictureInPicture();
            } else {
                await this.video.requestPictureInPicture();
            }
        } catch (error) {
            console.log('Picture-in-picture not supported');
        }
    }
    
    onLoadedMetadata() {
        this.updateTimeDisplay();
        this.hideLoading();
    }
    
    onTimeUpdate() {
        if (!this.isDragging) {
            const percent = (this.video.currentTime / this.video.duration) * 100;
            this.progressBar.style.width = `${percent}%`;
        }
        this.updateTimeDisplay();
    }
    
    onEnded() {
        this.pause();
        this.video.currentTime = 0;
    }
    
    updateTimeDisplay() {
        const current = this.formatTime(this.video.currentTime);
        const duration = this.formatTime(this.video.duration);
        this.timeDisplay.textContent = `${current} / ${duration}`;
    }
    
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
    
    showControls() {
        this.controls.classList.add('active');
        this.resetHideTimer();
    }
    
    hideControls() {
        if (!this.isPlaying) return;
        this.hideControlsTimeout = setTimeout(() => {
            this.controls.classList.remove('active');
        }, 3000);
    }
    
    resetHideTimer() {
        clearTimeout(this.hideControlsTimeout);
        if (this.isPlaying) {
            this.hideControls();
        }
    }
    
    showLoading() {
        let loading = this.container.querySelector('.screencast-loading');
        if (!loading) {
            loading = document.createElement('div');
            loading.className = 'screencast-loading';
            loading.innerHTML = `
                <div class="loading-spinner"></div>
                <div>Chargement...</div>
            `;
            this.container.appendChild(loading);
        }
    }
    
    hideLoading() {
        const loading = this.container.querySelector('.screencast-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    setInitialVolume() {
        this.video.volume = 0.8;
        this.volumeSlider.value = 0.8;
        this.updateVolumeIcon();
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', function() {
    const players = document.querySelectorAll('.screencast-player');
    players.forEach(player => {
        new ScreencastPlayer(player);
    });
});

// Export pour utilisation dans d'autres scripts
window.ScreencastPlayer = ScreencastPlayer;
