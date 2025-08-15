<?php
/**
 * Script pour nettoyer et corriger les noms de démos existantes
 * Extrait des noms plus lisibles des filenames techniques
 * Corrige les problèmes de double extension et de noms alphanumériques
 */

echo "🧹 Nettoyage et correction des noms de démos...\n";

function extractReadableName($filename) {
    // Supprimer l'extension .json
    $filename = str_replace('.json', '', $filename);
    
    // Supprimer le préfixe "demo_"
    $filename = preg_replace('/^demo_/', '', $filename);
    
    // Extraire la partie descriptive (avant le premier timestamp)
    $parts = explode('_', $filename);
    $descriptiveParts = [];
    
    foreach ($parts as $part) {
        // Si c'est un timestamp (10+ chiffres) ou un ID hex (16 caractères), arrêter
        if (is_numeric($part) && strlen($part) >= 10) {
            break;
        }
        if (strlen($part) === 16 && ctype_xdigit($part)) {
            break;
        }
        $descriptiveParts[] = $part;
    }
    
    $readableName = implode('_', $descriptiveParts);
    
    // Nettoyer et formater
    $readableName = str_replace('_', ' ', $readableName);
    $readableName = ucwords($readableName);
    $readableName = trim($readableName);
    
    // Si le nom est vide ou trop court, utiliser un nom par défaut
    if (strlen($readableName) < 3) {
        $readableName = 'Démo ' . substr($filename, 0, 10);
    }
    
    return $readableName;
}

function cleanLibraryNames($libraryPath) {
    echo "\n📁 Traitement de: $libraryPath\n";
    
    if (!is_dir($libraryPath)) {
        echo "❌ Dossier non trouvé: $libraryPath\n";
        return;
    }
    
    $indexFile = $libraryPath . '/index.json';
    if (!file_exists($indexFile)) {
        echo "❌ Fichier index.json non trouvé dans: $libraryPath\n";
        return;
    }
    
    $index = json_decode(file_get_contents($indexFile), true);
    if (!$index || !isset($index['demos'])) {
        echo "❌ Format d'index invalide dans: $libraryPath\n";
        return;
    }
    
    $updated = false;
    
    foreach ($index['demos'] as &$demo) {
        $originalName = $demo['name'] ?? '';
        $filename = $demo['filename'] ?? '';
        
        // Si le nom actuel est vide ou trop technique, le corriger
        if (empty($originalName) || 
            strpos($originalName, 'demo_') === 0 || 
            preg_match('/^\d+$/', $originalName) ||
            strlen($originalName) < 3) {
            
            $newName = extractReadableName($filename);
            
            if ($newName !== $originalName) {
                echo "  🔄 {$originalName} → {$newName}\n";
                $demo['name'] = $newName;
                $demo['originalName'] = $newName;
                $updated = true;
            }
        }
        
        // S'assurer que le filename n'a pas de double extension
        if (strpos($filename, '.json.json') !== false) {
            $cleanFilename = str_replace('.json.json', '.json', $filename);
            echo "  🔧 Correction extension: {$filename} → {$cleanFilename}\n";
            $demo['filename'] = $cleanFilename;
            $updated = true;
        }
    }
    
    if ($updated) {
        // Sauvegarder l'index mis à jour
        file_put_contents($indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "✅ Index mis à jour pour: $libraryPath\n";
    } else {
        echo "ℹ️ Aucune modification nécessaire pour: $libraryPath\n";
    }
}

// Traiter toutes les bibliothèques
$libraries = [
    'demospubliques/brouillon',
    'demospubliques/prayers',
    'demospubliques/bhajans'
];

foreach ($libraries as $library) {
    cleanLibraryNames($library);
}

echo "\n🎉 Nettoyage et correction terminés !\n";
echo "📋 Vérifiez maintenant les noms dans l'interface admin.\n";
?>
