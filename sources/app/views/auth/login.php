<?php
ob_start();
$title = $title ?? 'Connexion';
?>

<div class="login-container">
    <div class="login-modal">
        <div class="login-header">
            <div class="login-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                </svg>
            </div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p class="login-subtitle">Accédez à votre espace personnel</p>
        </div>
        
        <div class="login-content">
            <!-- Message d'erreur -->
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="login-error">
                    <?= htmlspecialchars($_SESSION['login_error']) ?>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" action="/login" method="POST">
                <div class="form-group">
                    <label for="login">Nom d'utilisateur</label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           class="form-input" 
                           required 
                           placeholder="Entrez votre nom d'utilisateur"
                           autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           required 
                           placeholder="Entrez votre mot de passe"
                           autocomplete="current-password">
                </div>
                
                <button type="submit" name="submit" class="login-button">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/auth.php';
?>