<?php
/**
 * Script de nettoyage des démos - Nettoyage sélectif et sécurisé
 * 
 * Ce script permet de :
 * 1. Voir l'état actuel de tous les répertoires
 * 2. Supprimer sélectivement les démos
 * 3. Créer des backups avant suppression
 */

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🧹 SCRIPT DE NETTOYAGE DES DÉMOS\n";
echo str_repeat("=", 50) . "\n\n";

// Chemins
$baseDir = __DIR__;
$aClasserDir = $baseDir . '/a-classer/';
$publicDemosDir = $baseDir . '/demospubliques/';

// Fonction pour lister les fichiers dans un répertoire
function listFiles($dir, $pattern = '*.json') {
    $files = [];
    if (is_dir($dir)) {
        $items = glob($dir . $pattern);
        foreach ($items as $item) {
            if (is_file($item)) {
                $files[] = basename($item);
            }
        }
    }
    return $files;
}

// Fonction pour créer un backup
function createBackup($sourceDir, $backupName) {
    $backupDir = $sourceDir . 'backup_' . date('Y-m-d_H-i-s') . '/';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $files = listFiles($sourceDir);
    $backedUp = 0;
    
    foreach ($files as $file) {
        if (copy($sourceDir . $file, $backupDir . $file)) {
            $backedUp++;
        }
    }
    
    return $backedUp;
}

// État actuel
echo "📋 ÉTAT ACTUEL DES RÉPERTOIRES :\n";
echo str_repeat("-", 30) . "\n";

// A Classer
echo "\n📁 A Classer :\n";
$aClasserFiles = listFiles($aClasserDir);
echo "  - " . count($aClasserFiles) . " fichiers JSON\n";
foreach ($aClasserFiles as $file) {
    echo "    • $file\n";
}

// Prayers
echo "\n📁 Prayers :\n";
$prayersFiles = listFiles($publicDemosDir . 'prayers/');
echo "  - " . count($prayersFiles) . " fichiers JSON\n";
foreach ($prayersFiles as $file) {
    echo "    • $file\n";
}

// Bhajans
echo "\n📁 Bhajans :\n";
$bhajansFiles = listFiles($publicDemosDir . 'bhajans/');
echo "  - " . count($bhajansFiles) . " fichiers JSON\n";
foreach ($bhajansFiles as $file) {
    echo "    • $file\n";
}

// Test
echo "\n📁 Test :\n";
$testFiles = listFiles($publicDemosDir . 'test/');
echo "  - " . count($testFiles) . " fichiers JSON\n";
foreach ($testFiles as $file) {
    echo "    • $file\n";
}

// Demos.json principal
echo "\n📁 Demos.json principal :\n";
$mainDemosFile = $publicDemosDir . 'demos.json';
if (file_exists($mainDemosFile)) {
    $mainDemos = json_decode(file_get_contents($mainDemosFile), true);
    echo "  - " . count($mainDemos) . " entrées\n";
} else {
    echo "  - Fichier inexistant\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 OPTIONS DE NETTOYAGE :\n";
echo str_repeat("=", 50) . "\n";
echo "1. Nettoyer A Classer (garder seulement index.json)\n";
echo "2. Nettoyer toutes les bibliothèques\n";
echo "3. Nettoyer tout sauf les backups\n";
echo "4. Vider demos.json principal\n";
echo "5. Créer un backup complet avant nettoyage\n";
echo "6. Quitter\n\n";

echo "💡 RECOMMANDATION :\n";
echo "- Gardez les démos dans les bibliothèques (elles fonctionnent)\n";
echo "- Vous pouvez nettoyer A Classer si vous voulez repartir à zéro\n";
echo "- Créez toujours un backup avant de supprimer\n\n";

echo "⚠️  ATTENTION :\n";
echo "- Toute suppression est définitive\n";
echo "- Créez un backup avant de nettoyer\n";
echo "- Les démos dans les bibliothèques sont fonctionnelles\n\n";

echo "✅ SCRIPT TERMINÉ - Aucune action automatique effectuée\n";
echo "Pour nettoyer, utilisez l'interface web ou supprimez manuellement les fichiers\n";
?>
