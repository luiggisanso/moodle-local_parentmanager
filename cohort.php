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

$action = optional_param('action', '', PARAM_ALPHA);

if ($action === 'assign' && data_submitted() && confirm_sesskey()) {
    $parentid = required_param('parentid', PARAM_INT);
    $roleid   = required_param('roleid', PARAM_INT);
    $selected_users = optional_param_array('selected_users', [], PARAM_INT);

    if (!empty($selected_users)) {
        $parent = $DB->get_record('user', ['id' => $parentid]);
        
        echo $OUTPUT->heading(get_string('processing', 'local_parentmanager'), 3);
        echo $OUTPUT->box_start();
        
        $templatedata = [
            'title' => get_string('parent_label', 'local_parentmanager', fullname($parent)),
            'results' => []
        ];

        list($in, $params) = $DB->get_in_or_equal($selected_users);
        $children = $DB->get_records_select('user', "id $in AND deleted = 0", $params);
        
        $count = 0;
        foreach ($selected_users as $childid) {
            try {
                \local_parentmanager\helper::create_link($parentid, $childid, $roleid);
                
                if (isset($children[$childid])) {
                    $child = $DB->get_record('user', ['id' => $childid]);
                    $templatedata['results'][] = [
                        'is_success' => true,
                        'message' => get_string('success_link', 'local_parentmanager', fullname($child))
                    ];
                    $count++;
                }
            } catch (Exception $e) {
                $err_obj = new \stdClass();
                $err_obj->id = $childid;
                $err_obj->msg = $e->getMessage();
                
                $templatedata['results'][] = [
                    'is_error' => true,
                    'message' => get_string('error_id', 'local_parentmanager', $err_obj)
                ];
            }
        }
        
        echo $OUTPUT->render_from_template('local_parentmanager/action_results', $templatedata);
        echo $OUTPUT->box_end();
        
        echo $OUTPUT->notification(get_string('users_added', 'local_parentmanager', $count), 'success');
        echo $OUTPUT->continue_button(new moodle_url('/local/parentmanager/cohort.php'));
        echo $OUTPUT->footer();
        exit;
    } else {
        echo $OUTPUT->notification(get_string('no_selection', 'local_parentmanager'), 'error');
    }
}

$mform = new \local_parentmanager\form\cohort_form();

if ($mform->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $mform->get_data()) {
    
    $cohort = $DB->get_record('cohort', ['id' => $data->cohortid]);
    $parent = $DB->get_record('user', ['id' => $data->parentid]);
    $members = \local_parentmanager\helper::get_cohort_members($data->cohortid);

    echo $OUTPUT->heading(get_string('cohort_members', 'local_parentmanager', $cohort->name), 3);
    
    if (empty($members)) {
        echo $OUTPUT->notification(get_string('empty_cohort', 'local_parentmanager'), 'warning');
        echo $OUTPUT->continue_button($PAGE->url);
    } else {
        $templatedata = [
            'assign_to_parent_str' => get_string('assign_to_parent', 'local_parentmanager', fullname($parent)),
            'sesskey' => sesskey(),
            'parentid' => $data->parentid,
            'roleid' => $data->roleid,
            'assign_selected_str' => get_string('assign_selected', 'local_parentmanager'),
            'cancel_str' => get_string('cancel'),
            'members' => []
        ];
        
        foreach ($members as $user) {
            $templatedata['members'][] = [
                'id' => $user->id,
                'fullname' => fullname($user),
                'email' => $user->email
            ];
        }
        echo $OUTPUT->render_from_template('local_parentmanager/cohort_members_form', $templatedata);
    }

} else {
    $mform->display();
}

echo $OUTPUT->footer();
echo $OUTPUT->footer();

