<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piano Solo - Interface Unifiée</title>
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
            padding: 0px 10px 10px 10px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            overflow: visible;
        }
        
        /* Ajouter de l'espace en bas quand on est en mode admin pour la barre de liens */
        .admin-mode .container {
            padding-bottom: 100px;
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
        .btn-play.playing:not(#playBtn),
        .demo-play-btn.playing,
        .menu-play-btn.playing,
        .keyboard-play-btn.playing,
        .play-btn.playing {
            background: #dc3545 !important;
            animation: pulse 1s infinite;
        }
        
        /* Style pour les notes actives en pause */
        .key.active-pause {
            background-color: #ffeb3b !important;
            animation: none !important;
        }
        
        /* ===== MODE ADMIN ===== */
        .admin-only {
            display: none;
        }
        
        .admin-mode .admin-only {
            display: block;
        }
        
        .admin-mode .public-only {
            display: none;
        }
        
        /* Modal de mot de passe */
        .admin-modal {
            position: relative;
            top: 0;
            left: -50;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1502;
        }
        
        .admin-modal.show {
            display: flex;
        }
        
        .admin-modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        
        .admin-modal input {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .admin-modal button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        
        .admin-modal button:hover {
            background: #218838;
        }
        
        /* Indicateur de mode admin */
        .admin-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            z-index: 1000000000;
            display: none;
        }
        
        .admin-mode .admin-indicator {
            display: block;
        }
        
        /* ===== MODE PUBLIC ===== */
        .public-only {
            display: block;
        }
        
        .admin-mode .public-only {
            display: none;
        }
        
        /* Styles pour les tableaux glissants */
        .student-tables-container {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) translateY(85%);
            width: 90%;
            max-width: 1200px;
            z-index: 999;
            transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            opacity: 0.95;
            backdrop-filter: blur(10px);
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 10px;
        }
        
        .student-tables-container.hidden {
            transform: translateX(-50%) translateY(100%);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .student-tables-container.show {
            transform: translateX(-50%) translateY(0);
            opacity: 0.95;
            transition: all 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        /* Hover pour faire monter les bandeaux */
        .student-tables-container:hover {
            transform: translateX(-50%) translateY(0);
            opacity: 0.95;
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        /* Garder les tableaux montés quand on survole le contenu */
        .student-tables-container:hover .student-tables-content {
            /* Pas de changement, juste pour maintenir le hover */
        }
        
        .student-tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .student-demo-table-container {
            background: rgba(248, 249, 250, 0.9);
            border-radius: 15px 15px 0 0;
            overflow: hidden;
            border: 1px solid rgba(233, 236, 239, 0.8);
            box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(15px);
        }
        
        /* En-têtes des tableaux - non cliquables maintenant */
        .student-table-header {
            background: linear-gradient(135deg, #2980b9 0%, #1f5f8b 100%);
            color: white;
            padding: 12px 15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
            text-align: center;
            border-bottom: 2px solid #1f5f8b;
            /* Supprimé: cursor: pointer; */
            /* Supprimé: hover effects */
        }
        
        .student-table-header:active {
            transform: translateY(0);
        }
        
        /* Indicateur visuel que le bandeau est cliquable */
        .student-table-header::after {
            content: '👆';
            font-size: 12px;
            margin-left: 8px;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }
        
        .student-table-header:hover::after {
            opacity: 1;
        }
        
        .student-table-header h4 {
            margin: 0;
            color: white;
            font-size: 14px;
            font-weight: 600;
        }
        
        .student-table-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .student-demo-table {
            height: 200px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.7);
        }
        
        .student-table-content {
            padding: 8px;
        }
        
        .student-demo-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 10px;
            border-bottom: 1px solid rgba(233, 236, 239, 0.6);
            transition: all 0.2s ease;
            border-radius: 8px;
            margin-bottom: 2px;
        }
        
        .student-demo-row:hover {
            background: rgba(248, 249, 250, 0.8);
            transform: translateX(2px);
        }
        
        .student-demo-title {
            font-weight: 600;
            color: #2c3e50;
            flex: 1;
            font-size: 13px;
        }
        
        .student-play-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }
        
        .student-play-btn:hover {
            background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }
        
        .student-play-btn.playing {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            animation: pulse 1s infinite;
        }
        
        .empty-table {
            text-align: center;
            color: #6c757d;
            padding: 20px;
            font-style: italic;
            font-size: 13px;
        }
        
        /* Bouton Synchroniser intégré dans l'en-tête */
        .sync-button-container {
            position: absolute;
            top: -40px;
            right: 0;
            z-index: 1000;
        }
        
        .sync-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            opacity: 0.9;
        }
        
        .sync-button:hover {
            opacity: 1;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        /* Animation d'apparition/disparition */
        @keyframes slideUp {
            from {
                transform: translateX(-50%) translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 0.95;
            }
        }
        
        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(0);
                opacity: 0.95;
            }
            to {
                transform: translateX(-50%) translateY(100%);
                opacity: 0;
            }
        }
        
        .slide-up {
            animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        .slide-down {
            animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        /* ===== BARRE DE LIENS ADMIN ===== */
        .admin-links-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: none;
            z-index: 1000;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.2);
            border-top: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-mode .admin-links-bar {
            display: block;
        }
        
        .admin-links-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .admin-link {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .admin-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        
        .admin-link.active {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        .admin-link-icon {
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .admin-links-container {
                gap: 10px;
                flex-direction: column;
            }
            
            .admin-link {
                width: 100%;
                justify-content: center;
                padding: 12px 16px;
            }
        }
        
        /* Indicateur de raccourci clavier */
        .keyboard-hint {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            z-index: 998;
            opacity: 0.8;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .keyboard-hint:hover {
            opacity: 1;
        }
        
        .keyboard-hint .key {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: bold;
            margin: 0 2px;
        }
        

    </style>
</head>
<body>
    <!-- Indicateur de synchronisation -->
    <div id="syncIndicator" class="sync-indicator">
        🔄 Synchronisé - Aucune lecture
    </div>
    
    <!-- Indicateur de mode admin -->
    <div class="admin-indicator" id="adminIndicator">🔧 Mode Admin</div>
    


    <!-- Modal de mot de passe admin -->
    <div class="admin-modal" id="adminModal">
        <div class="admin-modal-content">
            <h3>🔐 Accès Administrateur</h3>
            <p>Entrez le mot de passe pour accéder aux outils d'administration :</p>
            <input type="password" id="adminPassword" placeholder="Mot de passe" />
            <div>
                <button onclick="validateAdminPassword()">🔓 Accéder</button>
                <button onclick="closeAdminModal()">❌ Annuler</button>
            </div>
        </div>
    </div>

    <div class="container" id="mainContainer">

        
        <!-- Mode Public -->
        <div class="public-only">
            <!-- Titre supprimé - Vignette admin uniquement -->
        </div>
        
        <!-- Mode Admin -->
        <div class="admin-only">
            <!-- Titre supprimé - Vignette admin uniquement -->
        </div>

        <!-- Piano Core (toujours visible) -->
        <?php include 'includes/piano-core.php'; ?>
        
        <!-- Modules Admin (cachés par défaut) -->
        <div class="admin-only">
            <?php include 'includes/piano-notation.php'; ?>
            <?php include 'includes/piano-harmonium.php'; ?>
            <?php include 'includes/piano-recorder.php'; ?>
            <?php include 'includes/piano-save-upload.php'; ?>
            <?php include 'includes/piano-demo-manager.php'; ?>
            <s?php include 'includes/piano-midi.php'; ?>
        </div>
        
        <!-- Interface Élève (visible par défaut) -->
        <div class="public-only">
        <!-- Tableaux de démos pour les élèves (READ-ONLY) -->
        <div id="studentDemoTables">
                <div class="student-tables-container" id="studentTablesContainer">
                    <!-- Bouton Synchroniser intégré -->
                    <div class="sync-button-container">
                        <button class="sync-button" onclick="forceReloadStudentTables()">
                            🔄 Synchroniser
                        </button>
                    </div>
                
                <!-- Grille des 2 tableaux -->
                <div class="student-tables-grid">
                    <!-- Tableau Prayers -->
                    <div class="student-demo-table-container">
                            <div class="student-table-header">
                            <h4>🙏 Prayers</h4>
                            <div class="student-table-count" id="studentPrayersCount">0</div>
                        </div>
                        <div class="student-demo-table" id="studentPrayersTable">
                            <div class="student-table-content">
                                <!-- Les prayers seront chargés ici -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tableau Bhajans -->
                    <div class="student-demo-table-container">
                            <div class="student-table-header">
                            <h4>🎶 Bhajans</h4>
                            <div class="student-table-count" id="studentBhajansCount">0</div>
                        </div>
                        <div class="student-demo-table" id="studentBhajansTable">
                            <div class="student-table-content">
                                <!-- Les bhajans seront chargés ici -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Barre de liens Admin (visible uniquement en mode admin) -->
    <div class="admin-links-bar">
        <div class="admin-links-container">
            <a href="admin.php" class="admin-link" target="_blank">
                <span class="admin-link-icon">🔧</span>
                Page Admin
            </a>
            <a href="index.php?mode=public" class="admin-link" target="_blank">
                <span class="admin-link-icon">🌐</span>
                Mode Public
            </a>
            <a href="test-admin-help-video.html" class="admin-link" target="_blank">
                <span class="admin-link-icon">🧪</span>
                Test HELP/VIDEO
            </a>
            <a href="test-synchronisation-v1.2.html" class="admin-link" target="_blank">
                <span class="admin-link-icon">🔄</span>
                Test Synchronisation
            </a>
            <a href="diagnostic.php" class="admin-link" target="_blank">
                <span class="admin-link-icon">🔍</span>
                Diagnostic
            </a>
            <a href="edit-display-names.php" class="admin-link" target="_blank">
                <span class="admin-link-icon">✏️</span>
                Éditer Noms Démo
            </a>
            <a href="cleanup-demos.php" class="admin-link" target="_blank">
                <span class="admin-link-icon">🧹</span>
                Nettoyage
            </a>
            <a href="check-libraries.php" class="admin-link" target="_blank">
                <span class="admin-link-icon">📋</span>
                Vérif Bibliothèques
            </a>
            <a href="upload-demo.php" class="admin-link" target="_blank">
                <span class="admin-link-icon">📤</span>
                Upload Démo
            </a>
        </div>
    </div>
    
    <!-- Gestionnaire de modules -->
    <script src="includes/js/module-manager.js"></script>
    <!-- Chargeur de démos -->
    <script src="includes/js/demos-loader.js"></script>
    <!-- JavaScript centralisé -->
    <script src="includes/js/menu-handlers.js"></script>
    <!-- Module HELP et VIDÉO -->
    <script src="includes/help-video-module.js"></script>
    
    <!-- Initialisation des modules -->
    <script>
        // Variables de synchronisation globales
        let currentPlayingButton = null;
        let currentPlayingDemo = null;
        let playbackTimer = null;
        let playbackProgress = 0;
        
        // Variables pour le mode admin
        let adminMode = false;
        let adminClickCount = 0;
        let adminClickTimer = null;
        const ADMIN_PASSWORD = 'murali'; // À changer en production
        
        // Vérifier si on doit forcer le mode public
        function checkForcedPublicMode() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('mode') === 'public') {
                console.log('🌐 Mode public forcé via URL');
                // Désactiver complètement le mode admin
                adminMode = false;
                document.body.classList.remove('admin-mode');
                localStorage.removeItem('adminMode');
                
                // Masquer le logo admin
                const adminLogo = document.getElementById('adminLogo');
                if (adminLogo) {
                    adminLogo.style.display = 'none';
                }
                
                // Masquer l'indicateur admin
                const adminIndicator = document.getElementById('adminIndicator');
                if (adminIndicator) {
                    adminIndicator.style.display = 'none';
                }
                
                return true; // Mode public forcé
            }
            return false; // Mode normal
        }
        
        // Arrêter toutes les lectures
        window.stopAllPlayback = function() {
            console.log('🛑 Arrêt de toutes les lectures');
            
            // Arrêter la lecture en cours
            if (window.RECORDER_MODULE) {
                window.RECORDER_MODULE.stopPlayback();
            }
            
            // Arrêter toutes les notes
            if (window.stopAllNotes) {
                window.stopAllNotes();
            }
            
            // Garder les tableaux en position bandeau bas (pas de setTimeout)
            const container = document.getElementById('studentTablesContainer');
            if (container) {
                container.classList.remove('show');
                container.classList.remove('hidden');
                // Retour à la position initiale : juste le bandeau visible
            }
            
            console.log('✅ Toutes les lectures arrêtées');
        };
        
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
        
        // ===== GESTION DU MODE ADMIN =====
        
        // Initialiser le système admin
        function initAdminSystem() {
            console.log('🔧 Initialisation du système admin...');
            
            // Vérifier d'abord si le mode public est forcé
            if (checkForcedPublicMode()) {
                console.log('🌐 Mode public forcé - système admin désactivé');
                return; // Ne pas initialiser le système admin
            }
            
            const adminLogo = document.getElementById('adminLogo');
            if (adminLogo) {
                console.log('🎹 Logo admin trouvé, ajout de l\'écouteur de clic');
                adminLogo.addEventListener('click', handleAdminLogoClick);
                console.log('✅ Écouteur de clic ajouté au logo admin');
            } else {
                console.error('❌ Logo admin non trouvé');
            }
            
            // Vérifier si on est déjà en mode admin (session)
            if (localStorage.getItem('adminMode') === 'true') {
                console.log('🔧 Mode admin déjà actif en session');
                activateAdminMode();
            } else {
                console.log('👤 Mode public par défaut');
            }
        }
        
        // Gérer les clics sur le logo admin
        function handleAdminLogoClick() {
            console.log('🎹 Clic sur le logo admin, compteur:', adminClickCount + 1);
            adminClickCount++;
            const adminLogo = document.getElementById('adminLogo');
            
            // Ajouter l'effet visuel
            adminLogo.classList.add('clicked');
            setTimeout(() => adminLogo.classList.remove('clicked'), 200);
            
            // Réinitialiser le timer
            if (adminClickTimer) {
                clearTimeout(adminClickTimer);
            }
            
            // Si 3 clics, ouvrir le modal
            if (adminClickCount >= 3) {
                console.log('🎹 3 clics détectés, ouverture du modal admin');
                openAdminModal();
                adminClickCount = 0;
            } else {
                console.log('🎹 Clic', adminClickCount, 'sur 3, timer démarré');
                // Timer pour réinitialiser le compteur
                adminClickTimer = setTimeout(() => {
                    console.log('🎹 Timer expiré, réinitialisation du compteur');
                    adminClickCount = 0;
                }, 2000);
            }
        }
        
        // Ouvrir le modal admin
        function openAdminModal() {
            console.log('🔐 Ouverture du modal admin');
            const modal = document.getElementById('adminModal');
            if (modal) {
                modal.classList.add('show');
                const passwordInput = document.getElementById('adminPassword');
                if (passwordInput) {
                    passwordInput.focus();
                }
                console.log('✅ Modal admin ouvert');
            } else {
                console.error('❌ Modal admin non trouvé');
            }
        }
        
        // Fermer le modal admin
        function closeAdminModal() {
            const modal = document.getElementById('adminModal');
            modal.classList.remove('show');
            document.getElementById('adminPassword').value = '';
        }
        
        // Valider le mot de passe admin
        function validateAdminPassword() {
            const password = document.getElementById('adminPassword').value;
            
            if (password === ADMIN_PASSWORD) {
                activateAdminMode();
                closeAdminModal();
                localStorage.setItem('adminMode', 'true');
            } else {
                alert('❌ Mot de passe incorrect');
                document.getElementById('adminPassword').value = '';
            }
        }
        
        // Activer le mode admin
        function activateAdminMode() {
            adminMode = true;
            document.body.classList.add('admin-mode');
            console.log('🔧 Mode admin activé');
            
            // Initialiser les modules admin
            if (window.MODULE_MANAGER) {
                MODULE_MANAGER.initialize();
            }
            
            if (window.MENU_HANDLERS) {
                MENU_HANDLERS.init();
            }
            
            if (window.DEMOS_LOADER) {
                DEMOS_LOADER.init();
            }
        }
        
        // Désactiver le mode admin
        function deactivateAdminMode() {
            adminMode = false;
            document.body.classList.remove('admin-mode');
            localStorage.removeItem('adminMode');
            console.log('👤 Mode public activé');
        }
        
        // Exposer les fonctions admin globalement
        window.initAdminSystem = initAdminSystem;
        window.activateAdminMode = activateAdminMode;
        window.deactivateAdminMode = deactivateAdminMode;
        window.openAdminModal = openAdminModal;
        window.closeAdminModal = closeAdminModal;
        window.validateAdminPassword = validateAdminPassword;
        window.handleAdminLogoClick = handleAdminLogoClick;
        
        // ===== FONCTIONS POUR L'INTERFACE ÉLÈVE =====
        
        // Charger les tableaux de démos élèves
        function loadStudentDemoTables() {
            console.log('📚 Chargement des tableaux de démos pour les élèves...');
            
            // Ajouter un timestamp pour éviter le cache
            const timestamp = Date.now();
            
            // Afficher un indicateur de chargement
            showLoadingIndicator();
            
            // Charger les Prayers
            fetch(`demospubliques/prayers/index.json?t=${timestamp}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Données Prayers:', data);
                    populateStudentTable('studentPrayersTable', 'studentPrayersCount', data, 'prayers');
                })
                .catch(error => {
                    console.error('Erreur chargement Prayers:', error);
                    // Essayer de recharger après un délai
                    setTimeout(() => {
                        console.log('🔄 Nouvelle tentative de chargement Prayers...');
                        loadStudentDemoTables();
                    }, 2000);
                });
            
            // Charger les Bhajans
            fetch(`demospubliques/bhajans/index.json?t=${timestamp}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Données Bhajans:', data);
                    populateStudentTable('studentBhajansTable', 'studentBhajansCount', data, 'bhajans');
                })
                .catch(error => {
                    console.error('Erreur chargement Bhajans:', error);
                    // Essayer de recharger après un délai
                    setTimeout(() => {
                        console.log('🔄 Nouvelle tentative de chargement Bhajans...');
                        loadStudentDemoTables();
                    }, 2000);
                })
                .finally(() => {
                    // Masquer l'indicateur de chargement
                    hideLoadingIndicator();
                });
        }
        
        // Peupler un tableau élève
        function populateStudentTable(tableId, countId, data, libraryType) {
            console.log(`🎯 Peupler ${tableId} avec ${data.length} démos`);
            
            const table = document.getElementById(tableId);
            const countElement = document.getElementById(countId);
            
            if (!table) {
                console.error(`❌ Table ${tableId} non trouvé`);
                return;
            }
            
            if (countElement) {
                countElement.textContent = data.length;
            }
            
            const content = table.querySelector('.student-table-content');
            if (!content) {
                console.error(`❌ Contenu non trouvé pour ${tableId}`);
                return;
            }
            
            if (!data || data.length === 0) {
                content.innerHTML = '<div class="empty-table">Aucun contenu disponible</div>';
                return;
            }
            
            content.innerHTML = '';
            
            data.forEach(demo => {
                // Priorité pour le nom : originalName > name > filename nettoyé
                let demoName = 'Démo sans nom';
                
                // 1. Utiliser originalName si disponible et différent du filename
                if (demo.originalName && demo.originalName !== demo.filename && demo.originalName.length > 2) {
                    demoName = demo.originalName;
                } 
                // 2. Utiliser name si disponible et différent du filename
                else if (demo.name && demo.name !== demo.filename && demo.name.length > 2) {
                    demoName = demo.name;
                } 
                // 3. Extraire un nom lisible du filename si nécessaire
                else if (demo.filename) {
                    // Nettoyer le filename pour extraire un nom lisible
                    let cleanName = demo.filename;
                    
                    // Supprimer l'extension .json
                    cleanName = cleanName.replace(/\.json$/, '');
                    
                    // Supprimer le préfixe "demo_"
                    cleanName = cleanName.replace(/^demo_/, '');
                    
                    // Supprimer les timestamps et IDs hexadécimaux
                    cleanName = cleanName.replace(/_\d{10,}_[a-f0-9]{16}$/, '');
                    cleanName = cleanName.replace(/_\d{10,}$/, '');
                    cleanName = cleanName.replace(/_[a-f0-9]{16}$/, '');
                    
                    // Remplacer les underscores par des espaces
                    cleanName = cleanName.replace(/_/g, ' ');
                    
                    // Capitaliser
                    cleanName = cleanName.charAt(0).toUpperCase() + cleanName.slice(1);
                    
                    // Si le nom est trop court, utiliser un nom par défaut
                    if (cleanName.length < 3) {
                        demoName = 'Démo ' + demo.filename.substring(0, 8);
                    } else {
                        demoName = cleanName;
                    }
                }
                
                // S'assurer que le filename n'a pas d'extension .json
                let demoFilename = demo.filename || demo.id || 'demo';
                if (demoFilename.endsWith('.json')) {
                    demoFilename = demoFilename.replace('.json', '');
                }
                
                const row = document.createElement('div');
                row.className = 'student-demo-row';
                row.innerHTML = `
                    <div class="student-demo-title">${demoName}</div>
                    <div class="student-demo-actions">
                        <button class="student-play-btn" onclick="playStudentDemo('${demoFilename}', '${libraryType}')">
                            ▶️ Écouter
                        </button>
                    </div>
                `;
                content.appendChild(row);
            });
            
            console.log(`✅ Tableau ${tableId} peuplé avec ${data.length} démos`);
        }
        
        // Lancer la lecture de la démo
        function playStudentDemo(demoId, libraryType) {
            console.log(`🎵 Lecture de démo élève: ${demoId} (${libraryType})`);
            
            // Arrêter toute lecture en cours
            stopAllPlayback();
            
            // Garder les tableaux en position bandeau bas (pas de hideStudentTables)
            const container = document.getElementById('studentTablesContainer');
            if (container) {
                container.classList.remove('show');
                container.classList.remove('hidden');
                // Reste en position initiale : juste le bandeau visible
            }
            
            // Ajouter la classe playing au bouton cliqué
            const buttonElement = document.querySelector(`.student-play-btn[onclick="playStudentDemo('${demoId}', '${libraryType}')"]`);
            if (buttonElement) {
                buttonElement.classList.add('playing');
                buttonElement.innerHTML = '⏸️ Écouter';
            }
            
            // S'assurer que le demoId n'a pas déjà l'extension .json
            let cleanDemoId = demoId;
            if (cleanDemoId.endsWith('.json')) {
                cleanDemoId = cleanDemoId.replace('.json', '');
            }
            
            // Charger et jouer la démo
            fetch(`demospubliques/${libraryType}/${cleanDemoId}.json`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(demoData => {
                    console.log('📄 Données de démo chargées:', demoData);
                    
                    if (window.RECORDER_MODULE && window.RECORDER_MODULE.playRecording) {
                        // Utiliser la méthode playRecording standard
                        if (demoData && demoData.data && demoData.data.recording) {
                            console.log('🎵 Lecture de la démo via RECORDER_MODULE');
                            window.RECORDER_MODULE.playRecording(demoData.data.recording, buttonElement);
                        } else {
                            console.error('Format de démo invalide:', demoData);
                            const buttonElement = document.querySelector(`.student-play-btn[onclick="playStudentDemo('${demoId}', '${libraryType}')"]`);
                            if (buttonElement) {
                                buttonElement.classList.remove('playing');
                                buttonElement.innerHTML = '▶️ Écouter';
                            }
                            alert('Format de démo invalide');
                        }
                    } else {
                        console.error('Module RECORDER non disponible');
                        const buttonElement = document.querySelector(`.student-play-btn[onclick="playStudentDemo('${demoId}', '${libraryType}')"]`);
                        if (buttonElement) {
                            buttonElement.classList.remove('playing');
                            buttonElement.innerHTML = '▶️ Écouter';
                        }
                        alert('Module de lecture non disponible');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement de la démo:', error);
                    const buttonElement = document.querySelector(`.student-play-btn[onclick="playStudentDemo('${demoId}', '${libraryType}')"]`);
                    if (buttonElement) {
                        buttonElement.classList.remove('playing');
                        buttonElement.innerHTML = '▶️ Écouter';
                    }
                    
                    // Afficher un message d'erreur à l'utilisateur
                    alert(`Erreur lors du chargement de la démo: ${error.message}`);
                });
        }
        
        // Fonction pour afficher les tableaux (maintenant gérée par CSS hover)
        function showStudentTables() {
            const container = document.getElementById('studentTablesContainer');
            if (container && !container.classList.contains('show')) {
                container.classList.add('show');
                container.classList.remove('hidden');
                // Pas d'animation slide-up, transition CSS directe
            }
            console.log('📊 Tableaux élèves affichés');
        }
        
        // Fonction pour cacher les tableaux (maintenant gérée par CSS)
        function hideStudentTables() {
            const container = document.getElementById('studentTablesContainer');
            if (container && container.classList.contains('show')) {
                container.classList.remove('show');
                container.classList.add('hidden');
                // Transition CSS directe vers le bas
            }
            console.log('📊 Tableaux élèves cachés');
        }
        
        // Fonction pour gérer les clics sur les bandeaux des tableaux
        function handleTableHeaderClick() {
            const container = document.getElementById('studentTablesContainer');
            if (container && !container.classList.contains('show')) {
                showStudentTables();
            }
        }

        // Fonction pour forcer le rechargement des tableaux élèves
        function forceReloadStudentTables() {
            console.log('🔄 Forçage du rechargement des tableaux de démos élèves...');
            
            // Afficher un message de synchronisation
            const syncButton = document.querySelector('button[onclick="forceReloadStudentTables()"]');
            if (syncButton) {
                const originalText = syncButton.innerHTML;
                syncButton.innerHTML = '🔄 Synchronisation...';
                syncButton.disabled = true;
                
                setTimeout(() => {
                    syncButton.innerHTML = originalText;
                    syncButton.disabled = false;
                }, 2000);
            }
            
            // Vider le cache du navigateur si possible
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                });
            }
            
            // Recharger après un délai pour s'assurer que le cache est vidé
            setTimeout(() => {
                loadStudentDemoTables();
            }, 100);
        }
        
        // Synchronisation automatique périodique (toutes les 30 secondes)
        function startAutoSync() {
            setInterval(() => {
                console.log('🔄 Synchronisation automatique des tableaux...');
                loadStudentDemoTables();
            }, 30000); // 30 secondes
        }
        
        // Détecter les changements de fichiers et synchroniser
        function detectChangesAndSync() {
            // Vérifier si les fichiers ont changé en comparant les timestamps
            const timestamp = Date.now();
            
            Promise.all([
                fetch(`demospubliques/prayers/index.json?t=${timestamp}`).then(r => r.headers.get('last-modified')),
                fetch(`demospubliques/bhajans/index.json?t=${timestamp}`).then(r => r.headers.get('last-modified'))
            ]).then(timestamps => {
                // Si les timestamps ont changé, recharger
                if (timestamps[0] || timestamps[1]) {
                    console.log('📋 Changements détectés, synchronisation...');
                    loadStudentDemoTables();
                }
            }).catch(error => {
                console.log('📋 Vérification des changements:', error);
            });
        }
        
        // Fonction pour vérifier si les fichiers existent
        function checkFilesExist() {
            const timestamp = Date.now();
            
            // Vérifier les fichiers index.json
            Promise.all([
                fetch(`demospubliques/prayers/index.json?t=${timestamp}`).then(r => r.ok),
                fetch(`demospubliques/bhajans/index.json?t=${timestamp}`).then(r => r.ok)
            ]).then(results => {
                console.log('📋 Vérification des fichiers:', {
                    prayers: results[0],
                    bhajans: results[1]
                });
                
                if (!results[0] || !results[1]) {
                    console.warn('⚠️ Certains fichiers index.json sont manquants');
                }
            }).catch(error => {
                console.error('❌ Erreur lors de la vérification des fichiers:', error);
            });
        }
        
        // Afficher un indicateur de chargement
        function showLoadingIndicator() {
            const prayersContent = document.querySelector('#studentPrayersTable .student-table-content');
            const bhajansContent = document.querySelector('#studentBhajansTable .student-table-content');
            
            if (prayersContent) {
                prayersContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">🔄 Chargement...</div>';
            }
            if (bhajansContent) {
                bhajansContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">🔄 Chargement...</div>';
            }
        }
        
        // Masquer l'indicateur de chargement
        function hideLoadingIndicator() {
            // L'indicateur est remplacé par le contenu lors du populateStudentTable
        }
        
        // ===== INITIALISATION =====
        
        // Initialisation au chargement avec gestionnaire de modules
        window.addEventListener('DOMContentLoaded', async () => {
            console.log('✅ JavaScript des menus chargé');
            
            // Attendre un peu pour s'assurer que toutes les fonctions sont définies
            setTimeout(() => {
                // Initialiser le système admin (toujours)
                if (window.initAdminSystem) {
                    window.initAdminSystem();
                }
                
                // Vérifier si on doit forcer le mode admin
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('forceAdmin') === '1') {
                    console.log('🔧 Mode admin forcé via URL');
                    // Activer le mode admin automatiquement
                    setTimeout(() => {
                        if (window.activateAdminMode) {
                            window.activateAdminMode();
                        }
                    }, 1000);
                }
            }, 100);
            
            // Attendre que le gestionnaire de modules soit disponible
            if (window.MODULE_MANAGER) {
                await MODULE_MANAGER.initialize();
            }
            
            // Initialiser le module de lecture élève
            if (window.RECORDER_MODULE) {
                window.RECORDER_MODULE.init();
            }
            
            // Fonction globale pour arrêter toutes les notes
            window.stopAllNotes = function() {
                console.log('🔇 Arrêt de toutes les notes');
                // Retirer la classe active de toutes les touches
                document.querySelectorAll('.key.active').forEach(key => {
                    key.classList.remove('active');
                });
            };
            
            // Fonction globale pour arrêter toutes les lectures
            window.stopAllPlayback = function() {
                console.log('🛑 Arrêt de toutes les lectures');
                if (window.RECORDER_MODULE && window.RECORDER_MODULE.stopPlayback) {
                    window.RECORDER_MODULE.stopPlayback();
                }
            };
            
            // Charger les tableaux de démos élèves
            loadStudentDemoTables();
            
            // Les tableaux restent descendus au chargement (juste les bandeaux visibles)
            // Pas d'affichage automatique
            
            // Écouter les événements de rechargement depuis l'interface admin
            window.addEventListener('reloadStudentTables', () => {
                console.log('🔄 Événement de rechargement reçu, mise à jour des tableaux...');
                loadStudentDemoTables();
            });
            
            // Gestion des touches clavier pour le modal admin
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAdminModal();
                }
                if (e.key === 'Enter' && document.getElementById('adminModal').classList.contains('show')) {
                    validateAdminPassword();
                }
                

            });
            


            // Démarrer la synchronisation automatique
            startAutoSync();
            
            // Initialisation alternative du système admin (en cas d'échec de la première)
            setTimeout(() => {
                console.log('🔄 Tentative d\'initialisation alternative du système admin...');
                const adminLogo = document.getElementById('adminLogo');
                if (adminLogo && !adminLogo.hasAttribute('data-initialized')) {
                    console.log('🎹 Initialisation alternative du logo admin');
                    adminLogo.setAttribute('data-initialized', 'true');
                    adminLogo.addEventListener('click', handleAdminLogoClick);
                    console.log('✅ Logo admin initialisé via méthode alternative');
                }
            }, 2000);
        });
    </script>
</body>
</html>
