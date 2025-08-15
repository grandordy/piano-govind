<?php
// delete-demo.php
// Script pour supprimer une démo du serveur

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fonction de log pour le débogage
function debugLog($message) {
    error_log("DELETE-DEMO: " . $message);
}

debugLog("Script de suppression démarré");

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debugLog("Méthode non autorisée: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Récupérer les données JSON
$rawInput = file_get_contents('php://input');
debugLog("Données reçues: " . $rawInput);

$input = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    debugLog("Erreur JSON: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données JSON invalides']);
    exit;
}

if (!isset($input['filename']) || empty($input['filename'])) {
    debugLog("Nom de fichier manquant");
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nom de fichier manquant']);
    exit;
}

$filename = $input['filename'];
debugLog("Fichier à supprimer: " . $filename);

// Sécuriser le nom de fichier
$originalFilename = $filename;
$filename = basename($filename); // Enlever les chemins
$filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename); // Caractères autorisés uniquement

if ($filename !== $originalFilename) {
    debugLog("Nom de fichier nettoyé: " . $originalFilename . " -> " . $filename);
}

// Chemin vers le dossier des démos
$demosDir = 'demospubliques/';
$filePath = $demosDir . $filename;

debugLog("Chemin complet: " . $filePath);

// Vérifier que le dossier existe
if (!is_dir($demosDir)) {
    debugLog("Dossier demospubliques n'existe pas");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Dossier des démos introuvable']);
    exit;
}

// Vérifier que le fichier existe
if (!file_exists($filePath)) {
    debugLog("Fichier n'existe pas: " . $filePath);
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Fichier non trouvé: ' . $filename]);
    exit;
}

// Vérifier que c'est bien un fichier JSON
if (pathinfo($filename, PATHINFO_EXTENSION) !== 'json') {
    debugLog("Type de fichier non autorisé: " . pathinfo($filename, PATHINFO_EXTENSION));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
    exit;
}

// Vérifier les permissions
if (!is_readable($filePath)) {
    debugLog("Fichier non lisible: " . $filePath);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fichier non accessible en lecture']);
    exit;
}

if (!is_writable($filePath)) {
    debugLog("Fichier non modifiable: " . $filePath);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fichier non accessible en écriture']);
    exit;
}

try {
    debugLog("Tentative de suppression du fichier: " . $filePath);
    
    // Supprimer le fichier
    if (unlink($filePath)) {
        debugLog("Fichier supprimé avec succès");
        
        // Mettre à jour le fichier demos.json
        $updateResult = updateDemosList($filename);
        if ($updateResult) {
            debugLog("Liste demos.json mise à jour");
        } else {
            debugLog("Erreur lors de la mise à jour de demos.json");
        }
        
        echo json_encode(['success' => true, 'message' => 'Démo supprimée avec succès']);
    } else {
        debugLog("Échec de la suppression du fichier");
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du fichier']);
    }
} catch (Exception $e) {
    debugLog("Exception lors de la suppression: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}

// Fonction pour mettre à jour la liste des démos
function updateDemosList($deletedFilename) {
    $demosFile = 'demospubliques/demos.json';
    
    debugLog("Mise à jour de la liste: " . $demosFile);
    
    if (!file_exists($demosFile)) {
        debugLog("Fichier demos.json n'existe pas");
        return false;
    }
    
    if (!is_readable($demosFile)) {
        debugLog("Fichier demos.json non lisible");
        return false;
    }
    
    if (!is_writable($demosFile)) {
        debugLog("Fichier demos.json non modifiable");
        return false;
    }
    
    try {
        // Lire la liste actuelle
        $content = file_get_contents($demosFile);
        if ($content === false) {
            debugLog("Impossible de lire demos.json");
            return false;
        }
        
        $demos = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            debugLog("Erreur JSON dans demos.json: " . json_last_error_msg());
            return false;
        }
        
        if (!is_array($demos)) {
            debugLog("demos.json ne contient pas un tableau valide");
            $demos = [];
        }
        
        debugLog("Démos avant suppression: " . count($demos));
        
        // Supprimer la démo de la liste
        $demos = array_filter($demos, function($demo) use ($deletedFilename) {
            return $demo['filename'] !== $deletedFilename;
        });
        
        // Réindexer le tableau
        $demos = array_values($demos);
        
        debugLog("Démos après suppression: " . count($demos));
        
        // Sauvegarder la liste mise à jour
        $result = file_put_contents($demosFile, json_encode($demos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        if ($result === false) {
            debugLog("Impossible d'écrire dans demos.json");
            return false;
        }
        
        debugLog("demos.json mis à jour avec succès");
        return true;
        
    } catch (Exception $e) {
        debugLog("Exception lors de la mise à jour: " . $e->getMessage());
        return false;
    }
}
?>
