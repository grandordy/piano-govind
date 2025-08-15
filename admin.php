<?php
/**
 * Page Admin - Gestion des informations HELP et vidéo
 * Piano Solo V1.2
 */

// Configuration de base
$configFile = 'admin-config.json';
$defaultConfig = [
    'help' => [
        'title' => 'Aide Piano Solo',
        'content' => 'Bienvenue dans l\'aide de Piano Solo. Utilisez les touches du clavier pour jouer.',
        'last_updated' => date('Y-m-d H:i:s')
    ],
    'video' => [
        'title' => 'Tutoriel Piano Solo',
        'filename' => 'tutorial.mp4',
        'description' => 'Vidéo de présentation et tutoriel d\'utilisation',
        'enabled' => true
    ]
];

// Charger ou créer la configuration
function loadConfig() {
    global $configFile, $defaultConfig;
    
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
        return array_merge($defaultConfig, $config);
    } else {
        // Créer le fichier de configuration par défaut
        file_put_contents($configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $defaultConfig;
    }
}

// Sauvegarder la configuration
function saveConfig($config) {
    global $configFile;
    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Traitement du formulaire
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config = loadConfig();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_help':
                $config['help']['title'] = $_POST['help_title'] ?? '';
                $config['help']['content'] = $_POST['help_content'] ?? '';
                $config['help']['last_updated'] = date('Y-m-d H:i:s');
                saveConfig($config);
                $message = 'Informations HELP sauvegardées avec succès !';
                $messageType = 'success';
                break;
                
            case 'save_video':
                $config['video']['title'] = $_POST['video_title'] ?? '';
                $config['video']['description'] = $_POST['video_description'] ?? '';
                $config['video']['enabled'] = isset($_POST['video_enabled']);
                
                // Gestion du upload de fichier vidéo
                if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'videos/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileInfo = pathinfo($_FILES['video_file']['name']);
                    $extension = strtolower($fileInfo['extension']);
                    
                    // Vérifier que c'est bien une vidéo MP4
                    if ($extension === 'mp4') {
                        $newFilename = 'tutorial_' . time() . '.mp4';
                        $uploadPath = $uploadDir . $newFilename;
                        
                        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadPath)) {
                            $config['video']['filename'] = $newFilename;
                            $message = 'Vidéo uploadée et configuration sauvegardée !';
                            $messageType = 'success';
                        } else {
                            $message = 'Erreur lors de l\'upload de la vidéo.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Seuls les fichiers MP4 sont acceptés.';
                        $messageType = 'error';
                    }
                } else {
                    saveConfig($config);
                    $message = 'Configuration vidéo sauvegardée !';
                    $messageType = 'success';
                }
                break;
        }
    }
}

$config = loadConfig();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Piano Solo V1.2</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .admin-header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .admin-header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .admin-content {
            padding: 30px;
        }
        
        .admin-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        
        .admin-section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .video-preview {
            background: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            text-align: center;
        }
        
        .video-preview video {
            max-width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .file-upload {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9ff;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover {
            background: #e8f0ff;
            border-color: #5a6fd8;
        }
        
        .file-upload input[type="file"] {
            display: none;
        }
        
        .file-upload label {
            cursor: pointer;
            color: #667eea;
            font-weight: 600;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .help-preview {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }
        
        .help-preview h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .admin-header {
                padding: 20px;
            }
            
            .admin-header h1 {
                font-size: 2em;
            }
            
            .admin-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🎹 Admin Piano Solo</h1>
            <p>Gestion des informations HELP et vidéo tutoriel</p>
        </div>
        
        <div class="admin-content">
            <a href="index-unified.php" class="back-link">← Retour à l'interface principale</a>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Section HELP -->
            <div class="admin-section">
                <h2>📚 Configuration HELP</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="save_help">
                    
                    <div class="form-group">
                        <label for="help_title">Titre de l'aide :</label>
                        <input type="text" id="help_title" name="help_title" class="form-control" 
                               value="<?php echo htmlspecialchars($config['help']['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="help_content">Contenu de l'aide :</label>
                        <textarea id="help_content" name="help_content" class="form-control" required><?php echo htmlspecialchars($config['help']['content']); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success">💾 Sauvegarder HELP</button>
                </form>
                
                <!-- Aperçu HELP -->
                <div class="help-preview">
                    <h3>Aperçu :</h3>
                    <h4><?php echo htmlspecialchars($config['help']['title']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($config['help']['content'])); ?></p>
                    <small>Dernière mise à jour : <?php echo $config['help']['last_updated']; ?></small>
                </div>
            </div>
            
            <!-- Section Vidéo -->
            <div class="admin-section">
                <h2>🎥 Configuration Vidéo</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_video">
                    
                    <div class="form-group">
                        <label for="video_title">Titre de la vidéo :</label>
                        <input type="text" id="video_title" name="video_title" class="form-control" 
                               value="<?php echo htmlspecialchars($config['video']['title']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_description">Description :</label>
                        <textarea id="video_description" name="video_description" class="form-control"><?php echo htmlspecialchars($config['video']['description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="video_enabled" name="video_enabled" 
                                   <?php echo $config['video']['enabled'] ? 'checked' : ''; ?>>
                            <label for="video_enabled">Activer la vidéo</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Fichier vidéo (MP4) :</label>
                        <div class="file-upload">
                            <input type="file" id="video_file" name="video_file" accept=".mp4">
                            <label for="video_file">
                                📁 Cliquez pour sélectionner un fichier MP4<br>
                                <small>ou glissez-déposez le fichier ici</small>
                            </label>
                        </div>
                        <?php if ($config['video']['filename'] && file_exists('videos/' . $config['video']['filename'])): ?>
                            <p><strong>Fichier actuel :</strong> <?php echo htmlspecialchars($config['video']['filename']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-success">💾 Sauvegarder Vidéo</button>
                </form>
                
                <!-- Aperçu Vidéo -->
                <?php if ($config['video']['enabled'] && $config['video']['filename'] && file_exists('videos/' . $config['video']['filename'])): ?>
                    <div class="video-preview">
                        <h3>Aperçu de la vidéo :</h3>
                        <video controls width="100%" max-width="600">
                            <source src="videos/<?php echo htmlspecialchars($config['video']['filename']); ?>" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture de vidéos.
                        </video>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Section Intégration -->
            <div class="admin-section">
                <h2>🔗 Intégration</h2>
                <p>Pour intégrer le menu HELP et la vidéo dans votre application :</p>
                
                <h3>1. Menu HELP</h3>
                <p>Ajoutez ce code JavaScript pour afficher le menu HELP :</p>
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
// Charger les informations HELP
function loadHelpInfo() {
    fetch('admin-config.json')
        .then(response => response.json())
        .then(data => {
            showHelpPopup(data.help.title, data.help.content);
        })
        .catch(error => console.error('Erreur chargement HELP:', error));
}

// Afficher le popup HELP
function showHelpPopup(title, content) {
    const popup = document.createElement('div');
    popup.className = 'help-popup';
    popup.innerHTML = `
        &lt;div class="help-content"&gt;
            &lt;h3&gt;${title}&lt;/h3&gt;
            &lt;p&gt;${content}&lt;/p&gt;
            &lt;button onclick="this.closest('.help-popup').remove()"&gt;Fermer&lt;/button&gt;
        &lt;/div&gt;
    `;
    document.body.appendChild(popup);
}</pre>
                
                <h3>2. Vidéo Popup</h3>
                <p>Ajoutez ce code pour afficher la vidéo en popup :</p>
                <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
// Charger et afficher la vidéo
function loadVideoPopup() {
    fetch('admin-config.json')
        .then(response => response.json())
        .then(data => {
            if (data.video.enabled) {
                showVideoPopup(data.video.title, data.video.filename);
            }
        })
        .catch(error => console.error('Erreur chargement vidéo:', error));
}

// Afficher le popup vidéo
function showVideoPopup(title, filename) {
    const popup = document.createElement('div');
    popup.className = 'video-popup';
    popup.innerHTML = `
        &lt;div class="video-content"&gt;
            &lt;h3&gt;${title}&lt;/h3&gt;
            &lt;video controls width="100%"&gt;
                &lt;source src="videos/${filename}" type="video/mp4"&gt;
            &lt;/video&gt;
            &lt;button onclick="this.closest('.video-popup').remove()"&gt;Fermer&lt;/button&gt;
        &lt;/div&gt;
    `;
    document.body.appendChild(popup);
}</pre>
            </div>
        </div>
    </div>

    <script>
        // Amélioration de l'interface de upload
        const fileUpload = document.querySelector('.file-upload');
        const fileInput = document.getElementById('video_file');
        
        if (fileUpload && fileInput) {
            // Drag and drop
            fileUpload.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileUpload.style.background = '#e8f0ff';
                fileUpload.style.borderColor = '#5a6fd8';
            });
            
            fileUpload.addEventListener('dragleave', (e) => {
                e.preventDefault();
                fileUpload.style.background = '#f8f9ff';
                fileUpload.style.borderColor = '#667eea';
            });
            
            fileUpload.addEventListener('drop', (e) => {
                e.preventDefault();
                fileUpload.style.background = '#f8f9ff';
                fileUpload.style.borderColor = '#667eea';
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileName(files[0].name);
                }
            });
            
            // Changement de fichier
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    updateFileName(e.target.files[0].name);
                }
            });
            
            function updateFileName(name) {
                const label = fileUpload.querySelector('label');
                label.innerHTML = `📁 Fichier sélectionné : ${name}`;
            }
        }
        
        // Auto-sauvegarde de l'aperçu HELP
        const helpTitle = document.getElementById('help_title');
        const helpContent = document.getElementById('help_content');
        const helpPreview = document.querySelector('.help-preview');
        
        function updateHelpPreview() {
            const title = helpTitle.value;
            const content = helpContent.value;
            
            helpPreview.querySelector('h4').textContent = title;
            helpPreview.querySelector('p').innerHTML = content.replace(/\n/g, '<br>');
        }
        
        if (helpTitle && helpContent) {
            helpTitle.addEventListener('input', updateHelpPreview);
            helpContent.addEventListener('input', updateHelpPreview);
        }
    </script>
</body>
</html>
