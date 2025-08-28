<?php
// Register the autoloader
spl_autoload_register(function ($class) {
    // Namespace prefix to match
    $prefix = 'App\\';
    // Base directory where that namespace lives
    $baseDir = __DIR__ . '/sources/app/';

    // Does the class use the "App\" prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // If not, move to the next registered autoloader
        return;
    }
    // Strip the "App\" prefix off the class
    $relativeClass = substr($class, $len);
    // Replace namespace separators with the directory separator
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

use App\Models\GalleryModel;

echo 'Test de chargement du GalleryModel...' . PHP_EOL;
try {
    $model = new GalleryModel();
    echo 'GalleryModel chargé avec succès!' . PHP_EOL;
    
    $videos = $model->getVideosByGallery(13);
    echo 'Nombre de vidéos trouvées: ' . count($videos) . PHP_EOL;
    
    if (!empty($videos)) {
        $video = $videos[0];
        echo 'Première vidéo:' . PHP_EOL;
        echo '- ID: ' . $video->id . PHP_EOL;
        echo '- Chemin: ' . $video->video_path . PHP_EOL;
        echo '- Taille: ' . $video->file_size . ' bytes' . PHP_EOL;
        
        // Vérifier si le fichier existe
        $fullPath = '/home/php' . $video->video_path;
        if (file_exists($fullPath)) {
            echo '- Fichier existe: OUI' . PHP_EOL;
            echo '- Taille réelle: ' . filesize($fullPath) . ' bytes' . PHP_EOL;
        } else {
            echo '- Fichier existe: NON' . PHP_EOL;
        }
    }
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage() . PHP_EOL;
    echo 'Trace: ' . $e->getTraceAsString() . PHP_EOL;
}
