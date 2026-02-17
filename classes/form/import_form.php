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
namespace local_parentmanager\form;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

class import_form extends \moodleform {
    public function definition() {
        global $DB, $OUTPUT;
        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('import_header', 'local_parentmanager'));

        $msg = get_string('csv_instructions', 'local_parentmanager');
        $mform->addElement('html', $OUTPUT->notification($msg, 'info', false));

        $mform->addElement('filepicker', 'importfile', get_string('csv_file', 'local_parentmanager'), null, ['accepted_types' => ['.csv', '.txt']]);
        $mform->addRule('importfile', null, 'required', null, 'client');

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

        $mform->addElement('select', 'roleid', get_string('role_select', 'local_parentmanager'), $roles);
        
        foreach ($roles as $id => $name) {
            if (stripos($name, 'parent') !== false) {
                $mform->setDefault('roleid', $id);
                break;
            }
        }

        $this->add_action_buttons(false, get_string('import_btn', 'local_parentmanager'));
    }
}
