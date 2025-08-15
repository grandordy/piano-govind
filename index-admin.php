<!-- piano-solo/test-complet.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piano Harmonium Complet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #8b9dc3 0%, #667eea 100%);
            min-height: 100vh;
            padding: 5px;
            margin: 0;
            overflow-x: hidden;
        }

        .container {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
            padding: 0px 30px 30px 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            overflow: visible;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
		
		        h4 {
            text-align: center;
            color: #96bcc5;
            margin-bottom: 5px;
        }
        
        /* Indicateur de synchronisation */
        .sync-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .sync-indicator.playing {
            background: rgba(220, 53, 69, 0.9);
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .btn-play-admin.playing,
        .btn-play.playing,
        .demo-play-btn.playing,
        .menu-play-btn.playing,
        .keyboard-play-btn.playing,
        .play-btn.playing,
        #playBtn.playing {
            background: #dc3545 !important;
            animation: pulse 1s infinite;
        }
        
        /* Style pour les notes actives en pause */
        .key.active-pause {
            background-color: #ffeb3b !important;
            box-shadow: 0 0 10px #ffeb3b !important;
            animation: none !important;
        }
        
        /* Bouton supprimé - plus nécessaire */
        

    </style>
</head>
<body>
    <!-- Indicateur de synchronisation -->
    <div id="syncIndicator" class="sync-indicator">
        🔄 Synchronisé - Aucune lecture
    </div>

    <!-- Bouton supprimé - le gestionnaire est maintenant toujours visible -->

    <div class="container">
        <?php include 'includes/piano-notation.php'; ?>
        <?php include 'includes/piano-core.php'; ?>
        <?php include 'includes/piano-harmonium.php'; ?>
        <?php include 'includes/piano-recorder.php'; ?>
        <?php include 'includes/piano-save-upload.php'; ?>
        <?php include 'includes/piano-demo-manager.php'; ?>
        <?php include 'includes/piano-midi.php'; ?>
    </div>
    

    
    <!-- Gestionnaire de modules -->
    <script src="includes/js/module-manager.js"></script>
    <!-- Chargeur de démos -->
    <script src="includes/js/demos-loader.js"></script>
    <!-- JavaScript centralisé -->
    <script src="includes/js/menu-handlers.js"></script>
    
    <!-- Initialisation des modules -->
    <script>
        // Variables de synchronisation globales
        let currentPlayingButton = null;
        let currentPlayingDemo = null;
        let playbackTimer = null;
        let playbackProgress = 0;
        
        // Fonction pour arrêter toutes les lectures
        function stopAllPlayback() {
            console.log('🛑 Arrêt de toutes les lectures...');
            
            // Arrêter le bouton actuellement en lecture
            if (currentPlayingButton) {
                currentPlayingButton.classList.remove('playing');
                currentPlayingButton.innerHTML = '▶️';
                currentPlayingButton = null;
            }
            
            // Arrêter TOUS les boutons de lecture qui pourraient être actifs
            document.querySelectorAll('.btn-play.playing, .btn-play-admin.playing, .demo-play-btn.playing, .menu-play-btn.playing, .keyboard-play-btn.playing, .play-btn.playing, #playBtn.playing').forEach(btn => {
                btn.classList.remove('playing');
                btn.innerHTML = '▶️';
            });
            
            // Arrêter le timer
            if (playbackTimer) {
                clearInterval(playbackTimer);
                playbackTimer = null;
            }
            
            currentPlayingDemo = null;
            playbackProgress = 0;
            
            // Mettre à jour l'indicateur visuel
            updateSyncIndicator();
            
            console.log('🛑 Toutes les lectures arrêtées');
        }
        
        // Fonction pour mettre à jour l'indicateur de synchronisation
        function updateSyncIndicator() {
            const indicator = document.getElementById('syncIndicator');
            if (indicator) {
                if (currentPlayingDemo) {
                    indicator.textContent = `🎵 Lecture: ${currentPlayingDemo}`;
                    indicator.classList.add('playing');
                } else {
                    indicator.textContent = '🔄 Synchronisé - Aucune lecture';
                    indicator.classList.remove('playing');
                }
            }
        }
        
        // Fonction pour démarrer une lecture avec synchronisation
        function startSynchronizedPlayback(library, demoId, button) {
            console.log(`🎵 Démarrage lecture synchronisée: ${demoId} (${library})`);
            
            // Arrêter toute lecture en cours
            stopAllPlayback();
            
            // Démarrer la nouvelle lecture
            currentPlayingButton = button;
            currentPlayingDemo = `${demoId} (${library})`;
            
            button.classList.add('playing');
            button.innerHTML = '⏸️';
            
            // Mettre à jour l'indicateur visuel
            updateSyncIndicator();
            
            // Ici on pourrait appeler le vrai système de lecture
            // Pour l'instant, on simule juste la synchronisation
            console.log(`✅ Lecture synchronisée démarrée: ${currentPlayingDemo}`);
        }
        
        // Initialiser les modules après le chargement de la page
        window.addEventListener('load', () => {
            console.log('🎹 Initialisation des modules Piano Solo Admin...');
            
            // Initialiser le gestionnaire de modules
            if (window.MODULE_MANAGER) {
                MODULE_MANAGER.initialize();
            }
            
            // Initialiser les gestionnaires de menus
            if (window.MENU_HANDLERS) {
                MENU_HANDLERS.init();
            }
            
            // Initialiser le chargeur de démos
            if (window.DEMOS_LOADER) {
                DEMOS_LOADER.init();
            }
            
            // Initialiser le gestionnaire de démos
            if (window.DEMO_MANAGER) {
                DEMO_MANAGER.init();
                // Forcer le rechargement des tables après un délai
                setTimeout(() => {
                    console.log('🔄 Forçage du rechargement des tables...');
                    DEMO_MANAGER.loadAllTables();
                }, 2000);
                
                // Forcer un second rechargement pour nettoyer les fantômes
                setTimeout(() => {
                    console.log('🧹 Nettoyage des données fantômes...');
                    DEMO_MANAGER.loadAllTables();
                }, 4000);
            }
            
            // Initialiser le module RECORDER (important pour la lecture des démos)
            if (window.RECORDER_MODULE) {
                console.log('🎵 Initialisation du module RECORDER...');
                window.RECORDER_MODULE.init();
            } else {
                console.error('❌ Module RECORDER non trouvé !');
            }
            
            // Ajouter la synchronisation aux boutons de lecture existants
            setTimeout(() => {
                addSynchronizationToExistingButtons();
            }, 1000);
            
            console.log('🔄 Système de synchronisation initialisé');
        });
        
        // Fonction pour ajouter la synchronisation aux boutons existants
        function addSynchronizationToExistingButtons() {
            // Sélectionner TOUS les types de boutons de lecture
            const playButtons = document.querySelectorAll(
                '.btn-play, .btn-play-admin, .demo-play-btn, .menu-play-btn, .keyboard-play-btn, .play-btn, #playBtn'
            );
            
            console.log(`🔍 Trouvé ${playButtons.length} boutons de lecture à synchroniser`);
            
            playButtons.forEach(button => {
                // Sauvegarder l'ancien onclick
                const oldOnClick = button.onclick;
                
                // Remplacer par la version synchronisée
                button.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Identifier le type de bouton et récupérer les informations
                    let demoName = 'Démo';
                    let library = 'unknown';
                    
                    // Boutons des tables admin
                    const demoItem = button.closest('.demo-item-admin, .demo-item, .table-row');
                    if (demoItem) {
                        demoName = demoItem.querySelector('.demo-name-admin, .demo-name, .row-title')?.textContent || 'Démo';
                        library = demoItem.closest('.demo-table-container')?.querySelector('.table-header h4')?.textContent?.trim().split(' ')[1] || 'unknown';
                    }
                    // Boutons des menus dropdown
                    else if (button.closest('.menu-dropdown')) {
                        demoName = button.closest('.demo-item')?.querySelector('.demo-name')?.textContent || 'Démo Menu';
                        library = button.closest('.menu-dropdown')?.querySelector('.menu-header')?.textContent?.trim() || 'menu';
                    }
                    // Boutons de la barre de contrôle
                    else if (button.closest('.demo-controls')) {
                        demoName = 'Démo Clavier';
                        library = 'keyboard';
                    }
                    // Boutons d'enregistrement
                    else if (button.id === 'playBtn') {
                        demoName = 'Enregistrement';
                        library = 'recorder';
                    }
                    
                    console.log(`🎵 Clic sur bouton play: ${demoName} (${library})`);
                    startSynchronizedPlayback(library.toLowerCase(), demoName, button);
                    
                    // Appeler l'ancien onclick si il existe
                    if (oldOnClick) {
                        oldOnClick.call(this, e);
                    }
                };
            });
            
            console.log(`✅ Synchronisation ajoutée à ${playButtons.length} boutons de lecture`);
        }
        
        // Raccourci clavier pour arrêter toutes les lectures
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                stopAllPlayback();
                console.log('🛑 Arrêt par raccourci clavier (Espace)');
            }
        });
    </script>
</body>
</html>