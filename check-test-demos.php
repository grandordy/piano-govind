<?php
// check-test-demos.php
// Script pour vérifier les démos de la catégorie "test"

header('Content-Type: text/plain; charset=utf-8');

echo "🔍 Vérification des démos de la catégorie 'test'...\n\n";

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

// Filtrer les démos de la catégorie "test"
$testDemos = array_filter($demos, function($demo) {
    return $demo['category'] === 'test';
});

echo "📚 Démos de la catégorie 'test' trouvées: " . count($testDemos) . "\n\n";

$existingTestDemos = [];
$missingTestDemos = [];

foreach ($testDemos as $demo) {
    $filePath = 'demospubliques/' . $demo['filename'];
    if (file_exists($filePath)) {
        $existingTestDemos[] = $demo;
        echo "✅ " . $demo['filename'] . " - " . $demo['name'] . "\n";
    } else {
        $missingTestDemos[] = $demo;
        echo "❌ " . $demo['filename'] . " - " . $demo['name'] . " (MANQUANT)\n";
    }
}

echo "\n📊 Résumé pour la catégorie 'test':\n";
echo "- Fichiers existants: " . count($existingTestDemos) . "\n";
echo "- Fichiers manquants: " . count($missingTestDemos) . "\n";

if (count($missingTestDemos) > 0) {
    echo "\n🗑️ Fichiers de test manquants:\n";
    foreach ($missingTestDemos as $demo) {
        echo "  - " . $demo['filename'] . " (" . $demo['name'] . ")\n";
    }
}

if (count($existingTestDemos) > 0) {
    echo "\n✅ Fichiers de test existants:\n";
    foreach ($existingTestDemos as $demo) {
        echo "  - " . $demo['filename'] . " (" . $demo['name'] . ")\n";
    }
}

echo "\n🎯 Recommandation:\n";
if (count($missingTestDemos) > 0) {
    echo "Supprimez les références aux fichiers manquants avec clean-demos.php\n";
} else {
    echo "Toutes les démos de test existent !\n";
}
?>

