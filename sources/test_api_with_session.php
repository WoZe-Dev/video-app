<?php
// Test direct de l'API TV avec session simulée
session_start();

// Simuler une session d'utilisateur
$_SESSION['user'] = [
    'id' => 1,
    'username' => 'testuser',
    'email' => 'test@example.com',
    'role' => 'user'
];

echo "=== TEST API TV AVEC SESSION ===\n";
echo "Utilisateur simulé: " . $_SESSION['user']['username'] . "\n";
echo "Rôle: " . $_SESSION['user']['role'] . "\n\n";

// Définir le paramètre endpoint
$_GET['endpoint'] = 'galleries';

// Capturer la sortie de l'API
ob_start();
try {
    include __DIR__ . '/test_api.php';
    $apiOutput = ob_get_clean();
    
    echo "Réponse brute de l'API:\n";
    echo $apiOutput . "\n\n";
    
    // Parser le JSON
    $data = json_decode($apiOutput, true);
    if ($data && isset($data['galleries'])) {
        echo "=== ANALYSE DONNÉES API ===\n";
        echo "Statut: " . ($data['success'] ? 'SUCCESS' : 'ERREUR') . "\n";
        echo "Nombre de galeries: " . count($data['galleries']) . "\n\n";
        
        foreach ($data['galleries'] as $i => $gallery) {
            echo "Galerie " . ($i + 1) . ": " . $gallery['gallery_name'] . "\n";
            echo "- ID: " . $gallery['gallery_id'] . "\n";
            echo "- Nombre de vidéos: " . $gallery['video_count'] . "\n";
            echo "- Nombre de sous-galeries: " . $gallery['subgallery_count'] . "\n";
            
            if (!empty($gallery['subgalleries'])) {
                echo "- Sous-galeries:\n";
                foreach ($gallery['subgalleries'] as $sub) {
                    echo "  * " . $sub['name'] . " (ID: " . $sub['id'] . ")\n";
                }
            }
            
            if (!empty($gallery['videos'])) {
                echo "- Vidéos:\n";
                foreach ($gallery['videos'] as $video) {
                    echo "  * " . $video['video_title'] . " [" . $video['gallery_path'] . "]\n";
                    echo "    Chemin: " . $video['video_path'] . "\n";
                }
            }
            echo "\n";
        }
    } else {
        echo "ERREUR: Impossible de parser la réponse JSON\n";
        if (isset($data['error'])) {
            echo "Erreur API: " . $data['error'] . "\n";
        }
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
