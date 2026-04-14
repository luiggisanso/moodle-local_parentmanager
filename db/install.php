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
 * Executes on plugin installation.
 *
 * Creates the Parent role and assigns required capabilities if it doesn't exist.
 *
 * @package     local_parentmanager
 * @copyright   2026 Luiggi Sansonetti <1565841+luiggisanso@users.noreply.github.com> (Coder)
 * @copyright   2026 E-learning Touch' <contact@elearningtouch.com> (Maintainer)
 * @contributor 2026 https://github.com/mussaab
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Fonction exécutée automatiquement par Moodle lors de l'installation du plugin.
 *
 * @return void
 */
function xmldb_local_parentmanager_install() {
    global $DB;

    if (!$DB->record_exists('role', ['shortname' => 'parent'])) {
        $rolename = get_string('parent_default_name', 'local_parentmanager');
        $description = get_string('parent_description_role', 'local_parentmanager');

        $roleid = create_role(
            $rolename,
            'parent',
            $description
        );

        if ($roleid) {
            set_role_contextlevels($roleid, [CONTEXT_USER]);

            // Liste des capacités par défaut attribuées au rôle
            $capabilities = [
                'moodle/user:viewdetails',
                'moodle/user:viewalldetails',
                'moodle/user:readuserblogs',
                'moodle/user:readuserposts',
                'moodle/user:viewuseractivitiesreport',
                'tool/policy:acceptbehalf',
                'moodle/user:editprofile', 
            ];

            $context = context_system::instance();

            foreach ($capabilities as $cap) {
                assign_capability($cap, CAP_ALLOW, $roleid, $context->id, true);
            }
        }
    }
}
