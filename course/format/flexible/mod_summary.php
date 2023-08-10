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
require_once('../../../config.php');
require_once($CFG->dirroot . '/course/format/lib.php');
//require_once($CFG->dirroot . '/course/format/flexible/lib.php');
require_login();

$courseid = required_param('courseid', PARAM_INT);
$showsummary = optional_param('showsummary', 1, PARAM_INT);  // One is top and two is grid.

$courseformat = course_get_format($courseid);
$data = array('section0attop' => $showsummary);
$courseformat->update_course_format_options($data);

redirect($CFG->wwwroot."/course/view.php?id=".$courseid);
