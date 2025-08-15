// includes/js/menu-handlers.js
// JavaScript centralisé pour les menus - Utilisé par index.php et index-admin.php

// État des menus
let currentDropdown = null;

// Fonction simple pour ouvrir/fermer les menus
function toggleDropdown(menuName) {
    const dropdown = document.getElementById(menuName + '-dropdown');
    
    if (!dropdown) {
        console.error('Menu non trouvé:', menuName);
        return;
    }
    
    // Fermer l'ancien menu si différent
    if (currentDropdown && currentDropdown !== menuName) {
        const oldDropdown = document.getElementById(currentDropdown + '-dropdown');
        if (oldDropdown) oldDropdown.classList.remove('show');
    }
    
    // Toggle le menu actuel
    if (dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
        currentDropdown = null;
    } else {
        dropdown.classList.add('show');
        currentDropdown = menuName;
    }
}

// Fermer un dropdown par la croix
function closeDropdown(menuName) {
    const dropdown = document.getElementById(menuName + '-dropdown');
    if (dropdown) {
        dropdown.classList.remove('show');
    }
    if (currentDropdown === menuName) {
        currentDropdown = null;
    }
}

// Fonction pour KEY ASSIST avec les 5 modes
function selectKeyMode(mode) {
    console.log('🎹 Mode KEY ASSIST sélectionné:', mode);
    
    // S'assurer que le module NOTATION est disponible
    if (!window.NOTATION_MODULE) {
        console.error('❌ Module NOTATION non disponible');
        alert('Erreur: Module de notation non chargé');
        return;
    }
    
    // Nettoyer les éléments custom des modes précédents
    document.querySelectorAll('.octave-marker').forEach(m => m.remove());
    
    // Nettoyer les styles des touches au cas où
    document.querySelectorAll('.key').forEach(key => {
        if (key.style.position === 'relative') {
            key.style.position = '';
        }
    });
    
    const piano = document.querySelector('.piano-container');
    const pianoElement = document.querySelector('.piano');
    
    if (piano) {
        piano.style.background = '';
        piano.style.animation = '';
        piano.classList.remove('aurora-active');
    }
    
    // Nettoyer le position relative sur le piano
    if (pianoElement && pianoElement.style.position === 'relative') {
        pianoElement.style.position = '';
    }
    
    // Appliquer le nouveau mode
    switch(mode) {
        case 'none':
            window.NOTATION_MODULE.setNotation(0); // Mode 0: aucune
            console.log('✅ Mode: Aucun label');
            break;
            
        case 'labels-en':
            window.NOTATION_MODULE.setNotation(1); // Mode 1: anglaise
            console.log('✅ Mode: Notes en anglais');
            break;
            
        case 'labels-fr':
            window.NOTATION_MODULE.setNotation(2); // Mode 2: française
            console.log('✅ Mode: Notes en français');
            break;
            
        case 'octaves':
            window.NOTATION_MODULE.setNotation(3); // Mode 3: repères Do uniquement
            console.log('✅ Mode: Marqueurs d\'octaves');
            break;
            
        default:
            console.error('❌ Mode inconnu:', mode);
            return;
    }
    
    // Mettre à jour les toggles visuels
    document.querySelectorAll('.toggle-switch').forEach(toggle => {
        toggle.classList.remove('on');
    });
    const activeToggle = document.getElementById('toggle-' + mode);
    if (activeToggle) {
        activeToggle.classList.add('on');
    }
    
    // Fermer le dropdown après sélection
    closeDropdown('keyassist');
}

// Fonction pour les démos
function loadDemo(demoId) {
    console.log('Chargement démo:', demoId);
    closeDropdown(currentDropdown);
    // À implémenter : charger et jouer la séquence de notes
}

// Fonction pour le son
function selectSound(soundId) {
    console.log('Son sélectionné:', soundId);
    
    // Fermer le menu
    closeDropdown('sound');
    
    // Changer le preset via le module harmonium
    if (window.HARMONIUM_MODULE && window.HARMONIUM_MODULE.changePreset) {
        window.HARMONIUM_MODULE.changePreset(soundId);
    } else {
        console.error('Module harmonium non disponible');
    }
}

// Fonction pour changer le périphérique MIDI
function changeMidiDevice(deviceId) {
    console.log('Périphérique MIDI sélectionné:', deviceId);
    // Connecter au module MIDI si disponible
    if (window.MIDI_MODULE && window.MIDI_MODULE.selectDevice) {
        window.MIDI_MODULE.selectDevice(deviceId);
    }
}

// Fonction pour changer le volume
function changeVolume(value) {
    const volumeDisplay = document.getElementById('volume-display');
    if (volumeDisplay) {
        volumeDisplay.textContent = value + '%';
    }
    
    const volume = value / 100;
    console.log('Volume:', value + '%');
    
    // S'assurer que l'audioContext est initialisé
    if (!window.audioContext) {
        console.warn('AudioContext non initialisé, tentative d\'initialisation...');
        if (window.initAudio) {
            window.initAudio();
        }
    }
    
    // Appliquer le volume au contexte audio global
    if (window.audioContext) {
        // Créer un gain master si il n'existe pas
        if (!window.masterGainNode) {
            window.masterGainNode = window.audioContext.createGain();
            window.masterGainNode.connect(window.audioContext.destination);
            console.log('🎵 Master gain node créé');
        }
        window.masterGainNode.gain.setValueAtTime(volume, window.audioContext.currentTime);
        console.log('🎵 Volume appliqué:', volume);
    } else {
        console.error('❌ AudioContext non disponible');
    }
    
    // Appliquer le volume au module harmonium s'il existe
    if (window.HARMONIUM_MODULE && window.HARMONIUM_MODULE.setVolume) {
        window.HARMONIUM_MODULE.setVolume(volume);
    }
    
    // Stocker le volume global
    window.masterVolume = volume;
    
    // Sauvegarder la préférence
    localStorage.setItem('pianoVolume', value);
}

// Exporter les fonctions globalement pour les menus
window.toggleDropdown = toggleDropdown;
window.closeDropdown = closeDropdown;
window.selectKeyMode = selectKeyMode;
window.loadDemo = loadDemo;
window.selectSound = selectSound;
window.changeMidiDevice = changeMidiDevice;
window.changeVolume = changeVolume;

// Fonction de vérification de disponibilité
window.checkMenuFunctions = function() {
    console.log('🔍 Vérification des fonctions de menu:');
    console.log('- toggleDropdown:', typeof window.toggleDropdown);
    console.log('- selectKeyMode:', typeof window.selectKeyMode);
    console.log('- NOTATION_MODULE:', typeof window.NOTATION_MODULE);
    return {
        toggleDropdown: typeof window.toggleDropdown === 'function',
        selectKeyMode: typeof window.selectKeyMode === 'function',
        notationModule: typeof window.NOTATION_MODULE === 'object'
    };
};

// Initialisation au chargement avec gestionnaire de modules
window.addEventListener('DOMContentLoaded', async () => {
    console.log('✅ JavaScript des menus chargé');
    
    // Attendre que le gestionnaire de modules soit disponible
    if (!window.MODULE_MANAGER) {
        console.error('❌ MODULE_MANAGER non disponible');
        return;
    }
    
    // Enregistrer les modules dans l'ordre de dépendances
    if (window.NOTATION_MODULE) {
        MODULE_MANAGER.register('NOTATION_MODULE', async () => {
            if (!window.NOTATION_MODULE.initialized) {
                window.NOTATION_MODULE.init();
                window.NOTATION_MODULE.initialized = true;
            }
            return true;
        }, [], { maxRetries: 2 });
    }
    
    if (window.HARMONIUM_MODULE) {
        MODULE_MANAGER.register('HARMONIUM_MODULE', async () => {
            return await window.HARMONIUM_MODULE.init();
        }, ['NOTATION_MODULE'], { maxRetries: 3 });
    }
    
    if (window.MIDI_MODULE) {
        MODULE_MANAGER.register('MIDI_MODULE', async () => {
            // Initialiser le module MIDI
            if (window.MIDI_MODULE.init) {
                await window.MIDI_MODULE.init();
            }
            
            // Initialiser la liste des périphériques MIDI dans le menu
            if (navigator.requestMIDIAccess) {
                try {
                    const midiAccess = await navigator.requestMIDIAccess();
                    const select = document.getElementById('midi-device-select');
                    if (select) {
                        const inputs = midiAccess.inputs.values();
                        for (let input of inputs) {
                            const option = document.createElement('option');
                            option.value = input.id;
                            option.textContent = input.name;
                            select.appendChild(option);
                        }
                    }
                    return true;
                } catch (err) {
                    console.log('MIDI non disponible:', err);
                    return false; // Pas d'erreur critique
                }
            }
            return true;
        }, ['NOTATION_MODULE'], { maxRetries: 1 });
    }
    
    if (window.RECORDER_MODULE) {
        MODULE_MANAGER.register('RECORDER_MODULE', async () => {
            if (window.RECORDER_MODULE.init) {
                return await window.RECORDER_MODULE.init();
            }
            return true;
        }, ['NOTATION_MODULE'], { maxRetries: 2 });
    }
    
    if (window.SAVE_UPLOAD_MODULE) {
        MODULE_MANAGER.register('SAVE_UPLOAD_MODULE', async () => {
            if (!window.SAVE_UPLOAD_MODULE.initialized) {
                window.SAVE_UPLOAD_MODULE.init();
                window.SAVE_UPLOAD_MODULE.initialized = true;
            }
            return true;
        }, ['RECORDER_MODULE'], { maxRetries: 2 });
    }
    
    if (window.DEMOS_LOADER) {
        MODULE_MANAGER.register('DEMOS_LOADER', async () => {
            return await window.DEMOS_LOADER.init();
        }, ['NOTATION_MODULE'], { maxRetries: 2 });
    }
    
    // Initialiser tous les modules
    try {
        const result = await MODULE_MANAGER.initialize();
        console.log('🎉 Initialisation terminée:', result);
        
        // Appliquer le mode par défaut après initialisation
        setTimeout(() => {
            selectKeyMode('labels-en'); // Mode EN par défaut
            
            // Restaurer le volume sauvegardé
            const savedVolume = localStorage.getItem('pianoVolume');
            if (savedVolume) {
                const volumeSlider = document.getElementById('volume-control');
                if (volumeSlider) {
                    volumeSlider.value = savedVolume;
                    changeVolume(savedVolume);
                }
            }
        }, 200);
        
    } catch (error) {
        console.error('💥 Erreur lors de l\'initialisation:', error);
        
        // Mode dégradé - essayer d'appliquer le mode par défaut quand même
        setTimeout(() => {
            selectKeyMode('labels-en');
        }, 500);
    }
});

// Initialisation des modules admin (seulement si présents)
window.addEventListener('load', function() {
    setTimeout(function() {
        // Vérifier et initialiser SAVE_UPLOAD_MODULE (admin seulement)
        if (window.SAVE_UPLOAD_MODULE && !window.SAVE_UPLOAD_MODULE.initialized) {
            console.log('Initialisation forcée de SAVE_UPLOAD_MODULE');
            window.SAVE_UPLOAD_MODULE.init();
        }
        
        // Vérifier la connexion entre les modules (admin seulement)
        if (window.RECORDER_MODULE && window.SAVE_UPLOAD_MODULE) {
            console.log('✅ Tous les modules admin sont chargés');
        } else {
            console.log('ℹ️ Mode élève - Modules admin non présents');
        }
    }, 3000); // 3 secondes après le chargement
});