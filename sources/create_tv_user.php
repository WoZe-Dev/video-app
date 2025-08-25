<?php
// Script pour créer un utilisateur TV avec le bon hash
require_once '/home/php/app/Core/Model.php';

use App\Core\Model;

$model = new Model('users');

// Générer le hash
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Hash généré: " . $hash . "\n";

// Supprimer l'ancien utilisateur s'il existe
$deleteSQL = "DELETE FROM users WHERE username = 'tv'";
$stmt = $model->prepare($deleteSQL);
$model->execute($stmt, []);

// Créer le nouvel utilisateur
$insertSQL = "INSERT INTO users (first_name, last_name, username, email, password, role, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $model->prepare($insertSQL);
$result = $model->execute($stmt, [
    'TV',
    'User', 
    'tv',
    'tv@betweenus.com',
    $hash,
    'viewer',
    1
]);

if ($result) {
    echo "Utilisateur TV créé avec succès!\n";
    
    // Vérifier
    $selectSQL = "SELECT username, role, SUBSTR(password, 1, 30) as password_start FROM users WHERE username = 'tv'";
    $stmt = $model->prepare($selectSQL);
    $model->execute($stmt, []);
    $user = $model->fetch($stmt);
    
    if ($user) {
        echo "Vérification - Username: {$user->username}, Role: {$user->role}, Hash: {$user->password_start}...\n";
        
        // Test du mot de passe
        $fullSelectSQL = "SELECT password FROM users WHERE username = 'tv'";
        $stmt = $model->prepare($fullSelectSQL);
        $model->execute($stmt, []);
        $userFull = $model->fetch($stmt);
        
        if (password_verify('password', $userFull->password)) {
            echo "✅ Mot de passe 'password' fonctionne!\n";
        } else {
            echo "❌ Échec de vérification du mot de passe\n";
        }
    }
} else {
    echo "Erreur lors de la création\n";
}
?>
