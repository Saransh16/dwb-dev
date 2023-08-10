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
 * Flexible Format
 *
 * @package    format_flexible
 * @version    See the value of '$plugin->version' in the version.php file.
 * @copyright  &copy; 2019 G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @author     Based on code originally written by Paul Krix and Julian Ridden.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers supported by this format.
 */
class format_flexible_observer {

    /**
     * Observer for the event course_content_deleted.
     *
     * Deletes the settings entry for the given course upon course deletion.
     *
     * @param \core\event\course_content_deleted $event
     */
    public static function course_content_deleted(\core\event\course_content_deleted $event) {
        if (class_exists('format_flexible', false)) {
            // If class format_flexible was never loaded, this is definitely not a course in 'Flexible' format.
            self::delete_images($event->objectid);
        }
    }

    /**
     * Observer for the event course_restored.
     *
     * Deletes the settings entry for the given course upon course deletion.
     *
     * @param \core\event\course_restored $event
     */
    public static function course_restored(\core\event\course_restored $event) {
        global $DB;
        $format = $DB->get_field('course', 'format', array('id' => $event->objectid));
        if ($format != 'flexible') {
            // Then delete the images and any summary.
            self::delete_images($event->objectid);
        }
    }

    protected static function delete_images($courseid) {
        // Delete any images associated with the course.
        $courseformat = format_flexible::get_instance($courseid);
        $courseformat->delete_images();
        unset($courseformat);  // Destruct.
    }
}
