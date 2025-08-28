<?php
use App\Utility\FlashMessage;
$title = $title ?? 'Connexion';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shotcut icon" type="img/png" href="/assets/images/favicon.png">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="/assets/css/styles.css">
  <!-- Balise meta pour le token CSRF -->
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>

<body>
  <?php FlashMessage::display(); ?>
  <?= $content; ?>
</body>

</html>
