<?php
// Script de test pour l'interface TV
session_start();

echo "=== TEST D'AUTHENTIFICATION ET D'ACCÈS TV ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: État de la session
echo "1. ÉTAT DE LA SESSION:\n";
echo "Session ID: " . session_id() . "\n";
echo "Session active: " . (session_status() === PHP_SESSION_ACTIVE ? 'OUI' : 'NON') . "\n";
echo "Données session: " . print_r($_SESSION, true) . "\n";

// Test 2: Vérification de l'authentification
require_once __DIR__ . '/app/Middlewares/AuthMiddleware.php';

echo "2. VÉRIFICATION AUTHENTIFICATION:\n";
$isLoggedIn = App\Middlewares\AuthMiddleware::isLoggedIn();
echo "Utilisateur connecté: " . ($isLoggedIn ? 'OUI' : 'NON') . "\n";

if ($isLoggedIn && isset($_SESSION['user'])) {
    echo "Utilisateur: " . $_SESSION['user']['username'] . "\n";
    echo "Rôle: " . $_SESSION['user']['role'] . "\n";
}

// Test 3: Test du contrôleur TV
echo "\n3. TEST DU CONTRÔLEUR TV:\n";
if ($isLoggedIn) {
    try {
        require_once __DIR__ . '/app/Controllers/TVController.php';
        $tvController = new App\Controllers\TVController();
        echo "Contrôleur TV instancié avec succès\n";
        
        $user = $_SESSION['user'];
        if ($user['role'] === 'viewer' || $user['role'] === 'admin') {
            echo "Accès autorisé pour le rôle: " . $user['role'] . "\n";
        } else {
            echo "Accès refusé pour le rôle: " . $user['role'] . "\n";
        }
    } catch (Exception $e) {
        echo "Erreur contrôleur TV: " . $e->getMessage() . "\n";
    }
} else {
    echo "Test impossible: utilisateur non connecté\n";
}

// Test 4: Vérification du fichier de vue
echo "\n4. VÉRIFICATION FICHIERS:\n";
$tvViewPath = __DIR__ . '/app/views/tv/index.php';
echo "Vue TV existe: " . (file_exists($tvViewPath) ? 'OUI' : 'NON') . "\n";
if (file_exists($tvViewPath)) {
    echo "Taille du fichier: " . filesize($tvViewPath) . " bytes\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms($tvViewPath)), -4) . "\n";
}

echo "\n=== FIN DU TEST ===\n";
?>
