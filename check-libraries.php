<?php
/**
 * Script de vérification des bibliothèques
 * Affiche l'état des dossiers et fichiers
 */

// Configuration
$publicDemosDir = 'demospubliques/';
$libraries = ['prayers', 'bhajans', 'brouillon'];

echo "🔍 Vérification des bibliothèques...\n\n";

foreach ($libraries as $library) {
    $libraryDir = $publicDemosDir . $library . '/';
    $indexFile = $libraryDir . 'index.json';
    
    echo "📁 Bibliothèque: $library\n";
    
    // Vérifier si le dossier existe
    if (!is_dir($libraryDir)) {
        echo "   ❌ Dossier non trouvé\n";
        continue;
    }
    
    // Lister les fichiers JSON
    $files = glob($libraryDir . '*.json');
    $jsonFiles = array_filter($files, function($file) {
        return basename($file) !== 'index.json';
    });
    
    echo "   📄 Fichiers JSON: " . count($jsonFiles) . "\n";
    
    // Vérifier l'index.json
    if (file_exists($indexFile)) {
        $indexContent = file_get_contents($indexFile);
        $index = json_decode($indexContent, true);
        
        if ($index === null) {
            echo "   ❌ Index.json invalide (JSON corrompu)\n";
        } else {
            echo "   ✅ Index.json valide (" . count($index) . " entrées)\n";
            
            // Vérifier la cohérence
            $indexFilenames = [];
            if (is_array($index)) {
                foreach ($index as $entry) {
                    if (isset($entry['filename'])) {
                        $indexFilenames[] = $entry['filename'];
                    }
                }
            }
            
            $actualFiles = array_map('basename', $jsonFiles);
            $missingFiles = array_diff($indexFilenames, $actualFiles);
            $orphanFiles = array_diff($actualFiles, $indexFilenames);
            
            if (!empty($missingFiles)) {
                echo "   ⚠️ Fichiers manquants: " . implode(', ', $missingFiles) . "\n";
            }
            
            if (!empty($orphanFiles)) {
                echo "   ⚠️ Fichiers orphelins: " . implode(', ', $orphanFiles) . "\n";
            }
            
            if (empty($missingFiles) && empty($orphanFiles)) {
                echo "   ✅ Cohérence parfaite\n";
            }
        }
    } else {
        echo "   ❌ Index.json manquant\n";
    }
    
    echo "\n";
}

// Vérifier le demos.json principal
$mainDemosFile = $publicDemosDir . 'demos.json';
echo "📋 Demos.json principal:\n";

if (file_exists($mainDemosFile)) {
    $mainContent = file_get_contents($mainDemosFile);
    $mainDemos = json_decode($mainContent, true);
    
    if ($mainDemos === null) {
        echo "   ❌ Fichier invalide (JSON corrompu)\n";
    } else {
        echo "   ✅ Fichier valide (" . count($mainDemos) . " entrées)\n";
        
        // Compter par catégorie
        $categories = [];
        foreach ($mainDemos as $demo) {
            $cat = $demo['category'] ?? 'inconnue';
            $categories[$cat] = ($categories[$cat] ?? 0) + 1;
        }
        
        foreach ($categories as $cat => $count) {
            echo "      - $cat: $count démos\n";
        }
    }
} else {
    echo "   ❌ Fichier manquant\n";
}

echo "\n🎯 Recommandations:\n";
echo "1. Si des fichiers sont manquants ou orphelins, utilisez clean-libraries.php\n";
echo "2. Si les index.json sont corrompus, ils seront réécrits automatiquement\n";
echo "3. Vérifiez les permissions des dossiers si des erreurs persistent\n";
?>




