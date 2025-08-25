<?php
ob_start();
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

<div class="streaming-galleries">
    <div class="streaming-header">
        <h1 class="streaming-title"><?php echo $title; ?></h1>
        <div class="header-actions">
            
            <a href="/gallery/create" class="create-gallery-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                </svg>
                Nouvelle Galerie
            </a>
            
            <?php 
            use App\Middlewares\AuthMiddleware;
            if (AuthMiddleware::isAdmin()): ?>
                <a href="/logout" class="logout-btn" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                    </svg>
                    Déconnexion
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="galleries-grid">
        <?php foreach ($galleries as $gallery) {
            $galleryVideos = json_decode($gallery->galleryVideos);
            $videoCount = (count($galleryVideos) == 0 || (count($galleryVideos) == 1 && empty($galleryVideos[0]->id))) ? 0 : count($galleryVideos);
            ?>
            <a href="/gallery/<?php echo $gallery->gallery_id; ?>" class="gallery-card">
                <div class="gallery-thumbnail">
                    <?php if ($videoCount == 0) { ?>
                        <div class="no-videos-placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="#666">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                            <p>Aucune vidéo</p>
                        </div>
                    <?php } else { 
                        $firstVideo = $galleryVideos[0]; ?>
                        <video muted preload="metadata">
                            <source src="<?php echo $firstVideo->video_path; ?>" type="<?php echo $firstVideo->mime_type ?? 'video/mp4'; ?>">
                        </video>
                        <div class="video-count-badge"><?php echo $videoCount; ?> vidéo<?php echo $videoCount > 1 ? 's' : ''; ?></div>
                    <?php } ?>
                </div>
                <div class="gallery-info">
                    <h3 class="gallery-name"><?php echo htmlspecialchars($gallery->gallery_name); ?></h3>
                    <div class="gallery-meta">
                        <?php echo $videoCount; ?> vidéo<?php echo $videoCount > 1 ? 's' : ''; ?> • 
                        Créée le <?php echo date('d/m/Y', strtotime($gallery->gallery_created_at)); ?>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . "/../layouts/base.php";
