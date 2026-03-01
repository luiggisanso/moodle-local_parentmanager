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
require_once(__DIR__ . '/classes/form/import_form.php');
require_once(__DIR__ . '/classes/helper.php');

admin_externalpage_setup('local_parentmanager_csv');

$PAGE->set_url(new moodle_url('/local/parentmanager/index.php'));
$PAGE->set_title(get_string('pluginname', 'local_parentmanager'));
$PAGE->set_heading(get_string('import_nav', 'local_parentmanager'));

echo $OUTPUT->header();

$mform = new \local_parentmanager\form\import_form();

if ($data = $mform->get_data()) {
    $content = $mform->get_file_content('importfile');
    $lines = explode("\n", $content);
    $roleid = $data->roleid;

    echo $OUTPUT->box_start();
    
    $templatedata = [
        'title' => get_string('report', 'local_parentmanager'),
        'results' => []
    ];

    foreach ($lines as $i => $line) {
        $line = trim($line);
        if (empty($line) || ($i === 0 && stripos($line, '@') === false)) continue; 

        $sep = (strpos($line, ';') !== false) ? ';' : ',';
        $parts = str_getcsv($line, $sep);

        if (count($parts) >= 2) {
            $p_email = trim($parts[0]);
            $c_email = trim($parts[1]);

            $parent = $DB->get_record('user', ['email' => $p_email, 'deleted' => 0]);
            $child  = $DB->get_record('user', ['email' => $c_email, 'deleted' => 0]);

            if ($parent && $child) {
                try {
                    \local_parentmanager\helper::create_link($parent->id, $child->id, $roleid);
                    
                    $ok_obj = new \stdClass();
                    $ok_obj->parent = fullname($parent);
                    $ok_obj->child = fullname($child);
                    
                    $templatedata['results'][] = [
                        'is_success' => true,
                        'message' => get_string('import_ok', 'local_parentmanager', $ok_obj)
                    ];
                } catch (Exception $e) {
                    $err_obj = new \stdClass();
                    $err_obj->email = $parent->email;
                    $err_obj->msg = $e->getMessage();
                    
                    $templatedata['results'][] = [
                        'is_error' => true,
                        'message' => get_string('import_err', 'local_parentmanager', $err_obj)
                    ];
                }
            } else {
                $nf_obj = new \stdClass();
                $nf_obj->p_email = $p_email;
                $nf_obj->c_email = $c_email;
                
                $templatedata['results'][] = [
                    'is_warning' => true,
                    'message' => get_string('import_not_found', 'local_parentmanager', $nf_obj)
                ];
            }
        }
    }
    
    echo $OUTPUT->render_from_template('local_parentmanager/action_results', $templatedata);
    echo $OUTPUT->continue_button(new moodle_url('/local/parentmanager/index.php'));
    echo $OUTPUT->box_end();
} else {
    $mform->display();
}
echo $OUTPUT->footer();
