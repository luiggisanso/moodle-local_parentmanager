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
$string['pluginname'] = 'Parent/Child Manager';
$string['import_nav'] = 'CSV Import';
$string['manual_nav'] = 'Individual Association';
$string['cohort_nav'] = 'Cohort Association';
$string['manage_nav'] = 'Manage Parents (List)';

$string['import_header'] = 'Bulk CSV Import';
$string['manual_header'] = 'Individual Search';
$string['manage_header'] = 'Manage Relationships';

$string['csv_file'] = 'CSV File';
$string['role_select'] = 'Role to assign';
$string['import_btn'] = 'Import';
$string['assign_btn'] = 'Create Links';

$string['select_parent'] = 'Select Parent';
$string['select_children'] = 'Select Students';
$string['select_cohort'] = 'Select Cohort';
$string['load_cohort_btn'] = 'Load member list';

$string['report'] = 'Report';
$string['processing'] = 'Processing';
$string['success_link'] = 'Link created with {$a}';
$string['cohort_members'] = 'Cohort members: {$a}';
$string['assign_to_parent'] = 'Target Parent: <strong>{$a}</strong>';
$string['assign_selected'] = 'Assign selected';
$string['no_selection'] = 'No selection.';

$string['list_parents'] = 'List of Parents';
$string['norole'] = 'No role of type ‘User context’ was found. Please configure a parent role in the site administration.';
$string['children_of'] = 'Children of: {$a}';
$string['no_parents_found'] = 'No parents found.';
$string['no_children'] = 'No children associated.';
$string['manage_children'] = 'Manage';
$string['delete_selected'] = 'Remove selected';
$string['deleted_count'] = '{$a} link(s) removed.';
$string['back_to_list'] = 'Back to list';

$string['csv_instructions'] = '<div class="alert alert-info">CSV Format: 2 columns (Parent Email ; Child Email).</div>';
$string['privacy:metadata'] = 'Role management plugin.';

$string['parentmanager:manage'] = 'Manage Parents/Childs Relationships';

$string['parent_label'] = 'Parent: {$a}';
$string['error_id'] = 'Error ID {$a->id}: {$a->msg}';
$string['error_generic'] = 'Error: {$a}';
$string['users_added'] = '{$a} user(s) added.';
$string['empty_cohort'] = 'This cohort is empty.';
$string['no_user_context_role'] = 'No "User Context" role found. Please configure a Parent role in site administration.';
$string['import_ok'] = 'OK: <strong>{$a->parent}</strong> -> <strong>{$a->child}</strong>';
$string['import_err'] = 'Err: {$a->email} - {$a->msg}';
$string['import_not_found'] = 'Not found: {$a->p_email} or {$a->c_email}';
$string['action_label'] = 'Action';

$string['search_parent'] = 'Search for a parent...';
$string['search_children'] = 'Search for students...';
