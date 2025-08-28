<?php
// Script de test pour vérifier l'accès aux vidéos

echo "<h1>Test d'accès aux vidéos - Galerie 13</h1>\n";

require_once '/home/php/app/Models/GalleryModel.php';
use App\Models\GalleryModel;

try {
    $galleryModel = new GalleryModel();
    $videos = $galleryModel->getVideosByGallery(13);
    
    echo "<h2>Vidéos trouvées : " . count($videos) . "</h2>\n";
    
    foreach ($videos as $index => $video) {
        echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 15px;'>\n";
        echo "<h3>Vidéo #" . ($index + 1) . " (ID: {$video->id})</h3>\n";
        echo "<p><strong>Titre:</strong> " . htmlspecialchars($video->caption) . "</p>\n";
        echo "<p><strong>Chemin:</strong> " . htmlspecialchars($video->video_path) . "</p>\n";
        echo "<p><strong>Taille:</strong> " . round($video->file_size / 1024 / 1024, 1) . " MB</p>\n";
        
        // Vérifier si le fichier existe
        $fullPath = '/home/php' . $video->video_path;
        $exists = file_exists($fullPath);
        $readable = is_readable($fullPath);
        
        echo "<p><strong>Fichier existe:</strong> " . ($exists ? "✅ Oui" : "❌ Non") . "</p>\n";
        echo "<p><strong>Fichier lisible:</strong> " . ($readable ? "✅ Oui" : "❌ Non") . "</p>\n";
        echo "<p><strong>Chemin complet:</strong> " . $fullPath . "</p>\n";
        
        // Test d'URL d'accès
        $webUrl = "http://192.168.1.68:8000" . $video->video_path;
        echo "<p><strong>URL web:</strong> <a href='{$webUrl}' target='_blank'>{$webUrl}</a></p>\n";
        
        // Test avec curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webUrl);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "<p><strong>Test HTTP:</strong> " . ($httpCode == 200 ? "✅ Accessible (Code: $httpCode)" : "❌ Erreur (Code: $httpCode)") . "</p>\n";
        
        // Miniature HTML
        echo "<div style='margin-top: 10px;'>\n";
        echo "<strong>Test de lecture vidéo:</strong><br>\n";
        echo "<video width='320' height='180' preload='metadata' controls>\n";
        echo "  <source src='{$video->video_path}' type='video/mp4'>\n";
        echo "  Votre navigateur ne supporte pas la lecture de cette vidéo.\n";
        echo "</video>\n";
        echo "</div>\n";
        
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<h2>Informations système</h2>\n";
echo "<p><strong>Répertoire de travail:</strong> " . getcwd() . "</p>\n";
echo "<p><strong>Répertoire uploads:</strong> " . (is_dir('/home/php/uploads') ? "✅ Existe" : "❌ N'existe pas") . "</p>\n";
echo "<p><strong>Répertoire videos:</strong> " . (is_dir('/home/php/videos') ? "✅ Existe" : "❌ N'existe pas") . "</p>\n";

if (is_dir('/home/php/uploads')) {
    echo "<p><strong>Contenu uploads:</strong></p>\n";
    echo "<pre>" . shell_exec('ls -la /home/php/uploads/') . "</pre>\n";
}
?>
