<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information.
 *
 * Parent Manager - This plugin allows you to easily manage parent-child mecanism.
 *
 * @package     local_parentmanager
 * @copyright   2026 Luiggi Sansonetti <1565841+luiggisanso@users.noreply.github.com> (Coder)
 * @copyright   2026 E-learning Touch' <contact@elearningtouch.com> (Maintainer)
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Gestionnaire Parents/Enfants';
$string['import_nav'] = 'Importer par CSV';
$string['manual_nav'] = 'Association Manuelle';
$string['cohort_nav'] = 'Association par Cohorte';
$string['manage_nav'] = 'Gestion des Parents (Liste)';

$string['import_header'] = 'Importation massive CSV';
$string['manual_header'] = 'Recherche individuelle';
$string['manage_header'] = 'Gestion des relations';

$string['csv_file'] = 'Fichier CSV';
$string['role_select'] = 'Rôle à attribuer';
$string['import_btn'] = 'Importer';
$string['assign_btn'] = 'Créer les liens';

$string['select_parent'] = 'Choisir un Parent';
$string['select_children'] = 'Choisir des Élèves';
$string['select_cohort'] = 'Choisir une Cohorte';
$string['load_cohort_btn'] = 'Charger la liste des membres';

$string['report'] = 'Rapport';
$string['processing'] = 'Traitement en cours';
$string['success_link'] = 'Lien créé avec {$a}';
$string['cohort_members'] = 'Membres de la cohorte : {$a}';
$string['assign_to_parent'] = 'Parent cible : <strong>{$a}</strong>';
$string['assign_selected'] = 'Attribuer la sélection';
$string['no_selection'] = 'Aucun élément sélectionné.';

$string['list_parents'] = 'Liste des Parents';
$string['col_left'] = 'Parents';
$string['col_right_manual'] = 'Nombre d\'enfants';
$string['norole'] = 'Aucun rôle de type "Contexte Utilisateur" n\'a été trouvé. Veuillez configurer un rôle Parent dans l\'administration du site.';
$string['children_of'] = 'Enfants de : {$a}';
$string['no_parents_found'] = 'Aucun parent trouvé.';
$string['no_children'] = 'Aucun enfant associé.';
$string['manage_children'] = 'Gérer';
$string['delete_selected'] = 'Supprimer la sélection';
$string['deleted_count'] = '{$a} lien(s) supprimé(s).';
$string['back_to_list'] = 'Retour à la liste';

$string['csv_instructions'] = '<div class="alert alert-info">Format CSV : 2 colonnes (Email Parent ; Email Enfant).</div>';
$string['privacy:metadata'] = 'Plugin de gestion de rôles.';

$string['parentmanager:manage'] = 'Gérer les relations Parents/Enfants';

$string['parent_label'] = 'Parent : {$a}';
$string['error_id'] = 'Erreur ID {$a->id} : {$a->msg}';
$string['error_generic'] = 'Erreur : {$a}';
$string['users_added'] = '{$a} utilisateur(s) ajouté(s).';
$string['empty_cohort'] = 'Cette cohorte est vide.';
$string['no_user_context_role'] = 'Aucun rôle de type "Contexte Utilisateur" n\'a été trouvé. Veuillez configurer un rôle Parent dans l\'administration du site.';
$string['import_ok'] = 'OK : <strong>{$a->parent}</strong> -> <strong>{$a->child}</strong>';
$string['import_err'] = 'Err : {$a->email} - {$a->msg}';
$string['import_not_found'] = 'Introuvable : {$a->p_email} ou {$a->c_email}';
$string['action_label'] = 'Action';

$string['search_parent'] = 'Rechercher un parent...';
$string['search_children'] = 'Rechercher des élèves...';

