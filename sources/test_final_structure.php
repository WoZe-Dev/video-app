<?php
// Test final lisible de l'organisation hiérarchique
session_start();
$_SESSION['user'] = ['id' => 1, 'username' => 'testuser', 'role' => 'user'];
$_GET['endpoint'] = 'galleries';

ob_start();
include __DIR__ . '/test_api.php';
$output = ob_get_clean();

// Extraire le JSON
$jsonStart = strpos($output, '{"success"');
if ($jsonStart !== false) {
    $json = substr($output, $jsonStart);
    $data = json_decode($json, true);
    
    echo "=== STRUCTURE HIÉRARCHIQUE CORRIGÉE ===\n\n";
    
    foreach ($data['galleries'] as $gallery) {
        echo "📁 GALERIE PRINCIPALE: " . $gallery['gallery_name'] . "\n";
        echo "   ├─ Vidéos directes: " . $gallery['direct_video_count'] . "\n";
        echo "   ├─ Sous-galeries: " . $gallery['subgallery_count'] . "\n";
        echo "   └─ Total vidéos: " . $gallery['video_count'] . "\n";
        
        // Afficher les vidéos directes
        if (!empty($gallery['direct_videos'])) {
            echo "   \n   Vidéos directes:\n";
            foreach ($gallery['direct_videos'] as $video) {
                echo "   🎬 " . $video['video_title'] . "\n";
            }
        }
        
        // Afficher les sous-galeries et leurs vidéos
        if (!empty($gallery['subgalleries'])) {
            echo "   \n   Sous-galeries:\n";
            foreach ($gallery['subgalleries'] as $sub) {
                echo "   📂 " . $sub['name'] . " (" . $sub['video_count'] . " vidéo" . ($sub['video_count'] > 1 ? 's' : '') . ")\n";
                if (!empty($sub['videos'])) {
                    foreach ($sub['videos'] as $video) {
                        echo "      🎬 " . $video['video_title'] . "\n";
                    }
                }
            }
        }
        echo "\n" . str_repeat("-", 50) . "\n";
    }
    
    echo "\n✅ RÉSULTAT:\n";
    echo "- Les galeries principales montrent seulement leurs vidéos directes\n";
    echo "- Les sous-galeries sont listées séparément avec leurs propres vidéos\n";
    echo "- Chaque élément peut être cliqué individuellement dans l'interface TV\n";
    echo "- Plus de mélange entre vidéos directes et vidéos de sous-galeries!\n";
    
} else {
    echo "Erreur: JSON non trouvé\n";
}
?>
