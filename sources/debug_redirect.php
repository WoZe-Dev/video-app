<?php
// Script de débogage pour les redirections
session_start();

echo "=== DÉBOGAGE DES REDIRECTIONS ===\n";
echo "URL demandée: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "Méthode: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session données: " . print_r($_SESSION, true) . "\n";

// Inclure les classes nécessaires
require_once __DIR__ . '/app/Middlewares/AuthMiddleware.php';

echo "Vérification AuthMiddleware:\n";
$isLoggedIn = App\Middlewares\AuthMiddleware::isLoggedIn();
echo "- isLoggedIn(): " . ($isLoggedIn ? 'TRUE' : 'FALSE') . "\n";

if ($isLoggedIn && isset($_SESSION['user'])) {
    echo "- Utilisateur: " . $_SESSION['user']['username'] . "\n";
    echo "- Rôle: " . $_SESSION['user']['role'] . "\n";
}

// Test de la route demandée
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);
echo "Chemin parsé: $path\n";

// Simulation de la logique de redirection d'AuthController
if ($path === '/' || $path === '/login') {
    echo "\nLogique AuthController::login:\n";
    if ($isLoggedIn) {
        $userRole = $_SESSION['user']['role'] ?? 'viewer';
        echo "- Utilisateur connecté avec rôle: $userRole\n";
        if ($userRole === 'admin') {
            echo "- Devrait rediriger vers: /gallery\n";
        } else {
            echo "- Devrait rediriger vers: /tv-mode\n";
        }
    } else {
        echo "- Utilisateur non connecté, afficher formulaire de login\n";
    }
}

// Test spécifique pour /tv-mode
if ($path === '/tv-mode') {
    echo "\nLogique TVController::index:\n";
    if (!$isLoggedIn) {
        echo "- Utilisateur non connecté, devrait rediriger vers: /login\n";
    } else {
        $user = $_SESSION['user'];
        if ($user['role'] !== 'viewer' && $user['role'] !== 'admin') {
            echo "- Rôle '{$user['role']}' non autorisé, devrait rediriger vers: /login\n";
        } else {
            echo "- Accès autorisé pour rôle: {$user['role']}\n";
        }
    }
}

echo "\n=== FIN DU DÉBOGAGE ===\n";
?>
