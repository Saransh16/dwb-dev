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
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

// Horrible backwards compatible parameter aliasing..
if ($topic = optional_param('topic', 0, PARAM_INT)) { // Topics old section parameter.
    $url = $PAGE->url;
    $url->param('section', $topic);
    debugging('Outdated topic / flexible param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($ctopic = optional_param('ctopics', 0, PARAM_INT)) { // Collapsed Topics old section parameter.
    $url = $PAGE->url;
    $url->param('section', $ctopic);
    debugging('Outdated collapsed topic param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
if ($week = optional_param('week', 0, PARAM_INT)) { // Weeks old section parameter.
    $url = $PAGE->url;
    $url->param('section', $week);
    debugging('Outdated week param passed to course/view.php', DEBUG_DEVELOPER);
    redirect($url);
}
// End backwards-compatible aliasing..

$coursecontext = context_course::instance($course->id);

// Retrieve course format option fields and add them to the $course object.
$courseformat = course_get_format($course);
$course = $courseformat->get_course();

if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $coursecontext) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Make sure all sections are created.
course_create_sections_if_missing($course, range(0, $course->numsections));

$renderer = $PAGE->get_renderer('format_flexible');

$ffsettings = $courseformat->get_settings();
$imageproperties = $courseformat->calculate_image_container_properties(
    $ffsettings['imagecontainerratio'], $ffsettings['borderwidth']);

echo '<style type="text/css" media="screen">';
echo '/* <![CDATA[ */';
echo '#flexibleiconcontainer  ul.flexibleicons {';
$imagecontaineralignment = $ffsettings['imagecontaineralignment'];
if ($imagecontaineralignment == 'left') {
    $imagecontaineralignment = 'flex-start';
} else if ($imagecontaineralignment == 'right') {
    $imagecontaineralignment = 'flex-end';
}
echo 'justify-content: '.$imagecontaineralignment.';';
echo '}';
echo '.course-content ul.flexibleicons li .icon_content {';
if ($ffsettings['sectiontitlefontsize']) { // Font size is set.
    echo 'font-size: '.$ffsettings['sectiontitlefontsize'].'px;';
    if (($ffsettings['sectiontitlefontsize'] + 4) > 20) {
        echo 'height: '.($ffsettings['sectiontitlefontsize'] + 4).'px;';
    }
}
echo 'text-align: '.$ffsettings['sectiontitlealignment'].';';
echo '}';
echo '.course-content ul.flexibleicons li .flexiblesectionwrapper {';
echo 'border-color: ';
if ($ffsettings['bordercolour'][0] != '#') {
    echo '#';
}
echo $ffsettings['bordercolour'].';';
echo 'background-color: ';
if ($ffsettings['imagecontainerbackgroundcolour'][0] != '#') {
    echo '#';
}
echo $ffsettings['imagecontainerbackgroundcolour'].';';
echo 'border-width: '.$ffsettings['borderwidth'];
if ($ffsettings['borderwidth']) {
    echo 'px';
}
echo ';';
if ($ffsettings['borderradius'] == 2) { // On.
    echo 'border-radius: '.$ffsettings['borderwidth'];
    if ($ffsettings['borderwidth']) {
        echo 'px';
    }
    echo ';';
}
echo '}';

$startindex = 0;
if ($ffsettings['bordercolour'][0] == '#') {
    $startindex++;
}
$red = hexdec(substr($ffsettings['bordercolour'], $startindex, 2));
$green = hexdec(substr($ffsettings['bordercolour'], $startindex + 2, 2));
$blue = hexdec(substr($ffsettings['bordercolour'], $startindex + 4, 2));

echo '.course-content ul.flexibleicons li:hover .flexiblesectionwrapper {';
echo 'box-shadow: 0 0 0 '.$ffsettings['borderwidth'];
if ($ffsettings['borderwidth']) {
    echo 'px';
}
echo ' rgba('.$red.','.$green.','.$blue.', 0.3);';
echo '}';

echo '.course-content ul.flexibleicons li.currenticon .flexiblesectionwrapper {';
echo 'box-shadow: 0 0 2px 4px ';
if ($ffsettings['currentselectedsectioncolour'][0] != '#') {
    echo '#';
}
echo $ffsettings['currentselectedsectioncolour'].';';
echo '}';

if ($ffsettings['sectiontitleboxposition'] == 1) { // Inside.
    echo '.course-content ul.flexibleicons li .icon_content.content_inside {';
    echo 'background-color: ';
    if ($ffsettings['boxinsidetextbackgroundcolour'][0] != '#') {
        echo '#';
    }
    echo $ffsettings['boxinsidetextbackgroundcolour'].';';
    echo 'color: ';
    if ($ffsettings['boxinsidetextcolour'][0] != '#') {
        echo '#';
    }
    echo $ffsettings['boxinsidetextcolour'].';';
    if ($ffsettings['sectiontitleboxheight'] != 0) {
        echo 'height: ';
        echo $ffsettings['sectiontitleboxheight'];
        echo 'px;';
    }
    echo 'opacity: '.$ffsettings['insideboxopacity'].';';
    echo '}';
}

echo '.course-content ul.flexibleicons > li .render_as {';
echo 'background-color: ';
if ($ffsettings['boxinsidetextbackgroundcolour'][0] != '#') {
    echo '#';
}
echo $ffsettings['boxinsidetextbackgroundcolour'].';';
echo 'color: ';
if ($ffsettings['boxinsidetextcolour'][0] != '#') {
    echo '#';
}
echo $ffsettings['boxinsidetextcolour'].';';
echo 'opacity: '.$ffsettings['insideboxopacity'].';';
echo '}';

echo '.course-content ul.flexibleicons img.new_activity {';
echo 'margin-top: '.$imageproperties['margin-top'].'px;';
echo 'margin-left: '.$imageproperties['margin-left'].'px;';
echo '}';

echo '/* ]]> */';
echo '</style>';

if ($sectionid) {
    /* The section id has been specified so use the value of $displaysection as that
       will be set to the actual section number. */
    $sectionparam = $displaysection;
} else {
    $sectionparam = optional_param('section', -1, PARAM_INT);
}
if ($sectionparam != -1) {
    $renderer->set_initialsection($sectionparam);
    $displaysection = $sectionparam;
}

if ($sectionparam != -1) {
    $renderer->print_single_section_page($course, null, null, null, null, $displaysection);
} else {
    $renderer->print_multiple_section_page($course, null, null, null, null);
}

// Include course format js module.
$PAGE->requires->js('/course/format/flexible/format.js');
