<?php
// Test simple pour l'API galleries
session_start();

// Register the autoloader (copié depuis index.php)
spl_autoload_register(function ($class) {
    // Namespace prefix to match
    $prefix = 'App\\';
    // Base directory where that namespace lives
    $baseDir = __DIR__ . '/app/';

    // Does the class use the "App\" prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // If not, move to the next registered autoloader
        return;
    }
    // Strip the "App\" prefix off the class
    $relativeClass = substr($class, $len);
    // Replace backslashes with forward slashes for directory traversal
    $relativeClass = str_replace('\\', '/', $relativeClass);
    // Build the file path
    $file = $baseDir . $relativeClass . '.php';

    // If the file exists, load it
    if (file_exists($file)) {
        require_once $file;
    }
});

// Headers pour JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Utilisateur non authentifié']);
    exit;
}

$userId = $_SESSION['user']['id'];
$endpoint = $_GET['endpoint'] ?? 'galleries';
$galleryId = $_GET['gallery_id'] ?? null;

try {
    $galleryModel = new \App\Models\GalleryModel();
    
    if ($endpoint === 'videos' && $galleryId) {
        // Endpoint pour les vidéos d'une galerie spécifique
        $galleries = $galleryModel->getUserGalleriesAndContent($userId);
        $targetGallery = null;
        
        // Trouver la galerie demandée
        foreach ($galleries as $gallery) {
            if ($gallery->gallery_id == $galleryId) {
                $targetGallery = $gallery;
                break;
            }
        }
        
        if (!$targetGallery) {
            echo json_encode([
                'success' => false,
                'error' => 'Galerie non trouvée'
            ]);
            exit;
        }
        
        $galleryVideos = json_decode($targetGallery->galleryVideos ?? '[]');
        $videos = [];
        
        if (!empty($galleryVideos)) {
            foreach ($galleryVideos as $video) {
                if (!empty($video->id)) {
                    $videos[] = [
                        'id' => $video->id,
                        'video_title' => $video->caption ?? 'Vidéo sans titre',
                        'video_path' => $video->video_path,
                        'mime_type' => $video->mime_type ?? 'video/mp4',
                        'duration' => $video->duration ?? '00:00:00',
                        'file_size' => $video->file_size ?? 0,
                        'created_at' => $video->created_at ?? date('Y-m-d H:i:s')
                    ];
                }
            }
        }
        
        echo json_encode([
            'success' => true,
            'videos' => $videos,
            'gallery_name' => $targetGallery->gallery_name,
            'total' => count($videos)
        ]);
        
    } else {
        // Endpoint pour les galeries (par défaut)
        $galleries = $galleryModel->getUserGalleriesAndContent($userId);
        
        // Transformer les données comme dans ApiController
        $tvData = [];
        foreach ($galleries as $gallery) {
            $galleryVideos = json_decode($gallery->galleryVideos ?? '[]');
            $videos = [];
            
            if (!empty($galleryVideos)) {
                foreach ($galleryVideos as $video) {
                    if (!empty($video->id)) {
                        $videos[] = [
                            'id' => $video->id,
                            'caption' => $video->caption ?? 'Vidéo sans titre',
                            'video_path' => $video->video_path,
                            'mime_type' => $video->mime_type ?? 'video/mp4',
                            'duration' => $video->duration ?? 0,
                            'file_size' => $video->file_size ?? 0,
                            'created_at' => $video->created_at ?? date('Y-m-d H:i:s')
                        ];
                    }
                }
            }
            
            // Thumbnail - première vidéo
            $thumbnailPath = !empty($videos) ? $videos[0]['video_path'] : null;
            
            $tvData[] = [
                'gallery_id' => $gallery->gallery_id,
                'gallery_name' => $gallery->gallery_name,
                'gallery_description' => $gallery->description ?? '',
                'video_count' => count($videos),
                'thumbnail_path' => $thumbnailPath,
                'videos' => $videos
            ];
        }
        
        echo json_encode([
            'success' => true,
            'galleries' => $tvData,
            'total' => count($tvData)
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur: ' . $e->getMessage()]);
}
?>
