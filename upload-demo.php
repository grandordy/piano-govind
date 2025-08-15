<?php
// piano-solo/upload-demo.php
// Script d'upload pour les démos

header('Content-Type: application/json');

// Configuration
$uploadDir = 'demospubliques/';
$maxFileSize = 5 * 1024 * 1024; // 5 MB

// Créer le dossier si nécessaire
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Traitement de l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['demo_file'])) {
        $file = $_FILES['demo_file'];
        
        // Vérifications
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Erreur upload']);
            exit;
        }
        
        if ($file['size'] > $maxFileSize) {
            http_response_code(400);
            echo json_encode(['error' => 'Fichier trop volumineux']);
            exit;
        }
        
        // Lire les métadonnées
        $metadata = json_decode($_POST['metadata'], true);
        
        // Créer un nom de fichier basé sur le nom de la démo
        $safeName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $metadata['name']);
        $safeName = str_replace(' ', '_', $safeName);
        $filename = $safeName . '_' . time() . '.json';
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Mettre à jour demos.json
            $demosFile = $uploadDir . 'demos.json';
            $demos = [];
            
            if (file_exists($demosFile)) {
                $demos = json_decode(file_get_contents($demosFile), true) ?: [];
            }
            
            // Lire le contenu du fichier uploadé
            $content = json_decode(file_get_contents($filepath), true);
            
            // Ajouter à la liste
            $demos[] = [
                'filename' => $filename,
                'name' => $content['name'],
                'category' => $metadata['category'],
                'description' => $metadata['description'] ?? '',
                'duration' => $content['duration'],
                'noteCount' => $content['noteCount'],
                'uploadedAt' => date('c')
            ];
            
            // Sauvegarder
            file_put_contents($demosFile, json_encode($demos, JSON_PRETTY_PRINT));
            
            echo json_encode(['success' => true, 'filename' => $filename]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Aucun fichier']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
?>