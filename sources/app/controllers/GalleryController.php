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
        $galleries = $galleryModel->getUserGalleriesAndContent($user['id']);

        $data = [
            'title' => 'Mes Galeries Vidéos',
            'galleries' => $galleries
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

        $galleryForm = new Form('/gallery/create', 'POST', '', 'w-100');
        $galleryForm->addTextField(
            'name',
            '',
            $data['name'] ?? '',
            [
                'required' => true,
                'placeholder' => 'Nom de la Galerie',
                'class' => 'mb-6'
            ]
        )->addHiddenField(
                'csrf_token',
                $_SESSION['csrf_token'] ?? AuthMiddleware::generateCsrfToken()
            )->addSubmitButton(
                'Créer',
                ['class' => 'button form__button']
            );


        $data = [
            'title' => 'Créer une Galerie',
            'form' => $galleryForm
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            FlashMessage::add('Méthode non autorisée.', 'error');
            // redirect to the create gallery page
            $this->redirect('/gallery/create');
        }


        if (!AuthMiddleware::verifyCsrfToken($_POST['csrf_token'])) {
            // redirect to the create gallery page
            FlashMessage::add('L\'authentification a échoué.', 'error');
            $this->redirect('/gallery/create');
        }

        $galleryModel = $this->loadModel('GalleryModel');

        // Sanitize the POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $user = $_SESSION['user'];

        try {
            $galleryId = $galleryModel->createGallery([
                'name' => $_POST['name'],
                'created_by' => $user['id']
            ]);
            $this->redirect('/gallery/' . $galleryId);
        } catch (\Exception $e) {
            $this->redirect('/gallery/create');
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

        $gallery = $galleryModel->getGallery($id, $user['id']);

        if (!$gallery || empty($gallery) || !$user) {
            $this->redirect('/gallery');
        }

        // Get the gallery videos
        $galleryVideos = json_decode($gallery->galleryVideos);

        if ((count($galleryVideos) == 0 || (count($galleryVideos) == 1 && empty($galleryVideos[0]->id)))) {
            $galleryVideos = [];
        }

        // Get the gallery users
        $galleryUsers = $this->getGalleryUsers($id);
        $data = [
            'title' => $gallery->gallery_name,
            'galleryId' => $id,
            'galleryVideos' => $galleryVideos,
            'galleryUsers' => $galleryUsers,
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
        $formAction = "/gallery/upload/{$id}";
        $videoForm = new Form($formAction, 'POST', 'multipart/form-data');
        $videoForm->addFileField(
            'video',
            '',
            [
                'required' => true,
                'class' => 'button button-cta',
                'accept' => 'video/mp4,video/mpeg,video/quicktime,video/x-msvideo,video/webm,video/ogg'
            ]
        )->addHiddenField(
                'csrf_token',
                AuthMiddleware::generateCsrfToken()
            )
            ->addSubmitButton('Upload Vidéo', ['class' => 'button']);

        $data = [
            'title' => 'Importer une vidéo',
            'form' => $videoForm
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

        // Vérification du token CSRF
        if (!isset($_POST['csrf_token']) || !AuthMiddleware::verifyCsrfToken($_POST['csrf_token'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Token CSRF invalide']);
            return;
        }

        $user = AuthMiddleware::getSessionUser();
        $galleryId = $id;
        $galleryModel = $this->loadModel('GalleryModel');

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
                $videoPath = FileManager::uploadGalleryVideo($file, $user['id'], $galleryId);
                
                // Extraire le nom du fichier sans l'extension
                $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
                
                $data = [
                    'gallery_id' => $galleryId,
                    'user_id' => $user['id'],
                    'video_path' => $videoPath,
                    'caption' => $originalName, // Utiliser le nom du fichier au lieu de "Vidéo sans titre"
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
            $videoPath = FileManager::uploadGalleryVideo($video, $user['id'], $galleryId);
            
            // Extraire le nom du fichier sans l'extension
            $originalName = pathinfo($video['name'], PATHINFO_FILENAME);
            
            $data = [
                'gallery_id' => $galleryId,
                'user_id' => $user['id'],
                'video_path' => $videoPath,
                'caption' => $originalName, // Utiliser le nom du fichier au lieu de "Vidéo sans titre"
                'is_public' => 1,
                'file_size' => $video['size'],
                'mime_type' => $video['type']
            ];
            $galleryModel->createVideo($data);
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
        $galleryModel = $this->loadModel('GalleryModel');
        $user = $_SESSION['user'];
        $video = $galleryModel->getVideo($videoId, $user['id']);

        if (!$video) {
            FlashMessage::add('Vidéo non trouvée.', 'error');
            $this->redirect('/gallery');
        }

        $isVideoDeleted = $galleryModel->deleteGalleryVideo($videoId, $user['id']);
        if (!$isVideoDeleted) {
            FlashMessage::add('Échec de suppression de la vidéo.', 'error');
            $this->redirect('/gallery/' . $video->gallery_id);
        }

        FileManager::deleteGalleryVideo($video->video_path);
        FlashMessage::add('Vidéo supprimée avec succès.', 'success');
        $this->redirect('/gallery/' . $video->gallery_id);
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
     * Delete the specified gallery.
     * @param int $galleryId
     * @return void
     */

    public function deleteGallery(int $galleryId): void
    {
        $galleryModel = $this->loadModel('GalleryModel');
        $userId = AuthMiddleware::getSessionUser()['id'];
        $isOwner = $galleryModel->checkOwner($userId);
        if (!$isOwner) {
            FlashMessage::add('Vous n\'avez pas la permission d\'effectuer cette action.', 'error');
            $this->redirect('/gallery/' . $galleryId);
        }

        $isDeleted = $galleryModel->deleteGalleryById($galleryId);
        if (!$isDeleted) {
            FlashMessage::add('Échec de la suppression de la galerie.', 'error');
            $this->redirect('/gallery/' . $galleryId);
        }

        FileManager::emptyGalleryPhotos($galleryId);
        FlashMessage::add('La galerie a été supprimée.', 'success');
        $this->redirect('/gallery');
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
