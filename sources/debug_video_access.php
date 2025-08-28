<?php
echo '<h1>Test d\'accès aux vidéos - Galerie 13</h1>';

// Test de connexion à la base de données
try {
    $pdo = new PDO('mysql:host=mariadb;dbname=database;charset=utf8', 'root', '.Optile17');
    echo '<p>✓ Connexion à la base de données réussie</p>';
    
    // Récupérer les vidéos de la galerie 13
    $sql = "SELECT id, caption, video_path, file_size FROM videos WHERE gallery_id = 13";
    $stmt = $pdo->query($sql);
    $videos = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    echo '<p>Nombre de vidéos trouvées: ' . count($videos) . '</p>';
    
    if (!empty($videos)) {
        echo '<h2>Vidéos trouvées:</h2>';
        foreach ($videos as $video) {
            echo '<div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">';
            echo '<h3>Vidéo ID: ' . $video->id . '</h3>';
            echo '<p>Caption: ' . htmlspecialchars($video->caption) . '</p>';
            echo '<p>Chemin BDD: ' . htmlspecialchars($video->video_path) . '</p>';
            echo '<p>Taille BDD: ' . number_format($video->file_size / 1024 / 1024, 2) . ' MB</p>';
            
            // Vérifier si le fichier existe
            $fullPath = '/home/php' . $video->video_path;
            if (file_exists($fullPath)) {
                echo '<p style="color: green;">✓ Fichier existe sur le disque</p>';
                echo '<p>Taille réelle: ' . number_format(filesize($fullPath) / 1024 / 1024, 2) . ' MB</p>';
                
                // Test d'accès HTTP
                $httpPath = 'http://nginx' . $video->video_path;
                echo '<p>URL HTTP: <a href="' . $httpPath . '" target="_blank">' . $httpPath . '</a></p>';
                
                // Vérifier les permissions
                echo '<p>Permissions: ' . substr(sprintf('%o', fileperms($fullPath)), -4) . '</p>';
            } else {
                echo '<p style="color: red;">✗ Fichier n\'existe pas sur le disque</p>';
                echo '<p>Chemin recherché: ' . $fullPath . '</p>';
            }
            echo '</div>';
        }
    } else {
        echo '<p>Aucune vidéo trouvée dans la galerie 13</p>';
    }
    
} catch (Exception $e) {
    echo '<p style="color: red;">Erreur: ' . $e->getMessage() . '</p>';
}
?>
