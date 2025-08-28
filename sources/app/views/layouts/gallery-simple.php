<?php
use App\Utility\FlashMessage;
$title = $title ?? 'Default Title';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shotcut icon" type="img/png" href="/assets/images/favicon.png">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <link rel="stylesheet" href="/assets/css/modern-header.css">
  <link rel="stylesheet" href="/assets/css/modern-streaming.css">
  <link rel="stylesheet" href="/assets/css/screencast-player.css">
  <link rel="stylesheet" href="/assets/css/tv-interface.css">
  <!-- Balise meta pour le token CSRF -->
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>

<body>
  <?php require __DIR__ . '/header.php'; ?>
  <main>
    <?php FlashMessage::display(); ?>
    <?= $content; ?>
  </main>
</body>
<!-- Scripts spécifiques pour la galerie simple -->
<script src="/assets/js/upload.js"></script>
<!-- On ne charge pas streaming.js pour éviter les conflits -->
<script src="/assets/js/tv-interface.js"></script>

</html>
