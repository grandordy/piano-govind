<?php
// Version simplifiée de l'API pour test
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'getDemosAClasser':
            // Retourner une réponse simple
            echo json_encode([
                'success' => true,
                'demos' => [],
                'message' => 'API fonctionnelle'
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'test':
            // Test simple
            echo json_encode([
                'success' => true,
                'message' => 'API test fonctionnelle',
                'timestamp' => date('c')
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action non reconnue: ' . $action
            ], JSON_UNESCAPED_UNICODE);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
