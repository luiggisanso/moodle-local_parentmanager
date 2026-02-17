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
namespace local_parentmanager;

defined('MOODLE_INTERNAL') || die();

class helper {

    /**
     * Users dropdown
     */
    public static function get_users_for_dropdown(): array {
        global $DB;
        $sql = "SELECT id, CONCAT(lastname, ' ', firstname, ' (', email, ')') as fullname 
                FROM {user} WHERE deleted = 0 AND id > 1 ORDER BY lastname ASC, firstname ASC";
        return $DB->get_records_sql_menu($sql, null, 0, 5000);
    }

    /**
     * Cohort list
     */
    public static function get_cohorts_menu(): array {
        global $DB;
        return $DB->get_records_menu('cohort', null, 'name ASC', 'id, name');
    }

    /**
     * Cohort members)
     */
    public static function get_cohort_members(int $cohortid): array {
        global $DB;
        $fields = 'u.id, u.firstname, u.lastname, u.email, u.idnumber, u.middlename, u.alternatename, u.firstnamephonetic, u.lastnamephonetic';
        $sql = "SELECT $fields
                FROM {cohort_members} cm
                JOIN {user} u ON cm.userid = u.id
                WHERE cm.cohortid = :cohortid AND u.deleted = 0
                ORDER BY u.lastname, u.firstname";
        return $DB->get_records_sql($sql, ['cohortid' => $cohortid]);
    }

    /**
     * Role's parent
     */
    public static function get_parents_list(int $roleid): array {
        global $DB;
        $fields = 'u.id, u.firstname, u.lastname, u.email, u.idnumber, u.middlename, u.alternatename, u.firstnamephonetic, u.lastnamephonetic';
        $sql = "SELECT DISTINCT $fields
                FROM {role_assignments} ra
                JOIN {context} ctx ON ra.contextid = ctx.id
                JOIN {user} u ON ra.userid = u.id
                WHERE ra.roleid = :roleid AND ctx.contextlevel = " . CONTEXT_USER . "
                ORDER BY u.lastname, u.firstname";
        return $DB->get_records_sql($sql, ['roleid' => $roleid]);
    }

    /**
     * Parent's child
     */
    public static function get_children_of_parent(int $parentid, int $roleid): array {
        global $DB;
        $fields = 'u.id, u.firstname, u.lastname, u.email, u.idnumber, u.middlename, u.alternatename, u.firstnamephonetic, u.lastnamephonetic';
        $sql = "SELECT $fields
                FROM {role_assignments} ra
                JOIN {context} ctx ON ra.contextid = ctx.id
                JOIN {user} u ON ctx.instanceid = u.id
                WHERE ra.userid = :parentid AND ra.roleid = :roleid AND ctx.contextlevel = " . CONTEXT_USER . "
                ORDER BY u.lastname, u.firstname";
        return $DB->get_records_sql($sql, ['parentid' => $parentid, 'roleid' => $roleid]);
    }

    /**
     * Parent's child (refresh)
     */
    public static function get_parents_of_child(int $childid, int $roleid): array {
        global $DB;
        $context = \context_user::instance($childid);
        $fields = 'u.id, u.firstname, u.lastname, u.email, u.idnumber, u.middlename, u.alternatename, u.firstnamephonetic, u.lastnamephonetic';
        $sql = "SELECT $fields
                FROM {role_assignments} ra
                JOIN {user} u ON ra.userid = u.id
                WHERE ra.contextid = :contextid AND ra.roleid = :roleid
                ORDER BY u.lastname, u.firstname";
        return $DB->get_records_sql($sql, ['contextid' => $context->id, 'roleid' => $roleid]);
    }

    /**
     * Relation creation
     */
    public static function create_link(int $parentid, int $childid, int $roleid): void {
        global $DB;
        $parent = $DB->get_record('user', ['id' => $parentid]);
        $child  = $DB->get_record('user', ['id' => $childid]);

        if (!$parent || !$child) return;

        $childcontext = \context_user::instance($childid);
        role_assign($roleid, $parentid, $childcontext->id);

        self::update_custom_profile_field($parentid, 'enfant_email', fullname($child));
        self::update_custom_profile_field($childid, 'parent_email', fullname($parent));
    }

    /**
     * Deletion
     */
    public static function delete_link(int $parentid, int $childid, int $roleid): void {
        global $DB;
        $childcontext = \context_user::instance($childid);
        role_unassign($roleid, $parentid, $childcontext->id);

        // Refresh Parent
        $children = self::get_children_of_parent($parentid, $roleid);
        $names = [];
        foreach ($children as $c) $names[] = fullname($c);
        self::set_profile_field($parentid, 'enfant_email', implode(', ', $names));

        // Refresh Child
        $parents = self::get_parents_of_child($childid, $roleid);
        $names = [];
        foreach ($parents as $p) $names[] = fullname($p);
        self::set_profile_field($childid, 'parent_email', implode(', ', $names));
    }

    /**
     * UPDATE FIELD (Append)
     */
    public static function update_custom_profile_field(int $userid, string $fieldshortname, string $value): void {
        global $DB;
        $field = $DB->get_record('user_info_field', ['shortname' => $fieldshortname]);
        if (!$field) return;

        $record = $DB->get_record('user_info_data', ['userid' => $userid, 'fieldid' => $field->id]);
        
        if ($record) {
            $items = array_map('trim', explode(',', $record->data));
            if (!in_array($value, $items)) {
                $items[] = $value;
                $items = array_filter($items); 
                $record->data = implode(', ', $items);
                $DB->update_record('user_info_data', $record);
            }
        } else {
            $new = new \stdClass();
            $new->userid = $userid;
            $new->fieldid = $field->id;
            $new->data = $value;
            $new->dataformat = 0;
            $DB->insert_record('user_info_data', $new);
        }
    }

    /**
     * SET FIELD (Overwrite)
     */
    public static function set_profile_field(int $userid, string $fieldshortname, string $value): void {
        global $DB;
        $field = $DB->get_record('user_info_field', ['shortname' => $fieldshortname]);
        if (!$field) return;

        $record = $DB->get_record('user_info_data', ['userid' => $userid, 'fieldid' => $field->id]);
        
        if ($record) {
            if ($record->data !== $value) {
                $record->data = $value;
                $DB->update_record('user_info_data', $record);
            }
        } else {
            $new = new \stdClass();
            $new->userid = $userid;
            $new->fieldid = $field->id;
            $new->data = $value;
            $new->dataformat = 0;
            $DB->insert_record('user_info_data', $new);
        }
    }
}