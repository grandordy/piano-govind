<?php
/**
 * Diagnostic Piano Solo V1.2
 * Vérification de l'état des fichiers et de la configuration
 */

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Diagnostic Piano Solo V1.2</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #8b9dc3 0%, #667eea 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .status-section {
            margin: 20px 0;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid;
        }
        .status-ok {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .status-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .status-error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .status-info {
            background: #d1ecf1;
            border-color: #17a2b8;
            color: #0c5460;
        }
        .file-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .file-item {
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .file-item:last-child {
            border-bottom: none;
        }
        .file-exists { color: #28a745; }
        .file-missing { color: #dc3545; }
        .back-link {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Diagnostic Piano Solo V1.2</h1>";

// Vérifier les fichiers principaux
echo "<div class='status-section status-info'>
    <h3>📄 Fichiers Principaux</h3>";

$mainFiles = [
    'index.php' => 'Interface Unifiée (Public + Admin)',
    'admin.php' => 'Page Admin (Configuration HELP/VIDEO)',
    'includes/piano-core.php' => 'Module Piano Principal',
    'includes/piano-demo-manager.php' => 'Gestionnaire de Démos',
    'includes/help-video-module.js' => 'Module HELP/VIDEO',
    'admin-config.json' => 'Configuration Admin',
    'demospubliques/brouillon/index.json' => 'Index Brouillon',
    'demospubliques/prayers/index.json' => 'Index Prayers',
    'demospubliques/bhajans/index.json' => 'Index Bhajans'
];

foreach ($mainFiles as $file => $description) {
    $exists = file_exists($file);
    $status = $exists ? 'file-exists' : 'file-missing';
    $icon = $exists ? '✅' : '❌';
    echo "<div class='file-item'>
        <span class='$status'>$icon $file</span> - $description
    </div>";
}

echo "</div>";

// Explication sur index-admin.php
echo "<div class='status-section status-warning'>
    <h3>⚠️ À propos de index-admin.php</h3>
    <p><strong>Le fichier index-admin.php a été supprimé intentionnellement lors de la migration vers l'interface unifiée V1.2.</strong></p>
    <p>✅ <strong>Raison :</strong> L'interface a été fusionnée en un seul fichier <code>index.php</code> qui gère à la fois le mode public et le mode admin.</p>
    <p>✅ <strong>Avantages :</strong></p>
    <ul>
        <li>Une seule page à maintenir</li>
        <li>Transition fluide entre public et admin</li>
        <li>Code plus simple et cohérent</li>
        <li>Pas de duplication de code</li>
    </ul>
    <p>✅ <strong>Fonctionnement actuel :</strong></p>
    <ul>
        <li>Mode public par défaut</li>
        <li>3 clics sur le logo 🎹 + mot de passe pour accéder au mode admin</li>
        <li>Barre de liens admin en bas de page</li>
        <li>Lien 'Mode Public' pour ouvrir l'interface publique dans un nouvel onglet</li>
    </ul>
</div>";

// Vérifier les dossiers de démos
echo "<div class='status-section status-info'>
    <h3>📁 Dossiers de Démonstrations</h3>";

$demoDirs = [
    'demospubliques/brouillon',
    'demospubliques/prayers', 
    'demospubliques/bhajans',
    'videos'
];

foreach ($demoDirs as $dir) {
    $exists = is_dir($dir);
    $status = $exists ? 'file-exists' : 'file-missing';
    $icon = $exists ? '✅' : '❌';
    echo "<div class='file-item'>
        <span class='$status'>$icon $dir/</span>
    </div>";
}

echo "</div>";

// Vérifier les modules JavaScript
echo "<div class='status-section status-info'>
    <h3>🔧 Modules JavaScript</h3>";

$jsModules = [
    'includes/js/module-manager.js' => 'Gestionnaire de Modules',
    'includes/js/demos-loader.js' => 'Chargeur de Démos',
    'includes/js/menu-handlers.js' => 'Gestionnaires de Menu',
    'includes/help-video-module.js' => 'Module HELP/VIDEO'
];

foreach ($jsModules as $file => $description) {
    $exists = file_exists($file);
    $status = $exists ? 'file-exists' : 'file-missing';
    $icon = $exists ? '✅' : '❌';
    echo "<div class='file-item'>
        <span class='$status'>$icon $file</span> - $description
    </div>";
}

echo "</div>";

// Informations système
echo "<div class='status-section status-info'>
    <h3>💻 Informations Système</h3>
    <p><strong>Version PHP :</strong> " . phpversion() . "</p>
    <p><strong>Version Piano Solo :</strong> V1.2 (Interface Unifiée)</p>
    <p><strong>Date de diagnostic :</strong> " . date('d/m/Y H:i:s') . "</p>
    <p><strong>Dossier de travail :</strong> " . getcwd() . "</p>
</div>";

// Instructions
echo "<div class='status-section status-ok'>
    <h3>🎯 Instructions d'Utilisation</h3>
    <p><strong>Mode Public :</strong> Accès direct à <code>index.php</code></p>
    <p><strong>Mode Admin :</strong> 3 clics sur le logo 🎹 + mot de passe 'murali'</p>
    <p><strong>Page Admin :</strong> <code>admin.php</code> pour configurer HELP/VIDEO</p>
    <p><strong>Barre de liens :</strong> Visible en bas de page en mode admin</p>
</div>";

echo "<a href='index.php' class='back-link'>🔙 Retour à l'Interface Principale</a>
    </div>
</body>
</html>";
?>




