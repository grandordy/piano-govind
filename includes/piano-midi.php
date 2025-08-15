<!-- /includes/piano-midi.php -->
<!-- Module MIDI simplifié pour intégration dans le menu SOUND -->

<script>
// Module MIDI simplifié pour Piano Virtuel
const MIDI_MODULE = (function() {
    'use strict';
    
    // Configuration simplifiée
    const config = {
        midiAccess: null,
        currentInput: null,
        octaveOffset: 0,
        initialized: false
    };
    
    // Mapping MIDI vers notes
    function midiNoteToNoteName(midiNote) {
        const noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const octave = Math.floor(midiNote / 12) - 1 + config.octaveOffset;
        const noteIndex = midiNote % 12;
        return noteNames[noteIndex] + octave;
    }
    
    // Trouver l'élément de touche correspondant
    function findKeyElement(noteName) {
        return document.querySelector(`.key[data-note="${noteName}"]`);
    }
    
    // Gérer les messages MIDI (version simplifiée)
    function handleMIDIMessage(event) {
        const [status, note, velocity] = event.data;
        const command = status & 0xF0;
        
        // Note On
        if (command === 0x90 && velocity > 0) {
            const noteName = midiNoteToNoteName(note);
            const keyElement = findKeyElement(noteName);
            
            if (keyElement && window.playNote) {
                window.playNote(noteName, keyElement);
            }
        }
        // Note Off
        else if (command === 0x80 || (command === 0x90 && velocity === 0)) {
            const noteName = midiNoteToNoteName(note);
            const keyElement = findKeyElement(noteName);
            
            if (keyElement && window.stopNote) {
                window.stopNote(noteName, keyElement);
            }
        }
    }
    
    // Connecter un périphérique MIDI
    function connectMIDIDevice(input) {
        if (config.currentInput) {
            config.currentInput.onmidimessage = null;
        }
        
        config.currentInput = input;
        
        if (input) {
            input.onmidimessage = handleMIDIMessage;
            console.log(`✅ MIDI connecté: ${input.name}`);
            updateMidiStatus(true, input.name);
        } else {
            console.log('❌ MIDI déconnecté');
            updateMidiStatus(false);
        }
    }
    
    // Mettre à jour le statut MIDI dans le menu
    function updateMidiStatus(connected, deviceName = '') {
        const select = document.getElementById('midi-device-select');
        if (select) {
            if (connected) {
                select.style.borderColor = '#28a745';
                select.title = `Connecté: ${deviceName}`;
            } else {
                select.style.borderColor = '#dc3545';
                select.title = 'MIDI déconnecté';
            }
        }
    }
    
    // Rafraîchir la liste des périphériques
    function refreshDeviceList() {
        const select = document.getElementById('midi-device-select');
        if (!select) return;
        
        select.innerHTML = '<option value="">Aucun périphérique MIDI</option>';
        
        if (!config.midiAccess) return;
        
        config.midiAccess.inputs.forEach((input, key) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = input.name || `Périphérique MIDI ${key}`;
            select.appendChild(option);
            
            // Auto-sélectionner le premier périphérique
            if (select.options.length === 2 && !config.currentInput) {
                select.value = key;
                connectMIDIDevice(input);
            }
        });
    }
    
    // Gérer les changements de connexion
    function handleStateChange(event) {
        console.log(`MIDI ${event.port.type} ${event.port.state}: ${event.port.name}`);
        refreshDeviceList();
    }
    
    // Initialisation
    async function init() {
        if (config.initialized) return;
        
        console.log('🎹 Initialisation du module MIDI simplifié...');
        
        // Vérifier la disponibilité de Web MIDI API
        if (!navigator.requestMIDIAccess) {
            console.warn('Web MIDI API non supportée');
            config.initialized = true;
            return;
        }
        
        try {
            // Demander l'accès MIDI
            config.midiAccess = await navigator.requestMIDIAccess();
            
            // Écouter les changements de connexion
            config.midiAccess.onstatechange = handleStateChange;
            
            // Rafraîchir la liste
            refreshDeviceList();
            
            // Event listener pour le select dans le menu SOUND
            const deviceSelect = document.getElementById('midi-device-select');
            if (deviceSelect) {
                deviceSelect.addEventListener('change', (e) => {
                    const inputId = e.target.value;
                    if (inputId) {
                        const input = config.midiAccess.inputs.get(inputId);
                        connectMIDIDevice(input);
                    } else {
                        connectMIDIDevice(null);
                    }
                });
            }
            
            config.initialized = true;
            console.log('✅ Module MIDI simplifié prêt !');
            
        } catch (error) {
            console.error('Erreur MIDI:', error);
            config.initialized = true;
        }
    }
    
    // API publique
    return {
        init: init,
        isConnected: () => config.currentInput !== null,
        getCurrentDevice: () => config.currentInput?.name || null,
        setOctaveOffset: (offset) => { config.octaveOffset = offset; },
        selectDevice: (deviceId) => {
            if (config.midiAccess && deviceId) {
                const input = config.midiAccess.inputs.get(deviceId);
                connectMIDIDevice(input);
            }
        }
    };
    
})();

// Exporter le module
window.MIDI_MODULE = MIDI_MODULE;

// Initialiser automatiquement
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        MIDI_MODULE.init();
    }, 1000);
});

// Fonction globale pour le menu SOUND
window.changeMidiDevice = function(deviceId) {
    if (window.MIDI_MODULE) {
        MIDI_MODULE.selectDevice(deviceId);
    }
};
</script>