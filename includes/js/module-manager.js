// includes/js/module-manager.js
// Gestionnaire centralisé pour l'initialisation des modules

const MODULE_MANAGER = (function() {
    'use strict';
    
    // Configuration
    const config = {
        modules: [],
        initialized: new Set(),
        failed: new Set(),
        maxRetries: 3,
        retryDelay: 1000
    };
    
    // Ajouter un module avec ses dépendances
    function register(moduleName, initFunction, dependencies = [], options = {}) {
        config.modules.push({
            name: moduleName,
            init: initFunction,
            deps: dependencies,
            retries: 0,
            options: options
        });
        console.log(`📋 Module enregistré: ${moduleName} (dépendances: ${dependencies.join(', ') || 'aucune'})`);
    }
    
    // Trier les modules par dépendances (topological sort)
    function sortByDependencies() {
        const sorted = [];
        const visited = new Set();
        const temp = new Set();
        
        function visit(module) {
            if (temp.has(module.name)) {
                throw new Error(`Dépendance circulaire détectée: ${module.name}`);
            }
            if (visited.has(module.name)) {
                return;
            }
            
            temp.add(module.name);
            
            // Visiter les dépendances d'abord
            for (const depName of module.deps) {
                const dep = config.modules.find(m => m.name === depName);
                if (dep) {
                    visit(dep);
                } else {
                    console.warn(`⚠️ Dépendance manquante: ${depName} pour ${module.name}`);
                }
            }
            
            temp.delete(module.name);
            visited.add(module.name);
            sorted.push(module);
        }
        
        for (const module of config.modules) {
            if (!visited.has(module.name)) {
                visit(module);
            }
        }
        
        return sorted;
    }
    
    // Initialiser un module avec retry
    async function initializeModule(module) {
        const maxRetries = module.options.maxRetries || config.maxRetries;
        const retryDelay = module.options.retryDelay || config.retryDelay;
        
        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            try {
                console.log(`🔄 Initialisation ${module.name} (tentative ${attempt}/${maxRetries})`);
                
                // Vérifier les dépendances
                for (const depName of module.deps) {
                    if (!config.initialized.has(depName)) {
                        throw new Error(`Dépendance non initialisée: ${depName}`);
                    }
                }
                
                // Initialiser le module
                const result = await module.init();
                config.initialized.add(module.name);
                console.log(`✅ ${module.name} initialisé avec succès`);
                return result;
                
            } catch (error) {
                console.error(`❌ Erreur ${module.name} (tentative ${attempt}):`, error.message);
                
                if (attempt === maxRetries) {
                    config.failed.add(module.name);
                    console.error(`💥 ${module.name} a échoué après ${maxRetries} tentatives`);
                    
                    // Mode dégradé si option activée
                    if (module.options.fallback) {
                        console.log(`🛡️ Activation du mode dégradé pour ${module.name}`);
                        try {
                            await module.options.fallback();
                            console.log(`✅ Mode dégradé activé pour ${module.name}`);
                        } catch (fallbackError) {
                            console.error(`💥 Mode dégradé échoué pour ${module.name}:`, fallbackError);
                        }
                    }
                    return false;
                }
                
                // Attendre avant de réessayer
                await new Promise(resolve => setTimeout(resolve, retryDelay));
            }
        }
    }
    
    // Initialiser tous les modules
    async function initialize() {
        console.log('🚀 Début de l\'initialisation des modules...');
        
        try {
            const sortedModules = sortByDependencies();
            console.log('📋 Ordre d\'initialisation:', sortedModules.map(m => m.name).join(' → '));
            
            for (const module of sortedModules) {
                await initializeModule(module);
            }
            
            // Rapport final
            const successCount = config.initialized.size;
            const failCount = config.failed.size;
            const totalCount = config.modules.length;
            
            console.log(`📊 Rapport d'initialisation:`);
            console.log(`   ✅ Succès: ${successCount}/${totalCount}`);
            console.log(`   ❌ Échecs: ${failCount}/${totalCount}`);
            
            if (failCount > 0) {
                console.log(`   💥 Modules échoués:`, Array.from(config.failed));
            }
            
            return {
                success: successCount,
                failed: failCount,
                total: totalCount,
                initialized: Array.from(config.initialized),
                failed: Array.from(config.failed)
            };
            
        } catch (error) {
            console.error('💥 Erreur critique lors de l\'initialisation:', error);
            throw error;
        }
    }
    
    // Vérifier l'état d'un module
    function isInitialized(moduleName) {
        return config.initialized.has(moduleName);
    }
    
    // Obtenir le statut global
    function getStatus() {
        return {
            initialized: Array.from(config.initialized),
            failed: Array.from(config.failed),
            pending: config.modules.filter(m => 
                !config.initialized.has(m.name) && !config.failed.has(m.name)
            ).map(m => m.name)
        };
    }
    
    // API publique
    return {
        register: register,
        initialize: initialize,
        isInitialized: isInitialized,
        getStatus: getStatus
    };
    
})();

// Exporter globalement
window.MODULE_MANAGER = MODULE_MANAGER;
