<?php
namespace App\Controllers;

use App\Models\GalleryModel;
use App\Utility\Http;
use Exception;

class ApiController {
    private $galleryModel;

    public function __construct() {
        $this->galleryModel = new GalleryModel();
    }

    public function galleries() {
        error_log('ApiController::galleries() appelée');
        try {
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['user']['id'])) {
                error_log('API Error: Utilisateur non authentifié');
                Http::jsonError('Utilisateur non authentifié', 401);
                return;
            }

            $userId = $_SESSION['user']['id'];
            error_log('API galleries pour userId: ' . $userId);
            
            // Récupérer uniquement les galeries principales pour le mode TV
            $galleries = $this->galleryModel->getMainGalleries();
            
            // Transformer les données pour l'API TV - structure hiérarchique
            $tvData = [];
            foreach ($galleries as $gallery) {
                // Récupérer le contenu de cette galerie
                $content = $this->galleryModel->getGalleryContent($gallery->gallery_id);
                
                // Combiner les vidéos de cette galerie et de ses sous-galeries
                $allVideos = [];
                
                // Ajouter les vidéos directes de cette galerie
                foreach ($content['videos'] as $video) {
                    $allVideos[] = [
                        'id' => $video->id,
                        'caption' => $video->caption ?? 'Vidéo sans titre',
                        'video_path' => $video->video_path,
                        'full_path' => $video->full_path ?? '/' . $gallery->gallery_name . '/' . ($video->original_filename ?? $video->caption),
                        'mime_type' => $video->mime_type ?? 'video/mp4',
                        'duration' => $video->duration ?? 0,
                        'file_size' => $video->file_size ?? 0,
                        'created_at' => $video->created_at ?? date('Y-m-d H:i:s'),
                        'gallery_path' => $gallery->gallery_name
                    ];
                }
                
                // Ajouter les vidéos des sous-galeries
                foreach ($content['subgalleries'] as $subgallery) {
                    $subContent = $this->galleryModel->getGalleryContent($subgallery->gallery_id);
                    foreach ($subContent['videos'] as $video) {
                        $allVideos[] = [
                            'id' => $video->id,
                            'caption' => $video->caption ?? 'Vidéo sans titre',
                            'video_path' => $video->video_path,
                            'full_path' => $video->full_path ?? '/' . $gallery->gallery_name . '/' . $subgallery->gallery_name . '/' . ($video->original_filename ?? $video->caption),
                            'mime_type' => $video->mime_type ?? 'video/mp4',
                            'duration' => $video->duration ?? 0,
                            'file_size' => $video->file_size ?? 0,
                            'created_at' => $video->created_at ?? date('Y-m-d H:i:s'),
                            'gallery_path' => $gallery->gallery_name . '/' . $subgallery->gallery_name
                        ];
                    }
                }
                
                // Thumbnail - première vidéo trouvée
                $thumbnailPath = !empty($allVideos) ? $allVideos[0]['video_path'] : null;
                
                $tvData[] = [
                    'gallery_id' => $gallery->gallery_id,
                    'gallery_name' => $gallery->gallery_name,
                    'gallery_description' => '',
                    'video_count' => count($allVideos),
                    'subgalleries_count' => $gallery->subgalleries_count,
                    'videos_count' => $gallery->videos_count,
                    'total_videos' => count($allVideos),
                    'thumbnail_path' => $thumbnailPath,
                    'videos' => $allVideos
                ];
            }
            
            Http::json([
                'success' => true,
                'galleries' => $tvData,
                'total' => count($tvData)
            ]);
        } catch (Exception $e) {
            error_log('API Error in galleries(): ' . $e->getMessage());
            Http::jsonError('Erreur lors du chargement des galeries: ' . $e->getMessage(), 500);
        }
    }

    public function video($videoId) {
        try {
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['user']['id'])) {
                Http::jsonError('Utilisateur non authentifié', 401);
                return;
            }

            // Récupérer les détails d'une vidéo spécifique
            $video = $this->galleryModel->getVideoById($videoId);
            
            if (!$video) {
                Http::jsonError('Vidéo non trouvée', 404);
                return;
            }

            Http::json([
                'id' => $video->id,
                'caption' => $video->caption ?? 'Vidéo sans titre',
                'video_path' => $video->video_path,
                'mime_type' => $video->mime_type ?? 'video/mp4',
                'duration' => $video->duration ?? 0,
                'file_size' => $video->file_size ?? 0,
                'created_at' => $video->created_at ?? date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            error_log('API Error: ' . $e->getMessage());
            Http::jsonError('Erreur lors du chargement de la vidéo', 500);
        }
    }

    public function galleryVideos($galleryId) {
        try {
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['user']['id'])) {
                Http::jsonError('Utilisateur non authentifié', 401);
                return;
            }

            $userId = $_SESSION['user']['id'];
            
            // Utiliser la méthode getGallery corrigée au lieu de getUserGalleriesAndContent
            $targetGallery = $this->galleryModel->getGallery($galleryId, $userId);
            
            if (!$targetGallery) {
                Http::jsonError('Galerie non trouvée', 404);
                return;
            }

            $galleryVideos = json_decode($targetGallery->galleryVideos ?? '[]');
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

            Http::json([
                'gallery_id' => $targetGallery->gallery_id,
                'gallery_name' => $targetGallery->gallery_name,
                'gallery_description' => $targetGallery->description ?? '',
                'video_count' => count($videos),
                'videos' => $videos
            ]);
        } catch (Exception $e) {
            error_log('API Error: ' . $e->getMessage());
            Http::jsonError('Erreur lors du chargement des vidéos de la galerie', 500);
        }
    }
}
