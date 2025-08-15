<?php
/**
 * Script de nettoyage des bibliothèques
 * Vide les dossiers prayers et bhajans et réécrit les index.json
 */

// Configuration
$publicDemosDir = 'demospubliques/';
$libraries = ['prayers', 'bhajans'];

echo "🧹 Début du nettoyage des bibliothèques...\n\n";

foreach ($libraries as $library) {
    $libraryDir = $publicDemosDir . $library . '/';
    $indexFile = $libraryDir . 'index.json';
    
    echo "📁 Traitement de la bibliothèque: $library\n";
    
    // Vérifier si le dossier existe
    if (!is_dir($libraryDir)) {
        echo "   ❌ Dossier $library non trouvé\n";
        continue;
    }
    
    // Supprimer tous les fichiers JSON (sauf index.json)
    $files = glob($libraryDir . '*.json');
    $deletedCount = 0;
    
    foreach ($files as $file) {
        $filename = basename($file);
        if ($filename !== 'index.json') {
            if (unlink($file)) {
                echo "   🗑️ Supprimé: $filename\n";
                $deletedCount++;
            } else {
                echo "   ❌ Erreur lors de la suppression de $filename\n";
            }
        }
    }
    
    // Réécrire l'index.json vide
    $emptyIndex = [];
    if (file_put_contents($indexFile, json_encode($emptyIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        echo "   ✅ Index $library réécrit (vide)\n";
    } else {
        echo "   ❌ Erreur lors de l'écriture de l'index $library\n";
    }
    
    echo "   📊 $deletedCount fichiers supprimés\n\n";
}

// Nettoyer aussi le demos.json principal
$mainDemosFile = $publicDemosDir . 'demos.json';
if (file_exists($mainDemosFile)) {
    $mainDemos = json_decode(file_get_contents($mainDemosFile), true);
    
    // Supprimer les entrées des bibliothèques nettoyées
    $cleanedDemos = array_filter($mainDemos, function($demo) use ($libraries) {
        return !in_array($demo['category'], $libraries);
    });
    
    if (file_put_contents($mainDemosFile, json_encode($cleanedDemos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        echo "✅ Demos.json principal nettoyé\n";
        echo "📊 " . count($mainDemos) . " → " . count($cleanedDemos) . " entrées\n";
    } else {
        echo "❌ Erreur lors du nettoyage du demos.json principal\n";
    }
}

echo "\n🎉 Nettoyage terminé !\n";
echo "💡 Vous pouvez maintenant recharger la page admin pour voir les changements.\n";
?>




