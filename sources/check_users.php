<?php
require_once __DIR__ . '/app/Core/Model.php';

// Connexion à la base de données et vérification des utilisateurs
$pdo = new PDO(
    'mysql:host=mariadb;dbname=database',
    'voxio',
    '.Optile17',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "=== UTILISATEURS EXISTANTS ===\n";
$stmt = $pdo->query("SELECT id, username, first_name, last_name, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "Aucun utilisateur trouvé.\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user['id']} - Username: {$user['username']} - Nom: {$user['first_name']} {$user['last_name']} - Rôle: {$user['role']}\n";
    }
}

// Vérifier s'il existe un utilisateur viewer
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'viewer'");
$stmt->execute();
$viewerCount = $stmt->fetchColumn();

if ($viewerCount == 0) {
    echo "\n=== CRÉATION D'UN UTILISATEUR VIEWER ===\n";
    
    // Créer un utilisateur viewer de test
    $stmt = $pdo->prepare("
        INSERT INTO users (username, first_name, last_name, password, email, role, is_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $password = password_hash('viewer123', PASSWORD_DEFAULT);
    $stmt->execute([
        'viewer',
        'Utilisateur',
        'TV',
        $password,
        'viewer@betweenus.local',
        'viewer',
        1
    ]);
    
    echo "Utilisateur viewer créé avec succès!\n";
    echo "Username: viewer\n";
    echo "Password: viewer123\n";
} else {
    echo "\nIl y a déjà $viewerCount utilisateur(s) avec le rôle viewer.\n";
}

// Vérifier s'il existe un utilisateur admin
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$stmt->execute();
$adminCount = $stmt->fetchColumn();

if ($adminCount == 0) {
    echo "\n=== CRÉATION D'UN UTILISATEUR ADMIN ===\n";
    
    // Créer un utilisateur admin de test
    $stmt = $pdo->prepare("
        INSERT INTO users (username, first_name, last_name, password, email, role, is_verified) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt->execute([
        'admin',
        'Administrateur',
        'Système',
        $password,
        'admin@betweenus.local',
        'admin',
        1
    ]);
    
    echo "Utilisateur admin créé avec succès!\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
} else {
    echo "\nIl y a déjà $adminCount utilisateur(s) avec le rôle admin.\n";
}

echo "\n=== UTILISATEURS APRÈS CRÉATION ===\n";
$stmt = $pdo->query("SELECT id, username, first_name, last_name, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo "ID: {$user['id']} - Username: {$user['username']} - Nom: {$user['first_name']} {$user['last_name']} - Rôle: {$user['role']}\n";
}
?>
