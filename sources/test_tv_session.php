<?php
// Script pour créer une session d'utilisateur TV et tester l'API
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/Models/UserModel.php';

session_start();

echo "=== CRÉATION SESSION TV ET TEST API ===\n";

try {
    $userModel = new \App\Models\UserModel();
    
    // Chercher un utilisateur existant ou créer un utilisateur TV de test
    $users = $userModel->getAllUsers();
    $tvUser = null;
    
    foreach ($users as $user) {
        if ($user->role === 'user') {
            $tvUser = $user;
            break;
        }
    }
    
    if (!$tvUser) {
        echo "Aucun utilisateur TV trouvé. Création d'un utilisateur de test...\n";
        // Ici on pourrait créer un utilisateur de test si nécessaire
        exit("Veuillez créer un utilisateur avec le rôle 'user' pour tester le mode TV.\n");
    }
    
    // Créer la session
    $_SESSION['user'] = [
        'id' => $tvUser->id,
        'username' => $tvUser->username,
        'email' => $tvUser->email,
        'role' => $tvUser->role
    ];
    
    echo "Session créée pour l'utilisateur: " . $tvUser->username . " (rôle: " . $tvUser->role . ")\n";
    echo "ID utilisateur: " . $tvUser->id . "\n\n";
    
    // Maintenant tester l'API
    echo "=== TEST DE L'API GALLERIES ===\n";
    
    // Simuler l'appel API
    $_GET['endpoint'] = 'galleries';
    
    // Inclure le contenu de test_api.php
    ob_start();
    include __DIR__ . '/test_api.php';
    $apiResponse = ob_get_clean();
    
    echo "Réponse API:\n";
    echo $apiResponse . "\n";
    
    // Parser et analyser la réponse
    $data = json_decode($apiResponse, true);
    if ($data && isset($data['galleries'])) {
        echo "\n=== ANALYSE DES DONNÉES ===\n";
        echo "Nombre de galeries: " . count($data['galleries']) . "\n";
        
        foreach ($data['galleries'] as $index => $gallery) {
            echo "\nGalerie " . ($index + 1) . ":\n";
            echo "- ID: " . $gallery['gallery_id'] . "\n";
            echo "- Nom: " . $gallery['gallery_name'] . "\n";
            echo "- Nombre de vidéos: " . $gallery['video_count'] . "\n";
            echo "- Nombre de sous-galeries: " . $gallery['subgallery_count'] . "\n";
            
            if (!empty($gallery['videos'])) {
                echo "- Vidéos:\n";
                foreach ($gallery['videos'] as $video) {
                    echo "  * " . $video['video_title'] . " (" . $video['gallery_path'] . ")\n";
                }
            }
            
            if (!empty($gallery['subgalleries'])) {
                echo "- Sous-galeries:\n";
                foreach ($gallery['subgalleries'] as $subgallery) {
                    echo "  * " . $subgallery['gallery_name'] . "\n";
                }
            }
        }
    } else {
        echo "Erreur dans la réponse API ou pas de galeries trouvées.\n";
        if (isset($data['error'])) {
            echo "Erreur: " . $data['error'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
