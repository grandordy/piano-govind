<?php
/**
 * API pour le Gestionnaire de Démos - Nouvelle Architecture
 * 
 * Point d'entrée pour les requêtes AJAX du gestionnaire de démos
 */

// Désactiver l'affichage des erreurs pour éviter qu'elles polluent le JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Fonction de log pour le débogage
function debugLog($message) {
    error_log("[DemoManager API] " . $message);
}

// Inclure seulement la classe DemoManager
class DemoManager {
    private $aClasserDir;
    private $indexFile;
    private $publicDemosDir;
    
    public function __construct() {
        // Utiliser le répertoire du script actuel
        $scriptDir = dirname(__FILE__);
        $this->aClasserDir = $scriptDir . '/demospubliques/brouillon/';
        $this->indexFile = $scriptDir . '/demospubliques/brouillon/index.json';
        $this->publicDemosDir = $scriptDir . '/demospubliques/';
        
        // Créer le dossier brouillon s'il n'existe pas
        if (!is_dir($this->aClasserDir)) {
            mkdir($this->aClasserDir, 0755, true);
        }
        
        // Créer l'index s'il n'existe pas
        if (!file_exists($this->indexFile)) {
            $this->createIndexFile();
        }
    }
    
    /**
     * Créer le fichier index initial
     */
    private function createIndexFile() {
        $index = [
            'metadata' => [
                'created' => date('c'),
                'version' => '1.0',
                'description' => 'Index des démos en brouillon - Nouvelle architecture Piano Solo'
            ],
            'demos' => []
        ];
        
        file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Enregistrer une nouvelle démo dans "A Classer"
     */
    public function saveNewDemo($demoData, $originalName = '') {
        try {
            // Générer un nom de fichier unique avec le nom de baptême
            $timestamp = time();
            $randomId = bin2hex(random_bytes(8));
            
            // Créer un nom de fichier sécurisé basé sur le nom original
            if ($originalName && !empty(trim($originalName))) {
                // Nettoyer le nom pour en faire un nom de fichier valide
                $safeName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $originalName);
                $safeName = preg_replace('/\s+/', '_', trim($safeName));
                $safeName = strtolower($safeName);
                
                // Limiter la longueur du nom
                if (strlen($safeName) > 30) {
                    $safeName = substr($safeName, 0, 30);
                }
                
                $filename = "demo_{$safeName}_{$timestamp}_{$randomId}.json";
            } else {
                // Fallback si pas de nom
                $filename = "demo_brouillon_{$timestamp}_{$randomId}.json";
            }
            
            $filepath = $this->aClasserDir . $filename;
            
            // Préparer les métadonnées
            $demoInfo = [
                'id' => $timestamp . '_' . $randomId,
                'filename' => $filename,
                'originalName' => $originalName ?: 'Brouillon sans nom',
                'created' => date('c'),
                'status' => 'a_classer',
                'data' => $demoData
            ];
            
            // Sauvegarder le fichier
            file_put_contents($filepath, json_encode($demoInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Mettre à jour l'index
            $this->addToIndex($demoInfo);
            
            return [
                'success' => true,
                'filename' => $filename,
                'id' => $demoInfo['id'],
                'message' => 'Démo enregistrée dans "Brouillon"'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Ajouter une démo à l'index
     */
    private function addToIndex($demoInfo) {
        $index = json_decode(file_get_contents($this->indexFile), true);
        $index['demos'][] = $demoInfo;
        file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Obtenir toutes les démos "A Classer"
     */
    public function getDemosAClasser() {
        if (!file_exists($this->indexFile)) {
            return [];
        }
        
        $index = json_decode(file_get_contents($this->indexFile), true);
        return $index['demos'] ?? [];
    }
    
    /**
     * Obtenir toutes les démos d'une bibliothèque spécifique
     */
    public function getDemosFromLibrary($library) {
        try {
            if ($library === 'brouillon') {
                return $this->getDemosAClasser();
            }
            
            $libraryDir = $this->publicDemosDir . $library . '/';
            $indexFile = $libraryDir . 'index.json';
            
            if (!is_dir($libraryDir) || !file_exists($indexFile)) {
                return [];
            }
            
            $index = json_decode(file_get_contents($indexFile), true);
            return $index['demos'] ?? [];
            
        } catch (Exception $e) {
            debugLog("Erreur getDemosFromLibrary: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Déplacer une démo vers une bibliothèque
     */
    public function moveToLibrary($demoId, $libraryName, $newName = '', $sourceType = 'brouillon') {
        try {
            if ($libraryName === 'brouillon') {
                return $this->moveToBrouillon($demoId, $sourceType, $newName);
            } elseif ($sourceType === 'brouillon') {
                return $this->moveFromBrouillon($demoId, $libraryName, $newName);
            } else {
                return $this->moveFromLibrary($demoId, $sourceType, $libraryName, $newName);
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Forcer la synchronisation des fichiers
     */
    private function forceSync() {
        // Vider le cache PHP
        clearstatcache();
        
        // Forcer la synchronisation des fichiers
        foreach (['brouillon', 'prayers', 'bhajans'] as $library) {
            $libraryDir = $this->publicDemosDir . $library . '/';
            if (is_dir($libraryDir)) {
                // Recharger l'index de la bibliothèque
                $indexFile = $libraryDir . 'index.json';
                if (file_exists($indexFile)) {
                    // Lire et réécrire le fichier pour forcer la synchronisation
                    $content = file_get_contents($indexFile);
                    file_put_contents($indexFile, $content);
                }
            }
        }
    }
    
    /**
     * Déplacer depuis le brouillon vers une bibliothèque
     */
    private function moveFromBrouillon($demoId, $libraryName, $newName = '') {
        $index = json_decode(file_get_contents($this->indexFile), true);
        $demoIndex = -1;
        $demo = null;
        
        // Trouver la démo dans l'index par nom technique, ID ou filename
        foreach ($index['demos'] as $i => $d) {
            if (isset($d['technicalName']) && $d['technicalName'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            } elseif (isset($d['id']) && $d['id'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            } elseif (isset($d['filename']) && $d['filename'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            }
        }
        
        if ($demoIndex === -1) {
            return ['success' => false, 'message' => 'Démo non trouvée dans le brouillon'];
        }
            
            // Créer le dossier de la bibliothèque s'il n'existe pas
            $libraryDir = $this->publicDemosDir . $libraryName . '/';
            if (!is_dir($libraryDir)) {
                mkdir($libraryDir, 0755, true);
            }
            
            // Créer un nouveau nom de fichier avec le nom de baptême pour la bibliothèque
            $finalName = $newName ?: $demo['originalName'];
            $timestamp = time();
            $randomId = bin2hex(random_bytes(8));
            
            // Créer un nom de fichier sécurisé basé sur le nom final
            if ($finalName && !empty(trim($finalName))) {
                $safeName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $finalName);
                $safeName = preg_replace('/\s+/', '_', trim($safeName));
                $safeName = strtolower($safeName);
                
                // Limiter la longueur du nom
                if (strlen($safeName) > 30) {
                    $safeName = substr($safeName, 0, 30);
                }
                
                $newFilename = "demo_{$safeName}_{$timestamp}_{$randomId}.json";
            } else {
                $newFilename = "demo_{$libraryName}_{$timestamp}_{$randomId}.json";
            }
            
            // Copier le fichier vers la bibliothèque avec le nouveau nom
            $sourceFile = $this->aClasserDir . $demo['filename'];
            $targetFile = $libraryDir . $newFilename;
            
            if (!copy($sourceFile, $targetFile)) {
                return ['success' => false, 'message' => 'Erreur lors de la copie du fichier'];
            }
            
            // Mettre à jour le nom dans le fichier
            $demoData = json_decode(file_get_contents($targetFile), true);
            $demoData['originalName'] = $finalName;
            if (isset($demoData['data'])) {
                $demoData['data']['name'] = $finalName; // Mettre à jour aussi dans les données
            }
            file_put_contents($targetFile, json_encode($demoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Supprimer de l'index "A Classer"
            array_splice($index['demos'], $demoIndex, 1);
            file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Supprimer le fichier original
            unlink($sourceFile);
            
            // Mettre à jour demos.json de la bibliothèque
            $this->updateLibraryIndex($libraryName, $newFilename, $finalName);
            
            // Forcer la synchronisation
            $this->forceSync();
            
            return [
                'success' => true,
                'message' => "Brouillon déplacé vers la bibliothèque '$libraryName'"
            ];
        }
        
        /**
         * Déplacer vers le brouillon depuis une bibliothèque
         */
        private function moveToBrouillon($demoId, $sourceLibrary, $newName = '') {
            // Vérifier que la bibliothèque source existe
            $sourceDir = $this->publicDemosDir . $sourceLibrary . '/';
            if (!is_dir($sourceDir)) {
                return ['success' => false, 'message' => "Bibliothèque source '$sourceLibrary' non trouvée"];
            }
            
            // Chercher dans l'index de la bibliothèque source
            $sourceIndexFile = $sourceDir . 'index.json';
            if (!file_exists($sourceIndexFile)) {
                return ['success' => false, 'message' => "Index de la bibliothèque '$sourceLibrary' non trouvé"];
            }
            
            $sourceIndex = json_decode(file_get_contents($sourceIndexFile), true);
            $demoIndex = -1;
            $demo = null;
            
            // Trouver la démo par ID ou filename
            foreach ($sourceIndex as $i => $d) {
                if (isset($d['id']) && $d['id'] === $demoId) {
                    $demoIndex = $i;
                    $demo = $d;
                    break;
                } elseif (isset($d['filename']) && $d['filename'] === $demoId) {
                    $demoIndex = $i;
                    $demo = $d;
                    break;
                }
            }
            
            if ($demoIndex === -1) {
                return ['success' => false, 'message' => "Démo non trouvée dans la bibliothèque '$sourceLibrary'"];
            }
            
            // Créer un nouveau nom de fichier pour le brouillon
            $finalName = $newName ?: ($demo['name'] ?? $demo['originalName'] ?? 'Démo');
            $timestamp = time();
            $randomId = bin2hex(random_bytes(8));
            
            // Créer un nom de fichier sécurisé
            $safeName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $finalName);
            $safeName = preg_replace('/\s+/', '_', trim($safeName));
            $safeName = strtolower($safeName);
            
            if (strlen($safeName) > 30) {
                $safeName = substr($safeName, 0, 30);
            }
            
            $newFilename = "demo_{$safeName}_{$timestamp}_{$randomId}.json";
            
            // Copier le fichier vers le brouillon
            $sourceFile = $sourceDir . $demo['filename'];
            $targetFile = $this->aClasserDir . $newFilename;
            
            if (!copy($sourceFile, $targetFile)) {
                return ['success' => false, 'message' => 'Erreur lors de la copie du fichier'];
            }
            
            // Mettre à jour le nom dans le fichier
            $demoData = json_decode(file_get_contents($targetFile), true);
            $demoData['originalName'] = $finalName;
            if (isset($demoData['data'])) {
                $demoData['data']['name'] = $finalName;
            }
            file_put_contents($targetFile, json_encode($demoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Supprimer de l'index source
            array_splice($sourceIndex, $demoIndex, 1);
            file_put_contents($sourceIndexFile, json_encode($sourceIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Supprimer le fichier source
            unlink($sourceFile);
            
            // Ajouter au brouillon
            $this->addToBrouillon($newFilename, $finalName);
            
            // Supprimer du demos.json principal
            $this->removeFromMainDemosJson($sourceLibrary, $demo['filename']);
            
            // Forcer la synchronisation
            $this->forceSync();
            
            return [
                'success' => true,
                'message' => "Démo déplacée de '$sourceLibrary' vers le brouillon"
            ];
        }
        
        /**
         * Ajouter une démo au brouillon
         */
        private function addToBrouillon($filename, $name) {
            $index = json_decode(file_get_contents($this->indexFile), true);
            
            $demoEntry = [
                'id' => time() . '_' . bin2hex(random_bytes(8)),
                'filename' => $filename,
                'originalName' => $name,
                'created' => date('c'),
                'status' => 'a_classer'
            ];
            
            $index['demos'][] = $demoEntry;
            file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        
        /**
         * Déplacer depuis une bibliothèque vers une autre
         */
        private function moveFromLibrary($demoId, $sourceLibrary, $targetLibrary, $newName = '') {
            // Vérifier que la bibliothèque source existe
            $sourceDir = $this->publicDemosDir . $sourceLibrary . '/';
            if (!is_dir($sourceDir)) {
                return ['success' => false, 'message' => "Bibliothèque source '$sourceLibrary' non trouvée"];
            }
            
            // Chercher dans l'index de la bibliothèque source
            $sourceIndexFile = $sourceDir . 'index.json';
            if (!file_exists($sourceIndexFile)) {
                return ['success' => false, 'message' => "Index de la bibliothèque '$sourceLibrary' non trouvé"];
            }
            
            $sourceIndex = json_decode(file_get_contents($sourceIndexFile), true);
            $demoIndex = -1;
            $demo = null;
            
            // Trouver la démo par ID ou filename
            foreach ($sourceIndex as $i => $d) {
                if (isset($d['id']) && $d['id'] === $demoId) {
                    $demoIndex = $i;
                    $demo = $d;
                    break;
                } elseif (isset($d['filename']) && $d['filename'] === $demoId) {
                    $demoIndex = $i;
                    $demo = $d;
                    break;
                }
            }
            
            if ($demoIndex === -1) {
                return ['success' => false, 'message' => "Démo non trouvée dans la bibliothèque '$sourceLibrary'"];
            }
            
            // Créer le dossier de la bibliothèque cible s'il n'existe pas
            $targetDir = $this->publicDemosDir . $targetLibrary . '/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            // Créer un nouveau nom de fichier
            $finalName = $newName ?: ($demo['name'] ?? $demo['originalName'] ?? 'Démo');
            $timestamp = time();
            $randomId = bin2hex(random_bytes(8));
            
            // Créer un nom de fichier sécurisé
            $safeName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $finalName);
            $safeName = preg_replace('/\s+/', '_', trim($safeName));
            $safeName = strtolower($safeName);
            
            if (strlen($safeName) > 30) {
                $safeName = substr($safeName, 0, 30);
            }
            
            $newFilename = "demo_{$safeName}_{$timestamp}_{$randomId}.json";
            
            // Copier le fichier vers la bibliothèque cible
            $sourceFile = $sourceDir . $demo['filename'];
            $targetFile = $targetDir . $newFilename;
            
            if (!copy($sourceFile, $targetFile)) {
                return ['success' => false, 'message' => 'Erreur lors de la copie du fichier'];
            }
            
            // Mettre à jour le nom dans le fichier
            $demoData = json_decode(file_get_contents($targetFile), true);
            $demoData['originalName'] = $finalName;
            if (isset($demoData['data'])) {
                $demoData['data']['name'] = $finalName;
            }
            file_put_contents($targetFile, json_encode($demoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Supprimer de l'index source
            array_splice($sourceIndex, $demoIndex, 1);
            file_put_contents($sourceIndexFile, json_encode($sourceIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Supprimer le fichier source
            unlink($sourceFile);
            
            // Mettre à jour l'index de la bibliothèque cible
            $this->updateLibraryIndex($targetLibrary, $newFilename, $finalName);
            
            // Mettre à jour le demos.json principal
            $this->updateMainDemosJson($targetLibrary, $newFilename, $finalName);
            
            // Forcer la synchronisation
            $this->forceSync();
            
            return [
                'success' => true,
                'message' => "Démo déplacée de '$sourceLibrary' vers '$targetLibrary'"
            ];
        }
    
    /**
     * Mettre à jour l'index d'une bibliothèque
     */
    private function updateLibraryIndex($libraryName, $filename, $displayName) {
        // Mettre à jour l'index spécifique de la bibliothèque
        $libraryIndexFile = $this->publicDemosDir . $libraryName . '/index.json';
        
        $libraryIndex = [];
        if (file_exists($libraryIndexFile)) {
            $libraryIndex = json_decode(file_get_contents($libraryIndexFile), true);
        }
        
        $libraryIndex[] = [
            'filename' => $filename,
            'name' => $displayName,
            'added' => date('c')
        ];
        
        file_put_contents($libraryIndexFile, json_encode($libraryIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Mettre à jour le demos.json principal pour la compatibilité
        $this->updateMainDemosJson($libraryName, $filename, $displayName);
    }
    
    /**
     * Mettre à jour le demos.json principal pour la compatibilité avec l'interface existante
     */
    private function updateMainDemosJson($libraryName, $filename, $displayName) {
        $mainDemosFile = $this->publicDemosDir . 'demos.json';
        
        $mainDemos = [];
        if (file_exists($mainDemosFile)) {
            $mainDemos = json_decode(file_get_contents($mainDemosFile), true);
        }
        
        // Ajouter la démo au demos.json principal
        $mainDemos[] = [
            'filename' => $filename,
            'name' => $displayName,
            'category' => $libraryName,
            'added' => date('c')
        ];
        
        file_put_contents($mainDemosFile, json_encode($mainDemos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Modifier le nom d'affichage d'une démo (système de noms dual)
     */
    public function editDemo($demoId, $newName) {
        try {
            $index = json_decode(file_get_contents($this->indexFile), true);
            
            // Trouver la démo par nom technique ou ID
            $demoIndex = -1;
            foreach ($index['demos'] as $i => $demo) {
                // Chercher par nom technique d'abord, puis par ID
                if (isset($demo['technicalName']) && $demo['technicalName'] === $demoId) {
                    $demoIndex = $i;
                    break;
                } elseif (isset($demo['id']) && $demo['id'] === $demoId) {
                    $demoIndex = $i;
                    break;
                } elseif (isset($demo['filename']) && $demo['filename'] === $demoId) {
                    $demoIndex = $i;
                    break;
                }
            }
            
            if ($demoIndex === -1) {
                return ['success' => false, 'message' => 'Démo non trouvée'];
            }
            
            $demo = $index['demos'][$demoIndex];
            
            // Nettoyer le nouveau nom pour le fichier (sans extension)
            $safeName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $newName);
            $safeName = preg_replace('/\s+/', '_', trim($safeName));
            $safeName = strtolower($safeName);
            
            if (strlen($safeName) > 30) {
                $safeName = substr($safeName, 0, 30);
            }
            
            // Extraire l'ID existant du nom de fichier actuel
            $currentFilename = $demo['filename'];
            $filenameParts = explode('_', $currentFilename);
            
            // Garder les parties timestamp et ID du nom de fichier existant
            $timestamp = '';
            $randomId = '';
            
            // Chercher le timestamp et l'ID dans le nom de fichier existant
            foreach ($filenameParts as $part) {
                if (is_numeric($part) && strlen($part) >= 10) {
                    $timestamp = $part;
                } elseif (strlen($part) === 16 && ctype_xdigit($part)) {
                    $randomId = $part;
                }
            }
            
            // Si on n'a pas trouvé, générer de nouveaux
            if (empty($timestamp)) {
                $timestamp = time();
            }
            if (empty($randomId)) {
                $randomId = bin2hex(random_bytes(8));
            }
            
            // Créer le nouveau nom de fichier en préservant l'ID
            $newFilename = "demo_{$safeName}_{$timestamp}_{$randomId}.json";
            
            // Renommer le fichier
            $oldFilepath = $this->aClasserDir . $demo['filename'];
            $newFilepath = $this->aClasserDir . $newFilename;
            
            if (file_exists($oldFilepath)) {
                rename($oldFilepath, $newFilepath);
                
                // Mettre à jour le contenu du fichier JSON avec le nouveau nom
                $fileContent = json_decode(file_get_contents($newFilepath), true);
                if ($fileContent && isset($fileContent['data'])) {
                    $fileContent['data']['name'] = $newName;
                    file_put_contents($newFilepath, json_encode($fileContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }
            }
            
            // Mettre à jour l'index avec le système de noms dual
            $index['demos'][$demoIndex]['filename'] = $newFilename;
            $index['demos'][$demoIndex]['displayName'] = $newName; // Nom d'affichage
            $index['demos'][$demoIndex]['name'] = $newName; // Compatibilité
            $index['demos'][$demoIndex]['lastModified'] = date('c');
            if (isset($index['demos'][$demoIndex]['data'])) {
                $index['demos'][$demoIndex]['data']['name'] = $newName;
            }
            
            file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            return ['success' => true, 'message' => 'Nom modifié avec succès', 'newFilename' => $newFilename];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Supprimer une démo (brouillon ou bibliothèque)
     */
    public function deleteDemo($demoId, $sourceType = 'brouillon') {
        try {
            if ($sourceType === 'brouillon') {
                return $this->deleteBrouillonDemo($demoId);
            } else {
                return $this->deleteLibraryDemo($demoId, $sourceType);
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
    
    /**
     * Supprimer une démo du brouillon
     */
    private function deleteBrouillonDemo($demoId) {
        $index = json_decode(file_get_contents($this->indexFile), true);
        $demoIndex = -1;
        $demo = null;
        
        // Trouver la démo par nom technique, ID ou filename
        foreach ($index['demos'] as $i => $d) {
            if (isset($d['technicalName']) && $d['technicalName'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            } elseif (isset($d['id']) && $d['id'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            } elseif (isset($d['filename']) && $d['filename'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            }
        }
        
        if ($demoIndex === -1) {
            return ['success' => false, 'message' => 'Démo non trouvée dans le brouillon'];
        }
        
        // Supprimer le fichier
        $filepath = $this->aClasserDir . $demo['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Supprimer de l'index
        array_splice($index['demos'], $demoIndex, 1);
        file_put_contents($this->indexFile, json_encode($index, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return ['success' => true, 'message' => 'Brouillon supprimé'];
    }
    
    /**
     * Supprimer une démo d'une bibliothèque
     */
    private function deleteLibraryDemo($demoId, $libraryName) {
        // Vérifier que la bibliothèque existe
        $libraryDir = $this->publicDemosDir . $libraryName . '/';
        if (!is_dir($libraryDir)) {
            return ['success' => false, 'message' => "Bibliothèque '$libraryName' non trouvée"];
        }
        
        // Chercher dans l'index de la bibliothèque
        $libraryIndexFile = $libraryDir . 'index.json';
        if (!file_exists($libraryIndexFile)) {
            return ['success' => false, 'message' => "Index de la bibliothèque '$libraryName' non trouvé"];
        }
        
        $libraryIndex = json_decode(file_get_contents($libraryIndexFile), true);
        $demoIndex = -1;
        $demo = null;
        
        // Trouver la démo par nom technique, ID ou filename
        foreach ($libraryIndex as $i => $d) {
            if (isset($d['technicalName']) && $d['technicalName'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            } elseif (isset($d['id']) && $d['id'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            } elseif (isset($d['filename']) && $d['filename'] === $demoId) {
                $demoIndex = $i;
                $demo = $d;
                break;
            }
        }
        
        if ($demoIndex === -1) {
            return ['success' => false, 'message' => "Démo non trouvée dans la bibliothèque '$libraryName'"];
        }
        
        // Supprimer le fichier
        $filename = $demo['filename'] ?? $demoId;
        $filepath = $libraryDir . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Supprimer de l'index de la bibliothèque
        array_splice($libraryIndex, $demoIndex, 1);
        file_put_contents($libraryIndexFile, json_encode($libraryIndex, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        // Supprimer du demos.json principal
        $this->removeFromMainDemosJson($libraryName, $filename);
        
        return ['success' => true, 'message' => "Démo supprimée de la bibliothèque '$libraryName'"];
    }
    
    /**
     * Supprimer une démo du demos.json principal
     */
    private function removeFromMainDemosJson($libraryName, $filename) {
        $mainDemosFile = $this->publicDemosDir . 'demos.json';
        
        if (file_exists($mainDemosFile)) {
            $mainDemos = json_decode(file_get_contents($mainDemosFile), true);
            
            // Trouver et supprimer la démo
            foreach ($mainDemos as $i => $demo) {
                if ($demo['category'] === $libraryName && $demo['filename'] === $filename) {
                    array_splice($mainDemos, $i, 1);
                    break;
                }
            }
            
            file_put_contents($mainDemosFile, json_encode($mainDemos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}

// Initialiser le gestionnaire
$demoManager = new DemoManager();

try {
    // Lire l'input brut une seule fois
    $rawInput = file_get_contents('php://input');
    debugLog("Input brut: " . $rawInput);
    
    // Essayer de parser l'input JSON
    $jsonInput = null;
    if (!empty($rawInput)) {
        $jsonInput = json_decode($rawInput, true);
        debugLog("JSON parsé: " . json_encode($jsonInput));
    }
    
    // Récupérer l'action depuis GET, POST ou JSON
    $action = $_GET['action'] ?? $_POST['action'] ?? $jsonInput['action'] ?? '';
    
    debugLog("Action reçue: " . $action);
    debugLog("Méthode HTTP: " . $_SERVER['REQUEST_METHOD']);
    debugLog("GET: " . json_encode($_GET));
    debugLog("POST: " . json_encode($_POST));
    
    switch ($action) {
        case 'getDemosAClasser':
            $demos = $demoManager->getDemosAClasser();
            echo json_encode([
                'success' => true,
                'demos' => $demos
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'getDemos':
            $input = $jsonInput ?? json_decode($rawInput, true);
            $library = $input['library'] ?? 'brouillon';
            
            $demos = $demoManager->getDemosFromLibrary($library);
            echo json_encode([
                'success' => true,
                'demos' => $demos,
                'library' => $library
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'saveNewDemo':
            $input = $jsonInput ?? json_decode($rawInput, true);
            $demoData = $input['demoData'] ?? null;
            $originalName = $input['originalName'] ?? '';
            
            if (!$demoData) {
                throw new Exception('Données de démo manquantes');
            }
            
            $result = $demoManager->saveNewDemo($demoData, $originalName);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'moveToLibrary':
            $input = $jsonInput ?? json_decode($rawInput, true);
            $demoId = $input['demoId'] ?? '';
            $library = $input['library'] ?? '';
            $newName = $input['newName'] ?? '';
            $sourceType = $input['sourceType'] ?? 'brouillon';
            
            if (!$demoId || !$library) {
                throw new Exception('Paramètres manquants');
            }
            
            $result = $demoManager->moveToLibrary($demoId, $library, $newName, $sourceType);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'editDemo':
            $input = $jsonInput ?? json_decode($rawInput, true);
            $demoId = $input['demoId'] ?? '';
            $newName = $input['newName'] ?? '';
            
            if (!$demoId || !$newName) {
                throw new Exception('Paramètres manquants');
            }
            
            $result = $demoManager->editDemo($demoId, $newName);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        case 'deleteDemo':
            $input = $jsonInput ?? json_decode($rawInput, true);
            $demoId = $input['demoId'] ?? '';
            $sourceType = $input['sourceType'] ?? 'brouillon';
            
            if (!$demoId) {
                throw new Exception('ID de démo manquant');
            }
            
            $result = $demoManager->deleteDemo($demoId, $sourceType);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            throw new Exception('Action non reconnue: ' . $action);
    }
    
} catch (Exception $e) {
    debugLog("Erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
