<?php

namespace App\Controllers;
use App\Core\Controller;
use App\Core\Form;
use App\Middlewares\AuthMiddleware;
use App\Utility\FileManager;
use App\Utility\FlashMessage;


class GalleryController extends Controller
{
    /**
     * Display a listing of the gallery to show on the dashboard with all the galleries of which user is a part.
     * @return void
     */
    public function index(): void
    {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getSessionUser();
        $galleryModel = $this->loadModel('GalleryModel');
        
        // Afficher uniquement les galeries principales (racine)
        $galleries = $galleryModel->getMainGalleries();

        $data = [
            'title' => 'Galeries Vidéos',
            'galleries' => $galleries,
            'current_gallery' => null,
            'breadcrumb' => []
        ];
        $this->loadView('gallery/index', $data);
    }

    /**
     * Show the form for creating a new gallery.
     * @return void
     */
    public function createGallery(): void
    {
        AuthMiddleware::requireLogin();
        
        // Vérifier les permissions admin
        if (!AuthMiddleware::isAdmin()) {
            FlashMessage::add('Accès refusé. Seuls les administrateurs peuvent créer des galeries.', 'error');
            $this->redirect('/gallery');
        }

        // Récupérer le parent_id depuis l'URL si fourni
        $parentId = $_GET['parent'] ?? null;
        $parentGallery = null;
        
        if ($parentId) {
            $galleryModel = $this->loadModel('GalleryModel');
            $parentGallery = $galleryModel->getGalleryById((int)$parentId);
            if (!$parentGallery) {
                FlashMessage::add('Galerie parente introuvable.', 'error');
                $this->redirect('/gallery');
            }
        }

        $action = $parentId ? "/gallery/create?parent={$parentId}" : '/gallery/create';
        $galleryForm = new Form($action, 'POST', '', 'w-100');
        $galleryForm->addTextField(
            'name',
            'Nom de la galerie',
            $_POST['name'] ?? '',
            [
                'required' => true,
                'placeholder' => 'Nom de la galerie',
                'class' => 'mb-6'
            ]
        );
        
        if ($parentId) {
            $galleryForm->addHiddenField('parent_id', $parentId);
        }
        
        $galleryForm->addHiddenField(
                'csrf_token',
                $_SESSION['csrf_token'] ?? AuthMiddleware::generateCsrfToken()
            )->addSubmitButton(
                'Créer',
                ['class' => 'button form__button']
            );

        $breadcrumb = [];
        if ($parentGallery) {
            $galleryModel = $this->loadModel('GalleryModel');
            $breadcrumb = $galleryModel->getGalleryBreadcrumb((int)$parentId);
        }

        $data = [
            'title' => $parentGallery ? 'Créer une sous-galerie' : 'Créer une galerie principale',
            'form' => $galleryForm,
            'parent_gallery' => $parentGallery,
            'breadcrumb' => $breadcrumb
        ];

        $this->loadView('gallery/create', $data);
    }

    /**
     * Store a newly created gallery in storage.
     * @return void
     */
    public function storeGallery(): void
    {
        AuthMiddleware::requireLogin();
        
        // Vérifier les permissions admin
        if (!AuthMiddleware::isAdmin()) {
            FlashMessage::add('Accès refusé. Seuls les administrateurs peuvent créer des galeries.', 'error');
            $this->redirect('/gallery');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            FlashMessage::add('Méthode non autorisée.', 'error');
            $this->redirect('/gallery/create');
        }

        // Vérification du token CSRF
        if (!isset($_POST['csrf_token']) || !AuthMiddleware::verifyCsrfToken($_POST['csrf_token'])) {
            FlashMessage::add('Token de sécurité invalide ou manquant.', 'error');
            $this->redirect('/gallery/create');
        }

        $galleryModel = $this->loadModel('GalleryModel');
        
        // Sanitize the POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        $name = trim($_POST['name']);
        $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        
        // Validation du nom
        if (empty($name)) {
            FlashMessage::add('Le nom de la galerie est requis.', 'error');
            $redirectUrl = $parentId ? "/gallery/create?parent={$parentId}" : '/gallery/create';
            $this->redirect($redirectUrl);
        }
        
        // Vérifier les caractères interdits
        if (preg_match('/[\/\\\\:*?"<>|]/', $name)) {
            FlashMessage::add('Le nom ne peut pas contenir les caractères : / \\ : * ? " < > |', 'error');
            $redirectUrl = $parentId ? "/gallery/create?parent={$parentId}" : '/gallery/create';
            $this->redirect($redirectUrl);
        }
        
        // Vérifier l'unicité du nom au même niveau
        if (!$galleryModel->isGalleryNameUnique($name, $parentId)) {
            FlashMessage::add('Une galerie avec ce nom existe déjà à ce niveau.', 'error');
            $redirectUrl = $parentId ? "/gallery/create?parent={$parentId}" : '/gallery/create';
            $this->redirect($redirectUrl);
        }

        $user = $_SESSION['user'];

        try {
            $galleryId = $galleryModel->createGallery([
                'name' => $name,
                'parent_id' => $parentId,
                'created_by' => $user['id']
            ]);
            
            FlashMessage::add('Galerie créée avec succès.', 'success');
            $this->redirect('/gallery/' . $galleryId);
        } catch (\Exception $e) {
            FlashMessage::add('Erreur lors de la création de la galerie.', 'error');
            $redirectUrl = $parentId ? "/gallery/create?parent={$parentId}" : '/gallery/create';
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Display the specified gallery.
     * @param int $id
     * @return void
     */
    public function showGallery(int $id): void
    {
        AuthMiddleware::requireLogin();
        $user = AuthMiddleware::getSessionUser();

        $galleryModel = $this->loadModel('GalleryModel');

        // Récupérer les informations de base de la galerie
        $gallery = $galleryModel->getGalleryById($id);

        if (!$gallery) {
            FlashMessage::add('Galerie introuvable.', 'error');
            $this->redirect('/gallery');
        }

        // Récupérer le contenu de la galerie (sous-galeries et vidéos)
        $content = $galleryModel->getGalleryContent($id);
        
        // Récupérer le fil d'Ariane
        $breadcrumb = $galleryModel->getGalleryBreadcrumb($id);

        // Get the gallery users
        $galleryUsers = $this->getGalleryUsers($id);
        
        // Check if gallery has children (for delete confirmation)
        $hasChildren = $galleryModel->hasChildren($id);
        
        $data = [
            'title' => $gallery->name,
            'galleryId' => $id,
            'gallery' => $gallery,
            'subgalleries' => $content['subgalleries'],
            'videos' => $content['videos'],
            'galleryUsers' => $galleryUsers,
            'breadcrumb' => $breadcrumb,
            'isAdmin' => AuthMiddleware::isAdmin(),
            'hasChildren' => $hasChildren
        ];
        $this->loadView('gallery/single', $data);
    }

    /**
     * Show the form for uploading a new video.
     * @return void
     */
    public function uploadVideoForm(int $id): void
    {
        AuthMiddleware::requireLogin();
        
        // Vérifier les permissions admin
        if (!AuthMiddleware::isAdmin()) {
            FlashMessage::add('Accès refusé. Seuls les administrateurs peuvent importer des vidéos.', 'error');
            $this->redirect('/gallery/' . $id);
        }
        
        $galleryModel = $this->loadModel('GalleryModel');
        $gallery = $galleryModel->getGalleryById($id);
        
        if (!$gallery) {
            FlashMessage::add('Galerie introuvable.', 'error');
            $this->redirect('/gallery');
        }
        
        $breadcrumb = $galleryModel->getGalleryBreadcrumb($id);
        
        $formAction = "/gallery/upload/{$id}";
        $videoForm = new Form($formAction, 'POST', 'multipart/form-data');
        $videoForm->addFileField(
            'video',
            'Sélectionner une vidéo',
            [
                'required' => true,
                'class' => 'button button-cta',
                'accept' => '.mp4,.mov,.avi'
            ]
        )->addHiddenField(
                'csrf_token',
                AuthMiddleware::generateCsrfToken()
            )
            ->addSubmitButton('Importer la vidéo', ['class' => 'button']);

        $data = [
            'title' => 'Importer une vidéo',
            'form' => $videoForm,
            'gallery' => $gallery,
            'breadcrumb' => $breadcrumb
        ];
        $this->loadView('gallery/upload', $data);
    }

    /**
     * Store a newly created video in storage.
     * @return void
     */
    public function storeVideo($id): void
    {
        AuthMiddleware::requireLogin();
        
        // Vérifier les permissions admin
        if (!AuthMiddleware::isAdmin()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Accès refusé. Seuls les administrateurs peuvent importer des vidéos.']);
            return;
        }

        // Vérification du token CSRF
        if (!isset($_POST['csrf_token']) || !AuthMiddleware::verifyCsrfToken($_POST['csrf_token'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
            return;
        }

        $user = AuthMiddleware::getSessionUser();
        $galleryId = $id;
        $galleryModel = $this->loadModel('GalleryModel');
        
        // Récupérer les informations de la galerie pour construire le chemin
        $gallery = $galleryModel->getGalleryById($galleryId);
        if (!$gallery) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Galerie introuvable']);
            return;
        }

        // Gestion de l'upload multiple via AJAX (clé 'files')
        if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
            $uploadedFiles = $_FILES['files'];
            $fileCount = count($uploadedFiles['name']);
            $results = [];
            for ($i = 0; $i < $fileCount; $i++) {
                $file = [
                    'name' => $uploadedFiles['name'][$i],
                    'type' => $uploadedFiles['type'][$i],
                    'tmp_name' => $uploadedFiles['tmp_name'][$i],
                    'error' => $uploadedFiles['error'][$i],
                    'size' => $uploadedFiles['size'][$i]
                ];
                
                // Valider l'extension
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['mp4', 'mov', 'avi'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Extension non autorisée. Seuls les fichiers .mp4, .mov et .avi sont acceptés.']);
                    return;
                }
                
                $videoPath = FileManager::uploadGalleryVideo($file, $user['id'], $galleryId);
                
                // Construire le chemin complet
                $breadcrumb = $galleryModel->getGalleryBreadcrumb($galleryId);
                $fullPath = $this->buildVideoFullPath($breadcrumb, $file['name']);
                
                // Extraire le nom du fichier sans l'extension
                $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
                
                $data = [
                    'gallery_id' => $galleryId,
                    'user_id' => $user['id'],
                    'video_path' => $videoPath,
                    'full_path' => $fullPath,
                    'original_filename' => $file['name'],
                    'caption' => $originalName,
                    'is_public' => 1,
                    'file_size' => $file['size'],
                    'mime_type' => $file['type']
                ];
                $galleryModel->createVideo($data);
                $results[] = $data;
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'uploaded' => $results]);
            return;
        }
        // Cas d'upload d'un seul fichier avec la clé 'video'
        elseif (isset($_FILES['video']) && !empty($_FILES['video']['name'])) {
            $video = $_FILES['video'];
            
            // Valider l'extension
            $extension = strtolower(pathinfo($video['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['mp4', 'mov', 'avi'];
            
            if (!in_array($extension, $allowedExtensions)) {
                FlashMessage::add('Extension non autorisée. Seuls les fichiers .mp4, .mov et .avi sont acceptés.', 'error');
                $this->redirect('/gallery/upload/' . $galleryId);
            }
            
            $videoPath = FileManager::uploadGalleryVideo($video, $user['id'], $galleryId);
            
            // Construire le chemin complet
            $breadcrumb = $galleryModel->getGalleryBreadcrumb($galleryId);
            $fullPath = $this->buildVideoFullPath($breadcrumb, $video['name']);
            
            // Extraire le nom du fichier sans l'extension
            $originalName = pathinfo($video['name'], PATHINFO_FILENAME);
            
            $data = [
                'gallery_id' => $galleryId,
                'user_id' => $user['id'],
                'video_path' => $videoPath,
                'full_path' => $fullPath,
                'original_filename' => $video['name'],
                'caption' => $originalName,
                'is_public' => 1,
                'file_size' => $video['size'],
                'mime_type' => $video['type']
            ];
            $galleryModel->createVideo($data);
            
            FlashMessage::add('Vidéo importée avec succès.', 'success');
            $this->redirect('/gallery/' . $galleryId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            return;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Aucun fichier reçu']);
            return;
        }
    }


    /**
     * Delete the specified video from the gallery.
     * @param int $videoId
     * @return void
     */
    public function deleteVideo(int $videoId): void
    {
        AuthMiddleware::requireLogin();
        
        // Vérifier les permissions admin
        if (!AuthMiddleware::isAdmin()) {
            FlashMessage::add('Accès refusé. Seuls les administrateurs peuvent supprimer des vidéos.', 'error');
            $this->redirect('/gallery');
        }
        
        $galleryModel = $this->loadModel('GalleryModel');
        $video = $galleryModel->getVideoById($videoId);

        if (!$video) {
            FlashMessage::add('Vidéo non trouvée.', 'error');
            $this->redirect('/gallery');
        }

        $isVideoDeleted = $galleryModel->deleteGalleryVideo($videoId, $video->user_id);
        if (!$isVideoDeleted) {
            FlashMessage::add('Échec de suppression de la vidéo.', 'error');
            $this->redirect('/gallery/' . $video->gallery_id);
        }

        FileManager::deleteGalleryVideo($video->video_path);
        FlashMessage::add('Vidéo supprimée avec succès.', 'success');
        $this->redirect('/gallery/' . $video->gallery_id);
    }

    /**
     * Delete the specified gallery.
     * @param int $galleryId
     * @return void
     */
    public function deleteGallery(int $galleryId): void
    {
        AuthMiddleware::requireLogin();
        
        // Vérifier les permissions admin
        if (!AuthMiddleware::isAdmin()) {
            FlashMessage::add('Accès refusé. Seuls les administrateurs peuvent supprimer des galeries.', 'error');
            $this->redirect('/gallery');
        }
        
        $galleryModel = $this->loadModel('GalleryModel');
        $gallery = $galleryModel->getGalleryById($galleryId);

        if (!$gallery) {
            FlashMessage::add('Galerie non trouvée.', 'error');
            $this->redirect('/gallery');
        }

        // Vérifier le paramètre de suppression forcée
        $forceDelete = isset($_GET['force']) && $_GET['force'] === 'true';
        
        // Vérifier si la galerie a des enfants
        if ($galleryModel->hasChildren($galleryId) && !$forceDelete) {
            // Rediriger vers une page de confirmation
            $this->redirect('/gallery/' . $galleryId . '?delete_confirm=true');
            return;
        }

        if ($forceDelete) {
            // Suppression en cascade
            $this->deleteGalleryCascade($galleryId);
        } else {
            // Suppression normale (galerie vide)
            $isDeleted = $galleryModel->deleteGalleryById($galleryId);
            if (!$isDeleted) {
                FlashMessage::add('Échec de suppression de la galerie.', 'error');
                $this->redirect('/gallery/' . $galleryId);
            }
        }

        FlashMessage::add('Galerie supprimée avec succès.', 'success');
        
        // Rediriger vers la galerie parent ou vers l'accueil
        if ($gallery->parent_id) {
            $this->redirect('/gallery/' . $gallery->parent_id);
        } else {
            $this->redirect('/gallery');
        }
    }

    /**
     * Delete gallery with all its children (cascade delete)
     * @param int $galleryId
     * @return void
     */
    private function deleteGalleryCascade(int $galleryId): void
    {
        $galleryModel = $this->loadModel('GalleryModel');
        
        // Récupérer toutes les sous-galeries
        $subGalleries = $galleryModel->getSubGalleries($galleryId);
        
        // Supprimer récursivement toutes les sous-galeries
        foreach ($subGalleries as $subGallery) {
            $this->deleteGalleryCascade($subGallery->id);
        }
        
        // Supprimer toutes les vidéos de cette galerie
        $videos = $galleryModel->getVideosByGallery($galleryId);
        foreach ($videos as $video) {
            // Supprimer le fichier physique
            FileManager::deleteGalleryVideo($video->video_path);
            // Supprimer de la base de données
            $galleryModel->deleteGalleryVideo($video->id, $video->user_id);
        }
        
        // Supprimer les associations utilisateur-galerie
        $galleryModel->removeAllUsersFromGallery($galleryId);
        
        // Finalement, supprimer la galerie elle-même
        $galleryModel->deleteGalleryById($galleryId);
    }

    /**
     * Build full path for video display
     * @param array $breadcrumb
     * @param string $filename
     * @return string
     */
    private function buildVideoFullPath(array $breadcrumb, string $filename): string
    {
        $pathParts = [];
        foreach ($breadcrumb as $item) {
            $pathParts[] = $item['name'];
        }
        $pathParts[] = $filename;
        
        return '/' . implode('/', $pathParts);
    }


    /**
     * Empty the specified gallery.
     * @param int $galleryId
     * @return void
     */

    public function emptyGallery(int $galleryId): void
    {
        $galleryModel = $this->loadModel('GalleryModel');
        $userId = AuthMiddleware::getSessionUser()['id'];

        $isOwner = $galleryModel->checkOwner($userId);

        if (!$isOwner) {
            FlashMessage::add('Vous n\'avez pas la permission de vider la galerie.', 'error');
            $this->redirect('/gallery/' . $galleryId);
        }

        $isEmptied = $galleryModel->emptyGallery($galleryId, $userId);

        if (!$isEmptied) {
            FlashMessage::add('Échec pour vider la galerie.', 'error');
            $this->redirect('/gallery/' . $galleryId);
        }

        FileManager::emptyGalleryPhotos($galleryId);
        FlashMessage::add('La galerie a été vidée.', 'success');
        $this->redirect('/gallery/' . $galleryId);
    }

    /**
     * Get the users of a gallery
     * @param int $galleryId
     * @return mixed
     */
    private function getGalleryUsers(int $galleryId): mixed
    {
        AuthMiddleware::requireLogin();
        $galleryModel = $this->loadModel('GalleryModel');
        return $galleryModel->getGalleryUsers($galleryId);
    }
}
