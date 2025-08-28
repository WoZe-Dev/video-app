<?php
ob_start();
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

<div class="streaming-galleries">
    <div class="streaming-header">
        <h1 class="streaming-title"><?php echo $title; ?></h1>
        
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
        
        <div class="header-actions">
            <?php 
            use App\Middlewares\AuthMiddleware;
            if (AuthMiddleware::isAdmin()): ?>
                <a href="/gallery/create<?php echo $current_gallery ? '?parent=' . $current_gallery->id : ''; ?>" class="create-gallery-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                    </svg>
                    <?php echo $current_gallery ? 'Nouvelle Sous-galerie' : 'Nouvelle Galerie'; ?>
                </a>
                
                <a href="/logout" class="logout-btn" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16,17V14H9V10H16V7L21,12L16,17M14,2A2,2 0 0,1 16,4V6H14V4H5V20H14V18H16V20A2,2 0 0,1 14,22H5A2,2 0 0,1 3,20V4A2,2 0 0,1 5,2H14Z"/>
                    </svg>
                    Déconnexion
                </a>
            <?php else: ?>
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
        <?php foreach ($galleries as $gallery): ?>
            <a href="/gallery/<?php echo $gallery->gallery_id; ?>" class="gallery-card">
                <div class="gallery-thumbnail">
                    <div class="gallery-placeholder">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="#666">
                            <path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z"/>
                        </svg>
                        <p><?php echo $gallery->total_count; ?> élément<?php echo $gallery->total_count > 1 ? 's' : ''; ?></p>
                    </div>
                </div>
                <div class="gallery-info">
                    <h3 class="gallery-name"><?php echo htmlspecialchars($gallery->gallery_name); ?></h3>
                    <div class="gallery-meta">
                        <?php if ($gallery->subgalleries_count > 0): ?>
                            <?php echo $gallery->subgalleries_count; ?> sous-galerie<?php echo $gallery->subgalleries_count > 1 ? 's' : ''; ?>
                            <?php if ($gallery->videos_count > 0): ?> • <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($gallery->videos_count > 0): ?>
                            <?php echo $gallery->videos_count; ?> vidéo<?php echo $gallery->videos_count > 1 ? 's' : ''; ?>
                        <?php endif; ?>
                        <br>
                        Créée le <?php echo date('d/m/Y', strtotime($gallery->gallery_created_at)); ?>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
        
        <?php if (empty($galleries)): ?>
            <div class="no-galleries">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="#ccc">
                    <path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z"/>
                </svg>
                <h3>Aucune galerie</h3>
                <p>Il n'y a pas encore de galeries<?php echo $current_gallery ? ' dans cette galerie' : ''; ?>.</p>
                <?php if (AuthMiddleware::isAdmin()): ?>
                    <a href="/gallery/create<?php echo $current_gallery ? '?parent=' . $current_gallery->id : ''; ?>" class="button">
                        Créer la première galerie
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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

.gallery-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 240px;
    background: #f5f5f5;
   
}

.no-galleries {
    grid-column: 1/-1;
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-galleries h3 {
    margin: 20px 0 10px 0;
    color: #333;
}

.no-galleries p {
    margin: 0 0 20px 0;
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . "/../layouts/base.php";
?>
