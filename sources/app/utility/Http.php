<?php

namespace App\Utility;

class Http {
    
    /**
     * Envoie une réponse JSON
     * @param mixed $data Les données à encoder en JSON
     * @param int $statusCode Le code de statut HTTP
     */
    public static function json($data, $statusCode = 200) {
        // Définir le code de statut
        http_response_code($statusCode);
        
        // Définir les headers pour JSON
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Encoder et envoyer les données
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }
    
    /**
     * Envoie une réponse d'erreur JSON
     * @param string $message Le message d'erreur
     * @param int $statusCode Le code de statut HTTP
     */
    public static function jsonError($message, $statusCode = 400) {
        self::json(['error' => $message, 'status' => $statusCode], $statusCode);
    }
    
    /**
     * Envoie une réponse de succès JSON
     * @param mixed $data Les données à envoyer
     * @param string $message Un message de succès optionnel
     */
    public static function jsonSuccess($data = null, $message = null) {
        $response = ['success' => true];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        self::json($response);
    }
    
    /**
     * Vérifie si la requête est une requête AJAX/API
     * @return bool
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Vérifie si la requête accepte JSON
     * @return bool
     */
    public static function acceptsJson() {
        return strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }
    
    /**
     * Récupère les données JSON du body de la requête
     * @return mixed
     */
    public static function getJsonInput() {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
}
