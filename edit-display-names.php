<?php
/**
 * Script pour éditer les noms d'affichage des démos
 * Permet à l'utilisateur de modifier les noms visibles sans toucher aux noms techniques
 */

// Configuration
$libraries = [
    'demospubliques/brouillon' => 'Brouillon',
    'demospubliques/prayers' => 'Prayers',
    'demospubliques/bhajans' => 'Bhajans'
];

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $library = $_POST['library'];
        $technicalName = $_POST['technical_name'];
        $newDisplayName = trim($_POST['display_name']);
        
        if (!empty($newDisplayName) && isset($libraries[$library])) {
            $indexFile = $library . '/index.json';
            
            if (file_exists($indexFile)) {
                $index = json_decode(file_get_contents($indexFile), true);
                
                foreach ($index as &$demo) {
                    if ($demo['technicalName'] === $technicalName) {
                        $oldName = $demo['displayName'] ?? $demo['name'] ?? 'Sans nom';
                        $demo['displayName'] = $newDisplayName;
                        $demo['name'] = $newDisplayName; // Compatibilité
                        $demo['lastModified'] = date('c');
                        
                        file_put_contents($indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        
                        echo json_encode([
                            'success' => true,
                            'message' => "Nom modifié : {$oldName} → {$newDisplayName}"
                        ]);
                        exit;
                    }
                }
            }
        }
        
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la modification'
        ]);
        exit;
    }
}

// Affichage de l'interface
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Édition des Noms d'Affichage - Piano Solo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .library-section {
            margin-bottom: 40px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
        }
        
        .library-title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        
        .demo-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .demo-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .demo-info {
            flex: 1;
            margin-right: 20px;
        }
        
        .demo-display-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .demo-technical-name {
            font-size: 12px;
            color: #6c757d;
            font-family: monospace;
        }
        
        .demo-filename {
            font-size: 11px;
            color: #adb5bd;
            font-family: monospace;
        }
        
        .edit-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .edit-input {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            min-width: 200px;
        }
        
        .edit-input:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #000;
        }
        
        .btn-edit:hover {
            background: #e0a800;
        }
        
        .btn-save {
            background: #28a745;
            color: white;
        }
        
        .btn-save:hover {
            background: #218838;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Retour à l'interface principale</a>
        
        <h1>✏️ Édition des Noms d'Affichage</h1>
        
        <div class="status success">
            <strong>💡 Système de noms dual :</strong> Vous pouvez modifier les noms d'affichage sans affecter la gestion technique des fichiers.
        </div>
        
        <div id="status-message"></div>
        
        <?php foreach ($libraries as $libraryPath => $libraryName): ?>
            <div class="library-section">
                <h2 class="library-title">📁 <?= htmlspecialchars($libraryName) ?></h2>
                
                <?php
                $indexFile = $libraryPath . '/index.json';
                if (file_exists($indexFile)) {
                    $index = json_decode(file_get_contents($indexFile), true);
                    
                    if (is_array($index) && !empty($index)) {
                        foreach ($index as $demo) {
                            $technicalName = $demo['technicalName'] ?? '';
                            $displayName = $demo['displayName'] ?? $demo['name'] ?? 'Sans nom';
                            $filename = $demo['filename'] ?? '';
                            ?>
                            <div class="demo-item">
                                <div class="demo-info">
                                    <div class="demo-display-name"><?= htmlspecialchars($displayName) ?></div>
                                    <div class="demo-technical-name">ID: <?= htmlspecialchars($technicalName) ?></div>
                                    <div class="demo-filename">Fichier: <?= htmlspecialchars($filename) ?></div>
                                </div>
                                
                                <div class="edit-form">
                                    <input type="text" 
                                           class="edit-input" 
                                           value="<?= htmlspecialchars($displayName) ?>" 
                                           placeholder="Nouveau nom d'affichage"
                                           data-original="<?= htmlspecialchars($displayName) ?>"
                                           data-technical="<?= htmlspecialchars($technicalName) ?>"
                                           data-library="<?= htmlspecialchars($libraryPath) ?>">
                                    <button class="btn btn-edit" onclick="editName(this)">✏️ Modifier</button>
                                    <button class="btn btn-save" onclick="saveName(this)" style="display: none;">💾 Sauvegarder</button>
                                    <button class="btn btn-cancel" onclick="cancelEdit(this)" style="display: none;">❌ Annuler</button>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p>Aucune démo dans cette bibliothèque.</p>';
                    }
                } else {
                    echo '<p>Bibliothèque non trouvée.</p>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <script>
        function editName(button) {
            const form = button.parentElement;
            const input = form.querySelector('.edit-input');
            const saveBtn = form.querySelector('.btn-save');
            const cancelBtn = form.querySelector('.btn-cancel');
            
            input.disabled = false;
            input.focus();
            input.select();
            
            button.style.display = 'none';
            saveBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        }
        
        function saveName(button) {
            const form = button.parentElement;
            const input = form.querySelector('.edit-input');
            const editBtn = form.querySelector('.btn-edit');
            const cancelBtn = form.querySelector('.btn-cancel');
            
            const newName = input.value.trim();
            const originalName = input.dataset.original;
            const technicalName = input.dataset.technical;
            const library = input.dataset.library;
            
            if (newName === originalName) {
                cancelEdit(button);
                return;
            }
            
            if (newName.length < 2) {
                alert('Le nom doit contenir au moins 2 caractères.');
                return;
            }
            
            // Envoyer la modification au serveur
            const formData = new FormData();
            formData.append('action', 'update');
            formData.append('library', library);
            formData.append('technical_name', technicalName);
            formData.append('display_name', newName);
            
            fetch('edit-display-names.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus(data.message, 'success');
                    input.dataset.original = newName;
                    input.disabled = true;
                    button.style.display = 'none';
                    cancelBtn.style.display = 'none';
                    editBtn.style.display = 'inline-block';
                    
                    // Mettre à jour l'affichage
                    const demoInfo = form.parentElement.querySelector('.demo-display-name');
                    demoInfo.textContent = newName;
                } else {
                    showStatus(data.message, 'error');
                }
            })
            .catch(error => {
                showStatus('Erreur lors de la sauvegarde', 'error');
                console.error('Erreur:', error);
            });
        }
        
        function cancelEdit(button) {
            const form = button.parentElement;
            const input = form.querySelector('.edit-input');
            const editBtn = form.querySelector('.btn-edit');
            const saveBtn = form.querySelector('.btn-save');
            
            input.value = input.dataset.original;
            input.disabled = true;
            
            button.style.display = 'none';
            saveBtn.style.display = 'none';
            editBtn.style.display = 'inline-block';
        }
        
        function showStatus(message, type) {
            const statusDiv = document.getElementById('status-message');
            statusDiv.innerHTML = `<div class="status ${type}">${message}</div>`;
            
            setTimeout(() => {
                statusDiv.innerHTML = '';
            }, 3000);
        }
    </script>
</body>
</html>
