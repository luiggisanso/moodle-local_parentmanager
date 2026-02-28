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
require_once(__DIR__ . '/../helper.php');

class manual_form extends \moodleform {
    public function definition() {
        global $DB;
        $mform = $this->_form;

        $allusers = \local_parentmanager\helper::get_users_for_dropdown();
        
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

        $mform->addElement('header', 'general', get_string('manual_header', 'local_parentmanager'));

        $mform->addElement('select', 'roleid', get_string('role_select', 'local_parentmanager'), $roles);
        foreach ($roles as $id => $name) {
            if (stripos($name, 'parent') !== false) {
                $mform->setDefault('roleid', $id);
                break;
            }
        }

        $mform->addElement('header', 'parent_hdr', '1. ' . get_string('select_parent', 'local_parentmanager'));
        $mform->addElement('autocomplete', 'parentid', get_string('select_parent', 'local_parentmanager'), $allusers, [
            'multiple' => false, 
            'placeholder' => get_string('search_parent', 'local_parentmanager')
        ]);        $mform->addRule('parentid', null, 'required', null, 'client');

        $mform->addElement('header', 'children_hdr', '2. ' . get_string('select_children', 'local_parentmanager'));
        $mform->addElement('autocomplete', 'childrenids', get_string('select_children', 'local_parentmanager'), $allusers, [
            'multiple' => true, 
            'placeholder' => get_string('search_children', 'local_parentmanager')
        ]);        $mform->addRule('childrenids', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('assign_btn', 'local_parentmanager'));
    }
}


