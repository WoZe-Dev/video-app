<?php
ob_start();
$title = $title ?? 'Connexion';
?>
<div class="modal">
    <form class="form" action="/login" method="POST">
        <h1 class="form__title mb-1"><?= htmlspecialchars($title) ?></h1>

        <!-- Message d'erreur -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="form__error-message">
                <?= htmlspecialchars($_SESSION['login_error']) ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>
        
        <label for="login">Login</label>
        <input type="text" id="login" name="login" required class="mb-2" placeholder="Entrez votre login">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required class="mb-3" placeholder="Entrez votre mot de passe">
        <button type="submit" name="submit" class="button form__button mb-2">Se connecter</button>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>