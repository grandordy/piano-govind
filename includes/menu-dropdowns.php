<!-- /includes/menu-dropdowns.php -->

<!-- KEY ASSIST Dropdown -->
<div id="keyassist-dropdown" class="menu-dropdown">
    <div class="dropdown-header">
        <span class="dropdown-title">KEY ASSIST</span>
        <button class="dropdown-close" onclick="closeDropdown('keyassist')">×</button>
    </div>
    <div class="dropdown-content">
        <div class="toggle-item" onclick="selectKeyMode('none')">
            <span>NO LABELS</span>
            <div class="toggle-switch" id="toggle-none"></div>
        </div>
        <div class="toggle-item" onclick="selectKeyMode('labels-fr')">
            <span>NOTES FR</span>
            <div class="toggle-switch" id="toggle-labels-fr"></div>
        </div>
        <div class="toggle-item" onclick="selectKeyMode('labels-en')">
            <span>NOTES EN</span>
            <div class="toggle-switch on" id="toggle-labels-en"></div>
        </div>
        <div class="toggle-item" onclick="selectKeyMode('octaves')">
            <span>OCTAVES (C)</span>
            <div class="toggle-switch" id="toggle-octaves"></div>
        </div>

        

     
    </div>
</div>

<!-- SOUND Dropdown -->
<div id="sound-dropdown" class="menu-dropdown">
    <div class="dropdown-header">
        <span class="dropdown-title">SOUND</span>
        <button class="dropdown-close" onclick="closeDropdown('sound')">×</button>
    </div>
    <div class="dropdown-content">
        <div class="sound-item selected" onclick="selectSound('harmonium')">
            <span class="sound-name">🎹 Harmonium de rue</span>
            <span class="sound-info">3 octaves - Son authentique</span>
        </div>
        <div class="sound-item" onclick="selectSound('harmonium-paul-and-co')">
            <span class="sound-name">🎹 Harmonium Paul and Co</span>
            <span class="sound-info">3 octaves - Son premium</span>
        </div>
        
        <!-- Séparateur -->
        <div style="border-top: 1px solid #444; margin: 15px 0;"></div>
        
        <!-- MIDI Device -->
        <div class="audio-control">
            <label class="control-label">🎹 SELECT MIDI DEVICE</label>
            <select class="midi-select" id="midi-device-select" onchange="changeMidiDevice(this.value)">
                <option value="">Aucun périphérique MIDI</option>
            </select>
        </div>
        
        <!-- Volume -->
        <div class="audio-control" style="margin-top: 15px;">
            <label class="control-label">🔊 VOLUME</label>
            <input type="range" class="volume-slider" id="volume-control" 
                   min="0" max="100" value="75" 
                   oninput="changeVolume(this.value)">
            <span class="volume-value" id="volume-display">75%</span>
        </div>
    </div>
</div>



<!-- HELP Dropdown -->
<div id="help-dropdown" class="menu-dropdown">
    <div class="dropdown-header">
        <span class="dropdown-title">HELP</span>
        <button class="dropdown-close" onclick="closeDropdown('help')">×</button>
    </div>
    <div class="dropdown-content">
        <!-- Boutons Aide et Tutoriel -->
        <div class="help-actions">
            <button class="help-btn dropdown-help-btn" onclick="window.HELP_VIDEO_MODULE.showHelp()">
                <span class="help-icon">❓</span>
                Aide
            </button>
            <button class="video-btn dropdown-video-btn" onclick="window.HELP_VIDEO_MODULE.showVideo()">
                <span class="help-icon">🎥</span>
                Tutoriel Vidéo
            </button>
            <button class="admin-btn dropdown-admin-btn" onclick="openAdminPiano()">
                <span class="help-icon">🔧</span>
                Piano Solo Admin
            </button>
            <button class="admin-btn dropdown-admin-btn" onclick="window.open('admin.php', '_blank')">
                <span class="help-icon">🎹</span>
                Page Admin
            </button>
        </div>
        
        <div class="help-section">
            <h4>Raccourcis clavier</h4>
            <p>Utilisez les touches A-Z pour jouer</p>
        </div>
        <div class="help-section">
            <h4>Navigation</h4>
            <p>Shift + flèches pour changer d'octave</p>
        </div>
        <div class="help-section">
            <h4>Contact</h4>
            <p>harmonium@example.com</p>
        </div>
    </div>
</div>

<script>
// Fonction pour ouvrir la page piano en mode admin
function openAdminPiano() {
    // Ouvrir avec un paramètre pour forcer le mode admin
    window.open('index.php?forceAdmin=1', '_blank');
}
</script>

<style>
.menu-dropdown {
    display: none;
    position: fixed;
    top: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: #2a2a2a;
    border: 1px solid #444;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.8);
    z-index: 9999;
    min-width: 280px;
    max-width: 400px;
}

.menu-dropdown.show {
    display: block;
}

.dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #444;
}

.dropdown-title {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 1px;
}

.dropdown-close {
    background: none;
    border: none;
    color: #ff6b35;
    font-size: 28px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border-radius: 4px;
}

.dropdown-close:hover {
    color: #fff;
    background: #ff6b35;
    transform: rotate(90deg);
}

.dropdown-content {
    padding: 12px;
    max-height: 280px;  /* Environ 7 items de 40px */
    overflow-y: auto;
    overflow-x: hidden;
}

/* Styles pour les éléments toggle du menu KEY ASSIST */
.toggle-item {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px !important;
    border-bottom: 1px solid #444;
    cursor: pointer;
    transition: background-color 0.2s ease;
    color: #ccc;
}

.toggle-item:hover {
    background-color: #3a3a3a;
}

.toggle-item:last-child {
    border-bottom: none;
}

.toggle-switch {
    width: 40px;
    height: 20px;
    background: #555;
    border-radius: 10px;
    position: relative;
    transition: background-color 0.3s ease;
}

.toggle-switch.on {
    background: #4CAF50;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s ease;
}

.toggle-switch.on::after {
    transform: translateX(20px);
}

/* Scrollbar personnalisée */
.dropdown-content::-webkit-scrollbar {
    width: 6px;
}

.dropdown-content::-webkit-scrollbar-track {
    background: #333;
    border-radius: 3px;
}

.dropdown-content::-webkit-scrollbar-thumb {
    background: #666;
    border-radius: 3px;
}

.dropdown-content::-webkit-scrollbar-thumb:hover {
    background: #888;
}

/* Styles pour les éléments de son */
.sound-item {
    display: flex !important;
    flex-direction: column;
    padding: 12px 15px !important;
    border-bottom: 1px solid #444;
    cursor: pointer;
    transition: background-color 0.2s ease;
    color: #ccc;
}

.sound-item:hover {
    background-color: #3a3a3a;
}

.sound-item.selected {
    background-color: #2c5aa0;
    color: white;
}

.sound-item:last-child {
    border-bottom: none;
}

.sound-name {
    font-weight: 500;
    color: inherit;
    margin-bottom: 2px;
}

.sound-info {
    font-size: 12px;
    color: #aaa;
    font-style: italic;
}

/* Styles pour les éléments de démo dans les menus */
.demo-item {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px !important;
    border-bottom: 1px solid #444;
    cursor: pointer;
    transition: background-color 0.2s ease;
    color: #ccc;
}

.demo-item:hover {
    background-color: #3a3a3a;
}

/* Styles pour les boutons Aide et Tutoriel dans le dropdown HELP */
.help-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #444;
}

.dropdown-help-btn,
.dropdown-video-btn,
.dropdown-admin-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    text-align: left;
    width: 100%;
}

.dropdown-help-btn:hover,
.dropdown-video-btn:hover,
.dropdown-admin-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.dropdown-admin-btn {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.dropdown-admin-btn:hover {
    background: linear-gradient(135deg, #d63031 0%, #b71540 100%);
}

/* Style spécial pour le bouton d'enregistrement */
.dropdown-admin-btn:last-child {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

.dropdown-admin-btn:last-child:hover {
    background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
}

.help-icon {
    font-size: 16px;
    min-width: 20px;
    text-align: center;
}

/* Styles pour les sections d'aide */
.help-section {
    padding: 12px 0;
    border-bottom: 1px solid #444;
}

.help-section:last-child {
    border-bottom: none;
}

.help-section h4 {
    color: #fff;
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: 600;
}

.help-section p {
    color: #ccc;
    margin: 0;
    font-size: 13px;
    line-height: 1.4;
}

/* Styles pour les modes de surlignage */
body.highlight-mode .key:active,
body.highlight-mode .key.active {
    background: linear-gradient(to bottom, #4CAF50, #45a049) !important;
    box-shadow: 0 0 15px rgba(76, 175, 80, 0.6) !important;
}

body.highlight-mode .key.highlighted {
    background: linear-gradient(to bottom, #ffeb3b, #ffc107) !important;
    box-shadow: 0 0 20px rgba(255, 235, 59, 0.8) !important;
    transform: translateY(2px);
    transition: all 0.1s ease;
}

.piano-container.keyboard-highlight-mode {
    box-shadow: inset 0 0 30px rgba(76, 175, 80, 0.3);
    background: linear-gradient(to bottom, #3e4a5c 0%, #2c3e50 50%, #263d2f 100%);
}

.piano-container.keyboard-highlight-mode .key {
    transition: all 0.3s ease;
}

.piano-container.keyboard-highlight-mode .key.pulse {
    animation: keyPulse 1s ease-in-out;
}

@keyframes keyPulse {
    0%, 100% { transform: scale(1); }
    50% { 
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(255, 255, 255, 0.3);
    }
}

/* Style des labels */
.key-label {
    position: absolute;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 14px;
    font-weight: 600;
    color: #666;
    pointer-events: none;
    user-select: none;
    background: rgba(255, 255, 255, 0.9);
    padding: 2px 6px;
    border-radius: 3px;
}

.key.black .key-label {
    color: #fff;
    background: transparent; /* Plus de fond noir */
    bottom: 10px;
    font-size: 11px;
    font-weight: bold;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8); /* Ombre pour la lisibilité */
}

/* Effet de surbrillance du clavier complet */
.piano-container.keyboard-highlight-mode::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, 
        transparent 30%, 
        rgba(255, 255, 255, 0.1) 50%, 
        transparent 70%);
    animation: shimmer 3s infinite;
    pointer-events: none;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Mode Aurore Boréale */
.piano-container.aurora-mode {
    background: linear-gradient(135deg, #000428 0%, #004e92 50%, #000428 100%);
    position: relative;
    overflow: hidden;
}

.piano-container.aurora-mode::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, 
        rgba(0, 255, 136, 0.1) 0%, 
        rgba(0, 255, 255, 0.1) 25%, 
        rgba(255, 0, 255, 0.1) 50%, 
        transparent 70%);
    animation: auroraRotate 20s linear infinite;
}

@keyframes auroraRotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.aurora-wave {
    animation: auroraGlow 3s ease-in-out infinite alternate;
}

@keyframes auroraGlow {
    0% { filter: brightness(1); }
    100% { filter: brightness(1.3); }
}

/* Nouveaux modes KEY ASSIST */
.key.scale-note {
    animation: scalePulse 2s ease-in-out infinite;
}

@keyframes scalePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

.key.chord-note {
    animation: chordGlow 1.5s ease-in-out infinite;
}

@keyframes chordGlow {
    0%, 100% { box-shadow: 0 0 15px rgba(255, 152, 0, 0.6); }
    50% { box-shadow: 0 0 25px rgba(255, 152, 0, 0.8); }
}

.key.finger-guide {
    position: relative;
}

.finger-number {
    animation: fingerBounce 0.5s ease-out;
}

@keyframes fingerBounce {
    0% { transform: translateX(-50%) scale(0.8); opacity: 0; }
    50% { transform: translateX(-50%) scale(1.1); }
    100% { transform: translateX(-50%) scale(1); opacity: 1; }
}

/* Mode pratique amélioré */
.piano-container.practice-mode {
    position: relative;
}

.piano-container.practice-mode::after {
    content: '🎯 PRACTICE MODE';
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(52, 152, 219, 0.9);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    z-index: 10;
}

        /* Contrôles audio dans menu SOUND */
        
        /* Style pour le select MIDI */
        .midi-select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #444;
            border-radius: 5px;
            background: #2c2c2c;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        
        .midi-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .midi-select option {
            background: #2c2c2c;
            color: #fff;
        }
        
        /* Styles pour les éléments de démo */
        .demo-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .demo-item:hover {
            background: rgba(102, 126, 234, 0.1);
            border-left-color: #667eea;
            transform: translateX(5px);
        }
        
        .demo-item-play {
            color: #28a745;
            font-weight: 600;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            background: rgba(40, 167, 69, 0.1);
            transition: all 0.2s ease;
        }
        
        .demo-item:hover .demo-item-play {
            background: rgba(40, 167, 69, 0.2);
            transform: scale(1.05);
        }
.audio-control {
    margin: 10px 0;
}

.control-label {
    display: block;
    color: #999;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.midi-select {
    width: 100%;
    background: #333;
    border: 1px solid #555;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}

.midi-select:hover {
    border-color: #666;
    background: #3a3a3a;
}

.volume-slider {
    width: calc(100% - 45px);
    height: 4px;
    background: #444;
    border-radius: 2px;
    outline: none;
    -webkit-appearance: none;
    vertical-align: middle;
}

.volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.volume-slider::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.volume-value {
    display: inline-block;
    width: 35px;
    text-align: right;
    color: #999;
    font-size: 11px;
    margin-left: 8px;
}

/* Styles pour les éléments toggle du menu KEY ASSIST */
.toggle-item {
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px !important;
    border-bottom: 1px solid #444;
    cursor: pointer;
    transition: background-color 0.2s ease;
    color: #ccc;
}

.toggle-item:hover {
    background-color: #3a3a3a;
}

.toggle-item:last-child {
    border-bottom: none;
}

.toggle-switch {
    width: 40px;
    height: 20px;
    background: #555;
    border-radius: 10px;
    position: relative;
    transition: background-color 0.3s ease;
}

.toggle-switch.on {
    background: #4CAF50;
}

.toggle-switch::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s ease;
}

.toggle-switch.on::after {
    transform: translateX(20px);
}

</style>