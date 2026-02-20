# Moodle Parent/Child Manager (local_parentmanager)

![Moodle Version](https://img.shields.io/badge/Moodle-4.5%2B-orange)
![License](https://img.shields.io/badge/license-GPLv3-blue)
![Status](https://img.shields.io/badge/status-Stable-green)

[English](#english) | [Français](#français)

---

## English

### Description
**Parent/Child Manager** is a Moodle local plugin designed to facilitate the association between students and their parents (or mentors). It automates two actions:
1.  **Role Assignment:** Assigns a specific role (e.g., "Parent") to the user in the student's user context.
2.  **Profile Update:** Updates custom profile fields (`parent_email` and `enfant_email`) with the full names of the associated users for easy reference.

### Features
* **CSV Import:** Bulk association using a simple CSV file (Parent Email; Child Email).
* **Manual Association:** Search for a specific parent and select one or multiple students to assign.
* **Cohort Association:** Select a parent and assign an entire cohort of students in one click.
* **Management Dashboard:** View all parents, see their assigned children, and remove associations (unassign role + clear profile fields).
* **Smart Updates:** Profile fields are updated in "Append" mode (names are added to the list, not overwritten).
* **GDPR Compliance:** Implements Moodle's Privacy API (Null provider).

### Prerequisites
Before using the plugin, you **must** configure Moodle:

1.  **Create the Role:**
    * Go to *Site administration > Users > Permissions > Define roles*.
    * Create a role (e.g., "Parent").
    * **Crucial:** Check **"User"** in the "Context types where this role may be assigned" setting.
2.  **Create Profile Fields:**
    * Go to *Site administration > Users > Accounts > User profile fields*.
    * Create two **Text area** fields.
    * Shortname 1: `parent_email`
    * Shortname 2: `enfant_email`

### Installation
1.  Download the plugin folder.
2.  Place the `parentmanager` folder into the `local/` directory of your Moodle installation.
3.  Go to *Site administration > Notifications* to trigger the installation.

### Usage
Access the plugin via: **Site administration > Accounts > Parent/Child Manager**.

1.  **CSV Import:** Upload a file with format `parent@email.com;child@email.com`. Headers are ignored.
2.  **Individual Association:** Search for a parent, then search for students to link.
3.  **Cohort Association:** Select a parent, then select a cohort to link all its members.
4.  **Manage Parents:** View the list of active parents. Click on a parent to view or remove specific children.

---

## Français

### Description
**Gestionnaire Parents/Enfants** est un plugin local pour Moodle conçu pour faciliter l'association entre les élèves et leurs parents (ou tuteurs). Il automatise deux actions :
1.  **Attribution de rôle :** Assigne un rôle spécifique (ex: "Parent") à l'utilisateur dans le contexte de l'élève.
2.  **Mise à jour du profil :** Met à jour des champs de profil personnalisés (`parent_email` et `enfant_email`) avec les noms complets des utilisateurs associés pour une consultation facile.

### Fonctionnalités
* **Import CSV :** Association en masse via un fichier CSV simple (Email Parent ; Email Enfant).
* **Association Manuelle :** Recherche d'un parent spécifique et sélection d'un ou plusieurs élèves.
* **Association par Cohorte :** Sélection d'un parent et assignation de tous les membres d'une cohorte en un clic.
* **Tableau de bord de gestion :** Liste tous les parents, permet de voir les enfants assignés et de supprimer des liens (désassignation du rôle + nettoyage des champs de profil).
* **Mise à jour intelligente :** Les champs de profil sont mis à jour en mode "Ajout" (les noms sont ajoutés à la suite, sans écraser l'existant).
* **Conformité RGPD :** Implémente l'API Privacy de Moodle.

### Pré-requis
Avant d'utiliser le plugin, vous **devez** configurer Moodle :

1.  **Créer le Rôle :**
    * Allez dans *Administration du site > Utilisateurs > Permissions > Définition des rôles*.
    * Créez un rôle (ex: "Parent").
    * **Crucial :** Cochez **"Utilisateur"** dans la section "Types de contextes où ce rôle peut être affecté".
2.  **Créer les Champs de profil :**
    * Allez dans *Administration du site > Utilisateurs > Comptes > Champs de profil utilisateur*.
    * Créez deux champs de type **Zone de texte**.
    * Nom court 1 : `parent_email`
    * Nom court 2 : `enfant_email`

### Installation
1.  Téléchargez le dossier du plugin.
2.  Placez le dossier `parentmanager` dans le répertoire `local/` de votre installation Moodle.
3.  Allez dans *Administration du site > Notifications* pour lancer l'installation.

### Utilisation
Accédez au plugin via : **Administration du site > Comptes > Gestionnaire Parents/Enfants**.

1.  **Importer par CSV :** Chargez un fichier au format `parent@email.com;enfant@email.com`. Les en-têtes sont ignorés.
2.  **Association Manuelle :** Recherchez un parent, puis recherchez les élèves à lier.
3.  **Association par Cohorte :** Choisissez un parent, puis choisissez une cohorte pour lier tous ses membres.

4.  **Gestion des Parents :** Consultez la liste des parents actifs. Cliquez sur un parent pour voir ou supprimer des enfants spécifiques.
