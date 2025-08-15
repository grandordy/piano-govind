<!-- /includes/piano-save-upload.php -->
<!-- Module Save/Upload SÉCURISÉ - Suppression de l'upload direct vers les bibliothèques publiques -->
<!-- Tous les enregistrements vont maintenant dans "brouillon" par défaut -->
<!-- Gestion uniquement via le tableau admin "🎵 Bibliothèques de démos" -->

<style>
/* Notifications */
.save-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.3s ease;
    z-index: 1000;
    display: flex;
    align-items: center;
    gap: 10px;
}

.save-notification.show {
    opacity: 1;
    transform: translateY(0);
}

.save-notification.success {
    border-left: 4px solid #28a745;
}

.save-notification.error {
    border-left: 4px solid #dc3545;
}

.save-notification.info {
    border-left: 4px solid #17a2b8;
}

/* Liste des démos */
.demos-panel {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 2px;
    margin-top: 2px;
}

.demos-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.demos-panel-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

.btn-refresh {
    background: #17a2b8;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}

.demos-grid {
    display: grid;
    gap: 10px;
    max-height: 400px;
    overflow-y: auto;
}

.demo-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.demo-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.demo-info h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-size: 16px;
}

.demo-meta {
    font-size: 12px;
    color: #6c757d;
}

.demo-actions {
    display: flex;
    gap: 5px;
}

.btn-demo {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-demo-play {
    background: #28a745;
    color: white;
}

.btn-demo-download {
    background: #007bff;
    color: white;
}

.btn-demo-delete {
    background: #dc3545;
    color: white;
}

.btn-demo:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.empty-demos {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

/* Onglets des catégories */
.demos-tabs {
    display: flex;
    gap: 2px;
    margin-bottom: 15px;
    background: #e9ecef;
    border-radius: 8px;
    padding: 3px;
}

.tab-btn {
    flex: 1;
    padding: 10px 15px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #6c757d;
}

.tab-btn:hover {
    background: rgba(255, 255, 255, 0.7);
}

.tab-btn.active {
    background: white;
    color: #2c3e50;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Compteur de démos par catégorie */
.tab-btn .count {
    background: #007bff;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 11px;
    margin-left: 5px;
}
</style>

<!-- Ancien tableau supprimé - Remplacé par le nouveau Gestionnaire de Brouillons -->

<script>
// Module Save/Upload SÉCURISÉ
const SAVE_UPLOAD_MODULE = (function() {
    'use strict';
    
    const config = {
        demosEndpoint: 'demospubliques/demos.json',
        demos: [],
        currentCategory: 'prayers',
        initialized: false
    };
    
    // Afficher une notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `save-notification ${type}`;
        
        const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️';
        notification.innerHTML = `<span>${icon}</span><span>${message}</span>`;
        
        document.body.appendChild(notification);
        
        requestAnimationFrame(() => {
            notification.classList.add('show');
        });
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Sauvegarder un enregistrement (NOUVEAU SYSTÈME SÉCURISÉ)
    function save(recording) {
        if (!recording) {
            showNotification('Aucun enregistrement à sauvegarder', 'error');
            return;
        }
        
        // Enregistrement direct dans "brouillon" via la nouvelle API
        saveToBrouillon(recording);
    }
    
    // Sauvegarder dans "brouillon" (NOUVEAU SYSTÈME SÉCURISÉ)
    async function saveToBrouillon(recording) {
        try {
            // Demander le nom de la démo
            const demoName = prompt('Nom de la démo :', recording.name || 'Ma mélodie');
            if (!demoName) {
                showNotification('Enregistrement annulé', 'info');
                return;
            }
            
            // Utiliser la nouvelle API "brouillon"
            const response = await fetch('demo-manager-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'saveNewDemo',
                    demoData: {
                        name: demoName,
                        category: 'brouillon', // TOUJOURS "brouillon" par défaut
                        description: '',
                        recording: recording,
                        uploadedAt: new Date().toISOString(),
                        noteCount: recording.notes ? recording.notes.length : 0,
                        duration: recording.duration || 0
                    },
                    originalName: demoName,
                    library: 'brouillon' // Force l'enregistrement dans "brouillon"
                })
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    showNotification('✅ Démo enregistrée dans "Brouillon" !', 'success');
                    
                    // Rafraîchir l'interface du gestionnaire si elle existe
                    if (window.DEMO_MANAGER) {
                        window.DEMO_MANAGER.loadDemosBrouillon();
                    }
                    
                    // Déclencher un événement pour rafraîchir les menus
                    window.dispatchEvent(new CustomEvent('demosUpdated'));
                } else {
                    throw new Error(result.message || 'Erreur lors de l\'enregistrement');
                }
            } else {
                throw new Error('Erreur serveur');
            }
        } catch (error) {
            console.error('Erreur enregistrement:', error);
            showNotification('❌ Erreur lors de l\'enregistrement', 'error');
        }
    }
    
    // Charger les démos publiques
    async function loadDemos() {
        try {
            const response = await fetch(config.demosEndpoint + '?t=' + Date.now());
            if (response.ok) {
                config.demos = await response.json();
                updateDemosGrid();
                updateTabCounts();
            } else {
                // Si le fichier n'existe pas, créer un tableau vide
                config.demos = [];
                updateDemosGrid();
                updateTabCounts();
            }
        } catch (error) {
            console.log('Pas de démos publiques pour le moment');
            config.demos = [];
            updateDemosGrid();
            updateTabCounts();
        }
    }
    
    // Changer d'onglet
    function switchTab(category) {
        config.currentCategory = category;
        
        // Mettre à jour les boutons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-category="${category}"]`).classList.add('active');
        
        // Mettre à jour l'affichage
        updateDemosGrid();
        updateTabCounts();
    }
    
    // Mettre à jour les compteurs des onglets
    function updateTabCounts() {
        const categories = ['prayers', 'bhajans', 'brouillon']; // "test" devient "brouillon"
        
        categories.forEach(category => {
            const count = config.demos.filter(demo => demo.category === category).length;
            const btn = document.querySelector(`[data-category="${category}"]`);
            if (btn) {
                let countSpan = btn.querySelector('.count');
                if (!countSpan) {
                    countSpan = document.createElement('span');
                    countSpan.className = 'count';
                    btn.appendChild(countSpan);
                }
                countSpan.textContent = count;
            }
        });
    }
    
    // Mettre à jour la grille
    function updateDemosGrid() {
        const grid = document.getElementById('demosGrid');
        if (!grid) return;
        
        // Filtrer par catégorie actuelle
        const categoryDemos = config.demos.filter(demo => demo.category === config.currentCategory);
        
        if (categoryDemos.length === 0) {
            grid.innerHTML = `<div class="empty-demos">Aucune démo dans la catégorie "${config.currentCategory}"</div>`;
            return;
        }
        
        grid.innerHTML = categoryDemos.map(demo => `
            <div class="demo-card" data-demo-id="${demo.id || ''}" data-filename="${demo.filename}">
                <div class="demo-info">
                    <h4>${demo.name}</h4>
                    <div class="demo-meta">
                        ${demo.noteCount || 0} notes | ${Math.ceil((demo.duration || 0) / 1000)}s
                        ${demo.description ? `<br><small>${demo.description}</small>` : ''}
                    </div>
                </div>
                <div class="demo-actions">
                    <button class="btn-demo btn-demo-play" onclick="SAVE_UPLOAD_MODULE.playDemo('${demo.filename}')" title="Lecture">
                        ▶
                    </button>
                    <button class="btn-demo btn-demo-download" onclick="SAVE_UPLOAD_MODULE.downloadDemo('${demo.filename}')" title="Télécharger">
                        💾
                    </button>
                    <button class="btn-demo btn-demo-delete" onclick="SAVE_UPLOAD_MODULE.deleteDemo('${demo.filename}', '${demo.name}')" title="Supprimer">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    // Jouer une démo
    async function playDemo(filename) {
        try {
            const response = await fetch(`demospubliques/${filename}`);
            if (response.ok) {
                const demo = await response.json();
                if (window.RECORDER_MODULE) {
                    RECORDER_MODULE.stop();
                }
                showNotification(`Lecture de "${demo.name}"`, 'info');
                playDemoRecording(demo);
            }
        } catch (error) {
            showNotification('Erreur lors de la lecture', 'error');
        }
    }
    
    // Jouer l'enregistrement d'une démo
    function playDemoRecording(demo) {
        const timeouts = [];
        
        demo.notes.forEach(event => {
            const timeout = setTimeout(() => {
                const key = document.querySelector(`.key[data-note="${event.note}"]`);
                if (key) {
                    if (event.type === 'on') {
                        window.playNote(event.note, key);
                    } else {
                        window.stopNote(event.note, key);
                    }
                }
            }, event.time);
            timeouts.push(timeout);
        });
        
        // Arrêt automatique
        setTimeout(() => {
            timeouts.forEach(t => clearTimeout(t));
            document.querySelectorAll('.key.active').forEach(key => {
                window.stopNote(key.dataset.note, key);
            });
        }, demo.duration + 500);
    }
    
    // Télécharger une démo
    function downloadDemo(filename) {
        const a = document.createElement('a');
        a.href = `demospubliques/${filename}`;
        a.download = filename;
        a.click();
    }
    
    // Supprimer une démo
    async function deleteDemo(filename, demoName) {
        if (!confirm(`Êtes-vous sûr de vouloir supprimer "${demoName}" ?`)) {
            return;
        }
        
        try {
            // Trouver la démo dans la liste pour obtenir son ID
            const demo = config.demos.find(d => d.filename === filename);
            if (!demo) {
                showNotification('Démo non trouvée', 'error');
                return;
            }
            
            // Utiliser la nouvelle API de suppression
            const formData = new FormData();
            formData.append('action', 'deleteDemo');
            formData.append('demoId', demo.id || filename); // Fallback sur filename si pas d'ID
            
            const response = await fetch('demo-manager-api.php', {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    showNotification(`Démo "${demoName}" supprimée avec succès`, 'success');
                    // Recharger la liste des démos
                    loadDemos();
                } else {
                    showNotification(`Erreur: ${result.message}`, 'error');
                }
            } else {
                showNotification('Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            console.error('Erreur suppression:', error);
            showNotification('Erreur lors de la suppression', 'error');
        }
    }
    
    // Initialisation
    function init() {
        if (config.initialized) return;
        
        console.log('💾 Initialisation du module Save/Upload SÉCURISÉ...');
        
        // Charger les démos (sans afficher d'erreur)
        loadDemos();
        
        // Event global pour les mises à jour
        window.addEventListener('demosUpdated', loadDemos);
        
        config.initialized = true;
        console.log('✅ Module Save/Upload SÉCURISÉ prêt !');
    }
    
    // API publique
    return {
        init: init,
        save: save,
        showNotification: showNotification,
        loadDemos: loadDemos,
        playDemo: playDemo,
        downloadDemo: downloadDemo,
        deleteDemo: deleteDemo,
        switchTab: switchTab
    };
})();

// Exposer globalement IMMÉDIATEMENT
window.SAVE_UPLOAD_MODULE = SAVE_UPLOAD_MODULE;

// Initialiser après un délai pour éviter les conflits
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        try {
            SAVE_UPLOAD_MODULE.init();
            console.log('✅ Module Save/Upload SÉCURISÉ initialisé');
        } catch (error) {
            console.error('Erreur init Save/Upload:', error);
        }
    }, 2500);
});
</script>