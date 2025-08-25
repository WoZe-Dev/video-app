<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Form;
use App\Middlewares\AuthMiddleware;
use App\Models\UserModel;
use Exception;

/**
 * AuthController avec support des rôles :
 * - Connexion avec base de données
 * - Redirection selon le rôle (admin vers galeries, viewer vers mode TV)
 */
class AuthController extends Controller
{
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }
    /**
     * Page d'accueil (GET "/")
     */
    public function index()
    {
        echo "<h1>Accueil</h1>";
        echo "<p><a href='/login'>Login</a> | <a href='/logout'>Logout</a></p>";
    }

    /**
     * Formulaire de connexion (GET "/login")
     */
    public function login(): void
    {
        if (AuthMiddleware::isLoggedIn()) {
            // Rediriger selon le rôle de l'utilisateur connecté
            $userRole = $_SESSION['user']['role'] ?? 'viewer';
            if ($userRole === 'admin') {
                $this->redirect('/gallery');
            } else {
                $this->redirect('/tv-mode');
            }
        }

        $form = new Form('/login', 'POST');

        $form->addTextField('login', 'Login', '', [
            'required' => 'required',
            'placeholder' => 'Entrez votre login (admin ou tv)',
            'class' => 'form-group'
        ])
            ->addPasswordField('password', 'Mot de passe', [
                'required' => 'required',
                'placeholder' => 'password123 ou password',
                'class' => 'form-group'
            ])
            ->addSubmitButton('Connexion', ['name' => 'submit', 'class' => 'login-button']);

        $data = [
            'title' => 'Connexion',
            'form' => $form
        ];

        $this->loadView('auth/login', $data);
    }

    /**
     * Traitement du login (POST "/login")
     */
    public function attemptLogin(): void
    {
        $isSubmitted = isset($_POST['submit']) || Form::isSubmitted();
        if (!$isSubmitted) {
            $this->redirect('/login');
        }

        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // D'abord essayer la connexion admin via .env
        $adminLogin = $_ENV['ADMIN_LOGIN'] ?? '';
        $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? '';

        if ($login === $adminLogin && $password === $adminPassword) {
            // Connexion admin réussie
            $this->loginUser([
                'id' => 1,
                'username' => $adminLogin,
                'email' => 'admin@betweenus.com',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => 'admin'
            ]);
            $this->redirect('/gallery');
        }

        // Sinon essayer la base de données
        try {
            error_log("Tentative de connexion DB pour: " . $login);
            $user = $this->userModel->getUserByUsername($login);
            
            if ($user) {
                error_log("Utilisateur trouvé: " . $user->username . ", role: " . $user->role);
                error_log("Hash en DB: " . substr($user->password, 0, 20) . "...");
                
                if (password_verify($password, $user->password)) {
                    error_log("Mot de passe vérifié avec succès");
                    // Connexion réussie
                    $this->loginUser([
                        'id' => $user->id,
                        'username' => $user->username,
                        'email' => $user->email,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'role' => $user->role
                    ]);
                    
                    // Redirection selon le rôle
                    if ($user->role === 'admin') {
                        $this->redirect('/gallery');
                    } else {
                        $this->redirect('/tv-mode');
                    }
                } else {
                    error_log("Échec de la vérification du mot de passe");
                }
            } else {
                error_log("Utilisateur non trouvé: " . $login);
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
        }

        // Échec de connexion
        $_SESSION['login_error'] = "Identifiants incorrects";
        $this->redirect('/login');
    }

    private function loginUser($userData): void
    {
        // CSRF Token generation for the session
        $token = AuthMiddleware::generateCsrfToken();
        $_SESSION['csrf_token'] = $token;

        $_SESSION['user'] = $userData;
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: /');
        exit;
    }
}
