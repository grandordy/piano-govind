<?php
/**
 * Script de migration robuste vers le système de noms dual
 * Applique le système de noms dual à toutes les démos existantes
 */

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "🎯 Migration robuste vers le système de noms dual...\n";
echo "==================================================\n\n";

class RobustMigration {
    private $libraries = [
        'demospubliques/brouillon' => 'Brouillon',
        'demospubliques/prayers' => 'Prayers', 
        'demospubliques/bhajans' => 'Bhajans'
    ];
    
    private $backupDir = 'backups/';
    private $migrationLog = [];
    
    public function __construct() {
        // Créer le dossier de backup
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
        
        // Créer le fichier de log
        $this->migrationLog[] = "=== Migration démarrée le " . date('Y-m-d H:i:s') . " ===";
    }
    
    /**
     * Générer un nom technique unique
     */
    private function generateTechnicalName($originalName = '') {
        $timestamp = time();
        $hash = substr(md5($originalName . $timestamp . uniqid()), 0, 8);
        return "demo_{$timestamp}_{$hash}";
    }
    
    /**
     * Extraire un nom d'affichage lisible
     */
    private function extractDisplayName($demo) {
        // Priorité 1: originalName
        if (!empty($demo['originalName'])) {
            return $this->cleanDisplayName($demo['originalName']);
        }
        
        // Priorité 2: name dans data
        if (isset($demo['data']['name']) && !empty($demo['data']['name'])) {
            return $this->cleanDisplayName($demo['data']['name']);
        }
        
        // Priorité 3: extraire du filename
        if (!empty($demo['filename'])) {
            return $this->extractNameFromFilename($demo['filename']);
        }
        
        // Fallback
        return 'Démo sans nom';
    }
    
    /**
     * Nettoyer un nom d'affichage
     */
    private function cleanDisplayName($name) {
        // Supprimer les extensions
        $name = preg_replace('/\.json$/', '', $name);
        
        // Supprimer les préfixes techniques
        $name = preg_replace('/^demo_/', '', $name);
        
        // Nettoyer les caractères spéciaux
        $name = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $name);
        
        // Remplacer les underscores par des espaces
        $name = str_replace('_', ' ', $name);
        
        // Capitaliser
        $name = ucwords(trim($name));
        
        // Limiter la longueur
        if (strlen($name) > 50) {
            $name = substr($name, 0, 47) . '...';
        }
        
        return $name ?: 'Démo sans nom';
    }
    
    /**
     * Extraire un nom du filename
     */
    private function extractNameFromFilename($filename) {
        // Supprimer l'extension
        $filename = str_replace('.json', '', $filename);
        
        // Supprimer le préfixe demo_
        $filename = preg_replace('/^demo_/', '', $filename);
        
        // Extraire la partie descriptive (avant le premier timestamp)
        $parts = explode('_', $filename);
        $descriptiveParts = [];
        
        foreach ($parts as $part) {
            // Si c'est un timestamp (10+ chiffres) ou un ID hex (8+ caractères), arrêter
            if (is_numeric($part) && strlen($part) >= 10) {
                break;
            }
            if (strlen($part) >= 8 && ctype_xdigit($part)) {
                break;
            }
            $descriptiveParts[] = $part;
        }
        
        $name = implode(' ', $descriptiveParts);
        return $this->cleanDisplayName($name);
    }
    
    /**
     * Créer un backup avant modification
     */
    private function createBackup($filepath) {
        $backupPath = $this->backupDir . basename($filepath) . '.backup.' . date('Y-m-d_H-i-s');
        
        if (file_exists($filepath)) {
            if (copy($filepath, $backupPath)) {
                $this->migrationLog[] = "✅ Backup créé: " . basename($backupPath);
                return true;
            } else {
                $this->migrationLog[] = "❌ Erreur backup: " . $filepath;
                return false;
            }
        }
        return true; // Pas de fichier à sauvegarder
    }
    
    /**
     * Migrer une bibliothèque
     */
    public function migrateLibrary($libraryPath) {
        echo "\n📁 Migration de: $libraryPath\n";
        echo str_repeat('-', 50) . "\n";
        
        if (!is_dir($libraryPath)) {
            echo "❌ Dossier non trouvé: $libraryPath\n";
            return false;
        }
        
        $indexFile = $libraryPath . '/index.json';
        if (!file_exists($indexFile)) {
            echo "❌ Fichier index.json non trouvé: $indexFile\n";
            return false;
        }
        
        // Créer un backup
        if (!$this->createBackup($indexFile)) {
            echo "❌ Impossible de créer le backup\n";
            return false;
        }
        
        // Lire l'index
        $index = json_decode(file_get_contents($indexFile), true);
        if (!$index) {
            echo "❌ Erreur de lecture JSON: $indexFile\n";
            return false;
        }
        
        $updated = false;
        $demos = isset($index['demos']) ? $index['demos'] : $index;
        
        foreach ($demos as &$demo) {
            $originalDemo = $demo;
            
            // Générer un nom technique si nécessaire
            if (empty($demo['technicalName'])) {
                $technicalName = $this->generateTechnicalName($demo['originalName'] ?? $demo['filename'] ?? '');
                $demo['technicalName'] = $technicalName;
                echo "  🔧 Nom technique généré: {$technicalName}\n";
                $updated = true;
            }
            
            // Extraire ou améliorer le nom d'affichage
            $displayName = $this->extractDisplayName($demo);
            
            // Mettre à jour le nom d'affichage si nécessaire
            if (empty($demo['displayName']) || $demo['displayName'] !== $displayName) {
                $oldName = $demo['displayName'] ?? $demo['name'] ?? 'Sans nom';
                $demo['displayName'] = $displayName;
                $demo['name'] = $displayName; // Compatibilité
                echo "  📝 {$oldName} → {$displayName}\n";
                $updated = true;
            }
            
            // Ajouter des métadonnées utiles
            if (empty($demo['added'])) {
                $demo['added'] = $demo['created'] ?? date('c');
            }
            
            if (empty($demo['lastModified'])) {
                $demo['lastModified'] = date('c');
            }
            
            // S'assurer que le filename n'a pas de double extension
            if (isset($demo['filename']) && strpos($demo['filename'], '.json.json') !== false) {
                $cleanFilename = str_replace('.json.json', '.json', $demo['filename']);
                echo "  🔧 Correction extension: {$demo['filename']} → {$cleanFilename}\n";
                $demo['filename'] = $cleanFilename;
                $updated = true;
            }
        }
        
        // Mettre à jour la structure si nécessaire
        if (isset($index['demos'])) {
            $index['demos'] = $demos;
        } else {
            $index = $demos;
        }
        
        // Ajouter des métadonnées de migration
        $index['migration'] = [
            'version' => '2.0',
            'date' => date('c'),
            'system' => 'dual-names',
            'description' => 'Migration vers le système de noms dual'
        ];
        
        if ($updated) {
            // Sauvegarder l'index mis à jour
            $result = file_put_contents($indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            if ($result !== false) {
                echo "✅ Index mis à jour: $indexFile\n";
                $this->migrationLog[] = "✅ Migration réussie: $libraryPath";
                return true;
            } else {
                echo "❌ Erreur d'écriture: $indexFile\n";
                $this->migrationLog[] = "❌ Erreur d'écriture: $libraryPath";
                return false;
            }
        } else {
            echo "ℹ️ Aucune modification nécessaire\n";
            $this->migrationLog[] = "ℹ️ Aucune modification: $libraryPath";
            return true;
        }
    }
    
    /**
     * Exécuter la migration complète
     */
    public function run() {
        echo "🚀 Démarrage de la migration robuste...\n\n";
        
        $successCount = 0;
        $totalCount = count($this->libraries);
        
        foreach ($this->libraries as $libraryPath => $libraryName) {
            if ($this->migrateLibrary($libraryPath)) {
                $successCount++;
            }
        }
        
        // Sauvegarder le log
        $this->saveLog();
        
        echo "\n" . str_repeat('=', 60) . "\n";
        echo "🎉 Migration terminée !\n";
        echo "✅ Succès: $successCount/$totalCount\n";
        echo "📁 Backups: " . $this->backupDir . "\n";
        echo "📋 Log: migration-log-" . date('Y-m-d_H-i-s') . ".txt\n";
        echo str_repeat('=', 60) . "\n";
        
        return $successCount === $totalCount;
    }
    
    /**
     * Sauvegarder le log de migration
     */
    private function saveLog() {
        $logFile = 'migration-log-' . date('Y-m-d_H-i-s') . '.txt';
        file_put_contents($logFile, implode("\n", $this->migrationLog));
    }
}

// Exécuter la migration
try {
    $migration = new RobustMigration();
    $success = $migration->run();
    
    if ($success) {
        echo "\n🎯 Migration réussie ! Le système de noms dual est maintenant actif.\n";
        echo "📋 Prochaines étapes:\n";
        echo "  1. Tester les fonctions de modification/déplacement/suppression\n";
        echo "  2. Vérifier l'affichage dans les interfaces\n";
        echo "  3. Tester la lecture des démos\n";
    } else {
        echo "\n⚠️ Migration partielle. Vérifiez les erreurs ci-dessus.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Erreur critique: " . $e->getMessage() . "\n";
    echo "📋 Vérifiez les logs pour plus de détails.\n";
}
?>
