<?php
use App\Core\Router;
session_start();

// Register the autoloader
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

	// Replace namespace separators with directory separators
	// Then append ".php"
	$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

	// If the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});

// All the routes that we want to add to handle the requests
$router = new Router();
$router->get('/', ['AuthController', 'login']);
$router->post('/', ['HomeController', 'index']);

// Page d'accueil d'exemple
//$router->get('/', ['HomeController', 'index']);

// Connexion
$router->get('/login', ['AuthController', 'login']);
$router->post('/login', ['AuthController', 'attemptLogin']);

// Déconnexion
$router->get('/logout', ['AuthController', 'logout']);

//Gallery routes
$router->get('/gallery', ['GalleryController', 'index']);
$router->get('/gallery/create', ['GalleryController', 'createGallery']);
$router->post('/gallery/create', ['GalleryController', 'storeGallery']);
$router->get('/gallery/{id}', ['GalleryController', 'showGallery']);
$router->get('/gallery/upload/{id}', ['GalleryController', 'uploadVideoForm']);
$router->post('/gallery/upload/{id}', ['GalleryController', 'storeVideo']);

// Delete video
$router->get('/gallery/delete-video/{id}', ['GalleryController', 'deleteVideo']);

// Delete gallery
$router->get('/gallery/delete/{galleryId}', ['GalleryController', 'deleteGallery']);

// Empty gallery 
$router->get('/gallery/empty/{galleryId}', ['GalleryController', 'emptyGallery']);



// Gallery user routes
$router->get('/gallery/addusers/{id}', ['GalleryUserController', 'addUsersInGallery']);
$router->post('/gallery/addusers/{id}', ['GalleryUserController', 'addUsersInGallery']);
$router->get('/gallery/send_invite/{userid}', ['GalleryUserController', 'addUserAndSendMail']);

// Remove user from gallery
$router->get('/gallery/removeuser/{userid}', ['GalleryUserController', 'removeUserFromGallery']);

// API routes pour l'interface TV
$router->get('/api/galleries', ['ApiController', 'galleries']);
$router->get('/api/video/{id}', ['ApiController', 'video']);
$router->get('/api/gallery/{id}/videos', ['ApiController', 'galleryVideos']);

// Route pour le mode TV direct (rôle viewer)
$router->get('/tv-mode', ['TVController', 'index']);

// Route upload pour l'interface TV
$router->post('/gallery/upload', ['GalleryController', 'uploadFromTV']);

// Temporary code for testing
$router->get('/profile', ['ProfileController', 'index']);

// Dispatch
$router->get('/designguide', ['DesignGuideController', 'index']);
$router->dispatch();
