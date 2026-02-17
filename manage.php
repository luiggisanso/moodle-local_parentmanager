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

$sql = "SELECT r.id 
        FROM {role} r
        JOIN {role_context_levels} rcl ON r.id = rcl.roleid
        WHERE rcl.contextlevel = :ctx";
$valid_ids = $DB->get_fieldset_sql($sql, ['ctx' => CONTEXT_USER]);

$roles = [];
foreach ($allroles as $r) {
    if (in_array($r->id, $valid_ids)) {
        $roles[$r->id] = $r->localname;
    }
}

if (!$roleid) {
    foreach ($roles as $rid => $rname) {
        if (stripos($rname, 'parent') !== false) {
            $roleid = $rid; 
            break;
        }
    }
    if (!$roleid && !empty($roles)) {
        $roleid = array_key_first($roles);
    }
}

if (!empty($roles)) {
    echo $OUTPUT->single_select($baseurl, 'roleid', $roles, $roleid, null, 'switchrole');
    echo "<hr>";
} else {
    echo $OUTPUT->notification(get_string('no_children', 'local_parentmanager'), 'error');
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
        echo '<form method="post" action="manage.php">';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
        echo '<input type="hidden" name="roleid" value="' . $roleid . '">';
        echo '<input type="hidden" name="parentid" value="' . $parentid . '">';
        echo '<input type="hidden" name="action" value="delete">';

        $table = new html_table();
        $table->head = ['<input type="checkbox" id="selectall">', 'Nom', 'Email'];
        foreach ($children as $child) {
            $link = html_writer::link(new moodle_url('/user/profile.php', ['id' => $child->id]), fullname($child));
            $table->data[] = [
                '<input type="checkbox" name="children[]" value="' . $child->id . '">',
                $link,
                $child->email
            ];
        }
        echo html_writer::table($table);
        echo '<br><button type="submit" class="btn btn-danger">' . get_string('delete_selected', 'local_parentmanager') . '</button>';
        echo ' <a href="manage.php?roleid='.$roleid.'" class="btn btn-secondary">' . get_string('back_to_list', 'local_parentmanager') . '</a>';
        echo '</form>';
        echo '<script>document.getElementById("selectall").addEventListener("change", function() { var c = document.querySelectorAll("input[name=\'children[]\']"); for(var i of c) i.checked = this.checked; });</script>';
    }
    echo $OUTPUT->box_end();

} else {
    $parents = \local_parentmanager\helper::get_parents_list($roleid);
    echo $OUTPUT->heading(get_string('list_parents', 'local_parentmanager'), 3);
    
    if (empty($parents)) {
        echo $OUTPUT->notification(get_string('no_parents_found', 'local_parentmanager'), 'info');
    } else {
        $table = new html_table();
        $table->head = ['Nom du Parent', 'Email', 'Enfants', 'Action'];
        foreach ($parents as $p) {
            $children = \local_parentmanager\helper::get_children_of_parent($p->id, $roleid);
            $count = count($children);
            $manage_url = new moodle_url('/local/parentmanager/manage.php', ['parentid' => $p->id, 'roleid' => $roleid]);
            $table->data[] = [
                html_writer::link($manage_url, fullname($p)),
                $p->email,
                "<span class='badge badge-info'>$count</span>",
                html_writer::link($manage_url, get_string('manage_children', 'local_parentmanager'), ['class' => 'btn btn-sm btn-primary'])
            ];
        }
        echo html_writer::table($table);
    }
}
echo $OUTPUT->footer();
