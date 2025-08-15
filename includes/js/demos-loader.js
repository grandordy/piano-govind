// includes/js/demos-loader.js
// Module pour charger les démos publiques dans les menus Prayers et Bhajans

const DEMOS_LOADER = (function() {
    'use strict';
    
    const config = {
        demosEndpoint: 'demospubliques/demos.json',
        demos: [],
        initialized: false
    };
    
    // Charger toutes les démos publiques
    async function loadAllDemos() {
        try {
            const response = await fetch(config.demosEndpoint + '?t=' + Date.now());
            if (response.ok) {
                config.demos = await response.json();
                console.log(`📚 ${config.demos.length} démos chargées`);
                return config.demos;
            } else {
                console.log('Aucune démo publique disponible');
                config.demos = [];
                return [];
            }
        } catch (error) {
            console.log('Erreur lors du chargement des démos:', error);
            config.demos = [];
            return [];
        }
    }
    
    // Obtenir les démos par catégorie
    function getDemosByCategory(category) {
        const filtered = config.demos.filter(demo => demo.category === category);
        console.log(`📚 Démos pour ${category}:`, filtered.length, filtered);
        return filtered;
    }
    
    // Créer le contenu HTML pour un menu de démos
    function createDemosMenuContent(category) {
        const demos = getDemosByCategory(category);
        
        if (demos.length === 0) {
            return `
                <div class="menu-section">
                    <div class="menu-item disabled">
                        <span>Aucune démo disponible</span>
                    </div>
                </div>
            `;
        }
        
        return `
            <div class="menu-section">
                ${demos.map(demo => `
                    <div class="menu-item demo-item" onclick="DEMOS_LOADER.playDemo('${demo.filename}')" title="Cliquer pour écouter immédiatement">
                        <div class="demo-item-info">
                            <div class="demo-title">${demo.name}</div>
                            <div class="demo-meta">${demo.noteCount} notes | ${Math.ceil(demo.duration / 1000)}s</div>
                            ${demo.description ? `<div class="demo-desc">${demo.description}</div>` : ''}
                        </div>
                        <div class="demo-item-play">▶ Écouter</div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    // Mettre à jour les menus de démos
    function updateDemosMenus() {
        // Les menus Prayers et Bhajans ont été supprimés
        // Les démos sont maintenant affichées dans les tableaux sous le clavier
        console.log('Menus de démos supprimés - utilisation des tableaux sous le clavier');
    }
    
    // Jouer une démo (lecture automatique immédiate)
    async function playDemo(filename) {
        try {
            const response = await fetch(`demospubliques/${filename}`);
            if (response.ok) {
                const demo = await response.json();
                
                // Fermer le menu immédiatement pour dégager la vue
                if (window.closeDropdown) {
                    // Les menus Prayers et Bhajans ont été supprimés
                    console.log('Menus fermés - utilisation des tableaux sous le clavier');
                }
                
                // Lecture automatique immédiate
                console.log(`🎵 Lecture automatique de "${demo.name}"`);
                playDemoRecording(demo);
                
            } else {
                console.error('Erreur lors du chargement de la démo');
            }
        } catch (error) {
            console.error('Erreur lors de la lecture:', error);
        }
    }
    
    // Jouer l'enregistrement d'une démo (version optimisée)
    function playDemoRecording(demo) {
        // Arrêter toute lecture en cours
        stopPlayback();
        
        // Marquer qu'on joue depuis un menu
        isPlayingFromMenu = true;
        
        // Nettoyer les touches actives
        document.querySelectorAll('.key.active').forEach(key => {
            if (window.stopNote) {
                window.stopNote(key.dataset.note, key);
            }
        });
        
        // Afficher un indicateur de lecture
        showPlaybackIndicator(demo.name);
        
        // Jouer les notes avec un délai minimal pour la réactivité
        if (demo.data && demo.data.recording) {
            const recording = demo.data.recording;
            let currentTime = 0;
            
            recording.notes.forEach((note, index) => {
                const duration = recording.durations[index] || 500;
                
                // Note ON
                const noteOnTimeout = setTimeout(() => {
                    if (isPlayingFromMenu) { // Vérifier si on joue toujours
                        const key = document.querySelector(`.key[data-note="${note}"]`);
                        if (key && window.playNote) {
                            window.playNote(note, key);
                        }
                    }
                }, currentTime);
                currentPlaybackTimeouts.push(noteOnTimeout);
                
                // Note OFF
                const noteOffTimeout = setTimeout(() => {
                    if (isPlayingFromMenu) { // Vérifier si on joue toujours
                        const key = document.querySelector(`.key[data-note="${note}"]`);
                        if (key && window.stopNote) {
                            window.stopNote(key.dataset.note, key);
                        }
                    }
                }, currentTime + duration);
                currentPlaybackTimeouts.push(noteOffTimeout);
                
                currentTime += duration;
            });
            
            // Arrêt automatique
            const autoStopTimeout = setTimeout(() => {
                if (isPlayingFromMenu) {
                    stopPlayback();
                }
            }, currentTime + 500);
            currentPlaybackTimeouts.push(autoStopTimeout);
        }
    }
    
    // Arrêter la lecture depuis les menus dropdown
    function stopPlayback() {
        console.log('⏹️ Arrêt de la lecture depuis menu dropdown');
        
        // Marquer qu'on ne joue plus
        isPlayingFromMenu = false;
        
        // Annuler tous les timeouts
        currentPlaybackTimeouts.forEach(timeout => clearTimeout(timeout));
        currentPlaybackTimeouts = [];
        
        // Nettoyer les touches actives
        document.querySelectorAll('.key.active').forEach(key => {
            if (window.stopNote) {
                window.stopNote(key.dataset.note, key);
            }
        });
        
        // Masquer l'indicateur
        hidePlaybackIndicator();
        
        // Réinitialiser la barre de lecture sous le clavier
        resetKeyboardPlayerBar();
    }
    
    // Réinitialiser la barre de lecture sous le clavier
    function resetKeyboardPlayerBar() {
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
    
    // Variables globales pour gérer la lecture
    let currentPlaybackTimeouts = [];
    let isPlayingFromMenu = false;
    
    // Afficher un indicateur de lecture
    function showPlaybackIndicator(demoName) {
        // Créer ou mettre à jour l'indicateur
        let indicator = document.getElementById('playback-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'playback-indicator';
            indicator.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(40, 167, 69, 0.9);
                color: white;
                padding: 10px 20px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            `;
            document.body.appendChild(indicator);
        }
        indicator.textContent = `🎵 Lecture: ${demoName}`;
        indicator.style.display = 'block';
    }
    
    // Masquer l'indicateur de lecture
    function hidePlaybackIndicator() {
        const indicator = document.getElementById('playback-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }
    
    // Initialisation
    async function init() {
        if (config.initialized) return;
        
        console.log('📚 Initialisation du module Demos Loader...');
        
        // Charger les démos
        await loadAllDemos();
        
        // Mettre à jour les menus
        updateDemosMenus();
        
        // Écouter les mises à jour
        window.addEventListener('demosUpdated', async () => {
            console.log('🔄 Mise à jour des menus de démos...');
            await loadAllDemos();
            updateDemosMenus();
        });
        
        config.initialized = true;
        console.log('✅ Module Demos Loader prêt !');
    }
    
    // API publique
    return {
        init: init,
        loadAllDemos: loadAllDemos,
        getDemosByCategory: getDemosByCategory,
        updateDemosMenus: updateDemosMenus,
        playDemo: playDemo,
        stopPlayback: stopPlayback
    };
})();

// Exporter globalement
window.DEMOS_LOADER = DEMOS_LOADER;
