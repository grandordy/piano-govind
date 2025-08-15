<!-- Interface du Gestionnaire de Brouillons - NOUVELLE ARCHITECTURE -->
<div id="demoManagerInterface">
    <div class="demo-manager-container">
        <h3>🎵 Gestionnaire de Contenu - Interface Admin</h3>
        
        <!-- Bouton de synchronisation -->
        <div class="sync-controls">
            <button class="sync-btn" onclick="window.DEMO_MANAGER.forceSync()" title="Synchroniser avec les fichiers">
                🔄 Synchroniser
            </button>
        </div>
        
        <!-- Grille des 3 tableaux -->
        <div class="tables-grid">
            <!-- Tableau Brouillon -->
            <div class="demo-table-container">
                <div class="table-header">
                    <h4>📝 Brouillon</h4>
                    <div class="table-count" id="brouillonCount">0</div>
                </div>
                <div class="demo-table" id="brouillonTable">
                    <div class="table-content">
                        <!-- Les brouillons seront chargés ici -->
                    </div>
                </div>
            </div>
            
            <!-- Tableau Prayers -->
            <div class="demo-table-container">
                <div class="table-header">
                    <h4>🙏 Prayers</h4>
                    <div class="table-count" id="prayersCount">0</div>
                </div>
                <div class="demo-table" id="prayersTable">
                    <div class="table-content">
                        <!-- Les prayers seront chargés ici -->
                    </div>
                </div>
            </div>
            
            <!-- Tableau Bhajans -->
            <div class="demo-table-container">
                <div class="table-header">
                    <h4>🎶 Bhajans</h4>
                    <div class="table-count" id="bhajansCount">0</div>
                </div>
                <div class="demo-table" id="bhajansTable">
                    <div class="table-content">
                        <!-- Les bhajans seront chargés ici -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.demo-manager-container {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 15px;
    padding: 25px;
    margin: 20px 0;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.sync-controls {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 15px;
}

.sync-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.sync-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.sync-btn:active {
    transform: translateY(0);
}

.tables-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.demo-table-container {
    background: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #e9ecef;
    overflow: hidden;
}

.table-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Bandeau orange spécial pour le tableau Brouillon */
.demo-table-container:first-child .table-header {
    background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
}

.table-header h4 {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
}

.table-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.9em;
    font-weight: 600;
}

.demo-table {
    height: 350px;
    overflow: hidden;
}

.table-content {
    height: 100%;
    overflow-y: auto;
    padding: 0;
}

.table-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 15px;
    border-bottom: 1px solid #e9ecef;
    min-height: 25px;
    transition: background-color 0.2s ease;
}

.table-row:nth-child(even) {
    background-color: #f8f9fa;
}

.table-row:nth-child(odd) {
    background-color: #ffffff;
}

.table-row:hover {
    background-color: #e3f2fd;
}

.row-info {
    flex: 1;
    min-width: 0;
}

.row-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.row-meta {
    font-size: 11px;
    color: #6c757d;
}

.row-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-shrink: 0;
}

 .action-btn {
     width: 28px;
     height: 28px;
     border: none;
     border-radius: 4px;
     cursor: pointer;
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: 12px;
     transition: all 0.2s ease;
     background: transparent;
     position: relative;
     z-index: 10002;
 }

.action-btn:hover {
    transform: scale(1.1);
}

.btn-play {
    color: #28a745;
}

.btn-play:hover {
    background: #d4edda;
}

.btn-stop {
    color: #dc3545;
}

.btn-stop:hover {
    background: #f8d7da;
}

.btn-edit {
    color: #ffc107;
}

.btn-edit:hover {
    background: #fff3cd;
}

.btn-move {
    color: #17a2b8;
}

.btn-move:hover {
    background: #d1ecf1;
}

.btn-delete {
    color: #dc3545;
}

.btn-delete:hover {
    background: #f8d7da;
}

.empty-table {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
}

.loading-state, .error-state {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 1200px) {
    .tables-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .demo-table {
        height: 250px;
    }
}

@media (max-width: 768px) {
    .demo-manager-container {
        padding: 15px;
    }
    
    .table-header {
        padding: 12px;
    }
    
    .table-row {
        padding: 5px 12px;
        min-height: 22px;
    }
    
    .demo-table {
        height: 200px;
    }
    
    .row-title {
        max-width: 120px;
    }
    
    .action-btn {
        width: 26px;
        height: 26px;
        font-size: 11px;
    }
}
</style>

<script>
// Module de gestion des brouillons - NOUVELLE ARCHITECTURE
window.DEMO_MANAGER = {
    init: function() {
        console.log('🎵 Initialisation du gestionnaire de contenu...');
        this.loadAllTables();
        this.setupEventListeners();
        this.initialized = true;
        console.log('✅ DEMO_MANAGER initialisé');
    },
    
    loadAllTables: function() {
        console.log('🔄 Rechargement de tous les tableaux...');
        
        // Afficher des indicateurs de chargement
        this.showLoadingStates();
        
        // Forcer un rechargement complet avec un délai minimal
        // pour s'assurer que les fichiers sont bien synchronisés
        setTimeout(() => {
            this.loadTable('brouillon');
            this.loadTable('prayers');
            this.loadTable('bhajans');
        }, 100);
    },
    
    // Fonction pour forcer la synchronisation avec les fichiers réels
    forceSync: function() {
        console.log('🔄 Synchronisation forcée avec les fichiers...');
        
        // Vider le cache du navigateur pour les fichiers JSON
        if ('caches' in window) {
            caches.keys().then(names => {
                names.forEach(name => {
                    caches.delete(name);
                });
            });
        }
        
        // Recharger tous les tableaux avec un délai
        setTimeout(() => {
            this.loadAllTables();
        }, 200);
    },
    
    showLoadingStates: function() {
        // Afficher des indicateurs de chargement pour chaque tableau
        ['brouillon', 'prayers', 'bhajans'].forEach(tableType => {
            const container = document.querySelector(`#${tableType}Table .table-content`);
            const countElement = document.getElementById(`${tableType}Count`);
            
            if (container) {
                container.innerHTML = '<div class="loading-state">🔄 Chargement...</div>';
            }
            if (countElement) {
                countElement.textContent = '...';
            }
        });
    },
    
    loadTable: function(tableType) {
        console.log(`📋 Chargement du tableau: ${tableType}`);
        
        if (tableType === 'brouillon') {
            this.loadBrouillonTable();
        } else {
            this.loadLibraryTable(tableType);
        }
    },
    
    loadBrouillonTable: function() {
        fetch('demo-manager-api.php?action=getDemos&library=brouillon')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.displayTableContent('brouillon', data.demos, 'brouillon');
                } else {
                    this.displayTableContent('brouillon', [], 'brouillon');
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des brouillons:', error);
                this.displayTableContent('brouillon', [], 'brouillon');
            });
    },
    
    loadLibraryTable: function(library) {
        console.log(`📋 Chargement de la bibliothèque: ${library}`);
        
        // Ajouter un timestamp pour éviter le cache
        const timestamp = Date.now();
        const url = `demospubliques/${library}/index.json?t=${timestamp}`;
        
        fetch(url)
            .then(response => {
                console.log(`📄 Réponse pour ${library}:`, response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(`📊 Données reçues pour ${library}:`, data);
                
                // Traiter les données reçues
                if (Array.isArray(data)) {
                    // Si c'est un tableau, l'utiliser directement
                    this.displayTableContent(library, data, library);
                } else if (data && data.demos) {
                    // Si c'est un objet avec des démos, les afficher
                    this.displayTableContent(library, data.demos, library);
                } else {
                    // Sinon, afficher vide
                    this.displayTableContent(library, [], library);
                }
            })
            .catch(error => {
                console.error(`Erreur lors du chargement de ${library}:`, error);
                // En cas d'erreur, afficher un message d'erreur
                this.displayTableContent(library, [], library, error.message);
            });
    },
    
    displayTableContent: function(tableType, demos, sourceType, errorMessage = null) {
        console.log(`🎯 displayTableContent: ${tableType}, ${demos.length} démos, source: ${sourceType}`);
        
        const container = document.querySelector(`#${tableType}Table .table-content`);
        const countElement = document.getElementById(`${tableType}Count`);
        
        console.log(`🔍 Container trouvé:`, !!container);
        console.log(`🔍 CountElement trouvé:`, !!countElement);
        
        if (!container) {
            console.error(`❌ Container non trouvé pour ${tableType}Table`);
            return;
        }
        
        // Mettre à jour le compteur
        if (countElement) {
            countElement.textContent = demos.length;
        }
        
        // Afficher un message d'erreur si nécessaire
        if (errorMessage) {
            container.innerHTML = `<div class="error-state">❌ Erreur: ${errorMessage}</div>`;
            return;
        }
        
        if (!demos || demos.length === 0) {
            container.innerHTML = '<div class="empty-table">Aucun contenu</div>';
            return;
        }
        
        container.innerHTML = demos.map((demo, index) => {
            const isEven = index % 2 === 0;
            
            // Système de noms dual : priorité displayName > name > fallback
            let demoName = 'Démo sans nom';
            let demoId = demo.filename || 'unknown';
            
            // Utiliser le nom d'affichage en priorité
            if (demo.displayName) {
                demoName = demo.displayName;
            } else if (demo.name && demo.name !== demo.filename) {
                demoName = demo.name;
            } else if (demo.filename) {
                // Fallback : extraire un nom lisible du filename
                demoName = extractReadableName(demo.filename);
            }
            
            // Utiliser le nom technique pour l'ID si disponible
            if (demo.technicalName) {
                demoId = demo.technicalName;
            } else if (demo.filename) {
                demoId = demo.filename.replace('.json', '');
            }
            
            const demoDate = demo.created || demo.added || demo.lastModified || Date.now();
            
            // Fonction pour extraire un nom lisible du filename (fallback)
            function extractReadableName(filename) {
                if (!filename) return 'Démo sans nom';
                
                // Supprimer l'extension .json
                let name = filename.replace(/\.json$/, '');
                
                // Supprimer le préfixe "demo_"
                name = name.replace(/^demo_/, '');
                
                // Extraire la partie descriptive (avant le premier timestamp)
                const parts = name.split('_');
                const descriptiveParts = [];
                
                for (let part of parts) {
                    // Si c'est un timestamp (10+ chiffres) ou un ID hex (8+ caractères), arrêter
                    if (/^\d{10,}$/.test(part) || /^[a-f0-9]{8,}$/i.test(part)) {
                        break;
                    }
                    descriptiveParts.push(part);
                }
                
                let readableName = descriptiveParts.join('_');
                
                // Nettoyer et formater
                readableName = readableName.replace(/_/g, ' ');
                readableName = readableName.charAt(0).toUpperCase() + readableName.slice(1);
                readableName = readableName.trim();
                
                // Si le nom est vide ou trop court, utiliser un nom par défaut
                if (readableName.length < 3) {
                    readableName = 'Démo ' + filename.substring(0, 10);
                }
                
                return readableName;
            }
            
            return `
                <div class="table-row ${isEven ? 'even' : 'odd'}" data-id="${demoId}" data-type="${sourceType}" data-filename="${demo.filename}" data-technical-name="${demo.technicalName || ''}">
                    <div class="row-info">
                        <div class="row-title" title="${demoName}">${demoName}</div>
                        <div class="row-meta">${new Date(demoDate).toLocaleDateString()}</div>
                    </div>
                    <div class="row-actions">
                        <button class="action-btn btn-play" onclick="window.DEMO_MANAGER.playDemo('${demoId}', '${sourceType}', this)" title="Lecture">
                            ▶️
                        </button>
                        <button class="action-btn btn-edit" onclick="window.DEMO_MANAGER.editDemo('${demoId}', '${demoName}', '${sourceType}')" title="Modifier">
                            ✏️
                        </button>
                        <button class="action-btn btn-move" onclick="window.DEMO_MANAGER.moveDemo('${demoId}', '${demoName}', '${sourceType}')" title="Déplacer">
                            📁
                        </button>
                        <button class="action-btn btn-delete" onclick="window.DEMO_MANAGER.deleteDemo('${demoId}', '${sourceType}')" title="Supprimer">
                            🗑️
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    },
    
    playDemo: function(demoId, sourceType, buttonElement = null) {
        console.log(`▶️ Lecture de ${demoId} depuis ${sourceType}`);
        
        // Arrêter toute lecture en cours
        if (window.stopAllPlayback) {
            window.stopAllPlayback();
        }
        
        // Trouver la démo dans les données du tableau pour obtenir le bon filename
        const tableContainer = document.querySelector(`#${sourceType}Table .table-content`);
        if (!tableContainer) {
            console.error('Tableau non trouvé:', sourceType);
            return;
        }
        
        // Chercher par nom technique d'abord, puis par ID
        let demoRow = tableContainer.querySelector(`[data-technical-name="${demoId}"][data-type="${sourceType}"]`);
        if (!demoRow) {
            demoRow = tableContainer.querySelector(`[data-id="${demoId}"][data-type="${sourceType}"]`);
        }
        
        if (!demoRow) {
            console.error('Démo non trouvée dans le tableau:', demoId);
            return;
        }
        
        // Récupérer les données de la démo depuis l'attribut data
        const demoData = demoRow.dataset;
        const filename = demoData.filename || demoId;
        
        // Charger le fichier JSON de la démo
        let demoFile = '';
        if (sourceType === 'brouillon') {
            demoFile = `demospubliques/brouillon/${filename}`;
        } else {
            demoFile = `demospubliques/${sourceType}/${filename}`;
        }
        
        console.log(`📁 Tentative de chargement: ${demoFile}`);
        
        // Mettre à jour le bouton pour indiquer la lecture
        if (buttonElement) {
            buttonElement.classList.add('playing');
            buttonElement.innerHTML = '⏸️';
        }
        
        fetch(demoFile)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📄 Données de démo chargées:', data);
                
                if (data && data.data && data.data.recording) {
                    console.log('🔍 Vérification du module RECORDER...');
                    
                    // Utiliser le module RECORDER pour jouer la démo
                    if (window.RECORDER_MODULE && window.RECORDER_MODULE.playRecording) {
                        console.log('✅ Module RECORDER disponible, lancement de la lecture...');
                        
                        // Récupérer le nom de la démo pour l'affichage
                        const demoName = demoRow.querySelector('.row-title')?.textContent || 'Démo';
                        
                        // Mettre à jour l'indicateur de synchronisation si disponible
                        if (window.updateSyncIndicator) {
                            window.currentPlayingDemo = `${demoName} (${sourceType})`;
                            window.currentPlayingButton = buttonElement;
                            window.updateSyncIndicator();
                        }
                        
                        // Lancer la lecture
                        window.RECORDER_MODULE.playRecording(data.data.recording, buttonElement);
                        
                    } else {
                        console.error('❌ Module RECORDER non disponible');
                        alert('Module de lecture non disponible - Vérifiez la console pour plus de détails');
                        
                        // Réinitialiser le bouton
                        if (buttonElement) {
                            buttonElement.classList.remove('playing');
                            buttonElement.innerHTML = '▶️';
                        }
                    }
                } else {
                    console.error('Format de démo invalide:', data);
                    alert('Format de démo non reconnu - données manquantes');
                    
                    // Réinitialiser le bouton
                    if (buttonElement) {
                        buttonElement.classList.remove('playing');
                        buttonElement.innerHTML = '▶️';
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement de la démo:', error);
                alert(`Erreur lors du chargement de la démo: ${error.message}`);
                
                // Réinitialiser le bouton
                if (buttonElement) {
                    buttonElement.classList.remove('playing');
                    buttonElement.innerHTML = '▶️';
                }
            });
    },
    
    stopDemo: function() {
        console.log('⏹️ Arrêt de la lecture');
        
        // Arrêter la lecture via le module RECORDER
        if (window.RECORDER_MODULE && window.RECORDER_MODULE.stopPlayback) {
            window.RECORDER_MODULE.stopPlayback();
        }
    },
    
    editDemo: function(demoId, currentName, sourceType) {
        const newName = prompt('Nouveau nom:', currentName);
        if (newName === null || newName.trim() === '') return;
        
        console.log(`✏️ Modification de ${demoId} vers "${newName}"`);
        
        // Appeler l'API pour modifier le nom
        fetch('demo-manager-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'editDemo',
                demoId: demoId,
                newName: newName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Nom modifié: "${newName}"`, 'success');
                }
                
                // Recharger le tableau correspondant
                setTimeout(() => {
                    this.loadTable(sourceType);
                }, 500);
            } else {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Erreur: ${data.message}`, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la modification:', error);
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Erreur lors de la modification', 'error');
            }
        });
    },
    
    moveDemo: function(demoId, demoName, sourceType) {
        // Créer une interface moderne pour choisir la bibliothèque
        this.showLibrarySelector(demoId, demoName, sourceType);
    },
    
    showLibrarySelector: function(demoId, demoName, sourceType) {
        // Créer le modal de sélection
        const modal = document.createElement('div');
        modal.className = 'library-selector-modal';
        modal.innerHTML = `
            <div class="library-selector-content">
                <div class="library-selector-header">
                    <h3>📁 Déplacer "${demoName}"</h3>
                    <button class="close-btn" onclick="this.closest('.library-selector-modal').remove()">✕</button>
                </div>
                <div class="library-selector-body">
                    <p>Choisissez la bibliothèque de destination :</p>
                    <div class="library-options">
                        <button class="library-option" data-library="brouillon" onclick="window.DEMO_MANAGER.executeMove('${demoId}', '${demoName}', '${sourceType}', 'brouillon', this)">
                            <div class="library-icon">📝</div>
                            <div class="library-info">
                                <div class="library-name">Brouillon</div>
                                <div class="library-desc">Zone de travail temporaire</div>
                            </div>
                        </button>
                        <button class="library-option" data-library="prayers" onclick="window.DEMO_MANAGER.executeMove('${demoId}', '${demoName}', '${sourceType}', 'prayers', this)">
                            <div class="library-icon">🙏</div>
                            <div class="library-info">
                                <div class="library-name">Prayers</div>
                                <div class="library-desc">Bibliothèque de prières</div>
                            </div>
                        </button>
                        <button class="library-option" data-library="bhajans" onclick="window.DEMO_MANAGER.executeMove('${demoId}', '${demoName}', '${sourceType}', 'bhajans', this)">
                            <div class="library-icon">🎶</div>
                            <div class="library-info">
                                <div class="library-name">Bhajans</div>
                                <div class="library-desc">Bibliothèque de chants</div>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="library-selector-footer">
                    <button class="cancel-btn" onclick="this.closest('.library-selector-modal').remove()">Annuler</button>
                </div>
            </div>
        `;
        
        // Ajouter les styles CSS
        if (!document.getElementById('library-selector-styles')) {
            const style = document.createElement('style');
            style.id = 'library-selector-styles';
            style.textContent = `
                .library-selector-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10001;
                    animation: fadeIn 0.3s ease;
                }
                
                .library-selector-content {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    max-width: 500px;
                    width: 90%;
                    animation: slideIn 0.3s ease;
                }
                
                .library-selector-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 20px 25px;
                    border-bottom: 1px solid #e9ecef;
                }
                
                .library-selector-header h3 {
                    margin: 0;
                    color: #2c3e50;
                    font-size: 1.2em;
                }
                
                .close-btn {
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                    color: #6c757d;
                    padding: 5px;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .close-btn:hover {
                    background: #f8f9fa;
                    color: #dc3545;
                }
                
                .library-selector-body {
                    padding: 25px;
                }
                
                .library-selector-body p {
                    margin: 0 0 20px 0;
                    color: #6c757d;
                    font-size: 14px;
                }
                
                .library-options {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }
                
                .library-option {
                    display: flex;
                    align-items: center;
                    padding: 15px;
                    border: 2px solid #e9ecef;
                    border-radius: 10px;
                    background: white;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    text-align: left;
                }
                
                .library-option:hover {
                    border-color: #007bff;
                    background: #f8f9ff;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
                }
                
                .library-option.selected {
                    border-color: #28a745;
                    background: #f8fff9;
                }
                
                .library-icon {
                    font-size: 24px;
                    margin-right: 15px;
                    width: 40px;
                    text-align: center;
                }
                
                .library-info {
                    flex: 1;
                }
                
                .library-name {
                    font-weight: 600;
                    color: #2c3e50;
                    font-size: 16px;
                    margin-bottom: 4px;
                }
                
                .library-desc {
                    color: #6c757d;
                    font-size: 13px;
                }
                
                .library-selector-footer {
                    padding: 20px 25px;
                    border-top: 1px solid #e9ecef;
                    text-align: right;
                }
                
                .cancel-btn {
                    background: #6c757d;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 14px;
                    transition: background 0.2s ease;
                }
                
                .cancel-btn:hover {
                    background: #5a6268;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                @keyframes slideIn {
                    from { transform: translateY(-20px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                
                @media (max-width: 768px) {
                    .library-selector-content {
                        width: 95%;
                        margin: 20px;
                    }
                    
                    .library-selector-header,
                    .library-selector-body,
                    .library-selector-footer {
                        padding: 15px;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Ajouter le modal au DOM
        document.body.appendChild(modal);
        
        // Fermer le modal en cliquant à l'extérieur
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        // Fermer avec la touche Escape
        document.addEventListener('keydown', function closeOnEscape(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', closeOnEscape);
            }
        });
    },
    
    executeMove: function(demoId, demoName, sourceType, targetLibrary, buttonElement) {
        // Ajouter un effet visuel de sélection
        const options = document.querySelectorAll('.library-option');
        options.forEach(opt => opt.classList.remove('selected'));
        buttonElement.classList.add('selected');
        
        console.log(`📁 Déplacement de ${demoId} vers ${targetLibrary}`);
        
        // Fermer le modal
        const modal = document.querySelector('.library-selector-modal');
        if (modal) modal.remove();
        
        // Masquer visuellement la ligne source immédiatement
        const sourceRow = document.querySelector(`[data-id="${demoId}"][data-type="${sourceType}"]`);
        if (sourceRow) {
            sourceRow.style.opacity = '0.3';
            sourceRow.style.backgroundColor = '#e8f5e8';
            sourceRow.style.transition = 'all 0.3s ease';
        }
        
        // Afficher un indicateur de chargement
        if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
            window.SAVE_UPLOAD_MODULE.showNotification(`Déplacement en cours vers ${targetLibrary}...`, 'info');
        }
        
        // Appeler l'API pour déplacer la démo
        fetch('demo-manager-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'moveToLibrary',
                demoId: demoId,
                library: targetLibrary,
                newName: demoName,
                sourceType: sourceType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Démo déplacée vers ${targetLibrary}`, 'success');
                }
                
                // Forcer un rechargement complet après un délai
                // pour s'assurer que les fichiers sont bien synchronisés
                setTimeout(() => {
                    console.log('🔄 Rechargement complet après déplacement...');
                    this.loadAllTables();
                    
                    // Recharger l'interface élève
                    this.reloadStudentInterface();
                }, 500);
            } else {
                // Restaurer l'apparence de la ligne en cas d'erreur
                if (sourceRow) {
                    sourceRow.style.opacity = '1';
                    sourceRow.style.backgroundColor = '';
                }
                
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Erreur: ${data.message}`, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors du déplacement:', error);
            
            // Restaurer l'apparence de la ligne en cas d'erreur
            if (sourceRow) {
                sourceRow.style.opacity = '1';
                sourceRow.style.backgroundColor = '';
            }
            
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Erreur lors du déplacement', 'error');
            }
        });
    },
    
    deleteDemo: function(demoId, sourceType) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) return;
        
        console.log(`🗑️ Suppression de ${demoId} depuis ${sourceType}`);
        
        // Masquer visuellement la ligne immédiatement
        const row = document.querySelector(`[data-id="${demoId}"][data-type="${sourceType}"]`);
        if (row) {
            row.style.opacity = '0.5';
            row.style.backgroundColor = '#ffe6e6';
            row.style.transition = 'all 0.3s ease';
        }
        
        // Appeler l'API pour supprimer la démo
        fetch('demo-manager-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'deleteDemo',
                demoId: demoId,
                sourceType: sourceType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification('Démo supprimée avec succès', 'success');
                }
                
                // Forcer un rechargement complet après un délai
                // pour s'assurer que les fichiers sont bien synchronisés
                setTimeout(() => {
                    console.log('🔄 Rechargement complet après suppression...');
                    this.loadAllTables();
                    
                    // Recharger l'interface élève
                    this.reloadStudentInterface();
                }, 500);
            } else {
                // Restaurer l'apparence de la ligne en cas d'erreur
                if (row) {
                    row.style.opacity = '1';
                    row.style.backgroundColor = '';
                }
                
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Erreur: ${data.message}`, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            
            // Restaurer l'apparence de la ligne en cas d'erreur
            if (row) {
                row.style.opacity = '1';
                row.style.backgroundColor = '';
            }
            
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Erreur lors de la suppression', 'error');
            }
        });
    },
    
        // Fonction globale pour recharger l'interface élève
    reloadStudentInterface: function() {
        console.log('🔄 Rechargement de l\'interface élève...');
        
        // Méthode 1: Fonction directe
        if (typeof loadStudentDemoTables === 'function') {
            loadStudentDemoTables();
            return;
        }
        
        // Méthode 2: Événement personnalisé
        if (window.dispatchEvent) {
            window.dispatchEvent(new CustomEvent('reloadStudentTables'));
            return;
        }
        
        // Méthode 3: Rechargement forcé de la page élève
        console.log('🔄 Tentative de rechargement forcé...');
        try {
            // Essayer de recharger la page élève si elle est dans un autre onglet
            if (window.opener && window.opener.location.href.includes('index.php')) {
                window.opener.location.reload();
            }
            // Essayer de recharger les frames
            const studentFrames = window.frames;
            for (let i = 0; i < studentFrames.length; i++) {
                try {
                    if (studentFrames[i].location.href.includes('index.php')) {
                        studentFrames[i].location.reload();
                    }
                } catch (e) {
                    // Ignorer les erreurs de sécurité cross-origin
                }
            }
        } catch (e) {
            console.log('⚠️ Impossible de recharger l\'interface élève automatiquement');
        }
    },

    editDemo: function(demoId, currentName, sourceType) {
        const newName = prompt('Nouveau nom:', currentName);
        if (newName === null || newName.trim() === '') return;
        
        console.log(`✏️ Modification de ${demoId} vers "${newName}"`);
        
        // Appeler l'API pour modifier le nom
        fetch('demo-manager-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'editDemo',
                demoId: demoId,
                newName: newName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Nom modifié: "${newName}"`, 'success');
                }
                
                // Recharger le tableau correspondant
                setTimeout(() => {
                    this.loadTable(sourceType);
                }, 500);
                
                // Recharger l'interface élève
                setTimeout(() => {
                    this.reloadStudentInterface();
                }, 1000);
            } else {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Erreur: ${data.message}`, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la modification:', error);
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Erreur lors de la modification', 'error');
            }
        });
    },
    
    moveDemo: function(demoId, demoName, sourceType) {
        // Créer une interface moderne pour choisir la bibliothèque
        this.showLibrarySelector(demoId, demoName, sourceType);
    },
    
    deleteDemo: function(demoId, sourceType) {
        if (!confirm(`Êtes-vous sûr de vouloir supprimer "${demoId}" ?\n\nCette action est irréversible.`)) {
            return;
        }
        
        console.log(`🗑️ Suppression de ${demoId} depuis ${sourceType}`);
        
        // Appeler l'API pour supprimer
        fetch('demo-manager-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'deleteDemo',
                demoId: demoId,
                sourceType: sourceType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Démo supprimée: "${demoId}"`, 'success');
                }
                
                // Recharger le tableau correspondant
                setTimeout(() => {
                    this.loadTable(sourceType);
                }, 500);
                
                // Recharger l'interface élève
                setTimeout(() => {
                    this.reloadStudentInterface();
                }, 1000);
            } else {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Erreur: ${data.message}`, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la suppression:', error);
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Erreur lors de la suppression', 'error');
            }
        });
    },

    setupEventListeners: function() {
         // Écouter les clics sur les en-têtes de tableaux pour recharger
         document.querySelectorAll('.table-header').forEach(header => {
             header.addEventListener('click', function() {
                 const tableType = this.closest('.demo-table-container').querySelector('.demo-table').id.replace('Table', '');
                 window.DEMO_MANAGER.loadTable(tableType);
             });
         });
         
         // SYNCHRONISATION DÉSACTIVÉE POUR TEST - CONFLIT AVEC index-admin.php
         // this.ensureButtonsAccessible();
     },
     
           ensureButtonsAccessible: function() {
          // SYNCHRONISATION DÉSACTIVÉE POUR TEST - CONFLIT AVEC index-admin.php
          /*
          // Fermer tous les menus dropdown quand on clique sur un bouton de lecture
          document.addEventListener('click', function(e) {
              if (e.target.classList.contains('btn-play') || e.target.closest('.btn-play')) {
                  // Fermer tous les menus dropdown
                  document.querySelectorAll('.menu-dropdown.show').forEach(dropdown => {
                      dropdown.classList.remove('show');
                  });
              }
          });
          
          // S'assurer que les boutons de lecture ont un z-index élevé
          setInterval(() => {
              document.querySelectorAll('.btn-play').forEach(btn => {
                  if (btn.style.zIndex !== '10002') {
                      btn.style.zIndex = '10002';
                      btn.style.position = 'relative';
                  }
              });
          }, 1000);
          
          // Synchroniser avec les menus dropdown
          this.syncWithDropdownMenus();
          */
      },
      
             // syncWithDropdownMenus: function() {
             // SYNCHRONISATION DÉSACTIVÉE POUR TEST - CONFLIT AVEC index-admin.php
             // },
       
   
    
    resetKeyboardPlayerBar: function() {
           // Réinitialiser le bouton play de la barre de lecture sous le clavier
           const playBtn = document.getElementById('playBtn');
           if (playBtn) {
               playBtn.classList.remove('playing');
               playBtn.innerHTML = '▶ Écouter';
               playBtn.disabled = true; // Désactiver car pas d'enregistrement en cours
           }
           
           // Réinitialiser le bouton stop
           const stopBtn = document.getElementById('stopBtn');
           if (stopBtn) {
               stopBtn.disabled = true;
           }
           
           // Réinitialiser le compteur de temps
           const timeCounter = document.getElementById('timeCounter');
           if (timeCounter) {
               timeCounter.textContent = '00:00';
           }
       }
};

// Initialisation gérée par index-admin.php pour éviter les conflits
console.log('🎯 DEMO_MANAGER défini, en attente d\'initialisation par index-admin.php');
</script>
