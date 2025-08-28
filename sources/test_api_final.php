<?php
// Test propre de l'API TV
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/Models/GalleryModel.php';

echo "=== TEST FINAL API TV ===\n";

try {
    // Simuler session
    session_start();
    $_SESSION['user'] = [
        'id' => 1,
        'username' => 'testuser',
        'role' => 'user'
    ];
    
    $galleryModel = new \App\Models\GalleryModel();
    $galleries = $galleryModel->getMainGalleries();
    
    echo "Nombre de galeries principales: " . count($galleries) . "\n\n";
    
    // Recréer la logique de l'API TV
    $tvData = [];
    foreach ($galleries as $gallery) {
        $content = $galleryModel->getGalleryContent($gallery->gallery_id);
        
        // Combiner toutes les vidéos
        $allVideos = [];
        
        // Vidéos directes
        foreach ($content['videos'] as $video) {
            $allVideos[] = [
                'id' => $video->id,
                'video_title' => $video->caption ?? 'Vidéo sans titre',
                'video_path' => $video->video_path,
                'full_path' => $video->full_path ?? '/' . $gallery->gallery_name . '/' . ($video->original_filename ?? $video->caption),
                'mime_type' => $video->mime_type ?? 'video/mp4',
                'duration' => $video->duration ?? '00:00:00',
                'file_size' => $video->file_size ?? 0,
                'created_at' => $video->created_at ?? date('Y-m-d H:i:s'),
                'gallery_path' => $gallery->gallery_name
            ];
        }
        
        // Vidéos des sous-galeries
        foreach ($content['subgalleries'] as $subgallery) {
            $subContent = $galleryModel->getGalleryContent($subgallery->gallery_id);
            foreach ($subContent['videos'] as $video) {
                $allVideos[] = [
                    'id' => $video->id,
                    'video_title' => $video->caption ?? 'Vidéo sans titre',
                    'video_path' => $video->video_path,
                    'full_path' => $video->full_path ?? '/' . $gallery->gallery_name . '/' . $subgallery->gallery_name . '/' . ($video->original_filename ?? $video->caption),
                    'mime_type' => $video->mime_type ?? 'video/mp4',
                    'duration' => $video->duration ?? '00:00:00',
                    'file_size' => $video->file_size ?? 0,
                    'created_at' => $video->created_at ?? date('Y-m-d H:i:s'),
                    'gallery_path' => $gallery->gallery_name . '/' . $subgallery->gallery_name
                ];
            }
        }
        
        $thumbnailPath = !empty($allVideos) ? $allVideos[0]['video_path'] : null;
        
        $tvData[] = [
            'gallery_id' => $gallery->gallery_id,
            'gallery_name' => $gallery->gallery_name,
            'gallery_description' => $gallery->description ?? '',
            'video_count' => count($allVideos),
            'subgallery_count' => count($content['subgalleries']),
            'total_videos' => count($allVideos),
            'thumbnail_path' => $thumbnailPath,
            'videos' => $allVideos,
            'subgalleries' => array_map(function($sub) {
                return [
                    'id' => $sub->gallery_id,
                    'name' => $sub->gallery_name,
                    'description' => $sub->description ?? ''
                ];
            }, $content['subgalleries'])
        ];
    }
    
    // Afficher les résultats
    echo "=== DONNÉES API TV ===\n";
    foreach ($tvData as $i => $gallery) {
        echo "\nGalerie " . ($i + 1) . ": " . $gallery['gallery_name'] . "\n";
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
    }
    
    // Afficher le JSON final
    echo "\n=== JSON FINAL ===\n";
    $result = [
        'success' => true,
        'galleries' => $tvData,
        'total' => count($tvData)
    ];
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
