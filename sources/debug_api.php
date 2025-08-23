<?php
session_start();

echo "<h2>Test de debug API</h2>";
echo "<h3>Session actuelle:</h3>";
var_dump($_SESSION);

if (isset($_SESSION['user']['id'])) {
    echo "<p>Utilisateur connecté: " . $_SESSION['user']['username'] . " (ID: " . $_SESSION['user']['id'] . ", Rôle: " . $_SESSION['user']['role'] . ")</p>";
    
    // Test de l'API directement
    require_once 'app/controllers/ApiController.php';
    require_once 'app/models/GalleryModel.php';
    require_once 'app/models/Model.php';
    require_once 'app/utility/Http.php';
    
    try {
        echo "<h3>Test API Galleries:</h3>";
        $apiController = new \App\Controllers\ApiController();
        
        // Capturer la sortie
        ob_start();
        $apiController->galleries();
        $output = ob_get_clean();
        
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Aucun utilisateur connecté</p>";
}
?>
