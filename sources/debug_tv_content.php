<?php
// Script pour diagnostiquer pourquoi l'API TV ne montre pas les sous-galeries et vidéos
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/Models/GalleryModel.php';

echo "=== DIAGNOSTIC API TV - GALERIES ET VIDÉOS ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $galleryModel = new \App\Models\GalleryModel();
    
    // 1. Vérifier les galeries principales
    echo "1. GALERIES PRINCIPALES:\n";
    $mainGalleries = $galleryModel->getMainGalleries();
    echo "Nombre de galeries principales: " . count($mainGalleries) . "\n";
    
    foreach ($mainGalleries as $gallery) {
        echo "- Galerie: " . $gallery->gallery_name . " (ID: " . $gallery->gallery_id . ")\n";
    }
    
    // 2. Pour chaque galerie principale, vérifier le contenu
    echo "\n2. CONTENU DES GALERIES:\n";
    foreach ($mainGalleries as $gallery) {
        echo "\n--- Galerie: " . $gallery->gallery_name . " ---\n";
        
        $content = $galleryModel->getGalleryContent($gallery->gallery_id);
        
        echo "Sous-galeries: " . count($content['subgalleries']) . "\n";
        foreach ($content['subgalleries'] as $subgallery) {
            echo "  * " . $subgallery->gallery_name . " (ID: " . $subgallery->gallery_id . ")\n";
        }
        
        echo "Vidéos directes: " . count($content['videos']) . "\n";
        foreach ($content['videos'] as $video) {
            echo "  * " . ($video->caption ?? 'Sans titre') . " (" . $video->video_path . ")\n";
        }
        
        // Vérifier les vidéos dans les sous-galeries
        echo "Vidéos dans les sous-galeries:\n";
        $totalSubVideos = 0;
        foreach ($content['subgalleries'] as $subgallery) {
            $subContent = $galleryModel->getGalleryContent($subgallery->gallery_id);
            $subVideoCount = count($subContent['videos']);
            $totalSubVideos += $subVideoCount;
            
            if ($subVideoCount > 0) {
                echo "  " . $subgallery->gallery_name . ": " . $subVideoCount . " vidéos\n";
                foreach ($subContent['videos'] as $video) {
                    echo "    - " . ($video->caption ?? 'Sans titre') . " (" . $video->video_path . ")\n";
                }
            }
        }
        echo "Total vidéos dans sous-galeries: " . $totalSubVideos . "\n";
    }
    
    // 3. Simuler la structure de données TV
    echo "\n3. SIMULATION STRUCTURE API TV:\n";
    $tvData = [];
    foreach ($mainGalleries as $gallery) {
        $content = $galleryModel->getGalleryContent($gallery->gallery_id);
        
        // Combiner toutes les vidéos
        $allVideos = [];
        
        // Vidéos directes
        foreach ($content['videos'] as $video) {
            $allVideos[] = [
                'source' => 'direct',
                'title' => $video->caption ?? 'Sans titre',
                'path' => $video->video_path
            ];
        }
        
        // Vidéos des sous-galeries
        foreach ($content['subgalleries'] as $subgallery) {
            $subContent = $galleryModel->getGalleryContent($subgallery->gallery_id);
            foreach ($subContent['videos'] as $video) {
                $allVideos[] = [
                    'source' => 'subgallery: ' . $subgallery->gallery_name,
                    'title' => $video->caption ?? 'Sans titre',
                    'path' => $video->video_path
                ];
            }
        }
        
        $tvData[] = [
            'gallery_name' => $gallery->gallery_name,
            'subgallery_count' => count($content['subgalleries']),
            'video_count' => count($allVideos),
            'videos' => $allVideos
        ];
    }
    
    echo "Structure pour API TV:\n";
    foreach ($tvData as $item) {
        echo "- " . $item['gallery_name'] . ": " . $item['subgallery_count'] . " sous-galeries, " . $item['video_count'] . " vidéos totales\n";
        
        if (!empty($item['videos'])) {
            echo "  Vidéos:\n";
            foreach ($item['videos'] as $video) {
                echo "    * " . $video['title'] . " [" . $video['source'] . "]\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
