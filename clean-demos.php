<?php
// clean-demos.php
// Script pour nettoyer demos.json et ne garder que les fichiers existants

header('Content-Type: application/json');

echo "🧹 Nettoyage du fichier demos.json...\n\n";

// Lire le fichier demos.json
$demosFile = 'demospubliques/demos.json';
if (!file_exists($demosFile)) {
    echo "❌ Fichier demos.json non trouvé\n";
    exit;
}

$demos = json_decode(file_get_contents($demosFile), true);
if (!$demos) {
    echo "❌ Erreur lors de la lecture de demos.json\n";
    exit;
}

echo "📚 Démos dans le fichier JSON: " . count($demos) . "\n";

// Vérifier quels fichiers existent réellement
$existingDemos = [];
$missingFiles = [];

foreach ($demos as $demo) {
    $filePath = 'demospubliques/' . $demo['filename'];
    if (file_exists($filePath)) {
        $existingDemos[] = $demo;
        echo "✅ " . $demo['filename'] . " - " . $demo['name'] . "\n";
    } else {
        $missingFiles[] = $demo['filename'];
        echo "❌ " . $demo['filename'] . " - " . $demo['name'] . " (MANQUANT)\n";
    }
}

echo "\n📊 Résumé:\n";
echo "- Fichiers existants: " . count($existingDemos) . "\n";
echo "- Fichiers manquants: " . count($missingFiles) . "\n";

if (count($missingFiles) > 0) {
    echo "\n🗑️ Fichiers manquants:\n";
    foreach ($missingFiles as $file) {
        echo "  - " . $file . "\n";
    }
}

// Sauvegarder le fichier nettoyé
$backupFile = 'demospubliques/demos_backup_' . date('Y-m-d_H-i-s') . '.json';
copy($demosFile, $backupFile);
echo "\n💾 Sauvegarde créée: " . basename($backupFile) . "\n";

// Écrire le nouveau fichier
$result = file_put_contents($demosFile, json_encode($existingDemos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($result !== false) {
    echo "✅ Fichier demos.json nettoyé avec succès!\n";
    echo "📝 " . count($existingDemos) . " démos conservées\n";
} else {
    echo "❌ Erreur lors de l'écriture du fichier\n";
}

echo "\n🎯 Démos conservées:\n";
foreach ($existingDemos as $demo) {
    echo "- " . $demo['name'] . " (" . $demo['filename'] . ")\n";
}
?>

