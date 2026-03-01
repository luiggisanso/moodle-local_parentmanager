/**
 * Version information.
 *
 * Parent Manager - This plugin allows you to easily manage parent-child mecanism.
 *
 * @package     local_parentmanager
 * @module     local_parentmanager/selectall
 * @copyright   2026 Luiggi Sansonetti <1565841+luiggisanso@users.noreply.github.com> (Coder)
 * @copyright   2026 E-learning Touch' <contact@elearningtouch.com> (Maintainer)
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    return {
        /**
         * Initialise l'écouteur d'événement sur la case #selectall
         * @param {string} targetSelector Le sélecteur CSS des cases à cocher cibles
         */
        init: function(targetSelector) {
            $('#selectall').on('change', function() {
                $(targetSelector).prop('checked', $(this).prop('checked'));
            });
        }
    };
});
