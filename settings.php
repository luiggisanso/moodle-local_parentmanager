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
 defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('accounts', new admin_category('local_parentmanager_cat', get_string('pluginname', 'local_parentmanager')));

    // 1. CSV Import
    $ADMIN->add('local_parentmanager_cat', new admin_externalpage(
        'local_parentmanager_csv',
        get_string('import_nav', 'local_parentmanager'),
        new moodle_url('/local/parentmanager/index.php'),
        'local/parentmanager:manage'
    ));

    // 2. Manual (Individual)
    $ADMIN->add('local_parentmanager_cat', new admin_externalpage(
        'local_parentmanager_manual',
        get_string('manual_nav', 'local_parentmanager'),
        new moodle_url('/local/parentmanager/manual.php'),
        'local/parentmanager:manage'
    ));

    // 3. Cohort
    $ADMIN->add('local_parentmanager_cat', new admin_externalpage(
        'local_parentmanager_cohort',
        get_string('cohort_nav', 'local_parentmanager'),
        new moodle_url('/local/parentmanager/cohort.php'),
        'local/parentmanager:manage'
    ));

    // 4. Gestion
    $ADMIN->add('local_parentmanager_cat', new admin_externalpage(
        'local_parentmanager_manage',
        get_string('manage_nav', 'local_parentmanager'),
        new moodle_url('/local/parentmanager/manage.php'),
        'local/parentmanager:manage'
    ));
}