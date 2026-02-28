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
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/helper.php');

admin_externalpage_setup('local_parentmanager_manage');

$roleid = optional_param('roleid', 0, PARAM_INT);
$parentid = optional_param('parentid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

$baseurl = new moodle_url('/local/parentmanager/manage.php');
$PAGE->set_url($baseurl);
$PAGE->set_title(get_string('pluginname', 'local_parentmanager'));
$PAGE->set_heading(get_string('manage_header', 'local_parentmanager'));

echo $OUTPUT->header();

$allroles = role_get_names(null, ROLENAME_ORIGINAL);
$sql = "SELECT r.id FROM {role} r JOIN {role_context_levels} rcl ON r.id = rcl.roleid WHERE rcl.contextlevel = :ctx";
$valid_ids = $DB->get_fieldset_sql($sql, ['ctx' => CONTEXT_USER]);

$roles = [];
foreach ($allroles as $r) {
    if (in_array($r->id, $valid_ids)) {
        $roles[$r->id] = $r->localname;
    }
}

if (!$roleid && !empty($roles)) {
    foreach ($roles as $rid => $rname) {
        if (stripos($rname, 'parent') !== false) {
            $roleid = $rid; break;
        }
    }
    if (!$roleid) $roleid = array_key_first($roles);
}

if (!empty($roles)) {
    echo $OUTPUT->single_select($baseurl, 'roleid', $roles, $roleid, null, 'switchrole');
    echo "<hr>";
} else {
    echo $OUTPUT->notification(get_string('no_user_context_role', 'local_parentmanager'), 'error');
    echo $OUTPUT->footer();
    die();
}

if ($action === 'delete' && $parentid && data_submitted() && confirm_sesskey()) {
    $children_to_remove = optional_param_array('children', [], PARAM_INT);
    if (!empty($children_to_remove)) {
        foreach ($children_to_remove as $childid) {
            \local_parentmanager\helper::delete_link($parentid, $childid, $roleid);
        }
        echo $OUTPUT->notification(get_string('deleted_count', 'local_parentmanager', count($children_to_remove)), 'success');
    } else {
        echo $OUTPUT->notification(get_string('no_selection', 'local_parentmanager'), 'warning');
    }
}

if ($parentid) {
    $parent = $DB->get_record('user', ['id' => $parentid]);
    $children = \local_parentmanager\helper::get_children_of_parent($parentid, $roleid);

    echo $OUTPUT->heading(get_string('children_of', 'local_parentmanager', fullname($parent)), 3);
    echo $OUTPUT->box_start();

    if (empty($children)) {
        echo "<p>" . get_string('no_children', 'local_parentmanager') . "</p>";
    } else {
        $templatedata = [
            'sesskey' => sesskey(),
            'roleid' => $roleid,
            'parentid' => $parentid,
            'delete_selected_str' => get_string('delete_selected', 'local_parentmanager'),
            'back_to_list_str' => get_string('back_to_list', 'local_parentmanager'),
            'children' => []
        ];
        
        foreach ($children as $child) {
            $templatedata['children'][] = [
                'id' => $child->id,
                'fullname' => fullname($child),
                'email' => $child->email,
                'profileurl' => new moodle_url('/user/profile.php', ['id' => $child->id])->out(false)
            ];
        }
        
        echo $OUTPUT->render_from_template('local_parentmanager/manage_children_form', $templatedata);
    }
    echo $OUTPUT->box_end();

} else {
    $parents = \local_parentmanager\helper::get_parents_list($roleid);
    echo $OUTPUT->heading(get_string('list_parents', 'local_parentmanager'), 3);
    
    if (empty($parents)) {
        echo $OUTPUT->notification(get_string('no_parents_found', 'local_parentmanager'), 'info');
    } else {
        $templatedata = [
            'parent_name_str' => get_string('col_left', 'local_parentmanager'),
            'children_str' => get_string('col_right_manual', 'local_parentmanager'),
            'action_str' => get_string('action_label', 'local_parentmanager'),
            'manage_str' => get_string('manage_children', 'local_parentmanager'),
            'parents' => []
        ];
        
        foreach ($parents as $p) {
            $children = \local_parentmanager\helper::get_children_of_parent($p->id, $roleid);
            $manage_url = new moodle_url('/local/parentmanager/manage.php', ['parentid' => $p->id, 'roleid' => $roleid]);
            $templatedata['parents'][] = [
                'fullname' => fullname($p),
                'email' => $p->email,
                'count' => count($children),
                'manageurl' => $manage_url->out(false)
            ];
        }
        
        echo $OUTPUT->render_from_template('local_parentmanager/manage_parents_list', $templatedata);
    }
}
echo $OUTPUT->footer();
