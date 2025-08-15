<!-- /includes/piano-recorder.php -->
<!-- Module Enregistreur compatible avec Harmonium - VERSION CORRIGÉE -->

<style>
/* Contrôles d'enregistrement */
.recorder-controls {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffeaa7;
    border-radius: 12px;
    padding: 2px;
    margin-bottom: 2px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.recorder-header {
    text-align: center;
    color: #856404;
    margin-bottom: 15px;
    font-size: 18px;
    font-weight: 600;
}

.recorder-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

/* Stats intégrées sur la même ligne */
.recorder-stats-inline {
    display: flex;
    gap: 15px;
    margin-left: 20px;
    align-items: center;
}

.recorder-btn {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.recorder-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.recorder-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-record {
    background: #dc3545;
    color: white;
}

.btn-record.recording {
    background: #8b0000;
    animation: recordPulse 1s ease-in-out infinite;
}

@keyframes recordPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.btn-stop {
    background: #6c757d;
    color: white;
}

.btn-play {
    background: #28a745;
    color: white;
}

.btn-play.playing {
    background: #1e7e34;
}

.btn-save {
    background: #007bff;
    color: white;
}

.btn-export {
    background: #17a2b8;
    color: white;
}

/* Informations d'enregistrement */
.recorder-info {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #495057;
}

.recorder-stat {
    display: flex;
    align-items: center;
    gap: 5px;
}

.recorder-stat-value {
    font-weight: 600;
    color: #212529;
}

/* Liste des enregistrements */
.recordings-list {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: white;
}

.recording-item {
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s ease;
}

.recording-item:hover {
    background: #e9ecef;
}

.recording-info {
    flex: 1;
}

.recording-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 3px;
}

.recording-meta {
    font-size: 12px;
    color: #6c757d;
}

.recording-actions {
    display: flex;
    gap: 5px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-sm:hover {
    transform: translateY(-1px);
}

.btn-play-sm {
    background: #28a745;
    color: white;
}

.btn-upload-sm {
    background: #17a2b8;
    color: white;
}

.btn-download-sm {
    background: #007bff;
    color: white;
}

.btn-delete-sm {
    background: #dc3545;
    color: white;
}

/* Compteur de temps */
.time-counter {
    font-family: 'Courier New', monospace;
    font-size: 20px;
    color: #dc3545;
    text-align: center;
    margin: 10px 0;
    font-weight: bold;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
    font-style: italic;
}

@media (max-width: 768px) {
    .recorder-info {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<div class="recorder-controls">
     

    
    <div class="recorder-buttons">
        <div class="time-counter" id="timeCounter">00:00</div>
        
        <!-- Boutons d'enregistrement -->
        <button class="recorder-btn btn-record" id="recordBtn" onclick="window.RECORDER_MODULE.toggleRecord()" title="Enregistrer">
            🔴 Enregistrer
        </button>
        
        <button class="recorder-btn btn-stop" id="stopBtn" onclick="window.RECORDER_MODULE.stop()" title="Arrêter" disabled>
            ⏹️ Arrêter
        </button>
        
        <button class="recorder-btn btn-play" id="playBtn" onclick="window.RECORDER_MODULE.togglePlay()" title="Écouter" disabled>
            ▶ Écouter
        </button>
        
        <button class="recorder-btn btn-save" id="saveBtn" onclick="window.RECORDER_MODULE.saveRecording()" title="Sauvegarder" disabled>
            💾 Sauvegarder
        </button>
        
        <!-- Stats intégrées sur la même ligne -->
        <div class="recorder-stats-inline">
            <span class="recorder-stat">
                <span>Notes:</span>
                <span class="recorder-stat-value" id="noteCount">0</span>
            </span>
            <span class="recorder-stat">
                <span>Durée:</span>
                <span class="recorder-stat-value" id="duration">0s</span>
            </span>
        </div>
    </div>
    
    <!-- Le tableau temporaire a été supprimé - les enregistrements vont directement dans le brouillon -->
</div>

<script>
// Module d'enregistrement - VERSION CORRIGÉE pour compatibilité Harmonium
const RECORDER_MODULE = (function() {
    'use strict';
    
         // État
     const state = {
         isRecording: false,
         isPlaying: false,
         startTime: null,
         currentRecording: null,
         playbackTimeouts: [],
         recordingTimer: null,
         noteTimestamps: new Map(),
         originalFunctions: {
             playNote: null,
             stopNote: null
         }
     };
    
    // Configuration
    const config = {
        maxRecordingTime: 300000, // 5 minutes max
        defaultTimeSignature: '4/4'
    };
    
    // Créer un nouvel enregistrement
    function createNewRecording() {
        return {
            name: '',
            timeSignature: config.defaultTimeSignature,
            notes: [],
            duration: 0,
            recordedAt: new Date().toISOString(),
            noteCount: 0,
            metadata: {
                version: '1.0',
                format: 'enriched_json',
                harmonium: window.HARMONIUM_MODULE && window.HARMONIUM_MODULE.isLoaded()
            }
        };
    }
    
    // Intercepter les événements de notes - VERSION CORRIGÉE
    function interceptNoteEvents() {
        console.log('🎙️ Interception des événements de notes...');
        
        // Attendre un peu pour que l'harmonium soit chargé
        setTimeout(() => {
            // Sauvegarder les fonctions actuelles (qui peuvent être celles de l'harmonium)
            if (!state.originalFunctions.playNote) {
                state.originalFunctions.playNote = window.playNote;
                state.originalFunctions.stopNote = window.stopNote;
            }
            
            // Créer nos wrappers
            window.playNote = function(noteId, keyElement) {
                // Appeler la fonction originale (harmonium ou core)
                if (state.originalFunctions.playNote) {
                    state.originalFunctions.playNote(noteId, keyElement);
                }
                
                // Enregistrer si nécessaire
                if (state.isRecording && state.startTime) {
                    const timestamp = Date.now() - state.startTime;
                    state.currentRecording.notes.push({
                        note: noteId,
                        time: timestamp,
                        type: 'on',
                        velocity: 80
                    });
                    state.noteTimestamps.set(noteId, timestamp);
                    updateStats();
                    console.log(`📝 Note enregistrée: ${noteId} à ${timestamp}ms`);
                }
            };
            
            window.stopNote = function(noteId, keyElement) {
                // Appeler la fonction originale
                if (state.originalFunctions.stopNote) {
                    state.originalFunctions.stopNote(noteId, keyElement);
                }
                
                // Enregistrer si nécessaire
                if (state.isRecording && state.startTime) {
                    const timestamp = Date.now() - state.startTime;
                    state.currentRecording.notes.push({
                        note: noteId,
                        time: timestamp,
                        type: 'off',
                        velocity: 0
                    });
                    
                    if (state.noteTimestamps.has(noteId)) {
                        state.noteTimestamps.delete(noteId);
                    }
                }
            };
            
            console.log('✅ Fonctions interceptées avec succès');
        }, 1500); // Attendre 1.5s pour que l'harmonium soit chargé
    }
    

    
    // Basculer l'enregistrement
    function toggleRecord() {
        if (state.isRecording) {
            stopRecording();
        } else {
            startRecording();
        }
    }
    
    // Démarrer l'enregistrement
    function startRecording() {
        if (state.isPlaying) {
            stopPlayback();
        }
        
        state.isRecording = true;
        state.startTime = Date.now();
        state.currentRecording = createNewRecording();
        state.noteTimestamps.clear();
        
        // UI
        document.getElementById('recordBtn').classList.add('recording');
        document.getElementById('recordBtn').innerHTML = '⏺ En cours...';
        document.getElementById('stopBtn').disabled = false;
        document.getElementById('playBtn').disabled = true;
        document.getElementById('saveBtn').disabled = true;
        
        startTimer();
        
        console.log('🔴 Enregistrement démarré');
        
        // Notification si SAVE_UPLOAD_MODULE existe
        if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
            window.SAVE_UPLOAD_MODULE.showNotification('Enregistrement démarré', 'success');
        }
    }
    
    // Arrêter l'enregistrement
    function stopRecording() {
        state.isRecording = false;
        
        // Calculer les données finales
        if (state.currentRecording && state.currentRecording.notes.length > 0) {
            const lastNote = state.currentRecording.notes[state.currentRecording.notes.length - 1];
            state.currentRecording.duration = lastNote.time + 500;
            state.currentRecording.noteCount = state.currentRecording.notes.filter(n => n.type === 'on').length;
            
            console.log(`✅ Enregistrement terminé: ${state.currentRecording.noteCount} notes`);
        }
        
        // UI
        document.getElementById('recordBtn').classList.remove('recording');
        document.getElementById('recordBtn').innerHTML = '⏺ Enregistrer';
        document.getElementById('stopBtn').disabled = true;
        
        stopTimer();
        
        if (state.currentRecording && state.currentRecording.notes.length > 0) {
            document.getElementById('playBtn').disabled = false;
            document.getElementById('saveBtn').disabled = false;
            
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification(`Enregistrement terminé: ${state.currentRecording.noteCount} notes`, 'success');
            }
        } else {
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Aucune note enregistrée', 'error');
            }
        }
        
        updateStats();
    }
    
    // Arrêter tout
    function stop() {
        if (state.isRecording) {
            stopRecording();
        }
        if (state.isPlaying) {
            stopPlayback();
        }
    }
    
    // Basculer la lecture
    function togglePlay() {
        if (state.isPlaying) {
            stopPlayback();
        } else {
            playRecording(state.currentRecording);
        }
    }
    
    // Jouer un enregistrement
    function playRecording(recording, button = null) {
        if (!recording || !recording.notes || recording.notes.length === 0) return;
        
        if (state.isRecording) {
            stopRecording();
        }
        
        state.isPlaying = true;
        
        // UI
        const playBtn = button || document.getElementById('playBtn');
        playBtn.classList.add('playing');
        playBtn.innerHTML = '⏸ Pause';
        
        console.log(`▶️ Lecture de ${recording.notes.length} événements`);
        
        // Jouer chaque note en utilisant les fonctions originales
        recording.notes.forEach(event => {
            const timeout = setTimeout(() => {
                const key = document.querySelector(`.key[data-note="${event.note}"]`);
                if (key) {
                    if (event.type === 'on') {
                        // Utiliser la fonction originale pour la lecture
                        if (state.originalFunctions.playNote) {
                            state.originalFunctions.playNote(event.note, key);
                        }
                    } else {
                        if (state.originalFunctions.stopNote) {
                            state.originalFunctions.stopNote(event.note, key);
                        }
                    }
                }
            }, event.time);
            
            state.playbackTimeouts.push(timeout);
        });
        
        // Arrêter à la fin
        const endTimeout = setTimeout(() => {
            stopPlayback();
        }, recording.duration);
        
        state.playbackTimeouts.push(endTimeout);
    }
    
    // Arrêter la lecture
    function stopPlayback() {
        console.log('🛑 Arrêt de la lecture...');
        state.isPlaying = false;
        state.currentDemoId = null; // Réinitialiser l'ID de la démo en cours
        
        // Annuler tous les timeouts
        state.playbackTimeouts.forEach(timeout => clearTimeout(timeout));
        state.playbackTimeouts = [];
        
        // Annuler tous les timeouts de lecture de démo
        if (window.currentPlaybackTimeouts) {
            window.currentPlaybackTimeouts.forEach(timeout => clearTimeout(timeout));
            window.currentPlaybackTimeouts = [];
        }
        
        // Arrêter tous les oscillateurs actifs
        if (window.activeOscillators) {
            window.activeOscillators.forEach(oscillator => {
                try {
                    oscillator.stop();
                } catch (e) {
                    // Ignorer les erreurs si l'oscillateur est déjà arrêté
                }
            });
            window.activeOscillators.clear();
        }
        
        // UI
        document.querySelectorAll('.btn-play, .btn-play-sm').forEach(btn => {
            btn.classList.remove('playing');
            if (btn.id === 'playBtn') {
                btn.innerHTML = '▶ Écouter';
            } else {
                btn.innerHTML = '▶';
            }
        });
        
        // Arrêter toutes les notes actives
        document.querySelectorAll('.key.active').forEach(key => {
            const noteId = key.dataset.note;
            if (state.originalFunctions.stopNote) {
                state.originalFunctions.stopNote(noteId, key);
            }
        });
        
        console.log('✅ Lecture arrêtée');
    }
    
    // Sauvegarder l'enregistrement directement dans le brouillon
    function saveRecording() {
        const name = prompt('Nom du brouillon:', `Brouillon ${new Date().toLocaleTimeString()}`);
        if (!name) return;
        
        // Préparer les données pour l'API
        const demoData = {
            name: name,
            category: 'brouillon',
            description: 'Enregistrement direct depuis le piano',
            recording: {
                notes: state.currentRecording.notes,
                durations: state.currentRecording.notes.map(note => note.time),
                timestamp: Date.now()
            },
            uploadedAt: new Date().toISOString()
        };
        
        // Sauvegarder directement via l'API
        fetch('demo-manager-api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'saveNewDemo',
                demoData: demoData,
                originalName: name
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Brouillon "${name}" sauvegardé!`, 'success');
                }
                
                // Recharger la liste des brouillons
                setTimeout(() => {
                    if (window.DEMO_MANAGER && window.DEMO_MANAGER.loadTable) {
                        window.DEMO_MANAGER.loadTable('brouillon');
                    }
                }, 500);
            } else {
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification(`Erreur: ${data.message}`, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la sauvegarde:', error);
            if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                window.SAVE_UPLOAD_MODULE.showNotification('Erreur lors de la sauvegarde', 'error');
            }
        });
        
        // Réinitialiser
        state.currentRecording = null;
        document.getElementById('playBtn').disabled = true;
        document.getElementById('saveBtn').disabled = true;
        updateStats();
    }
    
    // Timer
    function startTimer() {
        const timerEl = document.getElementById('timeCounter');
        const startTime = Date.now();
        
        state.recordingTimer = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (elapsed >= config.maxRecordingTime) {
                stopRecording();
                if (window.SAVE_UPLOAD_MODULE && window.SAVE_UPLOAD_MODULE.showNotification) {
                    window.SAVE_UPLOAD_MODULE.showNotification('Durée maximale atteinte (5 min)', 'error');
                }
            }
        }, 100);
    }
    
    function stopTimer() {
        clearInterval(state.recordingTimer);
        document.getElementById('timeCounter').textContent = '00:00';
    }
    
    // Mettre à jour les statistiques
    function updateStats() {
        if (state.currentRecording) {
            document.getElementById('noteCount').textContent = state.currentRecording.noteCount || 0;
            document.getElementById('duration').textContent = Math.ceil((state.currentRecording.duration || 0) / 1000) + 's';
        } else {
            document.getElementById('noteCount').textContent = '0';
            document.getElementById('duration').textContent = '0s';
        }
    }
    
    // Fonction supprimée - les enregistrements vont directement dans le brouillon
    
    // Fonctions supprimées - les enregistrements vont directement dans le brouillon
    
         // Fonctions supprimées - les enregistrements vont directement dans le brouillon
    
    // Initialisation
    function init() {
        console.log('🎵 Initialisation du module Enregistreur...');
        
        interceptNoteEvents();
        
        // Raccourcis clavier
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space' && !e.target.matches('input, textarea')) {
                e.preventDefault();
                if (state.isRecording) {
                    stopRecording();
                } else if (state.isPlaying) {
                    stopPlayback();
                } else {
                    toggleRecord();
                }
            }
        });
        
        console.log('✅ Module Enregistreur prêt !');
    }
    
         // API publique
     return {
         init: init,
         toggleRecord: toggleRecord,
         stop: stop,
         togglePlay: togglePlay,
         saveRecording: saveRecording,
         playRecording: function(recordingData = null, buttonElement = null) {
             if (recordingData && recordingData.notes) {
                 // Lecture directe des données de démo (depuis piano-demo-manager.php)
                 console.log('🎵 Lecture directe des données de démo:', recordingData);
                 
                 // Si on clique sur le même bouton qui est déjà en lecture, on arrête
                 if (state.isPlaying && state.currentDemoId === 'direct-playback') {
                     console.log('🛑 Arrêt de la lecture en cours...');
                     stopPlayback();
                     return false;
                 }
                 
                 // Arrêter toute lecture en cours
                 if (state.isPlaying) {
                     stopPlayback();
                 }
                 
                 // Marquer cette démo comme en cours de lecture
                 state.currentDemoId = 'direct-playback';
                 
                 // Jouer directement les notes
                 this.playRecordingNotes(recordingData.notes);
                 return true;
             } else if (recordingData) {
                 // Lecture d'une démo spécifique (ancienne méthode)
                 console.log('🎵 Lecture de démo spécifique:', recordingData);
                 const demoId = recordingData.id || recordingData.filename || 'demo';
                 const library = recordingData.library || 'brouillon';
                 return this.playDemo(demoId, library);
             } else {
                 // Lecture de l'enregistrement actuel
                 return togglePlay();
             }
         },
         stopPlayback: stopPlayback, // Fonction pour arrêter la lecture
         playDemo: function(demoId, library) {
             console.log(`🎵 Lecture de démo: ${demoId} (${library})`);
             
             // Si on clique sur le même bouton qui est déjà en lecture, on arrête
             if (state.isPlaying && state.currentDemoId === demoId) {
                 console.log('🛑 Arrêt de la lecture en cours...');
                 stopPlayback();
                 return false;
             }
             
             // Arrêter toute lecture en cours
             if (state.isPlaying) {
                 stopPlayback();
             }
             
             // Construire le bon nom de fichier
             let demoFile = '';
             if (library === 'brouillon') {
                 // Pour brouillon, utiliser le demoId qui contient le nom du fichier
                 demoFile = `demospubliques/brouillon/${demoId}`;
             } else {
                 demoFile = `demospubliques/${library}/${demoId}`;
             }
             
             console.log(`📁 Chargement de: ${demoFile}`);
             
             fetch(demoFile)
                 .then(response => {
                     if (!response.ok) {
                         throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                     }
                     return response.json();
                 })
                 .then(data => {
                     console.log('📄 Données de démo chargées:', data);
                     
                     if (data && data.data && data.data.recording && data.data.recording.notes) {
                         console.log('🎵 Démarrage de la lecture audio...');
                         // Marquer cette démo comme en cours de lecture
                         state.currentDemoId = demoId;
                         this.playRecordingNotes(data.data.recording.notes);
                     } else {
                         console.error('Format de démo invalide:', data);
                     }
                 })
                 .catch(error => {
                     console.error('Erreur lors du chargement de la démo:', error);
                 });
             
             return true;
         },
         
         playRecordingNotes: function(notes) {
             if (!notes || notes.length === 0) {
                 console.log('Aucune note à jouer');
                 return;
             }
             
             console.log(`🎵 Lecture de ${notes.length} notes...`);
             
             // Arrêter toute lecture en cours
             if (state.isPlaying) {
                 stopPlayback();
             }
             
             state.isPlaying = true;
             state.isPaused = false;
             state.currentNotes = notes;
             state.currentTime = 0;
             
             // Mettre à jour l'UI
             document.querySelectorAll('.btn-play, .btn-play-sm').forEach(btn => {
                 btn.classList.add('playing');
                 if (btn.id === 'playBtn') {
                     btn.innerHTML = '⏸️ Écouter';
                 } else {
                     btn.innerHTML = '⏸️';
                 }
             });
             
             // Ajouter les contrôles de pause si ils n'existent pas
             this.addPauseControls();
             
             // Initialiser l'audio si nécessaire
             if (!audioContext) {
                 initAudio();
             }
             
             // Créer un oscillateur pour chaque note
             window.activeOscillators = new Map();
             
             // Stocker tous les timeouts pour pouvoir les annuler
             window.currentPlaybackTimeouts = [];
             
             // Jouer chaque note selon son timing
             notes.forEach(note => {
                 const delay = note.time;
                 
                 const timeoutId = setTimeout(() => {
                     // Vérifier si la lecture est toujours active ET pas en pause
                     if (!state.isPlaying || state.isPaused) {
                         return;
                     }
                     
                     // Mettre à jour le temps actuel
                     state.currentTime = note.time;
                     
                     // Mettre à jour le slider de timeline
                     if (window.currentPlaybackTimeouts && window.currentPlaybackTimeouts.length > 0) {
                         const totalDuration = state.currentNotes[state.currentNotes.length - 1].time;
                         const percentage = (note.time / totalDuration) * 100;
                         const slider = document.getElementById('unifiedTimelineSlider');
                         if (slider) {
                             slider.value = percentage;
                         }
                         
                         // Mettre à jour l'affichage du temps
                         const minutes = Math.floor(note.time / 60000);
                         const seconds = Math.floor((note.time % 60000) / 1000);
                         const totalMinutes = Math.floor(totalDuration / 60000);
                         const totalSeconds = Math.floor((totalDuration % 60000) / 1000);
                         
                         const timeDisplay = document.getElementById('unifiedTimeDisplay');
                         if (timeDisplay) {
                             timeDisplay.textContent = 
                                 `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} / ${totalMinutes.toString().padStart(2, '0')}:${totalSeconds.toString().padStart(2, '0')}`;
                         }
                     }
                     
                     if (note.type === 'on') {
                         // Jouer la note avec Web Audio API
                         console.log(`🎵 Jouer note: ${note.note}`);
                         
                         // Convertir la note en fréquence
                         const frequency = this.noteToFrequency(note.note);
                         if (frequency) {
                             const oscillator = audioContext.createOscillator();
                             const gainNode = audioContext.createGain();
                             
                             oscillator.connect(gainNode);
                             gainNode.connect(audioContext.destination);
                             
                             oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
                             oscillator.type = 'sine';
                             
                             gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                             gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                             
                             oscillator.start(audioContext.currentTime);
                             oscillator.stop(audioContext.currentTime + 0.5);
                             
                             window.activeOscillators.set(note.note, oscillator);
                         }
                         
                         // Essayer aussi la méthode originale
                         if (state.originalFunctions.playNote) {
                             const keyElement = document.querySelector(`[data-note="${note.note}"]`);
                             if (keyElement) {
                                 state.originalFunctions.playNote(note.note, keyElement);
                             }
                         }
                         
                     } else if (note.type === 'off') {
                         // Arrêter la note
                         console.log(`🔇 Arrêter note: ${note.note}`);
                         
                         const oscillator = window.activeOscillators.get(note.note);
                         if (oscillator) {
                             oscillator.stop();
                             window.activeOscillators.delete(note.note);
                         }
                         
                         if (state.originalFunctions.stopNote) {
                             const keyElement = document.querySelector(`[data-note="${note.note}"]`);
                             if (keyElement) {
                                 state.originalFunctions.stopNote(note.note, keyElement);
                             }
                         }
                     }
                 }, delay);
                 
                 window.currentPlaybackTimeouts.push(timeoutId);
             });
             
             // Arrêter la lecture après la dernière note
             const lastNote = notes[notes.length - 1];
             const totalDuration = lastNote.time + 1000; // +1 seconde après la dernière note
             
             const endTimeoutId = setTimeout(() => {
                 if (state.isPlaying) {
                     stopPlayback();
                 }
             }, totalDuration);
             
             window.currentPlaybackTimeouts.push(endTimeoutId);
         },
         
         noteToFrequency: function(note) {
             const noteFrequencies = {
                 'C3': 130.81, 'C#3': 138.59, 'D3': 146.83, 'D#3': 155.56,
                 'E3': 164.81, 'F3': 174.61, 'F#3': 185.00, 'G3': 196.00,
                 'G#3': 207.65, 'A3': 220.00, 'A#3': 233.08, 'B3': 246.94,
                 'C4': 261.63, 'C#4': 277.18, 'D4': 293.66, 'D#4': 311.13,
                 'E4': 329.63, 'F4': 349.23, 'F#4': 369.99, 'G4': 392.00,
                 'G#4': 415.30, 'A4': 440.00, 'A#4': 466.16, 'B4': 493.88,
                 'C5': 523.25, 'C#5': 554.37, 'D5': 587.33, 'D#5': 622.25,
                 'E5': 659.25, 'F5': 698.46, 'F#5': 739.99, 'G5': 783.99,
                 'G#5': 830.61, 'A5': 880.00, 'A#5': 932.33, 'B5': 987.77,
                 'C6': 1046.50
             };
             
             return noteFrequencies[note] || null;
         },
         
         addPauseControls: function() {
             // Utiliser la nouvelle barre de contrôle unifiée
             const unifiedControls = document.getElementById('unifiedControls');
             if (unifiedControls) {
                 unifiedControls.style.display = 'flex';
                 
                 // Événements des boutons unifiés
                 document.getElementById('unifiedPauseBtn').onclick = () => this.pausePlayback();
                 document.getElementById('unifiedResumeBtn').onclick = () => this.resumePlayback();
                 document.getElementById('unifiedStopBtn').onclick = () => stopPlayback();
                 
                 // Timeline slider unifié
                 document.getElementById('unifiedTimelineSlider').oninput = (e) => {
                     this.seekToTime(parseInt(e.target.value));
                 };
             }
         },
         
         pausePlayback: function() {
             if (state.isPlaying && !state.isPaused) {
                 state.isPaused = true;
                 console.log('⏸️ Lecture en pause');
                 
                 // Mettre à jour l'UI unifiée - BOUTON SANS TEXTE
                 const pauseBtn = document.getElementById('unifiedPauseBtn');
                 if (pauseBtn) {
                     // JAMAIS de texte dans ce bouton
                     pauseBtn.textContent = '';
                     pauseBtn.innerHTML = '';
                     // Garder la couleur jaune et la taille originale
                     pauseBtn.style.background = '#ffc107 !important';
                     pauseBtn.style.color = '#000 !important';
                     pauseBtn.style.fontSize = '13px !important';
                     pauseBtn.style.padding = '8px 12px !important';
                     pauseBtn.style.width = '30px !important';
                     pauseBtn.style.minWidth = '30px !important';
                     pauseBtn.style.height = '30px !important';
                     pauseBtn.style.minHeight = '30px !important';
                 }
                 
                 // Afficher les notes actives à ce moment
                 this.showActiveNotesAtTime(state.currentTime);
             }
         },
         
         resumePlayback: function() {
             if (state.isPlaying && state.isPaused) {
                 state.isPaused = false;
                 console.log('▶️ Reprise de la lecture');
                 
                 // Mettre à jour l'UI unifiée - BOUTON SANS TEXTE
                 const pauseBtn = document.getElementById('unifiedPauseBtn');
                 if (pauseBtn) {
                     // JAMAIS de texte dans ce bouton
                     pauseBtn.textContent = '';
                     pauseBtn.innerHTML = '';
                     // Remettre la couleur jaune et garder la taille originale
                     pauseBtn.style.background = '#ffc107 !important';
                     pauseBtn.style.color = '#000 !important';
                     pauseBtn.style.fontSize = '13px !important';
                     pauseBtn.style.padding = '8px 12px !important';
                     pauseBtn.style.width = '30px !important';
                     pauseBtn.style.minWidth = '30px !important';
                     pauseBtn.style.height = '30px !important';
                     pauseBtn.style.minHeight = '30px !important';
                 }
                 
                 // Continuer la lecture depuis le temps actuel
                 this.continuePlaybackFromTime(state.currentTime);
             }
         },
         
         showActiveNotesAtTime: function(time) {
             // Trouver toutes les notes actives à ce moment précis
             const activeNotes = [];
             
             state.currentNotes.forEach(note => {
                 if (note.type === 'on' && note.time <= time) {
                     // Vérifier si la note n'a pas été arrêtée avant ce temps
                     const noteOff = state.currentNotes.find(n => 
                         n.type === 'off' && n.note === note.note && n.time > note.time && n.time <= time
                     );
                     
                     if (!noteOff) {
                         activeNotes.push(note.note);
                     }
                 }
             });
             
             console.log(`🎵 Notes actives à ${time}ms:`, activeNotes);
             
             // Afficher visuellement les notes actives
             this.highlightActiveNotes(activeNotes);
         },
         
         highlightActiveNotes: function(activeNotes) {
             // Supprimer tous les highlights précédents
             document.querySelectorAll('.key.active-pause').forEach(key => {
                 key.classList.remove('active-pause');
             });
             
             // Ajouter le highlight aux notes actives
             activeNotes.forEach(noteName => {
                 const keyElement = document.querySelector(`[data-note="${noteName}"]`);
                 if (keyElement) {
                     keyElement.classList.add('active-pause');
                     keyElement.style.backgroundColor = '#ffeb3b';
                     keyElement.style.boxShadow = '0 0 10px #ffeb3b';
                 }
             });
         },
         
         seekToTime: function(percentage) {
             if (!state.currentNotes || state.currentNotes.length === 0) return;
             
             const totalDuration = state.currentNotes[state.currentNotes.length - 1].time;
             const targetTime = (percentage / 100) * totalDuration;
             
             state.currentTime = targetTime;
             
             // Mettre à jour l'affichage du temps
             const minutes = Math.floor(targetTime / 60000);
             const seconds = Math.floor((targetTime % 60000) / 1000);
             const totalMinutes = Math.floor(totalDuration / 60000);
             const totalSeconds = Math.floor((totalDuration % 60000) / 1000);
             
             const timeDisplay = document.getElementById('unifiedTimeDisplay');
             if (timeDisplay) {
                 timeDisplay.textContent = 
                     `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} / ${totalMinutes.toString().padStart(2, '0')}:${totalSeconds.toString().padStart(2, '0')}`;
             }
             
             // Afficher les notes actives à ce moment
             this.showActiveNotesAtTime(targetTime);
         },
         
         continuePlaybackFromTime: function(startTime) {
             if (!state.currentNotes || state.currentNotes.length === 0) return;
             
             console.log(`▶️ Reprise de la lecture depuis ${startTime}ms`);
             
             // Trouver les notes qui doivent être jouées après ce temps
             const remainingNotes = state.currentNotes.filter(note => note.time > startTime);
             
             // Annuler tous les timeouts existants
             if (window.currentPlaybackTimeouts) {
                 window.currentPlaybackTimeouts.forEach(timeout => clearTimeout(timeout));
                 window.currentPlaybackTimeouts = [];
             }
             
             // Créer de nouveaux timeouts pour les notes restantes
             remainingNotes.forEach(note => {
                 const delay = note.time - startTime;
                 
                 const timeoutId = setTimeout(() => {
                     // Vérifier si la lecture est toujours active ET pas en pause
                     if (!state.isPlaying || state.isPaused) {
                         return;
                     }
                     
                     // Mettre à jour le temps actuel
                     state.currentTime = note.time;
                     
                     // Mettre à jour le slider de timeline
                     const totalDuration = state.currentNotes[state.currentNotes.length - 1].time;
                     const percentage = (note.time / totalDuration) * 100;
                     const slider = document.getElementById('unifiedTimelineSlider');
                     if (slider) {
                         slider.value = percentage;
                     }
                     
                     // Mettre à jour l'affichage du temps
                     const minutes = Math.floor(note.time / 60000);
                     const seconds = Math.floor((note.time % 60000) / 1000);
                     const totalMinutes = Math.floor(totalDuration / 60000);
                     const totalSeconds = Math.floor((totalDuration % 60000) / 1000);
                     
                     const timeDisplay = document.getElementById('unifiedTimeDisplay');
                     if (timeDisplay) {
                         timeDisplay.textContent = 
                             `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} / ${totalMinutes.toString().padStart(2, '0')}:${totalSeconds.toString().padStart(2, '0')}`;
                     }
                     
                     if (note.type === 'on') {
                         console.log(`🎵 Jouer note: ${note.note}`);
                         
                         const frequency = this.noteToFrequency(note.note);
                         if (frequency) {
                             const oscillator = audioContext.createOscillator();
                             const gainNode = audioContext.createGain();
                             
                             oscillator.connect(gainNode);
                             gainNode.connect(audioContext.destination);
                             
                             oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
                             oscillator.type = 'sine';
                             
                             gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                             gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                             
                             oscillator.start(audioContext.currentTime);
                             oscillator.stop(audioContext.currentTime + 0.5);
                             
                             window.activeOscillators.set(note.note, oscillator);
                         }
                         
                         if (state.originalFunctions.playNote) {
                             const keyElement = document.querySelector(`[data-note="${note.note}"]`);
                             if (keyElement) {
                                 state.originalFunctions.playNote(note.note, keyElement);
                             }
                         }
                         
                     } else if (note.type === 'off') {
                         console.log(`🔇 Arrêter note: ${note.note}`);
                         
                         const oscillator = window.activeOscillators.get(note.note);
                         if (oscillator) {
                             oscillator.stop();
                             window.activeOscillators.delete(note.note);
                         }
                         
                         if (state.originalFunctions.stopNote) {
                             const keyElement = document.querySelector(`[data-note="${note.note}"]`);
                             if (keyElement) {
                                 state.originalFunctions.stopNote(note.note, keyElement);
                             }
                         }
                     }
                 }, delay);
                 
                 window.currentPlaybackTimeouts.push(timeoutId);
             });
             
             // Ajouter le timeout de fin
             const lastNote = state.currentNotes[state.currentNotes.length - 1];
             const totalDuration = lastNote.time + 1000;
             const endDelay = totalDuration - startTime;
             
             const endTimeoutId = setTimeout(() => {
                 if (state.isPlaying) {
                     stopPlayback();
                 }
             }, endDelay);
             
             window.currentPlaybackTimeouts.push(endTimeoutId);
         },
         getState: () => ({
             isRecording: state.isRecording,
             isPlaying: state.isPlaying,
             hasCurrentRecording: state.currentRecording !== null
         })
     };
})();

// Assigner le module à window pour qu'il soit accessible globalement
window.RECORDER_MODULE = RECORDER_MODULE;

// Initialiser après que tout soit chargé
window.addEventListener('DOMContentLoaded', () => {
    // Attendre plus longtemps pour que l'harmonium soit chargé
    setTimeout(() => {
        RECORDER_MODULE.init();
    }, 2000); // 2 secondes pour être sûr
});
</script>