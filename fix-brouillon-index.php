<?php
/**
 * Script pour corriger l'index.json du brouillon
 */

$brouillonDir = 'demospubliques/brouillon/';
$indexFile = $brouillonDir . 'index.json';

echo "🔧 Correction de l'index.json du brouillon...\n\n";

// Lire l'index actuel
if (file_exists($indexFile)) {
    $indexContent = file_get_contents($indexFile);
    $index = json_decode($indexContent, true);
    
    echo "📄 Index actuel:\n";
    print_r($index);
    echo "\n";
    
    // Créer un nouvel index avec la structure correcte
    $newIndex = [
        "category" => "brouillon",
        "description" => "Zone de staging pour les démos avant validation",
        "created" => "2025-08-12T15:48:00Z",
        "demos" => []
    ];
    
    // Extraire les démos de l'ancienne structure
    foreach ($index as $key => $value) {
        if (is_numeric($key) && is_array($value)) {
            // C'est une démo
            $newIndex['demos'][] = $value;
        }
    }
    
    // Vérifier que les fichiers existent
    $validDemos = [];
    foreach ($newIndex['demos'] as $demo) {
        $filename = $demo['filename'] ?? '';
        $filepath = $brouillonDir . $filename;
        
        if (file_exists($filepath)) {
            $validDemos[] = $demo;
            echo "✅ Fichier trouvé: $filename\n";
        } else {
            echo "❌ Fichier manquant: $filename\n";
        }
    }
    
    $newIndex['demos'] = $validDemos;
    
    // Écrire le nouvel index
    if (file_put_contents($indexFile, json_encode($newIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        echo "\n✅ Index corrigé avec " . count($validDemos) . " démos valides\n";
        echo "📄 Nouvel index:\n";
        print_r($newIndex);
    } else {
        echo "\n❌ Erreur lors de l'écriture de l'index\n";
    }
    
} else {
    echo "❌ Fichier index.json non trouvé\n";
}

echo "\n🎉 Correction terminée !\n";
?>




