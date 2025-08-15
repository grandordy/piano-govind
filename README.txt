### ?? 1. Analyse des Langages et Technologies

L'application en cours de cr&ation est construite sur un ensemble de technologies web standards, ce qui est un bon choix pour la flexibilité et la compatibilité.

*   **Frontend (ce que l'utilisateur voit et avec quoi il interagit) :**
    *   **HTML :** La structure de base de toutes les pages.
    *   **CSS :** Pour le style, le design moderne, les couleurs, les dégradés et les animations.
    *   **JavaScript (JS) :** C'est le moteur de l'interactivité. Il gère le jeu au clavier, la lecture/enregistrement des démos, la communication avec les périphériques MIDI, et l'affichage dynamique des menus et des popups.

*   **Backend (la logique côté serveur) :**
    *   **PHP :** Utilisé pour gérer les fichiers, comme la lecture et l'écriture des fichiers de configuration (`admin-config.json`) et surtout pour l'API qui gère toute la logique des démos (sauvegarder, déplacer, supprimer les fichiers `.json`).
    *   **JSON (JavaScript Object Notation) :** C'est le format de fichier choisi pour stocker les données. Les enregistrements musicaux (les "démos"), les configurations et les listes de morceaux sont tous sauvegardés en fichiers `.json`. **Il n'y a pas de base de données SQL**, ce qui était une initiative non désirée du chef de projet qui n'a pas anticiper l'instabilité du système et l'inscalabilté de l'appli.

### ?? 2. Sections Identifiables et Parties de l'Application

Votre application est bien structurée autour de deux interfaces principales et de plusieurs modules fonctionnels.

**Interfaces Utilisateur :**

1.  **Interface Publique (Élèves) :**   ?? ?? ?? 
   
	- Public cible :** L'interface s'adresse aux élèves.
    - **Fonctionnalités principales :** Les élèves peuvent écouter des démos de prières et de bhajans, les mettre en pause, et jouer par-dessus.
    - **Compatibilité :** L'application est compatible avec les claviers MIDI, le clavier d'ordinateur, et même la souris.
    - **Innovation principale :** La visualisation des notes jouées. Au lieu de colorer la note entière, un petit marqueur orange est utilisé pour indiquer l'enfoncement de la touche.
    - **Avantage pédagogique :** L'objectif de cette approche est de réduire la charge cognitive des élèves en ne signalant que la partie de la touche qui est la plus visible pendant qu'ils jouent.

2.  **Interface d'Administration (Professeur) :** Elle inclut tout ce que l'élève peut faire, avec en plus des fonctionnalités de création et de gestion :
    *   **Enregistrement** de nouvelles mélodies.
    *   **Sauvegarde** des enregistrements dans un "Brouillon".
    *   **Gestion des bibliothèques** : un panneau d'administration permet de voir les démos dans le Brouillon, les Prayers et les Bhajans, et de les déplacer, renommer ou supprimer.

**Principaux Modules Fonctionnels (les "briques" de l'application) :**

*   **`piano-core.php` & `piano-harmonium.php` :** Le cœur du projet. Ils gèrent l'affichage du clavier de piano et la production des sons (soit par synthèse, soit en jouant les sons d'harmonium samplés).
*   **`piano-recorder.php` :** Le module qui gère la fonctionnalité d'enregistrement des notes jouées par le professeur.
*   **`piano-demo-manager.php` & `demo-manager-api.php` :** C'est le système de gestion de contenu. Il fournit l'interface visuelle (les 3 colonnes : Brouillon, Prayers, Bhajans) et la logique serveur (l'API en PHP) pour toutes les opérations sur les fichiers de démos.
*   **`piano-midi.php` :** Gère la connexion avec un clavier MIDI externe.
*   **`menu-dropdowns.php` & `menu-toolbar.php` :** Gèrent la barre de menu supérieure (KEY ASSIST, SOUND, HELP) et les panneaux déroulants correspondants.
*   **`help-video-module.js` & `admin.php` :** Un mini-système pour gérer le contenu des popups d'aide et de la vidéo tutoriel.