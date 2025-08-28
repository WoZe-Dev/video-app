<?php
ob_start();
?>
<link rel="stylesheet" href="/assets/css/modern-streaming.css">

<div class="streaming-gallery">
    <div class="streaming-header">
        <div class="header-left">
            <?php 
            $backUrl = '/gallery';
            if (!empty($parent_gallery)) {
                $backUrl = '/gallery/' . $parent_gallery->id;
            }
            ?>
            <a href="<?php echo $backUrl; ?>" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
            </a>
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
                <?php if (!empty($parent_gallery)): ?>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current">Nouvelle sous-galerie</span>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
    
    <div class="create-gallery-container">
        <div class="create-gallery-card">
            <!-- Header de la carte -->
            <div class="card-header">
                <div class="header-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                    </svg>
                </div>
                <div class="header-content">
                    <h2 class="card-title">Créer une nouvelle galerie</h2>
                    <?php if (!empty($parent_gallery)): ?>
                        <p class="card-subtitle">Dans la galerie : <strong><?php echo htmlspecialchars($parent_gallery->name); ?></strong></p>
                    <?php else: ?>
                        <p class="card-subtitle">Créer une galerie principale</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Formulaire -->
            <form class="gallery-form" method="POST">
                <!-- Token CSRF -->
                <input type="hidden" name="csrf_token" value="<?php 
                    echo $_SESSION['csrf_token'] ?? 
                    \App\Middlewares\AuthMiddleware::generateCsrfToken(); 
                ?>">
                
                <div class="form-group">
                    <label for="gallery-name" class="form-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z"/>
                        </svg>
                        Nom de la galerie
                    </label>
                    <input 
                        type="text" 
                        id="gallery-name" 
                        name="name" 
                        class="form-input" 
                        placeholder="Entrez le nom de votre galerie..."
                        required
                        autocomplete="off"
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                    >
                    <div class="input-helper">
                        <span class="char-count">0 / 50</span>
                    </div>
                </div>

                <?php if (!empty($parent_gallery)): ?>
                    <input type="hidden" name="parent_id" value="<?php echo $parent_gallery->id; ?>">
                <?php endif; ?>

                <button type="submit" class="submit-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,13H13V17H11V13H7V11H11V7H13V11H17M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                    </svg>
                    Créer la galerie
                </button>
            </form>

            <!-- Aide et règles -->
            <div class="rules-section">
                <div class="rules-header">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                    </svg>
                    <h3>Règles de nommage</h3>
                </div>
                <div class="rules-grid">
                    <div class="rule-item">
                        <div class="rule-icon success">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                            </svg>
                        </div>
                        <span>Nom unique à ce niveau</span>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon warning">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                            </svg>
                        </div>
                        <span>Pas de caractères spéciaux : <code>/ \ : * ? " &lt; &gt; |</code></span>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon info">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                            </svg>
                        </div>
                        <span>Privilégiez des noms courts et descriptifs</span>
                    </div>
                </div>
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

.breadcrumb-current {
    color: #333;
    font-weight: 500;
}

/* Design moderne pour la création de galerie */
.create-gallery-container {
    max-width: 680px;
    margin: 40px auto;
    padding: 0 20px;
}

.create-gallery-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 32px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-content h2 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 600;
}

.card-subtitle {
    margin: 0;
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px;
}

.gallery-form {
    padding: 32px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-weight: 600;
    color: #374151;
    font-size: 16px;
}

.form-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
    background: #fafafa;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.input-helper {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

.char-count {
    font-size: 12px;
    color: #9ca3af;
    background: #f3f4f6;
    padding: 4px 8px;
    border-radius: 6px;
}

.submit-btn {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 32px;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.submit-btn:active {
    transform: translateY(0);
}

.rules-section {
    background: #f8fafc;
    padding: 32px;
    border-top: 1px solid #e5e7eb;
}

.rules-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.rules-header h3 {
    margin: 0;
    color: #374151;
    font-size: 18px;
    font-weight: 600;
}

.rules-header svg {
    color: #6b7280;
}

.rules-grid {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.rule-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: white;
    border-radius: 12px;
    border-left: 4px solid transparent;
    transition: all 0.2s ease;
}

.rule-item:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.rule-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    flex-shrink: 0;
}

.rule-icon.success {
    background: #d1fae5;
    color: #059669;
    border-left-color: #10b981;
}

.rule-icon.warning {
    background: #fef3c7;
    color: #d97706;
}

.rule-icon.info {
    background: #dbeafe;
    color: #2563eb;
}

.rule-item:nth-child(1) {
    border-left-color: #10b981;
}

.rule-item:nth-child(2) {
    border-left-color: #f59e0b;
}

.rule-item:nth-child(3) {
    border-left-color: #3b82f6;
}

.rule-item span {
    color: #374151;
    font-size: 15px;
    line-height: 1.5;
}

.rule-item code {
    background: #f3f4f6;
    color: #ef4444;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 13px;
}

/* Animation de validation */
.form-input.valid {
    border-color: #10b981;
    background: #f0fdf4;
}

.form-input.invalid {
    border-color: #ef4444;
    background: #fef2f2;
}

/* Responsive */
@media (max-width: 768px) {
    .create-gallery-container {
        margin: 20px auto;
        padding: 0 16px;
    }
    
    .card-header {
        padding: 24px;
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .gallery-form {
        padding: 24px;
    }
    
    .rules-section {
        padding: 24px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('gallery-name');
    const charCount = document.querySelector('.char-count');
    const submitBtn = document.querySelector('.submit-btn');
    
    // Initialiser le compteur avec la valeur existante
    const initialValue = nameInput.value;
    if (initialValue) {
        updateValidation(initialValue);
    }
    
    // Validation en temps réel
    nameInput.addEventListener('input', function() {
        updateValidation(this.value);
    });
    
    function updateValidation(value) {
        const length = value.length;
        
        // Mettre à jour le compteur de caractères
        charCount.textContent = `${length} / 50`;
        
        // Validation des caractères interdits
        const forbiddenChars = /[\/\\:*?"<>|]/;
        const hasInvalidChars = forbiddenChars.test(value);
        
        // Appliquer les classes de validation
        nameInput.classList.remove('valid', 'invalid');
        if (length > 0) {
            if (hasInvalidChars || length > 50) {
                nameInput.classList.add('invalid');
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
            } else {
                nameInput.classList.add('valid');
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            }
        } else {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }
        
        // Mettre à jour le compteur de caractères avec des couleurs
        if (length > 45) {
            charCount.style.color = '#ef4444';
            charCount.style.background = '#fef2f2';
        } else if (length > 30) {
            charCount.style.color = '#f59e0b';
            charCount.style.background = '#fef3c7';
        } else {
            charCount.style.color = '#9ca3af';
            charCount.style.background = '#f3f4f6';
        }
    }
    
    // Animation du bouton de soumission
    submitBtn.addEventListener('click', function(e) {
        if (!this.disabled) {
            this.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="animation: spin 1s linear infinite;">
                    <path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/>
                </svg>
                Création en cours...
            `;
        }
    });
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php
$content = ob_get_clean();
require __DIR__ . "/../layouts/base.php";
?>
