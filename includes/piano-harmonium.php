<!-- /includes/piano-harmonium.php -->
<!-- Module Harmonium avec fixes Android et sans affichage MIDI parasite - Chargement des Samples -->
<!-- Le Badge "Harmonium" s'affiche en haut à droite du clavier si tout les samples sont chargés -->
<style>
/* Styles principaux */
.harmonium-loader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.95);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    text-align: center;
    z-index: 1000;
    min-width: 300px;
}

.harmonium-loader h3 {
    margin: 0 0 20px 0;
    color: #2c3e50;
}

.harmonium-progress {
    width: 100%;
    height: 20px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin: 10px 0;
}

.harmonium-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    width: 0%;
    transition: width 0.3s ease;
}

.harmonium-status {
    font-size: 14px;
    color: #666;
    margin-top: 10px;
}

.harmonium-badge {
    position: static;
    background: #9d794e;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    display: none;
    z-index: 10;
    margin-right: 15px;
}

.harmonium-badge.active {
    display: block;
    animation: fadeIn 0.5s ease;
}

/* Bouton mobile simple */
.mobile-audio-init {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(52, 152, 219, 0.95);
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 1001;
    display: none;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<div class="harmonium-loader" id="harmoniumLoader">
    <h3>🎹 Chargement de l'harmonium...</h3>
    <div class="harmonium-progress">
        <div class="harmonium-progress-bar" id="harmoniumProgressBar"></div>
    </div>
    <div class="harmonium-status" id="harmoniumStatus">Initialisation...</div>
</div>

<div class="mobile-audio-init" id="mobileAudioInit">
    🔊 Touchez n'importe où pour activer le son
</div>

<script>
// Module Harmonium - Version finale avec tous les fixes
const HARMONIUM_MODULE = (function() {
    'use strict';
    
    // Configuration
    const config = {
        samplePath: 'samples/harmonium/',
        notes: [],
        loaded: false,
        samples: {},
        activeSources: new Map(),
        audioInitialized: false,
        isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
        playbackMethod: null, // 'samples' ou 'oscillator'
        currentPreset: 'harmonium' // Preset actuel
    };
    
    // Presets disponibles
    const presets = {
        'harmonium': {
            name: 'Harmonium de rue',
            path: 'samples/harmonium/',
            description: '3 octaves - Son authentique'
        },
        'harmonium-paul-and-co': {
            name: 'Harmonium Paul and Co',
            path: 'samples/hamonium-paul-and-co/',
            description: '3 octaves - Son premium'
        }
    };
    
    // Générer la liste des notes
    function generateNoteList() {
        const noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
        const notes = [];
        
        for (let octave = 3; octave <= 5; octave++) {
            noteNames.forEach(note => {
                notes.push(note + octave);
            });
        }
        notes.push('C6');
        
        config.notes = notes;
        console.log(`Harmonium: ${notes.length} notes à charger`);
    }
    
    // Initialiser l'audio (spécial mobile)
    function initializeAudio() {
        if (config.audioInitialized) return;
        
        if (!window.audioContext) {
            // Utiliser le contexte audio global
            if (window.globalAudioContext) {
                window.audioContext = window.globalAudioContext;
            } else {
                window.globalAudioContext = new (window.AudioContext || window.webkitAudioContext)();
                window.audioContext = window.globalAudioContext;
                console.log('🎵 Contexte audio global créé dans harmonium (initializeAudio)');
            }
        }
        
        // Jouer un son silencieux pour débloquer
        const buffer = window.audioContext.createBuffer(1, 1, 22050);
        const source = window.audioContext.createBufferSource();
        source.buffer = buffer;
        source.connect(window.audioContext.destination);
        source.start(0);
        
        // Reprendre si suspendu
        if (window.audioContext.state === 'suspended') {
            window.audioContext.resume();
        }
        
        config.audioInitialized = true;
        
        // Masquer le message mobile
        const mobileInit = document.getElementById('mobileAudioInit');
        if (mobileInit) {
            mobileInit.style.display = 'none';
        }
        
        console.log('✅ Audio initialisé');
    }
    
    // Charger un sample
    async function loadSample(note, audioContext) {
        const fileNote = note.replace('#', 's');
        const possibleNames = [
            `harmonium_${fileNote}.mp3`,
            `harmonium_${note}.mp3`
        ];
        
        for (const filename of possibleNames) {
            const url = `${config.samplePath}${filename}`;
            
            try {
                const response = await fetch(url);
                if (response.ok) {
                    const arrayBuffer = await response.arrayBuffer();
                    const audioBuffer = await audioContext.decodeAudioData(arrayBuffer);
                    
                    config.samples[note] = audioBuffer;
                    return true;
                }
            } catch (error) {
                continue;
            }
        }
        
        return false;
    }
    
    // Changer de preset
    async function changePreset(presetId) {
        if (!presets[presetId]) {
            console.error('Preset non trouvé:', presetId);
            return false;
        }
        
        console.log(`🔄 Changement vers le preset: ${presets[presetId].name}`);
        
        // Arrêter toutes les notes actives
        config.activeSources.forEach((source, noteId) => {
            if (source.source) {
                source.source.stop();
            }
        });
        config.activeSources.clear();
        
        // Vider les samples actuels
        config.samples = {};
        config.loaded = false;
        
        // Changer le chemin
        config.samplePath = presets[presetId].path;
        config.currentPreset = presetId;
        
        // Recharger les samples
        if (window.audioContext) {
            await loadAllSamples(window.audioContext);
        }
        
        // Mettre à jour l'interface
        updateSoundMenuUI(presetId);
        
        console.log(`✅ Preset changé: ${presets[presetId].name}`);
        return true;
    }
    
    // Mettre à jour l'interface du menu SOUND
    function updateSoundMenuUI(selectedPreset) {
        const soundItems = document.querySelectorAll('.sound-item');
        soundItems.forEach(item => {
            item.classList.remove('selected');
        });
        
        // Trouver et sélectionner le bon item
        const selectedItem = document.querySelector(`[onclick*="${selectedPreset}"]`);
        if (selectedItem) {
            selectedItem.classList.add('selected');
        }
    }
    
    // Charger tous les samples
    async function loadAllSamples(audioContext) {
        const loader = document.getElementById('harmoniumLoader');
        const progressBar = document.getElementById('harmoniumProgressBar');
        const status = document.getElementById('harmoniumStatus');
        
        let loadedCount = 0;
        const totalNotes = config.notes.length;
        const batchSize = 6;
        
        for (let i = 0; i < totalNotes; i += batchSize) {
            const batch = config.notes.slice(i, i + batchSize);
            const promises = batch.map(note => loadSample(note, audioContext));
            
            const results = await Promise.all(promises);
            loadedCount += results.filter(r => r).length;
            
            const progress = (loadedCount / totalNotes) * 100;
            progressBar.style.width = progress + '%';
            status.textContent = `Chargement : ${loadedCount}/${totalNotes} notes`;
        }
        
        // Décider de la méthode de lecture
        if (loadedCount === totalNotes) {
            config.loaded = true;
            config.playbackMethod = 'samples';
            status.textContent = `✅ ${loadedCount} notes chargées !`;
            console.log('✅ Mode : Samples harmonium');
        } else {
            config.playbackMethod = 'oscillator';
            status.textContent = `⚠️ Mode dégradé : Synthé (${loadedCount}/${totalNotes})`;
            console.log('⚠️ Mode : Oscillateur de secours');
        }
        
        // Masquer le loader
        setTimeout(() => {
            loader.style.display = 'none';
            
            // Badge harmonium seulement si tous les samples sont chargés
            if (config.playbackMethod === 'samples') {
                const pianoHeader = document.querySelector('.piano-header');
                if (pianoHeader) {
                    // Supprimer l'ancien badge s'il existe
                    const existingBadge = pianoHeader.querySelector('.harmonium-badge');
                    if (existingBadge) {
                        existingBadge.remove();
                    }
                    
                    // Créer le nouveau badge avec le nom du preset
                    const badge = document.createElement('div');
                    badge.className = 'harmonium-badge active';
                    
                    // Personnaliser le badge selon le preset
                    const currentPreset = presets[config.currentPreset];
                    if (currentPreset) {
                        badge.textContent = `🎹 ${currentPreset.name}`;
                        badge.title = currentPreset.description; // Tooltip avec la description
                    } else {
                        badge.textContent = '🎹 Harmonium';
                    }
                    
                    pianoHeader.appendChild(badge);
                }
            }
            
            // Sur mobile, afficher le message d'init
            if (config.isMobile && !config.audioInitialized) {
                const mobileInit = document.getElementById('mobileAudioInit');
                if (mobileInit) {
                    mobileInit.style.display = 'block';
                }
            }
            
            // Remplacer les fonctions
            overridePianoFunctions();
        }, 500);
        
        return true;
    }
    
    // Jouer avec samples
    function playWithSample(noteId, keyElement) {
        const source = window.audioContext.createBufferSource();
        const gainNode = window.audioContext.createGain();
        
        source.buffer = config.samples[noteId];
        source.connect(gainNode);
        
        // Utiliser le gain master si disponible, sinon destination directe
        if (window.masterGainNode) {
            gainNode.connect(window.masterGainNode);
        } else {
            gainNode.connect(window.audioContext.destination);
        }
        
        // Appliquer le volume global
        const globalVolume = window.masterVolume || 0.75;
        gainNode.gain.value = 0.7 * globalVolume;
        source.start(0);
        
        config.activeSources.set(noteId, { source, gainNode });
        
        source.onended = () => {
            config.activeSources.delete(noteId);
        };
    }
    
    // Nouvelle fonction playNote universelle
    function playNoteHarmonium(noteId, keyElement) {
        // Sur mobile, initialiser l'audio au premier touch
        if (config.isMobile && !config.audioInitialized) {
            initializeAudio();
        }
        
        // S'assurer que le contexte existe
        if (!window.audioContext) {
            // Utiliser le contexte audio global
            if (window.globalAudioContext) {
                window.audioContext = window.globalAudioContext;
            } else {
                window.globalAudioContext = new (window.AudioContext || window.webkitAudioContext)();
                window.audioContext = window.globalAudioContext;
                console.log('🎵 Contexte audio global créé dans harmonium (playNote)');
            }
        }
        
        // Reprendre si suspendu
        if (window.audioContext.state === 'suspended') {
            window.audioContext.resume();
        }
        
        // Ne pas rejouer si déjà active
        if (config.activeSources.has(noteId)) return;
        
        // Visuel (toujours)
        if (keyElement) {
            keyElement.classList.add('active');
            
            if (!keyElement.querySelector('.calisson')) {
                const calisson = document.createElement('div');
                calisson.className = 'calisson';
                calisson.classList.add(keyElement.classList.contains('black') ? 'black-key' : 'white-key');
                keyElement.appendChild(calisson);
            }
        }
        
        try {
            // Choisir la méthode selon ce qui est disponible
            if (config.playbackMethod === 'samples' && config.samples[noteId]) {
                playWithSample(noteId, keyElement);
            } else if (window.playNoteOriginal) {
                // Utiliser l'oscillateur original
                window.playNoteOriginal(noteId, keyElement);
            }
        } catch (error) {
            console.error(`Erreur playNote ${noteId}:`, error);
        }
    }
    
    // Nouvelle fonction stopNote
    function stopNoteHarmonium(noteId, keyElement) {
        // Arrêter le sample si actif
        const noteData = config.activeSources.get(noteId);
        if (noteData) {
            try {
                noteData.source.stop();
                config.activeSources.delete(noteId);
            } catch (error) {
                // Ignorer
            }
        }
        
        // Visuel
        if (keyElement) {
            keyElement.classList.remove('active');
            
            const calisson = keyElement.querySelector('.calisson');
            if (calisson) {
                calisson.classList.add('fadeout');
                setTimeout(() => {
                    if (calisson && calisson.parentNode) {
                        calisson.remove();
                    }
                }, 300);
            }
        }
        
        // Si on utilise l'oscillateur, appeler l'ancienne fonction
        if (config.playbackMethod === 'oscillator' && window.stopNoteOriginal) {
            window.stopNoteOriginal(noteId, keyElement);
        }
    }
    
    // Remplacer les fonctions
    function overridePianoFunctions() {
        if (window.playNote && !window.playNoteOriginal) {
            window.playNoteOriginal = window.playNote;
            window.stopNoteOriginal = window.stopNote;
            
            window.playNote = playNoteHarmonium;
            window.stopNote = stopNoteHarmonium;
            
            console.log('✅ Fonctions remplacées - Mode:', config.playbackMethod);
        }
    }
    
    // Initialisation
    async function init() {
        console.log('🎹 Initialisation Harmonium...');
        console.log(`📱 Mobile: ${config.isMobile}`);
        
        generateNoteList();
        
        if (!window.audioContext) {
            // Utiliser le contexte audio global
            if (window.globalAudioContext) {
                window.audioContext = window.globalAudioContext;
            } else {
                window.globalAudioContext = new (window.AudioContext || window.webkitAudioContext)();
                window.audioContext = window.globalAudioContext;
                console.log('🎵 Contexte audio global créé dans harmonium');
            }
        }
        
        // Listeners pour mobile
        if (config.isMobile) {
            const initAudioOnTouch = () => {
                initializeAudio();
                document.removeEventListener('touchstart', initAudioOnTouch);
                document.removeEventListener('click', initAudioOnTouch);
            };
            
            document.addEventListener('touchstart', initAudioOnTouch);
            document.addEventListener('click', initAudioOnTouch);
        } else {
            config.audioInitialized = true;
        }
        
        await loadAllSamples(window.audioContext);
        
        return true;
    }
    
    // Fonction pour changer le volume
    function setVolume(volume) {
        // Mettre à jour le volume des sources actives
        config.activeSources.forEach((sourceData, noteId) => {
            if (sourceData.gainNode) {
                sourceData.gainNode.gain.setValueAtTime(0.7 * volume, window.audioContext.currentTime);
            }
        });
    }
    
    // API publique
    return {
        init: init,
        isLoaded: () => config.loaded,
        getPlaybackMethod: () => config.playbackMethod,
        changePreset: changePreset,
        getCurrentPreset: () => config.currentPreset,
        getPresets: () => presets,
        setVolume: setVolume,
        getSampleInfo: () => ({
            totalNotes: config.notes.length,
            loadedNotes: Object.keys(config.samples).length,
            activeSources: config.activeSources.size,
            method: config.playbackMethod,
            currentPreset: config.currentPreset
        })
    };
})();

// Initialiser
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        HARMONIUM_MODULE.init();
    }, 500);
});

window.HARMONIUM_MODULE = HARMONIUM_MODULE;
</script>