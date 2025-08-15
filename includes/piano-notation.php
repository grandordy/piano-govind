<!-- /includes/piano-notation.php -->
<!-- Module de notation pour afficher les noms des notes sur le piano mais le module choix n'est pas encore créé -->

<style>
/* Contrôles de notation */
.notation-controls {
    text-align: center;
    margin-bottom: 20px;
}

.notation-btn {
    padding: 10px 20px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0 5px;
}

.notation-btn:hover {
    background: #2980b9;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.notation-status {
    display: inline-block;
    margin-left: 10px;
    padding: 5px 15px;
    background: #e9ecef;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
}

/* Labels spécifiques pour le mode repères Do */
.key-label.do-marker {
    color: #e74c3c !important;
    font-size: 16px !important;
    font-weight: bold !important;
}

/* Animation de changement */
.key-label.fade-in {
    animation: labelFadeIn 0.3s ease;
}

@keyframes labelFadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Bouton supprimé - Fonctionnalité déplacée dans le menu KEY ASSIST -->

<script>
// Module de notation pour Piano Virtuel
const NOTATION_MODULE = (function() {
    'use strict';
    
    // Configuration
    const config = {
        currentMode: 0, // 0: aucune, 1: anglaise, 2: française, 3: repères Do
        modes: ['Aucune', 'Anglaise (C)', 'Française (Do)', 'Repères Do (C)'],
        frenchNames: {
            'C': 'Do', 'C#': 'Do#', 'D': 'Ré', 'D#': 'Ré#', 
            'E': 'Mi', 'F': 'Fa', 'F#': 'Fa#', 'G': 'Sol', 
            'G#': 'Sol#', 'A': 'La', 'A#': 'La#', 'B': 'Si'
        }
    };
    
    // Mettre à jour l'affichage des labels
    function updateLabels() {
        const keys = document.querySelectorAll('.key');
        
        keys.forEach(key => {
            const noteId = key.dataset.note;
            if (!noteId) return;
            
            const note = noteId.slice(0, -1); // Note sans octave
            const octave = noteId.slice(-1);  // Numéro d'octave
            let label = key.querySelector('.key-label');
            
            // Créer le label s'il n'existe pas
            if (!label) {
                label = document.createElement('div');
                label.className = 'key-label';
                key.appendChild(label);
            }
            
            // Réinitialiser les classes
            label.classList.remove('do-marker', 'fade-in');
            
            switch(config.currentMode) {
                case 0: // Aucune
                    label.style.display = 'none';
                    break;
                    
                case 1: // Anglaise
                    label.style.display = 'block';
                    label.textContent = note;
                    label.classList.add('fade-in');
                    break;
                    
                case 2: // Française
                    label.style.display = 'block';
                    label.textContent = config.frenchNames[note] || note;
                    label.classList.add('fade-in');
                    break;
                    
                case 3: // Repères Do uniquement
                    if (note === 'C') {
                        label.style.display = 'block';
                        label.textContent = 'C' + octave;
                        label.classList.add('do-marker', 'fade-in');
                    } else {
                        label.style.display = 'none';
                    }
                    break;
            }
        });
        
        // Mettre à jour le statut
        updateStatus();
    }
    
    // Mettre à jour l'affichage du statut
    function updateStatus() {
        const statusEl = document.getElementById('notationStatus');
        if (statusEl) {
            statusEl.textContent = `Notation : ${config.modes[config.currentMode]}`;
        }
    }
    
    // Changer de mode (faire tourner)
    function toggleNotation() {
        config.currentMode = (config.currentMode + 1) % 4;
        updateLabels();
        
        // Sauvegarder la préférence
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem('pianoNotationMode', config.currentMode);
        }
        
        console.log(`Notation changée : ${config.modes[config.currentMode]}`);
    }
    
    // Définir un mode spécifique
    function setNotation(mode) {
        if (mode >= 0 && mode <= 3) {
            config.currentMode = mode;
            updateLabels();
        }
    }
    
    // Obtenir l'état actuel
    function getState() {
        return {
            mode: config.currentMode,
            modeName: config.modes[config.currentMode]
        };
    }
    
    // Exporter les noms de notes
    function getNoteNames(language = 'english') {
        if (language === 'french') {
            return config.frenchNames;
        }
        return null;
    }
    
    // Initialisation
    function init() {
        console.log('🎵 Initialisation du module Notation...');
        
        // Charger la préférence sauvegardée
        if (typeof(Storage) !== "undefined") {
            const savedMode = localStorage.getItem('pianoNotationMode');
            if (savedMode !== null) {
                config.currentMode = parseInt(savedMode);
            }
        }
        
        // Attendre que le piano soit créé
        setTimeout(() => {
            updateLabels();
            
            // Ajouter les raccourcis clavier
            document.addEventListener('keydown', (e) => {
                if (e.altKey && !e.repeat) {
                    switch(e.key) {
                        case 'n':
                        case 'N':
                            e.preventDefault();
                            toggleNotation();
                            break;
                        case '0':
                            e.preventDefault();
                            setNotation(0);
                            break;
                        case '1':
                            e.preventDefault();
                            setNotation(1);
                            break;
                        case '2':
                            e.preventDefault();
                            setNotation(2);
                            break;
                        case '3':
                            e.preventDefault();
                            setNotation(3);
                            break;
                    }
                }
            });
            
            console.log('✅ Module Notation prêt !');
        }, 600);
    }
    
    // API publique
    return {
        init: init,
        toggleNotation: toggleNotation,
        setNotation: setNotation,
        getState: getState,
        getNoteNames: getNoteNames
    };
    
})();

// Exporter le module (mais ne pas l'initialiser automatiquement)
window.NOTATION_MODULE = NOTATION_MODULE;

// Initialisation automatique plus robuste
function initNotationModule() {
    if (window.NOTATION_MODULE && !window.NOTATION_MODULE.initialized) {
        console.log('🎵 Initialisation automatique du module NOTATION...');
        window.NOTATION_MODULE.init();
        window.NOTATION_MODULE.initialized = true;
    }
}

// Initialisation au chargement du DOM
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(initNotationModule, 100);
});

// Initialisation au chargement complet de la page
window.addEventListener('load', () => {
    setTimeout(initNotationModule, 500);
});

// Initialisation immédiate si le DOM est déjà chargé
if (document.readyState === 'loading') {
    // Le DOM est encore en cours de chargement
} else {
    // Le DOM est déjà chargé
    setTimeout(initNotationModule, 100);
}
</script>