<!-- /includes/piano-core.php -->
<!-- Module de base du piano virtuel - 3 octaves (C3 à C6) avec sons synthétisés >> oscillator -->

<style>
/* Styles du piano */
.piano-container {
    background: linear-gradient(to bottom, #3e4a5c 0%, #2c3e50 100%);
    border-radius: 15px;
    padding: 0;
    margin: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    overflow-x: auto;
    overflow-y: visible;
    position: relative;
}

/* Container pour la barre de menu intégrée */
.piano-header {
    background: #1a1a1a;
    padding: 0;
    border-radius: 15px 15px 0 0;
    position: relative;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-height: 50px;
    z-index: 1000;
}

/* Zone pour le piano */
.piano-content {
    padding: 0;
    margin: 0;
}

/* Barre de contrôle des démos */
.demo-controls {
    position: relative;
    margin: 20px auto 0 auto;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 25px;
    padding: 12px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    z-index: 100;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    max-width: 400px;
    justify-content: center;
}

.demo-controls button {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #2c3e50;
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    min-height: 40px;
}

.demo-controls button:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: scale(1.1);
}

.demo-controls button:active {
    transform: scale(0.95);
}

.demo-controls button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none;
}

.demo-controls button:disabled:hover {
    background: none;
    transform: none;
}

.demo-controls .play-btn {
    background: rgba(46, 204, 113, 0.2);
    border: 1px solid rgba(46, 204, 113, 0.3);
}

.demo-controls .play-btn:hover {
    background: rgba(46, 204, 113, 0.3);
}

.demo-controls .pause-btn {
    background: rgba(241, 196, 15, 0.2);
    border: 1px solid rgba(241, 196, 15, 0.3);
}

.demo-controls .pause-btn:hover {
    background: rgba(241, 196, 15, 0.3);
}

.demo-controls .stop-btn {
    background: rgba(231, 76, 60, 0.2);
    border: 1px solid rgba(231, 76, 60, 0.3);
}

.demo-controls .stop-btn:hover {
    background: rgba(231, 76, 60, 0.3);
}

.demo-controls .rewind-btn {
    background: rgba(52, 152, 219, 0.2);
    border: 1px solid rgba(52, 152, 219, 0.3);
}

.demo-controls .rewind-btn:hover {
    background: rgba(52, 152, 219, 0.3);
}

.demo-controls .demo-info {
    color: #2c3e50;
    font-size: 12px;
    margin-left: 10px;
    opacity: 0.9;
    font-weight: 500;
}

.demo-controls .progress-bar {
    width: 120px;
    height: 4px;
    background: rgba(44, 62, 80, 0.2);
    border-radius: 2px;
    overflow: hidden;
    margin: 0 10px;
}

.demo-controls .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    width: 0%;
    transition: width 0.1s ease;
}

/* NOUVELLE BARRE DE CONTRÔLE UNIFIÉE - STYLES SÉPARÉS */
.unified-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 8px 15px;
    margin-top: 8px;
}

.unified-controls .music-icon {
    color: #333;
    font-weight: 600;
    margin-right: 12px;
    font-size: 14px;
}

.unified-btn {
    margin: 2px;
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.unified-btn:hover {
    transform: scale(1.05);
}

.unified-btn:active {
    transform: scale(0.95);
}

.unified-btn.pause-btn {
    background: #ffc107 !important;
    color: #000 !important;
    font-size: 13px !important;
    padding: 8px 12px !important;
    width: 30px !important;
    min-width: 30px !important;
    max-width: 30px !important;
    height: 30px !important;
    min-height: 30px !important;
    max-height: 30px !important;
    box-sizing: border-box !important;
    border-radius: 6px !important;
    cursor: pointer !important;
}

.unified-btn.resume-btn {
    background: #28a745;
    color: #fff;
}

.unified-btn.stop-btn {
    background: #dc3545;
    color: #fff;
}

.unified-controls .timeline-container {
    margin-left: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.unified-controls .timeline-slider {
    width: 150px;
    height: 4px;
    border-radius: 2px;
    background: #e0e0e0;
    outline: none;
    cursor: pointer;
}

.unified-controls .time-display {
    font-size: 11px;
    color: #666;
    font-weight: 500;
    min-width: 80px;
    text-align: center;
}

.piano {
    position: relative;
    width: 1150px; /* 23 touches blanches × 50px */
    height: 200px;
    margin: 0 auto;
}

/* Touches du piano */
.key {
    position: absolute;
    cursor: pointer;
    user-select: none;
    transition: all 0.1s ease;
    border-radius: 0 0 6px 6px;
}

.key.white {
    width: 50px;
    height: 200px;
    background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.key.white:hover {
    background: linear-gradient(to bottom, #fafafa 0%, #f0f0f0 100%);
    transform: translateY(1px);
}

.key.black {
    width: 32px;
    height: 130px;
    background: linear-gradient(to bottom, #2c3e50 0%, #1e2a38 100%);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    z-index: 2;
}

.key.black:hover {
    background: linear-gradient(to bottom, #34495e 0%, #2c3e50 100%);
    transform: translateY(1px);
}

/* Styles pour les touches actives - SANS EFFET DE LUMIÈRE */
.key.active {
    background-color: #ffeb3b !important;
    /* SUPPRIMÉ: box-shadow: 0 0 10px #ffeb3b !important; */
    transform: translateY(2px);
    transition: all 0.1s ease;
}

/* Labels des notes */
.key-label {
    position: absolute;
    bottom: 8px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 14px;
    font-weight: 600;
    color: #666;
    display: none;
}

.key.black .key-label {
    color: #aaa;
    bottom: 6px;
    font-size: 11px;
}

/* Calisson orange (indicateur) */
.calisson {
    position: absolute;
    width: 35px;
    height: 45px;
    background: radial-gradient(ellipse at center, #ff8c00 0%, #ff6b00 70%);
    border-radius: 50%;
    transform: scaleX(0.65);
    opacity: 0;
    animation: calissonAppear 0.2s ease-out forwards;
    pointer-events: none;
    box-shadow: 0 3px 10px rgba(255, 140, 0, 0.6);
    z-index: 10;
}

.calisson.white-key {
    bottom: 25px;
    left: 50%;
    transform: translateX(-50%) scaleX(0.65);
}

.calisson.black-key {
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%) scaleX(0.65);
}

.calisson.fadeout {
    animation: calissonDisappear 0.3s ease-out forwards;
}

@keyframes calissonAppear {
    0% {
        opacity: 0;
        transform: translateX(-50%) scaleX(0.65) scale(0.8);
    }
    100% {
        opacity: 1;
        transform: translateX(-50%) scaleX(0.65) scale(1);
    }
}

@keyframes calissonDisappear {
    0% {
        opacity: 1;
        transform: translateX(-50%) scaleX(0.65) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateX(-50%) scaleX(0.65) scale(0.8);
    }
}

@media (max-width: 768px) {
    .piano-container {
        padding: 20px 10px;
    }
}

.ajout {
            text-align: center;
            color: #96bcc5;
            margin-bottom: 5px;
        }
</style>

<div class="piano-container">
    <!-- Header avec barre de menu intégrée -->
    <div class="piano-header">
        <!-- Barre de menus -->
        <?php include 'menu-toolbar.php'; ?>
        <!-- Dropdowns -->
        <?php include 'menu-dropdowns.php'; ?>
    </div>
    
    <!-- Contenu du piano -->
    <div class="piano-content">
        <div id="piano" class="piano"></div>
        
        <!-- BARRE DE CONTRÔLE UNIFIÉE - POSITIONNÉE SOUS LE CLAVIER -->
        <div class="unified-controls" id="unifiedControls" style="display: flex; margin-top: 15px;">
            <button id="unifiedPauseBtn" class="unified-btn pause-btn" title="Pause"></button>
            <button id="unifiedResumeBtn" class="unified-btn resume-btn">▶️</button>
            <button id="unifiedStopBtn" class="unified-btn stop-btn">⏹️</button>
            <div class="timeline-container">
                <input type="range" id="unifiedTimelineSlider" class="timeline-slider" min="0" max="100" value="0">
                <div id="unifiedTimeDisplay" class="time-display">00:00 / 00:00</div>
            </div>
        </div>
    </div>
</div>

<script>
// Configuration - Contexte audio global
let audioContext;

// Créer un contexte audio global unique
if (!window.globalAudioContext) {
    window.globalAudioContext = new (window.AudioContext || window.webkitAudioContext)();
    console.log('🎵 Contexte audio global créé');
}
audioContext = window.globalAudioContext;

// Fréquences des notes
const noteFrequencies = {
    'C': 261.63, 'C#': 277.18, 'D': 293.66, 'D#': 311.13,
    'E': 329.63, 'F': 349.23, 'F#': 369.99, 'G': 392.00,
    'G#': 415.30, 'A': 440.00, 'A#': 466.16, 'B': 493.88
};

// Initialisation audio
function initAudio() {
    if (!audioContext) {
        // Utiliser le contexte audio global
        if (window.globalAudioContext) {
            audioContext = window.globalAudioContext;
        } else {
            window.globalAudioContext = new (window.AudioContext || window.webkitAudioContext)();
            audioContext = window.globalAudioContext;
            console.log('🎵 Contexte audio global créé dans initAudio');
        }
    }
}

// Création du piano (3 octaves: C3 à B5 + C6)
function createPiano() {
    const piano = document.getElementById('piano');
    const notes = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
    
    let whiteKeyIndex = 0;
    
    // 3 octaves de touches blanches
    for (let octave = 3; octave <= 5; octave++) {
        notes.forEach((note, index) => {
            const key = document.createElement('div');
            key.className = 'key white';
            key.dataset.note = note + octave;
            key.style.left = (whiteKeyIndex * 50) + 'px';
            
            const label = document.createElement('div');
            label.className = 'key-label';
            label.textContent = note;
            key.appendChild(label);
            
            // Événements
            key.addEventListener('mousedown', () => playNote(note + octave, key));
            key.addEventListener('mouseup', () => stopNote(note + octave, key));
            key.addEventListener('mouseleave', () => stopNote(note + octave, key));
            
            piano.appendChild(key);
            whiteKeyIndex++;
        });
    }
    
    // Ajouter le Do final (C6)
    const finalKey = document.createElement('div');
    finalKey.className = 'key white';
    finalKey.dataset.note = 'C6';
    finalKey.style.left = (whiteKeyIndex * 50) + 'px';
    
    const finalLabel = document.createElement('div');
    finalLabel.className = 'key-label';
    finalLabel.textContent = 'C';
    finalKey.appendChild(finalLabel);
    
    // Événements
    finalKey.addEventListener('mousedown', () => playNote('C6', finalKey));
    finalKey.addEventListener('mouseup', () => stopNote('C6', finalKey));
    finalKey.addEventListener('mouseleave', () => stopNote('C6', finalKey));
    
    piano.appendChild(finalKey);
    
    // Touches noires pour 3 octaves
    const blackKeyPattern = [
        { note: 'C#', offset: 35 },
        { note: 'D#', offset: 85 },
        { note: 'F#', offset: 185 },
        { note: 'G#', offset: 235 },
        { note: 'A#', offset: 285 }
    ];
    
    for (let octave = 3; octave <= 5; octave++) {
        blackKeyPattern.forEach(({ note, offset }) => {
            const key = document.createElement('div');
            key.className = 'key black';
            key.dataset.note = note + octave;
            key.style.left = (offset + (octave - 3) * 350) + 'px';
            
            const label = document.createElement('div');
            label.className = 'key-label';
            label.textContent = note;
            key.appendChild(label);
            
            // Événements
            key.addEventListener('mousedown', () => playNote(note + octave, key));
            key.addEventListener('mouseup', () => stopNote(note + octave, key));
            key.addEventListener('mouseleave', () => stopNote(note + octave, key));
            
            piano.appendChild(key);
        });
    }
}

// Jouer une note
function playNote(noteId, keyElement) {
    initAudio();
    
    keyElement.classList.add('active');
    
    // Nettoyer tout calisson existant avant d'en créer un nouveau
    const existingCalissons = keyElement.querySelectorAll('.calisson');
    existingCalissons.forEach(calisson => calisson.remove());
    
    // Créer et afficher le calisson
    const calisson = document.createElement('div');
    calisson.className = 'calisson';
    calisson.classList.add(keyElement.classList.contains('black') ? 'black-key' : 'white-key');
    keyElement.appendChild(calisson);
    
    // Jouer le son
    const note = noteId.slice(0, -1);
    const octave = parseInt(noteId.slice(-1));
    const frequency = noteFrequencies[note] * Math.pow(2, octave - 4);
    
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    
    // Utiliser le gain master si disponible
    if (window.masterGainNode) {
        gainNode.connect(window.masterGainNode);
    } else {
        gainNode.connect(audioContext.destination);
    }
    
    oscillator.frequency.value = frequency;
    oscillator.type = 'triangle';
    
    // Appliquer le volume global
    const globalVolume = window.masterVolume || 0.75;
    gainNode.gain.setValueAtTime(0, audioContext.currentTime);
    gainNode.gain.linearRampToValueAtTime(0.3 * globalVolume, audioContext.currentTime + 0.01);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 1);
    
    oscillator.start();
    oscillator.stop(audioContext.currentTime + 1);
    
    // Stocker pour arrêt
    keyElement.oscillator = { oscillator, gainNode };
}

// Arrêter une note
function stopNote(noteId, keyElement) {
    keyElement.classList.remove('active');
    
    // Faire disparaître TOUS les calissons de cette touche
    const calissons = keyElement.querySelectorAll('.calisson');
    calissons.forEach(calisson => {
        if (!calisson.classList.contains('fadeout')) {
            calisson.classList.add('fadeout');
            // Utiliser un data-attribute pour éviter les doublons de timeout
            if (!calisson.dataset.removing) {
                calisson.dataset.removing = 'true';
                setTimeout(() => calisson.remove(), 300);
            }
        }
    });
    
    // Arrêter le son
    if (keyElement.oscillator) {
        const { gainNode } = keyElement.oscillator;
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
        delete keyElement.oscillator;
    }
}

// Hook pour modules externes
window.PIANO_HOOKS = {
    audioContext: null
};

// Exposer les fonctions globalement
window.playNote = playNote;
window.stopNote = stopNote;
window.initAudio = initAudio;
window.audioContext = audioContext;

// Initialisation
window.addEventListener('DOMContentLoaded', () => {
    createPiano();
    // Mettre à jour le hook après init
    window.PIANO_HOOKS.audioContext = audioContext;
    
    // Initialiser le masterGainNode si pas déjà fait
    if (audioContext && !window.masterGainNode) {
        window.masterGainNode = audioContext.createGain();
        window.masterGainNode.connect(audioContext.destination);
        window.masterVolume = 0.75; // Volume par défaut
        console.log('🎵 Master gain node initialisé');
    }
});

// ===== BARRE DE CONTRÔLE DES DÉMOS =====

// Variables globales pour la gestion des démos
let currentDemo = null;
let demoIsPlaying = false;
let demoStartTime = 0;
let demoProgressInterval = null;

// Afficher la barre de contrôle
function showDemoControls() {
    const controls = document.getElementById('demoControls');
    if (controls) {
        controls.style.display = 'flex';
    }
}

// Masquer la barre de contrôle
function hideDemoControls() {
    const controls = document.getElementById('demoControls');
    if (controls) {
        controls.style.display = 'none';
    }
}

// Mettre à jour les informations de la démo
function updateDemoInfo(demoName = 'Aucune démo sélectionnée') {
    const info = document.getElementById('demoInfo');
    if (info) {
        info.textContent = demoName;
    }
}

// Mettre à jour la barre de progression
function updateDemoProgress(progress = 0) {
    const progressBar = document.getElementById('demoProgress');
    if (progressBar) {
        progressBar.style.width = `${progress}%`;
    }
}

// Contrôles de lecture
function demoPlay() {
    if (!currentDemo) return;
    
    demoIsPlaying = true;
    demoStartTime = Date.now();
    
    // Afficher le bouton pause, masquer play
    const playBtn = document.querySelector('.play-btn');
    const pauseBtn = document.querySelector('.pause-btn');
    if (playBtn) playBtn.style.display = 'none';
    if (pauseBtn) pauseBtn.style.display = 'flex';
    
    // Démarrer la mise à jour de la progression
    demoProgressInterval = setInterval(() => {
        if (demoIsPlaying && currentDemo) {
            const elapsed = Date.now() - demoStartTime;
            const progress = Math.min((elapsed / currentDemo.duration) * 100, 100);
            updateDemoProgress(progress);
            
            // Arrêter automatiquement à la fin
            if (progress >= 100) {
                demoStop();
            }
        }
    }, 100);
    
    console.log('🎵 Lecture de la démo:', currentDemo.name);
}

function demoPause() {
    demoIsPlaying = false;
    
    // Afficher le bouton play, masquer pause
    const playBtn = document.querySelector('.play-btn');
    const pauseBtn = document.querySelector('.pause-btn');
    if (playBtn) playBtn.style.display = 'flex';
    if (pauseBtn) pauseBtn.style.display = 'none';
    
    // Arrêter la mise à jour de la progression
    if (demoProgressInterval) {
        clearInterval(demoProgressInterval);
        demoProgressInterval = null;
    }
    
    console.log('⏸️ Démos en pause');
}

function demoStop() {
    demoIsPlaying = false;
    
    // Afficher le bouton play, masquer pause
    const playBtn = document.querySelector('.play-btn');
    const pauseBtn = document.querySelector('.pause-btn');
    if (playBtn) playBtn.style.display = 'flex';
    if (pauseBtn) pauseBtn.style.display = 'none';
    
    // Réinitialiser la progression
    updateDemoProgress(0);
    
    // Arrêter la mise à jour de la progression
    if (demoProgressInterval) {
        clearInterval(demoProgressInterval);
        demoProgressInterval = null;
    }
    
    console.log('⏹️ Démos arrêtée');
}

function demoRewind() {
    if (!currentDemo) return;
    
    demoStop();
    demoStartTime = Date.now();
    updateDemoProgress(0);
    
    console.log('⏮️ Retour au début de la démo');
}

// Fonction pour charger une démo dans la barre de contrôle
function loadDemoInControls(demo) {
    currentDemo = demo;
    showDemoControls();
    updateDemoInfo(demo.name);
    updateDemoProgress(0);
    
    // Réinitialiser les boutons
    const playBtn = document.querySelector('.play-btn');
    const pauseBtn = document.querySelector('.pause-btn');
    if (playBtn) playBtn.style.display = 'flex';
    if (pauseBtn) pauseBtn.style.display = 'none';
    
    console.log('📀 Démo chargée dans les contrôles:', demo.name);
}

// Exposer les fonctions globalement
window.showDemoControls = showDemoControls;
window.hideDemoControls = hideDemoControls;
window.loadDemoInControls = loadDemoInControls;
window.demoPlay = demoPlay;
window.demoPause = demoPause;
window.demoStop = demoStop;
window.demoRewind = demoRewind;
</script>