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
require_once(__DIR__ . '/classes/form/manual_form.php');
require_once(__DIR__ . '/classes/helper.php');

admin_externalpage_setup('local_parentmanager_manual');

$PAGE->set_url(new moodle_url('/local/parentmanager/manual.php'));
$PAGE->set_title(get_string('pluginname', 'local_parentmanager'));
$PAGE->set_heading(get_string('manual_nav', 'local_parentmanager'));

echo $OUTPUT->header();

$mform = new \local_parentmanager\form\manual_form();

if ($mform->is_cancelled()) {
    redirect($PAGE->url);
} else if ($data = $mform->get_data()) {
    $parent = $DB->get_record('user', ['id' => $data->parentid]);
    
    echo $OUTPUT->heading(get_string('processing', 'local_parentmanager'), 3);
    echo $OUTPUT->box_start();
    
    $templatedata = [
        'title' => get_string('parent_label', 'local_parentmanager', fullname($parent)),
        'results' => []
    ];

    foreach ($data->childrenids as $childid) {
        try {
            \local_parentmanager\helper::create_link($parent->id, $childid, $data->roleid);
            $child = $DB->get_record('user', ['id' => $childid]);
            $templatedata['results'][] = [
                'is_success' => true,
                'message' => get_string('success_link', 'local_parentmanager', fullname($child))
            ];
        } catch (Exception $e) {
            $templatedata['results'][] = [
                'is_error' => true,
                'message' => get_string('error_generic', 'local_parentmanager', $e->getMessage())
            ];
        }
    }
    
    echo $OUTPUT->render_from_template('local_parentmanager/action_results', $templatedata);
    
    echo $OUTPUT->continue_button(new moodle_url('/local/parentmanager/manual.php'));
    echo $OUTPUT->box_end();

} else {
    $mform->display();
}

echo $OUTPUT->footer();
