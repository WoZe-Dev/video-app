<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Middlewares\AuthMiddleware;
use App\Models\GalleryModel;

/**
 * Contrôleur pour le mode TV direct (utilisateurs viewer)
 */
class TVController extends Controller
{
    private $galleryModel;

    public function __construct() {
        $this->galleryModel = new GalleryModel();
    }

    /**
     * Page du mode TV (accès direct pour les viewers)
     */
    public function index(): void
    {
        // Vérifier que l'utilisateur est connecté
        if (!AuthMiddleware::isLoggedIn()) {
            $this->redirect('/login');
        }

        $user = $_SESSION['user'];
        
        // Vérifier que l'utilisateur a accès au mode TV
        if ($user['role'] !== 'viewer' && $user['role'] !== 'admin') {
            $this->redirect('/login');
        }

        $data = [
            'title' => 'Mode TV - BetweenUs',
            'user' => $user,
            'isViewerMode' => ($user['role'] === 'viewer')
        ];

        $this->loadView('tv/index', $data);
    }
}
