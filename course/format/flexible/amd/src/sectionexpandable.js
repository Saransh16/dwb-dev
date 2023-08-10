/**
 * Flexible format
 *
 * @package    format_flexible
 * @version    See the value of '$plugin->version' in the version.php file.
 * @copyright  &copy; 2019 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/* jshint ignore:start */
define(['jquery', 'core/str', 'core/log'], function($, str, log) {

    log.debug('Flexible format section expandable AMD');
    return {
        init: function() {
            $(document).ready(function($) {
                $('.flexibleexpandabletoggleshowbutton').on('click', function(e) {
                    e.preventDefault();
                    var target = $(this).attr('data-target');
                    if (typeof target != 'undefined') {
                        $(target).collapse('show');
                        $(target).attr('data-state', 'expanded');
                        $(this).addClass('hidden');
                    }
                });
                $('.flexibleexpandabletogglehidebutton').on('click', function(e) {
                    e.preventDefault();
                    var target = $(this).attr('data-target');
                    if (typeof target != 'undefined') {
                        $(target).collapse('hide');
                        $(target).attr('data-state', 'collapsed');
                        var showmore = $(this).attr('data-show');
                        $(showmore).removeClass('hidden');
                    }
                });
            });
            log.debug('Flexible format section expandable AMD init');
        }
    }
});
/* jshint ignore:end */
