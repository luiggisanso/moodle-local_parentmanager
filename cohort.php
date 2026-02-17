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
require_once(__DIR__ . '/classes/form/cohort_form.php');
require_once(__DIR__ . '/classes/helper.php');

admin_externalpage_setup('local_parentmanager_cohort');

$PAGE->set_url(new moodle_url('/local/parentmanager/cohort.php'));
$PAGE->set_title(get_string('pluginname', 'local_parentmanager'));
$PAGE->set_heading(get_string('cohort_nav', 'local_parentmanager'));

echo $OUTPUT->header();

// 1. Relation
$action = optional_param('action', '', PARAM_ALPHA);

if ($action === 'assign' && data_submitted() && confirm_sesskey()) {
    $parentid = required_param('parentid', PARAM_INT);
    $roleid   = required_param('roleid', PARAM_INT);
    $selected_users = optional_param_array('selected_users', [], PARAM_INT);

    if (!empty($selected_users)) {
        $parent = $DB->get_record('user', ['id' => $parentid]);
        
        echo $OUTPUT->heading(get_string('processing', 'local_parentmanager'), 3);
        echo $OUTPUT->box_start();
        echo "<p><strong>Parent :</strong> " . fullname($parent) . "</p><ul>";
        
        $count = 0;
        foreach ($selected_users as $childid) {
            try {
                \local_parentmanager\helper::create_link($parentid, $childid, $roleid);
                $child = $DB->get_record('user', ['id' => $childid]);
                echo "<li class='text-success'>" . get_string('success_link', 'local_parentmanager', fullname($child)) . "</li>";
                $count++;
            } catch (Exception $e) {
                echo "<li class='text-danger'>Erreur ID $childid : " . $e->getMessage() . "</li>";
            }
        }
        echo "</ul>";
        echo $OUTPUT->box_end();
        echo $OUTPUT->notification("$count utilisateurs ajoutés.", 'success');
        echo $OUTPUT->continue_button(new moodle_url('/local/parentmanager/cohort.php'));
        echo $OUTPUT->footer();
        exit;
    } else {
        echo $OUTPUT->notification(get_string('no_selection', 'local_parentmanager'), 'error');
    }
}

// 2. Form
$mform = new \local_parentmanager\form\cohort_form();

if ($mform->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $mform->get_data()) {
    
    $cohort = $DB->get_record('cohort', ['id' => $data->cohortid]);
    $parent = $DB->get_record('user', ['id' => $data->parentid]);
    $members = \local_parentmanager\helper::get_cohort_members($data->cohortid);

    echo $OUTPUT->heading(get_string('cohort_members', 'local_parentmanager', $cohort->name), 3);
    
    if (empty($members)) {
        echo $OUTPUT->notification('Cette cohorte est vide.', 'warning');
        echo $OUTPUT->continue_button($PAGE->url);
    } else {
        echo "<p>" . get_string('assign_to_parent', 'local_parentmanager', fullname($parent)) . "</p>";
        
        echo '<form method="post" action="cohort.php">';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
        echo '<input type="hidden" name="action" value="assign">';
        echo '<input type="hidden" name="parentid" value="' . $data->parentid . '">';
        echo '<input type="hidden" name="roleid" value="' . $data->roleid . '">';

        $table = new html_table();
        $table->head = ['<input type="checkbox" id="selectall">', 'Nom', 'Email'];
        
        foreach ($members as $user) {
            $table->data[] = [
                '<input type="checkbox" name="selected_users[]" value="' . $user->id . '">',
                fullname($user),
                $user->email
            ];
        }
        echo html_writer::table($table);
        
        echo '<div class="mt-3">';
        echo '<button type="submit" class="btn btn-primary">' . get_string('assign_selected', 'local_parentmanager') . '</button>';
        echo ' <a href="cohort.php" class="btn btn-secondary">' . get_string('cancel') . '</a>';
        echo '</div>';
        echo '</form>';
        
        echo '<script>
            document.getElementById("selectall").addEventListener("change", function() {
                var checkboxes = document.querySelectorAll("input[name=\'selected_users[]\']");
                for (var checkbox of checkboxes) { checkbox.checked = this.checked; }
            });
        </script>';
    }

} else {
    $mform->display();
}

echo $OUTPUT->footer();