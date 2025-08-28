<?php
// Test de la nouvelle API structurée
session_start();
$_SESSION['user'] = [
    'id' => 1,
    'username' => 'testuser',
    'role' => 'user'
];

$_GET['endpoint'] = 'galleries';

// Capturer la sortie
ob_start();
include __DIR__ . '/test_api.php';
$output = ob_get_clean();

// Supprimer les warnings et ne garder que le JSON
$lines = explode("\n", $output);
$jsonLine = '';
foreach ($lines as $line) {
    if (strpos($line, '{"success"') === 0) {
        $jsonLine = $line;
        break;
    }
}

if ($jsonLine) {
    $data = json_decode($jsonLine, true);
    
    if ($data && isset($data['galleries'])) {
        echo "=== NOUVELLE API STRUCTURÉE ===\n";
        echo "Nombre de galeries: " . count($data['galleries']) . "\n\n";
        
        foreach ($data['galleries'] as $gallery) {
            echo "📁 GALERIE: " . $gallery['gallery_name'] . "\n";
            echo "   ├─ Vidéos totales: " . $gallery['video_count'] . "\n";
            echo "   ├─ Vidéos directes: " . ($gallery['direct_video_count'] ?? 'N/A') . "\n";
            echo "   ├─ Sous-galeries: " . $gallery['subgallery_count'] . "\n";
            
            if (isset($gallery['direct_videos']) && !empty($gallery['direct_videos'])) {
                echo "   ├─ Vidéos directes:\n";
                foreach ($gallery['direct_videos'] as $video) {
                    echo "   │  🎬 " . $video['video_title'] . " [" . $video['gallery_path'] . "]\n";
                }
            }
            
            if (!empty($gallery['subgalleries'])) {
                echo "   └─ Sous-galeries:\n";
                foreach ($gallery['subgalleries'] as $sub) {
                    echo "      📂 " . $sub['name'] . " (" . $sub['video_count'] . " vidéos)\n";
                    if (!empty($sub['videos'])) {
                        foreach ($sub['videos'] as $video) {
                            echo "         🎬 " . $video['video_title'] . " [" . $video['gallery_path'] . "]\n";
                        }
                    }
                }
            }
            echo "\n";
        }
    } else {
        echo "Erreur: données invalides\n";
        var_dump($data);
    }
} else {
    echo "Erreur: aucun JSON trouvé dans la sortie\n";
    echo "Sortie brute:\n" . $output . "\n";
}
?>
