/**
 * Module HELP et VIDÉO - Piano Solo V1.2
 * Gestion des popups d'aide et de vidéo tutoriel
 */

window.HELP_VIDEO_MODULE = {
    init: function() {
        console.log('🎹 Initialisation du module HELP/VIDEO...');
        this.createStyles();
        this.setupEventListeners();
        console.log('✅ Module HELP/VIDEO initialisé');
    },
    
    createStyles: function() {
        // Créer les styles CSS pour les popups
        const styles = `
            .help-popup, .video-popup {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            .help-content, .video-content {
                background: white;
                border-radius: 15px;
                padding: 30px;
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                animation: slideIn 0.3s ease;
            }
            
            .video-content {
                max-width: 800px;
            }
            
            .help-content h3, .video-content h3 {
                color: #667eea;
                margin-bottom: 20px;
                font-size: 1.5em;
            }
            
            .help-content p {
                line-height: 1.6;
                color: #333;
                margin-bottom: 20px;
            }
            
            .help-content button, .video-content button {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .help-content button:hover, .video-content button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }
            
            .video-content video {
                width: 100%;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
            
            .close-btn {
                position: absolute;
                top: 15px;
                right: 20px;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                font-size: 20px;
                cursor: pointer;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }
            
            .close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideIn {
                from { 
                    opacity: 0;
                    transform: translateY(-50px) scale(0.9);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            
            @media (max-width: 768px) {
                .help-content, .video-content {
                    width: 95%;
                    padding: 20px;
                    max-height: 90vh;
                }
                
                .help-content h3, .video-content h3 {
                    font-size: 1.3em;
                }
            }
        `;
        
        const styleSheet = document.createElement('style');
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    },
    
    setupEventListeners: function() {
        // Les boutons sont maintenant dans le dropdown HELP, pas besoin d'event listeners globaux
        // Les boutons utilisent directement onclick="window.HELP_VIDEO_MODULE.showHelp()" et "window.HELP_VIDEO_MODULE.showVideo()"
        
        // Fermer les popups avec Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllPopups();
            }
        });
    },
    
    showHelp: function() {
        console.log('📚 Affichage du popup HELP...');
        
        fetch('admin-config.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Configuration HELP non trouvée');
                }
                return response.json();
            })
            .then(data => {
                this.createHelpPopup(data.help.title, data.help.content);
            })
            .catch(error => {
                console.error('Erreur chargement HELP:', error);
                this.createHelpPopup('Aide Piano Solo', 'Aide non disponible pour le moment.');
            });
    },
    
    showVideo: function() {
        console.log('🎥 Affichage du popup VIDÉO...');
        
        fetch('admin-config.json')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Configuration vidéo non trouvée');
                }
                return response.json();
            })
            .then(data => {
                if (data.video.enabled) {
                    this.createVideoPopup(data.video.title, data.video.filename, data.video.description);
                } else {
                    alert('La vidéo tutoriel n\'est pas activée.');
                }
            })
            .catch(error => {
                console.error('Erreur chargement vidéo:', error);
                alert('Vidéo tutoriel non disponible pour le moment.');
            });
    },
    
    createHelpPopup: function(title, content) {
        // Fermer les popups existants
        this.closeAllPopups();
        
        const popup = document.createElement('div');
        popup.className = 'help-popup';
        popup.innerHTML = `
            <button class="close-btn" onclick="window.HELP_VIDEO_MODULE.closeAllPopups()">✕</button>
            <div class="help-content">
                <h3>${this.escapeHtml(title)}</h3>
                <div>${this.formatContent(content)}</div>
                <button onclick="window.HELP_VIDEO_MODULE.closeAllPopups()">Fermer</button>
            </div>
        `;
        
        document.body.appendChild(popup);
        
        // Fermer en cliquant à l'extérieur
        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                this.closeAllPopups();
            }
        });
    },
    
    createVideoPopup: function(title, filename, description) {
        // Fermer les popups existants
        this.closeAllPopups();
        
        const popup = document.createElement('div');
        popup.className = 'video-popup';
        popup.innerHTML = `
            <button class="close-btn" onclick="window.HELP_VIDEO_MODULE.closeAllPopups()">✕</button>
            <div class="video-content">
                <h3>${this.escapeHtml(title)}</h3>
                ${description ? `<p>${this.escapeHtml(description)}</p>` : ''}
                <video controls autoplay>
                    <source src="videos/${this.escapeHtml(filename)}" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                </video>
                <button onclick="window.HELP_VIDEO_MODULE.closeAllPopups()">Fermer</button>
            </div>
        `;
        
        document.body.appendChild(popup);
        
        // Fermer en cliquant à l'extérieur
        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                this.closeAllPopups();
            }
        });
    },
    
    closeAllPopups: function() {
        const popups = document.querySelectorAll('.help-popup, .video-popup');
        popups.forEach(popup => {
            popup.remove();
        });
    },
    
    escapeHtml: function(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    formatContent: function(content) {
        // Convertir les retours à la ligne en <br>
        return content.replace(/\n/g, '<br>');
    }
};

// Initialisation automatique
document.addEventListener('DOMContentLoaded', function() {
    window.HELP_VIDEO_MODULE.init();
});
