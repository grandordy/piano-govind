<?php
/**
 * Script pour implémenter un système de noms dual
 * - nom technique : pour la gestion système (unique, stable)
 * - nom d'affichage : pour l'utilisateur (lisible, modifiable)
 */

echo "🎯 Implémentation du système de noms dual...\n";

function generateTechnicalName($originalName) {
    // Générer un nom technique unique basé sur le timestamp et un hash
    $timestamp = time();
    $hash = substr(md5($originalName . $timestamp), 0, 8);
    return "demo_{$timestamp}_{$hash}";
}

function extractDisplayName($filename) {
    // Extraire un nom d'affichage lisible du filename
    $filename = str_replace('.json', '', $filename);
    
    // Supprimer le préfixe "demo_"
    $filename = preg_replace('/^demo_/', '', $filename);
    
    // Extraire la partie descriptive (avant le premier timestamp)
    $parts = explode('_', $filename);
    $descriptiveParts = [];
    
    foreach ($parts as $part) {
        // Si c'est un timestamp (10+ chiffres) ou un ID hex (8+ caractères), arrêter
        if (is_numeric($part) && strlen($part) >= 10) {
            break;
        }
        if (strlen($part) >= 8 && ctype_xdigit($part)) {
            break;
        }
        $descriptiveParts[] = $part;
    }
    
    $displayName = implode('_', $descriptiveParts);
    
    // Nettoyer et formater
    $displayName = str_replace('_', ' ', $displayName);
    $displayName = ucwords($displayName);
    $displayName = trim($displayName);
    
    // Si le nom est vide ou trop court, utiliser un nom par défaut
    if (strlen($displayName) < 3) {
        $displayName = 'Démo ' . substr($filename, 0, 10);
    }
    
    return $displayName;
}

function updateLibraryWithDualNames($libraryPath) {
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
    if (!$index || !is_array($index)) {
        echo "❌ Format d'index invalide dans: $libraryPath\n";
        return;
    }
    
    $updated = false;
    
    foreach ($index as &$demo) {
        $filename = $demo['filename'] ?? '';
        $currentName = $demo['name'] ?? '';
        
        // Générer un nom technique unique si nécessaire
        if (empty($demo['technicalName'])) {
            $technicalName = generateTechnicalName($currentName ?: $filename);
            $demo['technicalName'] = $technicalName;
            echo "  🔧 Nom technique généré: {$technicalName}\n";
            $updated = true;
        }
        
        // Extraire ou améliorer le nom d'affichage
        $displayName = extractDisplayName($filename);
        
        // Si le nom actuel est technique, le remplacer par le nom d'affichage
        if (empty($currentName) || 
            strpos($currentName, 'demo_') === 0 || 
            preg_match('/^\d+$/', $currentName) ||
            strlen($currentName) < 3 ||
            strpos($currentName, '_') !== false && preg_match('/\d{10,}/', $currentName)) {
            
            if ($displayName !== $currentName) {
                echo "  📝 {$currentName} → {$displayName}\n";
                $demo['name'] = $displayName;
                $demo['displayName'] = $displayName;
                $updated = true;
            }
        } else {
            // Le nom actuel est déjà lisible, le conserver
            $demo['displayName'] = $currentName;
        }
        
        // S'assurer que le filename n'a pas de double extension
        if (strpos($filename, '.json.json') !== false) {
            $cleanFilename = str_replace('.json.json', '.json', $filename);
            echo "  🔧 Correction extension: {$filename} → {$cleanFilename}\n";
            $demo['filename'] = $cleanFilename;
            $updated = true;
        }
        
        // Ajouter des métadonnées utiles
        if (empty($demo['added'])) {
            $demo['added'] = date('c');
        }
        
        if (empty($demo['lastModified'])) {
            $demo['lastModified'] = date('c');
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
    updateLibraryWithDualNames($library);
}

echo "\n🎉 Système de noms dual implémenté !\n";
echo "📋 Structure des données :\n";
echo "  - technicalName : nom unique pour le système\n";
echo "  - displayName : nom lisible pour l'utilisateur\n";
echo "  - name : alias vers displayName (compatibilité)\n";
echo "  - filename : nom du fichier physique\n";
?>
