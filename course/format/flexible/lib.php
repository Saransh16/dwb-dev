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

require_once($CFG->dirroot . '/course/format/lib.php'); // For format_base.

class format_flexible extends format_base {

    // Columns.
    private static $numcolumns = array(
        1 => '1', 2 => '2', 3 => '3', 4 => '4');
    // Ratio constants - 3-2, 3-1, 3-3, 2-3, 1-3, 4-3, 3-4 and 5-3:...
    private static $imagecontainerratios = array(
        1 => '3-2', 2 => '3-1', 3 => '3-3', 4 => '2-3', 5 => '1-3', 6 => '4-3', 7 => '3-4', 8 => '5-3');
    // Border width constants - 0 to 10:....
    private static $borderwidths = array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7',
        8 => '8', 9 => '9', 10 => '10');
    private static $imagecontainerwidth = 768;
    private static $currentratio = 1; // 3-2.
    private static $currentborderwidth = 3;
    private static $currentheight = 140;
    private static $activitymargintop = 101;
    private static $activitymarginleft = 1118;
    // Opacity constants - 0 to 1:....
    private static $opacities = array('0' => '0.0', '.1' => '0.1', '.2' => '0.2', '.3' => '0.3', '.4' => '0.4',
       '.5' => '0.5', '.6' => '0.6', '.7' => '0.7', '.8' => '0.8', '.9' => '0.9', '1' => '1.0');
    private static $sectiontitlefontsizes = array(0 => '0', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16',
       17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24');
    private $settings;

    /**
     * Creates a new instance of class
     *
     * Please use {@link course_get_format($courseorid)} to get an instance of the format class
     *
     * @param string $format
     * @param int $courseid
     * @return format_flexible
     */
    protected function __construct($format, $courseid) {
        if ($courseid === 0) {
            global $COURSE;
            $courseid = $COURSE->id;  // Save lots of global $COURSE as we will never be the site course.
        }
        parent::__construct($format, $courseid);
    }

    /**
     * Returns the default section name for the format.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section) {
        /* Follow the same logic so that this method is supported.  The MDL-51610 enchancement refactored things,
          but that is not appropriate for us. */
        return $this->get_section_name($section);
    }

    /**
     * Prevents ability to change a static variable outside of the class.
     * @return array Array of number of columns.
     */
    public static function get_num_columns() {
        return self::$numcolumns;
    }

    /**
     * Gets the default image container width.
     * @return int Default image container alignment.
     */
    public static function get_default_num_columns() {
        return 3;
    }

    /**
     * Prevents ability to change a static variable outside of the class.
     * @return array Array of horizontal alignments.
     */
    public static function get_horizontal_alignments() {
        $imagecontaineralignments = array(
            'left' => get_string('left', 'format_flexible'),
            'center' => get_string('centre', 'format_flexible'),
            'right' => get_string('right', 'format_flexible')
        );
        return $imagecontaineralignments;
    }

    /**
     * Gets the default image container width.
     * @return int Default image container alignment.
     */
    public static function get_default_image_container_alignment() {
        return 'center';
    }

    /**
     * Gets the default image container width.
     * @return int Default image container width.
     */
    public static function get_default_image_container_width() {
        return 210;
    }

    /**
     * Prevents ability to change a static variable outside of the class.
     * @return array Array of image container ratios.
     */
    public static function get_image_container_ratios() {
        return self::$imagecontainerratios;
    }

    /**
     * Gets the default image container ratio.
     * @return int Default image container ratio.
     */
    public static function get_default_image_container_ratio() {
        return 1; // Ratio of '3-2'.
    }

    /**
     * Gets the default image resize method.
     * @return int Default image resize method.
     */
    public static function get_default_image_resize_method() {
        return 1; // Scale.
    }

    /**
     * Gets the default section0attop.
     * @return int Default section0attop.
     */
    public static function get_default_section0attop() {
        return 2; // Grid.
    }

    /**
     * Gets the default generated section image colours.
     * @return string Default generated section image colours.
     */
    public static function get_default_generated_section_image_colours() {
        return '#81ecec, #74b9ff, #a29bfe, #dfe6e9, #00b894, #0984e3, #b2bec3, #fdcb6e, #fd79a8, #6c5ce7';
    }

    /**
     * Gets the default image file type.
     * @return int Default image file type.
     */
    public static function get_default_image_file_type() {
        return 1; // Original.
    }

    /**
     * Gets the default border colour.
     * @return string Default border colour.
     */
    public static function get_default_border_colour() {
        return '#dddddd';
    }

    /**
     * Prevents ability to change a static variable outside of the class.
     * @return array Array of border widths.
     */
    public static function get_border_widths() {
        return self::$borderwidths;
    }

    /**
     * Gets the default border width.
     * @return int Default border width.
     */
    public static function get_default_border_width() {
        return 3; // Pixels.
    }

    /**
     * Gets the default border width.
     * @return int Default border width.
     */
    public static function get_default_border_radius() {
        return 2; // On.
    }

    /**
     * Gets the default image container background colour.
     * @return string Default image container background colour.
     */
    public static function get_default_image_container_background_colour() {
        return '#f1f2f2';
    }

    /**
     * Gets the default current selected section colour.
     * @return string Default current selected section colour.
     */
    public static function get_default_current_selected_section_colour() {
        return '#8E66FF';
    }

    /**
     * Gets the default current selected image container text colour.
     * @return string Default current selected image container text colour.
     */
    public static function get_default_current_selected_image_container_text_colour() {
        return '#3b53ad';
    }

    /**
     * Gets the default current selected image container colour.
     * @return string Default current selected image container colour.
     */
    public static function get_default_current_selected_image_container_colour() {
        return '#ffc540';
    }

    /**
     * Gets the default hide section title.
     * @return int Default default hide section title.
     */
    public static function get_default_hide_section_title() {
        return 1; // No.
    }

    /**
     * Gets the default section title flexible length max option.
     * @return int Default default section title flexible length max option.
     */
    public static function get_default_section_title_flexible_length_max_option() {
        return 0; // No truncation.
    }

    /**
     * Gets the default section title box position.
     * @return int Default default section title box position.
     */
    public static function get_default_section_title_box_position() {
        return 2; // Outside.
    }

    /**
     * Gets the default section title box inside position.
     * @return int Default default section title box inside position.
     */
    public static function get_default_section_title_box_inside_position() {
        return 1; // Top.
    }

    /**
     * Gets the default section title box height.
     * @return int Default default section title box height.
     */
    public static function get_default_section_title_box_height() {
        return 0; // Calculated.
    }

    /**
     * Gets the default opacity for the section title box.
     * @return string Opacity of section title box.
     */
    public static function get_default_inside_box_opacity() {
        return '.8';
    }

    /**
     * Gets the default section summary icon.
     * @return string Section summary icon name.
     */
    public static function get_default_section_summary_icon() {
        return 'info-circle';
    }

    /**
     * Gets the default opacities.
     * @return Array Opacities.
     */
    public static function get_default_opacities() {
        return self::$opacities;
    }

    /**
     * Gets the default font size for the section title.
     * @return int Font size of the section title.
     */
    public static function get_default_section_title_font_size() {
        return 0;
    }

    /**
     * Gets the default alignment for the section title.
     * @return string Alignment of the section title.
     */
    public static function get_default_section_title_alignment() {
        return 'center';
    }

    /**
     * Gets the default section title font sizes.
     * @return Array Font sizes.
     */
    public static function get_default_section_font_sizes() {
        return self::$sectiontitlefontsizes;
    }

    /**
     * Gets the default section title inside text colour.
     * @return string Default default section title inside text colour.
     */
    public static function get_default_box_inside_text_colour() {
        return '#000000';
    }

    /**
     * Gets the default section title inside background colour.
     * @return string Default default section title inside background colour.
     */
    public static function get_default_box_inside_text_background_colour() {
        return '#ffffff';
    }

    /**
     * Gets the displayed image path for storage of the displayed image.
     * @return string The path.
     */
    public static function get_image_path() {
        return '/flexibleimage/';
    }

    public static function get_maximum_image_width() {
        return 768;
    }

    /**
     * Returns the format's settings and gets them if they do not exist.
     * @return array The settings as an array.
     */
    public function get_settings() {
        if (empty($this->settings) == true) {
            $this->settings = $this->get_format_options();
        }
        return $this->settings;
    }

    /**
     * Returns the column CSS class for the number of columns.
     * @param bool $expandable If the section will be 'expandable'.
     *
     * @return string CSS class name.
     */
    public function get_column_class($expandable) {
        $bs232 = self::bstwothreetwo();
        if ($bs232) {
            $returnvalue = 'span12'; // For 1 / Fallback / Expandable.
        } else {
            $returnvalue = 'col-sm-12'; // For 1 / Fallback / Expandable.
        }

        if (!$expandable) {
            $settings = $this->get_settings();
            switch($settings['numcolumns']) {
                case 2:
                    if ($bs232) {
                        $returnvalue = 'span6';
                    } else {
                        $returnvalue = 'col-sm-6';
                    }
                    break;
                case 3:
                    if ($bs232) {
                        $returnvalue = 'span4';
                    } else {
                        $returnvalue = 'col-sm-4';
                    }
                    break;
                case 4:
                    if ($bs232) {
                        $returnvalue = 'span3';
                    } else {
                        $returnvalue = 'col-sm-3';
                    }
                    break;
            }
        }

        return $returnvalue;
    }

    /**
     * Gets the name for the provided section.
     *
     * @param stdClass $section The section.
     * @return string The section name.
     */
    public function get_section_name($section) {
        $section = $this->get_section($section);
        if (!empty($section->name)) {
            return format_string($section->name, true, array('context' => $this->get_context()));
        } if ($section->section == 0) {
            return get_string('topic0', 'format_flexible');
        } else {
            return get_string('topic', 'format_flexible').' '. $section->section;
        }
    }

    /**
     * Indicates this format uses sections.
     *
     * @return bool Returns true
     */
    public function uses_sections() {
        return true;
    }

    /**
     * The URL to use for the specified course (with section)
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = array()) {
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', array('id' => $course->id));

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
            $sectionid = $section->id;
        } else {
            $sectionno = $section;
            $sectionid = null;
        }
        if ($sectionno !== null) {
            // Always COURSE_DISPLAY_MULTIPAGE.
            if ($sr !== null) {
                if ($sr) {
                    $sectionno = $sr;
                }
            }
            global $PAGE;
            if (($sectionno == 0) && ($course->section0attop == 1)) {
                $url->set_anchor('section-'.$sectionno);
            } else {
                if (is_null($sectionid)) {
                    $sectionid = $this->get_section($section)->id;
                }
                if (!empty($options['navigation'])) {
                    $url->param('section', $sectionno);
                } else {
                    $url->param('sectionid', $sectionid);
                }
            }
        }
        return $url;
    }

    /**
     * Returns the information about the ajax support in the given source format
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     * The property (array)testedbrowsers can be used as a parameter for {@link ajaxenabled()}.
     *
     * @return stdClass
     */
    public function supports_ajax() {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    /**
     * Custom action after section has been moved in AJAX mode
     *
     * Used in course/rest.php
     *
     * @return array This will be passed in ajax respose
     */
    public function ajax_section_move() {
        global $PAGE;
        $titles = array();
        $course = $this->get_course();
        $modinfo = get_fast_modinfo($course);
        $renderer = $this->get_renderer($PAGE);
        if ($renderer && ($sections = $modinfo->get_section_info_all())) {
            foreach ($sections as $number => $section) {
                $titles[$number] = $renderer->section_title($section, $course);
            }
        }
        return array('sectiontitles' => $titles, 'action' => 'move');
    }

    /**
     * Returns the list of blocks to be automatically added for the newly created course
     *
     * @return array of default blocks, must contain two keys BLOCK_POS_LEFT and BLOCK_POS_RIGHT
     *     each of values is an array of block names (for left and right side columns)
     */
    public function get_default_blocks() {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
        );
    }

    /**
     * Definitions of the additional options that this course format uses for the course.
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false) {
        static $courseformatoptions = false;
        $courseconfig = null;

        if ($courseformatoptions === false) {
            /* Note: Because 'admin_setting_configcolourpicker' in 'settings.php' needs to use a prefixing '#'
              this needs to be stripped off here if it's there for the format's specific colour picker. */
            $defaults = $this->get_course_format_colour_defaults();

            $courseconfig = get_config('moodlecourse');
            $courseid = $this->get_courseid();
            if ($courseid == 1) { // New course.
                $defaultnumsections = $courseconfig->numsections;
            } else { // Existing course that may not have 'numsections' - see get_last_section().
                global $DB;
                $defaultnumsections = $DB->get_field_sql('SELECT max(section) from {course_sections}
                    WHERE course = ?', array($courseid));
            }
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $defaultnumsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT
                ),
                'numcolumns' => array(
                    'default' => get_config('format_flexible', 'defaultnumcolumns'),
                    'type' => PARAM_INT
                ),
                'imagecontaineralignment' => array(
                    'default' => get_config('format_flexible', 'defaultimagecontaineralignment'),
                    'type' => PARAM_ALPHANUM
                ),
                'imagecontainerratio' => array(
                    'default' => get_config('format_flexible', 'defaultimagecontainerratio'),
                    'type' => PARAM_ALPHANUM
                ),
                'imageresizemethod' => array(
                    'default' => get_config('format_flexible', 'defaultimageresizemethod'),
                    'type' => PARAM_INT
                ),
                'section0attop' => array(
                    'default' => get_config('format_flexible', 'defaultsection0attop'),
                    'type' => PARAM_INT
                ),
                'bordercolour' => array(
                    'default' => $defaults['defaultbordercolour'],
                    'type' => PARAM_ALPHANUM
                ),
                'borderwidth' => array(
                    'default' => get_config('format_flexible', 'defaultborderwidth'),
                    'type' => PARAM_INT
                ),
                'borderradius' => array(
                    'default' => get_config('format_flexible', 'defaultborderradius'),
                    'type' => PARAM_INT
                ),
                'imagecontainerbackgroundcolour' => array(
                    'default' => $defaults['defaultimagecontainerbackgroundcolour'],
                    'type' => PARAM_ALPHANUM
                ),
                'currentselectedsectioncolour' => array(
                    'default' => $defaults['defaultcurrentselectedsectioncolour'],
                    'type' => PARAM_ALPHANUM
                ),
                'hidesectiontitle' => array(
                    'default' => get_config('format_flexible', 'defaulthidesectiontitle'),
                    'type' => PARAM_INT
                ),
                'sectiontitleflexiblelengthmaxoption' => array(
                    'default' => get_config('format_flexible', 'defaultsectiontitleflexiblelengthmaxoption'),
                    'type' => PARAM_INT
                ),
                'sectiontitleboxposition' => array(
                    'default' => get_config('format_flexible', 'defaultsectiontitleboxposition'),
                    'type' => PARAM_INT
                ),
                'sectiontitleboxinsideposition' => array(
                    'default' => get_config('format_flexible', 'defaultsectiontitleboxinsideposition'),
                    'type' => PARAM_INT
                ),
                'sectiontitleboxheight' => array(
                    'default' => get_config('format_flexible', 'defaultsectiontitleboxheight'),
                    'type' => PARAM_INT
                ),
                'insideboxopacity' => array(
                    'default' => get_config('format_flexible', 'defaultinsideboxopacity'),
                    'type' => PARAM_RAW
                ),
                'sectiontitlefontsize' => array(
                    'default' => get_config('format_flexible', 'defaultsectiontitlefontsize'),
                    'type' => PARAM_INT
                ),
                'sectiontitlealignment' => array(
                    'default' => get_config('format_flexible', 'defaultsectiontitlealignment'),
                    'type' => PARAM_ALPHANUM
                ),
                'boxinsidetextcolour' => array(
                    'default' => $defaults['defaultboxinsidetextcolour'],
                    'type' => PARAM_ALPHANUM
                ),
                'boxinsidetextbackgroundcolour' => array(
                    'default' => $defaults['defaultboxinsidetextbackgroundcolour'],
                    'type' => PARAM_ALPHANUM
                ),
                'newactivity' => array(
                    'default' => get_config('format_flexible', 'defaultnewactivity'),
                    'type' => PARAM_INT
                ),
                'fitsectioncontainertowindow' => array(
                    'default' => get_config('format_flexible', 'defaultfitsectioncontainertowindow'),
                    'type' => PARAM_INT
                ),
                'greyouthidden' => array(
                    'default' => get_config('format_flexible', 'defaultgreyouthidden'),
                    'type' => PARAM_INT
                ),
                'showsectioncompletiongraphic' => array(
                    'default' => 1, // No.
                    'type' => PARAM_INT,
                )
            );
        }
        if ($foreditform && !isset($courseformatoptions['hiddensections']['label'])) {
            /* Note: Because 'admin_setting_configcolourpicker' in 'settings.php' needs to use a prefixing '#'
               this needs to be stripped off here if it's there for the format's specific colour picker. */
            $defaults = $this->get_course_format_colour_defaults();

            $context = $this->get_context();

            if (is_null($courseconfig)) {
                $courseconfig = get_config('moodlecourse');
            }
            $sectionmenu = array();
            for ($i = 0; $i <= $courseconfig->maxsections; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new lang_string('numbersections', 'format_flexible'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                )
            );
            if (has_capability('format/flexible:changeimagecontainercolumns', $context)) {
                $courseformatoptionsedit['numcolumns'] = array(
                    'label' => new lang_string('setnumcolumns', 'format_flexible'),
                    'help' => 'setnumcolumns',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(self::get_num_columns())
                );
            } else {
                $courseformatoptionsedit['numcolumns'] = array('label' => get_config(
                    'format_flexible', 'defaultnumcolumns'), 'element_type' => 'hidden');
            }
            if (has_capability('format/flexible:changeimagecontaineralignment', $context)) {
                $courseformatoptionsedit['imagecontaineralignment'] = array(
                    'label' => new lang_string('setimagecontaineralignment', 'format_flexible'),
                    'help' => 'setimagecontaineralignment',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(self::get_horizontal_alignments())
                );
            } else {
                $courseformatoptionsedit['imagecontaineralignment'] = array('label' => get_config(
                    'format_flexible', 'defaultimagecontaineralignment'), 'element_type' => 'hidden');
            }

            if (has_capability('format/flexible:changeimagecontainersize', $context)) {
                $courseformatoptionsedit['imagecontainerratio'] = array(
                    'label' => new lang_string('setimagecontainerratio', 'format_flexible'),
                    'help' => 'setimagecontainerratio',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(self::$imagecontainerratios)
                );
            } else {
                $courseformatoptionsedit['imagecontainerratio'] = array('label' => get_config(
                            'format_flexible', 'defaultimagecontainerratio'), 'element_type' => 'hidden');
            }

            if (has_capability('format/flexible:changeimageresizemethod', $context)) {
                $courseformatoptionsedit['imageresizemethod'] = array(
                    'label' => new lang_string('setimageresizemethod', 'format_flexible'),
                    'help' => 'setimageresizemethod',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('scale', 'format_flexible'), // Scale.
                            2 => new lang_string('crop', 'format_flexible')   // Crop.
                        )
                    )
                );
            } else {
                $courseformatoptionsedit['imageresizemethod'] = array('label' => get_config(
                            'format_flexible', 'defaultimageresizemethod'), 'element_type' => 'hidden');
            }
            $courseformatoptionsedit['section0attop'] = array('label' => get_config('format_flexible', 'defaultsection0attop'),
                'element_type' => 'hidden');

            if (has_capability('format/flexible:changeimagecontainerstyle', $context)) {
                $courseformatoptionsedit['bordercolour'] = array(
                    'label' => new lang_string('setbordercolour', 'format_flexible'),
                    'help' => 'setbordercolour',
                    'help_component' => 'format_flexible',
                    'element_type' => 'ffcolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaults['defaultbordercolour'])
                    )
                );

                $courseformatoptionsedit['borderwidth'] = array(
                    'label' => new lang_string('setborderwidth', 'format_flexible'),
                    'help' => 'setborderwidth',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(self::$borderwidths)
                );

                $courseformatoptionsedit['borderradius'] = array(
                    'label' => new lang_string('setborderradius', 'format_flexible'),
                    'help' => 'setborderradius',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('off', 'format_flexible'),
                            2 => new lang_string('on', 'format_flexible'))
                    )
                );

                $courseformatoptionsedit['imagecontainerbackgroundcolour'] = array(
                    'label' => new lang_string('setimagecontainerbackgroundcolour', 'format_flexible'),
                    'help' => 'setimagecontainerbackgroundcolour',
                    'help_component' => 'format_flexible',
                    'element_type' => 'ffcolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaults['defaultimagecontainerbackgroundcolour'])
                    )
                );

                $courseformatoptionsedit['currentselectedsectioncolour'] = array(
                    'label' => new lang_string('setcurrentselectedsectioncolour', 'format_flexible'),
                    'help' => 'setcurrentselectedsectioncolour',
                    'help_component' => 'format_flexible',
                    'element_type' => 'ffcolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaults['defaultcurrentselectedsectioncolour'])
                    )
                );
            } else {
                $courseformatoptionsedit['bordercolour'] = array('label' => $defaults['defaultbordercolour'],
                    'element_type' => 'hidden');
                $courseformatoptionsedit['borderwidth'] = array('label' => get_config('format_flexible', 'defaultborderwidth'),
                    'element_type' => 'hidden');
                $courseformatoptionsedit['borderradius'] = array('label' => get_config('format_flexible', 'defaultborderradius'),
                    'element_type' => 'hidden');
                $courseformatoptionsedit['imagecontainerbackgroundcolour'] = array(
                    'label' => $defaults['defaultimagecontainerbackgroundcolour'], 'element_type' => 'hidden');
                $courseformatoptionsedit['currentselectedsectioncolour'] = array(
                    'label' => $defaults['defaultcurrentselectedsectioncolour'], 'element_type' => 'hidden');
            }

            if (has_capability('format/flexible:changesectiontitleoptions', $context)) {
                $courseformatoptionsedit['hidesectiontitle'] = array(
                    'label' => new lang_string('hidesectiontitle', 'format_flexible'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('no'), // No.
                            2 => new lang_string('yes') // Yes.
                        )
                    ),
                    'help' => 'hidesectiontitle',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['sectiontitleflexiblelengthmaxoption'] = array(
                    'label' => new lang_string('sectiontitleflexiblelengthmaxoption', 'format_flexible'),
                    'element_type' => 'text',
                    'element_attributes' => array('size' => 3),
                    'help' => 'sectiontitleflexiblelengthmaxoption',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['sectiontitleboxposition'] = array(
                    'label' => new lang_string('sectiontitleboxposition', 'format_flexible'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('sectiontitleboxpositioninside', 'format_flexible'),
                            2 => new lang_string('sectiontitleboxpositionoutside', 'format_flexible')
                        )
                    ),
                    'help' => 'sectiontitleboxposition',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['sectiontitleboxinsideposition'] = array(
                    'label' => new lang_string('sectiontitleboxinsideposition', 'format_flexible'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('sectiontitleboxinsidepositiontop', 'format_flexible'),
                            2 => new lang_string('sectiontitleboxinsidepositionmiddle', 'format_flexible'),
                            3 => new lang_string('sectiontitleboxinsidepositionbottom', 'format_flexible')
                        )
                    ),
                    'help' => 'sectiontitleboxinsideposition',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['sectiontitleboxheight'] = array(
                    'label' => new lang_string('sectiontitleboxheight', 'format_flexible'),
                    'element_type' => 'text',
                    'element_attributes' => array('size' => 3),
                    'help' => 'sectiontitleboxheight',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['insideboxopacity'] = array(
                    'label' => new lang_string('insideboxopacity', 'format_flexible'),
                    'element_type' => 'select',
                    'element_attributes' => array(self::get_default_opacities()),
                    'help' => 'insideboxopacity',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['sectiontitlefontsize'] = array(
                    'label' => new lang_string('sectiontitlefontsize', 'format_flexible'),
                    'element_type' => 'select',
                    'element_attributes' => array(self::get_default_section_font_sizes()),
                    'help' => 'sectiontitlefontsize',
                    'help_component' => 'format_flexible'
                );
                $courseformatoptionsedit['sectiontitlealignment'] = array(
                    'label' => new lang_string('sectiontitlealignment', 'format_flexible'),
                    'help' => 'sectiontitlealignment',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(self::get_horizontal_alignments())
                );
                $courseformatoptionsedit['boxinsidetextcolour'] = array(
                    'label' => new lang_string('boxinsidetextcolour', 'format_flexible'),
                    'help' => 'boxinsidetextcolour',
                    'help_component' => 'format_flexible',
                    'element_type' => 'ffcolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaults['defaultboxinsidetextcolour'])
                    )
                );
                $courseformatoptionsedit['boxinsidetextbackgroundcolour'] = array(
                    'label' => new lang_string('boxinsidetextbackgroundcolour', 'format_flexible'),
                    'help' => 'boxinsidetextbackgroundcolour',
                    'help_component' => 'format_flexible',
                    'element_type' => 'ffcolourpopup',
                    'element_attributes' => array(
                        array('value' => $defaults['defaultboxinsidetextbackgroundcolour'])
                    )
                );
            } else {
                $courseformatoptionsedit['hidesectiontitle'] = array('label' => get_config('format_flexible', 'defaulthidesectiontitle'),
                    'element_type' => 'hidden');
                $courseformatoptionsedit['sectiontitleflexiblelengthmaxoption'] = array('label' => get_config('format_flexible', 'defaultsectiontitleflexiblelengthmaxoption'),
                    'element_type' => 'hidden');
                $courseformatoptionsedit['sectiontitleboxposition'] = array('label' => get_config('format_flexible', 'defaultsectiontitleboxposition'),
                    'element_type' => 'hidden');
                $courseformatoptionsedit['sectiontitleboxinsideposition'] = array(
                    'label' => get_config('format_flexible', 'defaultsectiontitleboxinsideposition'), 'element_type' => 'hidden');
                $courseformatoptionsedit['sectiontitleboxheight'] = array(
                    'label' => get_config('format_flexible', 'defaultsectiontitleboxheight'), 'element_type' => 'hidden');
                $courseformatoptionsedit['insideboxopacity'] = array(
                    'label' => get_config('format_flexible', 'defaultinsideboxopacity'), 'element_type' => 'hidden');
                $courseformatoptionsedit['sectiontitlefontsize'] = array(
                    'label' => get_config('format_flexible', 'defaultsectiontitlefontsize'), 'element_type' => 'hidden');
                $courseformatoptionsedit['sectiontitlealignment'] = array(
                    'label' => get_config('format_flexible', 'defaultsectiontitlealignment'), 'element_type' => 'hidden');
                $courseformatoptionsedit['boxinsidetextcolour'] = array(
                    'label' => $defaults['defaultboxinsidetextcolour'], 'element_type' => 'hidden');
                $courseformatoptionsedit['boxinsidetextbackgroundcolour'] = array(
                    'label' => $defaults['defaultboxinsidetextbackgroundcolour'], 'element_type' => 'hidden');
            }

            $courseformatoptionsedit['newactivity'] = array(
                'label' => new lang_string('setnewactivity', 'format_flexible'),
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        1 => new lang_string('no'), // No.
                        2 => new lang_string('yes') // Yes.
                    )
                ),
                'help' => 'setnewactivity',
                'help_component' => 'format_flexible'
            );

            $courseformatoptionsedit['fitsectioncontainertowindow'] = array(
                'label' => new lang_string('setfitsectioncontainertowindow', 'format_flexible'),
                'help' => 'setfitsectioncontainertowindow',
                'help_component' => 'format_flexible',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        1 => new lang_string('no'), // No.
                        2 => new lang_string('yes') // Yes.
                    )
                )
            );

            $courseformatoptionsedit['greyouthidden'] = array(
                'label' => new lang_string('greyouthidden', 'format_flexible'),
                'help' => 'greyouthidden',
                'help_component' => 'format_flexible',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        1 => new lang_string('no'), // No.
                        2 => new lang_string('yes') // Yes.
                    )
                )
            );

            $courseformatoptionsedit['showsectioncompletiongraphic'] = array(
                'label' => new lang_string('setshowsectioncompletiongraphic', 'format_flexible'),
                'help' => 'setshowsectioncompletiongraphic',
                'help_component' => 'format_flexible',
                'element_type' => 'select',
                'element_attributes' => array(
                    array(
                        1 => new lang_string('no'), // No.
                        2 => new lang_string('yes') // Yes.
                    )
                )
            );

            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Definitions of the additional options that this course format uses for the section.
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function section_format_options($foreditform = false) {
        static $sectionformatoptions = false;

        if ($sectionformatoptions === false) {
            $sectionformatoptions = array(
                'sectionrenderas' => array(
                    'default' => 1, // Tiled.
                    'type' => PARAM_INT,
                ),
                'showimageonsectionpage' => array(
                    'default' => 1, // No.
                    'type' => PARAM_INT,
                ),
                'setsectionallowsummarymarkup' => array(
                    'default' => 1, // No.
                    'type' => PARAM_INT,
                )
            );
        }
        if ($foreditform && !isset($sectionformatoptions['sectionrenderas']['label'])) {
            $sectionformatoptionsedit = array(
                'sectionrenderas' => array(
                    'label' => new lang_string('setsectionrenderas', 'format_flexible'),
                    'help' => 'setsectionrenderas',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('tile', 'format_flexible'),
                            4 => new lang_string('expanded', 'format_flexible'),
                            2 => new lang_string('expandablecollapsed', 'format_flexible'),
                            3 => new lang_string('expandableexpanded', 'format_flexible')
                        )
                    )
                ),
                'showimageonsectionpage' => array(
                    'label' => new lang_string('setshowimageonsectionpage', 'format_flexible'),
                    'help' => 'setshowimageonsectionpage',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('no'),
                            2 => new lang_string('yes')
                        )
                    )
                ),
                'setsectionallowsummarymarkup' => array(
                    'label' => new lang_string('setsectionallowsummarymarkup', 'format_flexible'),
                    'help' => 'setsectionallowsummarymarkup',
                    'help_component' => 'format_flexible',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => new lang_string('no'),
                            2 => new lang_string('yes')
                        )
                    )
                )
            );
            $sectionformatoptions = array_merge_recursive($sectionformatoptions, $sectionformatoptionsedit);
        }
        return $sectionformatoptions;
    }

    protected function get_course_format_colour_defaults() {
        $defaults = array();

        $defaults['defaultbordercolour'] = get_config('format_flexible', 'defaultbordercolour');
        if ($defaults['defaultbordercolour'][0] == '#') {
            $defaults['defaultbordercolour'] = substr($defaults['defaultbordercolour'], 1);
        }
        $defaults['defaultimagecontainerbackgroundcolour'] = get_config('format_flexible', 'defaultimagecontainerbackgroundcolour');
        if ($defaults['defaultimagecontainerbackgroundcolour'][0] == '#') {
            $defaults['defaultimagecontainerbackgroundcolour'] = substr($defaults['defaultimagecontainerbackgroundcolour'], 1);
        }
        $defaults['defaultcurrentselectedsectioncolour'] = get_config('format_flexible', 'defaultcurrentselectedsectioncolour');
        if ($defaults['defaultcurrentselectedsectioncolour'][0] == '#') {
            $defaults['defaultcurrentselectedsectioncolour'] = substr($defaults['defaultcurrentselectedsectioncolour'], 1);
        }
        $defaults['defaultboxinsidetextcolour'] = get_config('format_flexible', 'defaultboxinsidetextcolour');
        if ($defaults['defaultboxinsidetextcolour'][0] == '#') {
            $defaults['defaultboxinsidetextcolour'] = substr($defaults['defaultboxinsidetextcolour'], 1);
        }
        $defaults['defaultboxinsidetextbackgroundcolour'] = get_config('format_flexible', 'defaultboxinsidetextbackgroundcolour');
        if ($defaults['defaultboxinsidetextbackgroundcolour'][0] == '#') {
            $defaults['defaultboxinsidetextbackgroundcolour'] = substr($defaults['defaultboxinsidetextbackgroundcolour'], 1);
        }

        return $defaults;
    }

    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@link course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $CFG, $OUTPUT, $PAGE, $USER;
        MoodleQuickForm::registerElementType('ffcolourpopup', "$CFG->dirroot/course/format/flexible/js/ff_colourpopup.php",
            'MoodleQuickForm_ffcolourpopup');

        $elements = parent::create_edit_form_elements($mform, $forsection);

        /* Increase the number of sections combo box values if the user has increased the number of sections
           using the icon on the course page beyond course 'maxsections' or course 'maxsections' has been
           reduced below the number of sections already set for the course on the site administration course
           defaults page.  This is so that the number of sections is not reduced leaving unintended orphaned
           activities / resources. */
        if (!$forsection) {
            $maxsections = get_config('moodlecourse', 'maxsections');
            $numsections = $mform->getElementValue('numsections');
            $numsections = $numsections[0];
            if ($numsections > $maxsections) {
                $element = $mform->getElement('numsections');
                for ($i = $maxsections + 1; $i <= $numsections; $i++) {
                    $element->addOption("$i", $i);
                }
            }
        }
        $context = $this->get_context();

        $changeimagecontaineralignment = has_capability('format/flexible:changeimagecontaineralignment', $context);
        $changeimagecontainercolumns = has_capability('format/flexible:changeimagecontainercolumns', $context);
        $changeimagecontainersize = has_capability('format/flexible:changeimagecontainersize', $context);
        $changeimageresizemethod = has_capability('format/flexible:changeimageresizemethod', $context);
        $changeimagecontainerstyle = has_capability('format/flexible:changeimagecontainerstyle', $context);
        $changesectiontitleoptions = has_capability('format/flexible:changesectiontitleoptions', $context);
        $resetall = is_siteadmin($USER); // Site admins only.

        $elements[] = $mform->addElement('header', 'ffreset', get_string('ffreset', 'format_flexible'));
        $mform->addHelpButton('ffreset', 'ffreset', 'format_flexible', '', true);

        $bs232 = self::bstwothreetwo();

        $resetelements = array();

        if (($changeimagecontaineralignment) ||
            ($changeimagecontainercolumns) ||
            ($changeimagecontainersize) ||
            ($changeimageresizemethod) ||
            ($changeimagecontainerstyle) ||
            ($changesectiontitleoptions)) {

            if ($changeimagecontaineralignment) {
                if ($bs232) {
                    $checkboxname = get_string('resetimagecontaineralignment', 'format_flexible').
                        $OUTPUT->help_icon('resetimagecontaineralignment', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontaineralignment', '', $checkboxname);
                } else {
                    $checkboxname = get_string('resetimagecontaineralignment', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontaineralignment', '', $checkboxname);
                    $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetimagecontaineralignment', 'format_flexible'));
                }
            }

            if ($changeimagecontainercolumns) {
                if ($bs232) {
                    $checkboxname = get_string('resetimagecontainercolumns', 'format_flexible').
                        $OUTPUT->help_icon('resetimagecontainercolumns', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontainercolumns', '', $checkboxname);
                } else {
                    $checkboxname = get_string('resetimagecontainercolumns', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontainercolumns', '', $checkboxname);
                    $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetimagecontainercolumns', 'format_flexible'));
                }
            }

            if ($changeimagecontainersize) {
                if ($bs232) {
                    $checkboxname = get_string('resetimagecontainersize', 'format_flexible').
                        $OUTPUT->help_icon('resetimagecontainersize', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontainersize', '', $checkboxname);
                } else {
                    $checkboxname = get_string('resetimagecontainersize', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontainersize', '', $checkboxname);
                    $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetimagecontainersize', 'format_flexible'));
                }
            }

            if ($changeimageresizemethod) {
                if ($bs232) {
                    $checkboxname = get_string('resetimageresizemethod', 'format_flexible').
                        $OUTPUT->help_icon('resetimageresizemethod', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimageresizemethod', '', $checkboxname);
                } else {
                    $checkboxname = get_string('resetimageresizemethod', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimageresizemethod', '', $checkboxname);
                    $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetimageresizemethod', 'format_flexible'));
                }
            }

            if ($changeimagecontainerstyle) {
                if ($bs232) {
                    $checkboxname = get_string('resetimagecontainerstyle', 'format_flexible').
                        $OUTPUT->help_icon('resetimagecontainerstyle', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontainerstyle', '', $checkboxname);
                } else {
                    $checkboxname = get_string('resetimagecontainerstyle', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetimagecontainerstyle', '', $checkboxname);
                    $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetimagecontainerstyle', 'format_flexible'));
                }
            }

            if ($changesectiontitleoptions) {
                if ($bs232) {
                    $checkboxname = get_string('resetsectiontitleoptions', 'format_flexible').
                        $OUTPUT->help_icon('resetsectiontitleoptions', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetsectiontitleoptions', '', $checkboxname);
                } else {
                    $checkboxname = get_string('resetsectiontitleoptions', 'format_flexible');
                    $resetelements[] = & $mform->createElement('checkbox', 'resetsectiontitleoptions', '', $checkboxname);
                    $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetsectiontitleoptions', 'format_flexible'));
                }
            }
        }

        if ($bs232) {
            $checkboxname = get_string('resetnewactivity', 'format_flexible').
                $OUTPUT->help_icon('resetnewactivity', 'format_flexible');
            $resetelements[] = & $mform->createElement('checkbox', 'resetnewactivity', '', $checkboxname);

            $checkboxname = get_string('resetfitpopup', 'format_flexible').
                $OUTPUT->help_icon('resetfitpopup', 'format_flexible');
            $resetelements[] = & $mform->createElement('checkbox', 'resepopup', '', $checkboxname);
        } else {
            $checkboxname = get_string('resetnewactivity', 'format_flexible');
            $resetelements[] = & $mform->createElement('checkbox', 'resetnewactivity', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetnewactivity', 'format_flexible'));

            $checkboxname = get_string('resetfitpopup', 'format_flexible');
            $resetelements[] = & $mform->createElement('checkbox', 'resetfitpopup', '', $checkboxname);
            $resetelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetfitpopup', 'format_flexible'));
        }
        $elements[] = $mform->addGroup($resetelements, 'resetgroup', get_string('resetgrp', 'format_flexible'), null, false);

        if ($resetall) {
            $resetallelements = array();

            if ($bs232) {
                $checkboxname = get_string('resetallimagecontaineralignment', 'format_flexible').
                    $OUTPUT->help_icon('resetallimagecontaineralignment', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontaineralignment', '', $checkboxname);

                $checkboxname = get_string('resetallimagecontainercolumns', 'format_flexible').
                    $OUTPUT->help_icon('resetallimagecontainercolumns', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontainercolumns', '', $checkboxname);

                $checkboxname = get_string('resetallimagecontainersize', 'format_flexible').
                    $OUTPUT->help_icon('resetallimagecontainersize', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontainersize', '', $checkboxname);

                $checkboxname = get_string('resetallimageresizemethod', 'format_flexible').
                    $OUTPUT->help_icon('resetallimageresizemethod', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimageresizemethod', '', $checkboxname);

                $checkboxname = get_string('resetallimagecontainerstyle', 'format_flexible').
                    $OUTPUT->help_icon('resetallimagecontainerstyle', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontainerstyle', '', $checkboxname);

                $checkboxname = get_string('resetallsectiontitleoptions', 'format_flexible').
                    $OUTPUT->help_icon('resetallsectiontitleoptions', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallsectiontitleoptions', '', $checkboxname);

                $checkboxname = get_string('resetallnewactivity', 'format_flexible').
                    $OUTPUT->help_icon('resetallnewactivity', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallnewactivity', '', $checkboxname);

                $checkboxname = get_string('resetallfitpopup', 'format_flexible').
                    $OUTPUT->help_icon('resetallfitpopup', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallfitpopup', '', $checkboxname);
            } else {
                $checkboxname = get_string('resetallimagecontaineralignment', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontaineralignment', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallimagecontaineralignment', 'format_flexible'));

                $checkboxname = get_string('resetallimagecontainercolumns', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontainercolumns', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallimagecontainercolumns', 'format_flexible'));

                $checkboxname = get_string('resetallimagecontainersize', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontainersize', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallimagecontainersize', 'format_flexible'));

                $checkboxname = get_string('resetallimageresizemethod', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimageresizemethod', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallimageresizemethod', 'format_flexible'));

                $checkboxname = get_string('resetallimagecontainerstyle', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallimagecontainerstyle', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallimagecontainerstyle', 'format_flexible'));

                $checkboxname = get_string('resetallsectiontitleoptions', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallsectiontitleoptions', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallsectiontitleoptions', 'format_flexible'));

                $checkboxname = get_string('resetallnewactivity', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallnewactivity', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallnewactivity', 'format_flexible'));

                $checkboxname = get_string('resetallfitpopup', 'format_flexible');
                $resetallelements[] = & $mform->createElement('checkbox', 'resetallfitpopup', '', $checkboxname);
                $resetallelements[] = & $mform->createElement('html', $OUTPUT->help_icon('resetallfitpopup', 'format_flexible'));
            }

            $elements[] = $mform->addGroup($resetallelements, 'resetallgroup', get_string('resetallgrp', 'format_flexible'), null,
                false);
        }

        return $elements;
    }

    /**
     * Override if you need to perform some extra validation of the format options
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param array $errors errors already discovered in edit form validation
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     *         Do not repeat errors from $errors param here
     */
    public function edit_form_validation($data, $files, $errors) {
        $retr = array();

        if (self::validate_colour($data['bordercolour']) === false) {
            $retr['bordercolour'] = get_string('colourrule', 'format_flexible');
        }
        if (self::validate_colour($data['imagecontainerbackgroundcolour']) === false) {
            $retr['imagecontainerbackgroundcolour'] = get_string('colourrule', 'format_flexible');
        }
        if (self::validate_colour($data['currentselectedsectioncolour']) === false) {
            $retr['currentselectedsectioncolour'] = get_string('colourrule', 'format_flexible');
        }
        if ($data['sectiontitleflexiblelengthmaxoption'] < 0) {
            $retr['sectiontitleflexiblelengthmaxoption'] = get_string('sectiontitleflexiblelengthmaxoptionrule', 'format_flexible');
        }
        if (self::validate_colour($data['boxinsidetextcolour']) === false) {
            $retr['boxinsidetextcolour'] = get_string('colourrule', 'format_flexible');
        }
        if (self::validate_colour($data['boxinsidetextbackgroundcolour']) === false) {
            $retr['boxinsidetextbackgroundcolour'] = get_string('colourrule', 'format_flexible');
        }
        if ($this->validate_opacity($data['insideboxopacity']) === false) {
            $retr['insideboxopacity'] = get_string('opacityrule', 'format_flexible');
        }
        if ($this->validate_section_title_font_size($data['sectiontitlefontsize']) === false) {
            $retr['sectiontitlefontsize'] = get_string('sectiontitlefontsizerule', 'format_flexible');
        }

        return $retr;
    }

    /**
     * Validates the colour that was entered by the user.
     * Borrowed from 'admin_setting_configcolourpicker' in '/lib/adminlib.php'.
     *
     * I'm not completely happy with this solution as would rather embed in the colour
     * picker code in the form, however I find this area rather fraut and I hear that
     * Dan Poltawski (via MDL-42270) will be re-writing the forms lib so hopefully more
     * developer friendly.
     *
     * Note: Colour names removed, but might consider putting them back in if asked, but
     *       at the moment that would require quite a few changes and coping with existing
     *       settings.  Either convert the names to hex or allow them as valid values and
     *       fix the colour picker code and the CSS code in 'format.php' for the setting.
     *
     * Colour name to hex on: http://www.w3schools.com/cssref/css_colornames.asp.
     *
     * @param string $data the colour string to validate.
     * @return true|false
     */
    public static function validate_colour($data) {
        if (preg_match('/^#?([[:xdigit:]]{3}){1,2}$/', $data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates the opacity that was entered by the user.
     *
     * @param string $data the opacity string to validate.
     * @return true|false
     */
    private function validate_opacity($data) {
        if (array_key_exists($data, self::$opacities)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates the font size that was entered by the user.
     *
     * @param string $data the font size integer to validate.
     * @return true|false
     */
    private function validate_section_title_font_size($data) {
        if (array_key_exists($data, self::$sectiontitlefontsizes)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Updates format options for a course
     *
     * In case if course format was changed to 'flexible', we try to copy options
     * 'numsections' and 'hiddensections' from the previous format.
     * The layout and colour defaults will come from 'course_format_options'.
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data.
     * @param stdClass $oldcourse if this function is called from {@link update_course()}
     *     this object contains information about the course before update.
     * @return bool whether there were any changes to the options values.
     */
    public function update_course_format_options($data, $oldcourse = null) {
        global $DB; // MDL-37976.
        /*
         * Notes: Using 'unset' to really ensure that the reset form elements never get into the database.
         *        This has to be done here so that the reset occurs after we have done updates such that the
         *        reset itself is not seen as an update.
         */
        $resetimagecontaineralignment = false;
        $resetimagecontainercolumns = false;
        $resetimagecontainersize = false;
        $resetimageresizemethod = false;
        $resetimagecontainerstyle = false;
        $resetsectiontitleoptions = false;
        $resetnewactivity = false;
        $resetfitpopup = false;
        $resetallimagecontaineralignment = false;
        $resetallimagecontainercolumns = false;
        $resetallimagecontainersize = false;
        $resetallimageresizemethod = false;
        $resetallimagecontainerstyle = false;
        $resetallsectiontitleoptions = false;
        $resetallnewactivity = false;
        $resetallfitpopup = false;
        $resetgreyouthidden = false;
        if (isset($data->resetimagecontaineralignment) == true) {
            $resetimagecontaineralignment = true;
            unset($data->resetimagecontaineralignment);
        }
        if (isset($data->resetimagecontainercolumns) == true) {
            $resetimagecontainercolumns = true;
            unset($data->resetimagecontainercolumns);
        }
        if (isset($data->resetimagecontainersize) == true) {
            $resetimagecontainersize = true;
            unset($data->resetimagecontainersize);
        }
        if (isset($data->resetimageresizemethod) == true) {
            $resetimageresizemethod = true;
            unset($data->resetimageresizemethod);
        }
        if (isset($data->resetimagecontainerstyle) == true) {
            $resetimagecontainerstyle = true;
            unset($data->resetimagecontainerstyle);
        }
        if (isset($data->resetsectiontitleoptions) == true) {
            $resetsectiontitleoptions = true;
            unset($data->resetsectiontitleoptions);
        }
        if (isset($data->resetnewactivity) == true) {
            $resetnewactivity = true;
            unset($data->resetnewactivity);
        }
        if (isset($data->resetfitpopup) == true) {
            $resetfitpopup = true;
            unset($data->resetfitpopup);
        }
        if (isset($data->resetallimagecontaineralignment) == true) {
            $resetallimagecontaineralignment = true;
            unset($data->resetallimagecontaineralignment);
        }
        if (isset($data->resetallimagecontainercolumns) == true) {
            $resetallimagecontainercolumns = true;
            unset($data->resetallimagecontainercolumns);
        }
        if (isset($data->resetallimagecontainersize) == true) {
            $resetallimagecontainersize = true;
            unset($data->resetallimagecontainersize);
        }
        if (isset($data->resetallimageresizemethod) == true) {
            $resetallimageresizemethod = true;
            unset($data->resetallimageresizemethod);
        }
        if (isset($data->resetallimagecontainerstyle) == true) {
            $resetallimagecontainerstyle = true;
            unset($data->resetallimagecontainerstyle);
        }
        if (isset($data->resetallsectiontitleoptions) == true) {
            $resetallsectiontitleoptions = true;
            unset($data->resetallsectiontitleoptions);
        }
        if (isset($data->resetallnewactivity) == true) {
            $resetallnewactivity = true;
            unset($data->resetallnewactivity);
        }
        if (isset($data->resetallfitpopup) == true) {
            $resetfitpopup = true;
            unset($data->resetallfitpopup);
        }
        if (isset($data->resetgreyouthidden) == true) {
            $resetgreyouthidden = true;
            unset($data->resetgreyouthidden);
        }

        $settings = $this->get_settings();
        $changedisplayedimages = false;
        if (isset($data->imagecontainerratio)) {
            if (((!(($resetimagecontainersize) || ($resetallimagecontainersize))) &&
                     ($settings['imagecontainerratio'] != $data->imagecontainerratio)) ||
                    ((!(($resetimageresizemethod) || ($resetallimageresizemethod))) &&
                    ($settings['imageresizemethod'] != $data->imageresizemethod))) {
                /* Detect now and action later as 'setup_displayed_image' when called from 'update_displayed_images()' will need to
                   use the new values. */
                $changedisplayedimages = true;
            }
        }

        $data = (array) $data;
        if ($oldcourse !== null) {
            $oldcourse = (array) $oldcourse;
            $options = $this->course_format_options();

            foreach ($options as $key => $unused) {
                if (!array_key_exists($key, $data)) {
                    if (array_key_exists($key, $oldcourse)) {
                        $data[$key] = $oldcourse[$key];
                    } else if ($key === 'numsections') {
                        /* If previous format does not have the field 'numsections'
                           and $data['numsections'] is not set,
                           we fill it with the maximum section number from the DB. */
                        $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                            WHERE course = ?', array($this->courseid));
                        if ($maxsection) {
                            // If there are no sections, or just default 0-section, 'numsections' will be set to default.
                            $data['numsections'] = $maxsection;
                        }
                    }
                }
            }
        }
        $changes = $this->update_format_options($data);

        if ($changes && array_key_exists('numsections', $data)) {
            // If the numsections was decreased, try to completely delete the orphaned sections (unless they are not empty).
            $numsections = (int)$data['numsections'];
            $maxsection = $DB->get_field_sql('SELECT max(section) from {course_sections}
                        WHERE course = ?', array($this->courseid));
            for ($sectionnum = $maxsection; $sectionnum > $numsections; $sectionnum--) {
                if (!$this->delete_section($sectionnum, false)) {
                    break;
                }
            }
        }

        // Now we can change the displayed images if needed.
        if ($changedisplayedimages) {
            $this->settings = null; // Invalidate as changed.
            $settings = $this->get_settings();

            $this->update_displayed_images($this->courseid, $this, $settings, true);
        }

        // Now we can do the reset.
        if (($resetallimagecontaineralignment) ||
            ($resetallimagecontainercolumns) ||
            ($resetallimagecontainersize) ||
            ($resetallimageresizemethod) ||
            ($resetallimagecontainerstyle) ||
            ($resetallsectiontitleoptions) ||
            ($resetallnewactivity) ||
            ($resetallfitpopup)) {
            $this->reset_flexible_setting(0, $resetallimagecontaineralignment, $resetallimagecontainercolumns,
                $resetallimagecontainersize, $resetallimageresizemethod, $resetallimagecontainerstyle,
                $resetallsectiontitleoptions, $resetallnewactivity, $resetallfitpopup);
            $changes = true;
        } else if (
            ($resetimagecontaineralignment) ||
            ($resetimagecontainercolumns) ||
            ($resetimagecontainersize) ||
            ($resetimageresizemethod) ||
            ($resetimagecontainerstyle) ||
            ($resetsectiontitleoptions) ||
            ($resetnewactivity) ||
            ($resetfitpopup)) {
            $this->reset_flexible_setting($this->courseid, $resetimagecontaineralignment, $resetimagecontainercolumns,
                $resetimagecontainersize, $resetimageresizemethod, $resetimagecontainerstyle,
                $resetsectiontitleoptions, $resetnewactivity, $resetfitpopup);
            $changes = true;
        }

        return $changes;
    }

    /**
     * Deletes a section
     *
     * Do not call this function directly, instead call {@link course_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @param bool $forcedeleteifnotempty if set to false section will not be deleted if it has modules in it.
     * @return bool whether section was deleted
     */
    public function delete_section($section, $forcedeleteifnotempty = false) {
        if (!$this->uses_sections()) {
            // Not possible to delete section if sections are not used.
            return false;
        }
        if (!is_object($section)) {
            global $DB;
            $section = $DB->get_record('course_sections', array('course' => $this->get_courseid(), 'section' => $section),
                'id,section,sequence,summary');
        }
        if (!$section || !$section->section) {
            // Not possible to delete 0-section.
            return false;
        }

        if (!$forcedeleteifnotempty && (!empty($section->sequence) || !empty($section->summary))) {
            return false;
        }
        if (parent::delete_section($section, $forcedeleteifnotempty)) {
            $this->delete_image($section->id, $this->get_context()->id);
            return true;
        }
        return false;
    }

    /**
     * Whether this format allows to delete sections
     *
     * Do not call this function directly, instead use {@link course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section) {
        return true;
    }

    /**
     * Updates format options for a section
     *
     * Section id is expected in $data->id (or $data['id'])
     * If $data does not contain property with the option name, the option will not be updated
     *
     * @param stdClass|array $data return value from {@link moodleform::get_data()} or array with data
     * @return bool whether there were any changes to the options values
     */
    public function update_section_format_options($data) {
        $data = (array) $data;

        /* Resets the displayed image because changing the section name / details deletes the file.
           See CONTRIB-4784. */
        $sectionimage = $this->get_image($this->courseid, $data['id']);
        if ($sectionimage) {
            // Set up our table to get the displayed image back.  The 'auto repair' on page reload will do the rest.
            global $DB;
            $DB->set_field('format_flexible_icon', 'displayedimageindex', 0, array('sectionid' => $sectionimage->sectionid));
            // We know the file is normally deleted, but just in case...
            $contextid = $this->get_context()->id;
            $fs = get_file_storage();
            $flexibleimagepath = $this->get_image_path();
            $this->delete_displayed_image($contextid, $sectionimage, $flexibleimagepath, $fs);
        }

        return parent::update_section_format_options($data);
    }

    /**
     * Resets the format setting to the default.
     * @param int $courseid If not 0, then a specific course to reset.
     * @param int $imagecontaineralignmentreset If true, reset the alignment to the default in the settings for the format.
     * @param int $imagecontainercolumnsreset If true, reset the alignment to the default in the settings for the format.
     * @param int $imagecontainersizereset If true, reset the layout to the default in the settings for the format.
     * @param int $imageresizemethodreset If true, reset the image resize method to the default in the settings for the format.
     * @param int $imagecontainerstylereset If true, reset the colour to the default in the settings for the format.
     * @param int $sectiontitleoptionsreset If true, reset the section title options to the default in the settings for the format.
     * @param int $newactivityreset If true, reset the new activity to the default in the settings for the format.
     * @param int $fitpopupreset If true, reset the fit popup to the default in the settings for the format.
     */
    public function reset_flexible_setting($courseid, $imagecontaineralignmentreset, $imagecontainercolumnsreset,
        $imagecontainersizereset, $imageresizemethodreset, $imagecontainerstylereset, $sectiontitleoptionsreset,
        $newactivityreset, $fitpopupreset) {
        global $DB, $USER;

        $context = $this->get_context();

        if ($courseid == 0) {
            $records = $DB->get_records('course', array('format' => $this->format), '', 'id');
        } else {
            $records = $DB->get_records('course', array('id' => $courseid, 'format' => $this->format), '', 'id');
        }

        $resetallifall = ((is_siteadmin($USER)) || ($courseid != 0)); // Will be true if reset all capability or a single course.

        $updatedata = array();
        $updateimagecontaineralignment = false;
        $updateimagecontainercolumns = false;
        $updateimagecontainersize = false;
        $updateimageresizemethod = false;
        $updateimagecontainerstyle = false;
        $updatesectiontitleoptions = false;
        $updatenewactivity = false;
        $updatefitpopup = false;
        if ($imagecontaineralignmentreset && has_capability('format/flexible:changeimagecontaineralignment', $context) && $resetallifall) {
            $updatedata['imagecontaineralignment'] = get_config('format_flexible', 'defaultimagecontaineralignment');
            $updateimagecontaineralignment = true;
        }
        if ($imagecontainercolumnsreset && has_capability('format/flexible:changeimagecontainercolumns', $context) && $resetallifall) {
            $updatedata['numcolumns'] = get_config('format_flexible', 'defaultnumcolumns');
            $updateimagecontainercolumns = true;
        }
        if ($imagecontainersizereset && has_capability('format/flexible:changeimagecontainersize', $context) && $resetallifall) {
            $updatedata['imagecontainerratio'] = get_config('format_flexible', 'defaultimagecontainerratio');
            $updateimagecontainersize = true;
        }
        if ($imageresizemethodreset && has_capability('format/flexible:changeimageresizemethod', $context) && $resetallifall) {
            $updatedata['imageresizemethod'] = get_config('format_flexible', 'defaultimageresizemethod');
            $updateimageresizemethod = true;
        }
        if ($imagecontainerstylereset && has_capability('format/flexible:changeimagecontainerstyle', $context) && $resetallifall) {
            $updatedata['bordercolour'] = get_config('format_flexible', 'defaultbordercolour');
            $updatedata['borderwidth'] = get_config('format_flexible', 'defaultborderwidth');
            $updatedata['borderradius'] = get_config('format_flexible', 'defaultborderradius');
            $updatedata['imagecontainerbackgroundcolour'] = get_config('format_flexible', 'defaultimagecontainerbackgroundcolour');
            $updatedata['currentselectedsectioncolour'] = get_config('format_flexible', 'defaultcurrentselectedsectioncolour');
            $updateimagecontainerstyle = true;
        }
        if ($sectiontitleoptionsreset && has_capability('format/flexible:changesectiontitleoptions', $context) && $resetallifall) {
            $updatedata['hidesectiontitle'] = get_config('format_flexible', 'defaulthidesectiontitle');
            $updatedata['sectiontitleflexiblelengthmaxoption'] = get_config('format_flexible', 'defaultsectiontitleflexiblelengthmaxoption');
            $updatedata['sectiontitleboxposition'] = get_config('format_flexible', 'defaultsectiontitleboxposition');
            $updatedata['sectiontitleboxinsideposition'] = get_config('format_flexible', 'defaultsectiontitleboxinsideposition');
            $updatedata['sectiontitleboxheight'] = get_config('format_flexible', 'defaultsectiontitleboxheight');
            $updatedata['insideboxopacity'] = get_config('format_flexible', 'defaultinsideboxopacity');
            $updatedata['sectiontitlefontsize'] = get_config('format_flexible', 'defaultsectiontitlefontsize');
            $updatedata['sectiontitlealignment'] = get_config('format_flexible', 'defaultsectiontitlealignment');
            $updatedata['boxinsidetextcolour'] = get_config('format_flexible', 'defaultboxinsidetextcolour');
            $updatedata['boxinsidetextbackgroundcolour'] = get_config('format_flexible', 'defaultboxinsidetextbackgroundcolour');
            $updatesectiontitleoptions = true;
        }
        if ($newactivityreset && $resetallifall) {
            $updatedata['newactivity'] = get_config('format_flexible', 'defaultnewactivity');
            $updatenewactivity = true;
        }
        if ($fitpopupreset && $resetallifall) {
            $updatedata['fitsectioncontainertowindow'] = get_config('format_flexible', 'defaultfitsectioncontainertowindow');
            $updatefitpopup = true;
        }

        foreach ($records as $record) {
            if (($updateimagecontaineralignment) ||
                ($updateimagecontainercolumns) ||
                ($updateimagecontainersize) ||
                ($updateimageresizemethod) ||
                ($updateimagecontainerstyle) ||
                ($updatesectiontitleoptions) ||
                ($updatenewactivity) ||
                ($updatefitpopup)) {
                $ourcourseid = $this->courseid;
                $this->courseid = $record->id;
                if (($updateimagecontainersize) || ($updateimageresizemethod)) {
                    $courseformat = null;
                    if ($ourcourseid !== $this->courseid) {
                        $courseformat = course_get_format($this->courseid);
                        $currentsettings = $courseformat->get_settings();
                    } else {
                        $currentsettings = $this->get_settings();
                        $courseformat = $this;
                    }

                    if (($updateimagecontainersize) &&
                            ($currentsettings['imagecontainerratio'] != $updatedata['imagecontainerratio'])) {
                        $performimagecontainersize = true; // Variable $updatedata will be correct.
                    } else {
                        // If image resize method needs to operate so use current settings.
                        $updatedata['imagecontainerratio'] = $currentsettings['imagecontainerratio'];
                        $performimagecontainersize = false;
                    }

                    if (($updateimageresizemethod) &&
                            ($currentsettings['imageresizemethod'] != $updatedata['imageresizemethod'])) {
                        $performimageresizemethod = true; // Variable $updatedata will be correct.
                    } else {
                        // If image container size needs to operate so use current setting.
                        $updatedata['imageresizemethod'] = $currentsettings['imageresizemethod'];
                        $performimageresizemethod = false;
                    }

                    if (($performimagecontainersize) || ($performimageresizemethod)) {
                        // No need to get the settings as parsing the updated ones, but do need to invalidate them.
                        $courseformat->settings = null;
                        $courseformat->update_displayed_images($record->id, $courseformat, $updatedata, false);
                    }
                }
                $this->update_format_options($updatedata);
                $this->courseid = $ourcourseid;
            }
        }
    }

    // flexible specific methods...
    /**
     * Returns a moodle_url to a flexible format file.
     * @param string $url The URL to append.
     * @param array $params URL parameters.
     * @return moodle_url The created URL.
     */
    public function flexible_moodle_url($url, array $params = null) {
        return new moodle_url('/course/format/flexible/' . $url, $params);
    }

    /**
     * Gets the flexible image entries for the given course.
     * @param int $courseid The course id to use.
     * @returns bool|array The records or false if the course id is 0 or the request failed.
     */
    public function get_images($courseid) {
        global $DB;

        if (!$courseid) {
            return false;
        }

        if (!$sectionimagecontainers = $DB->get_records('format_flexible_icon', array('courseid' => $courseid), '',
                'sectionid, image, displayedimageindex')) {
            $sectionimagecontainers = false;
        }
        return $sectionimagecontainers;
    }

    /**
     * Gets the flexible image entry for the given course and section.  If an entry cannot be found then one is created
     * and returned.  If the course id is set to the default then it is updated to the one supplied as the value
     * will be accurate.
     * @param int $courseid The course id to use.
     * @param int $sectionid The section id to use.
     * @returns bool|array The record or false if the course id is 0 or section id is 0 or the request failed.
     */
    public function get_image($courseid, $sectionid) {
        global $DB;

        if ((!$courseid) || (!$sectionid)) {
            return false;
        }

        if (!$sectionimage = $DB->get_record('format_flexible_icon', array('sectionid' => $sectionid))) {

            $newimagecontainer = new stdClass();
            $newimagecontainer->sectionid = $sectionid;
            $newimagecontainer->courseid = $courseid;
            $newimagecontainer->displayedimageindex = 0;

            if (!$newimagecontainer->id = $DB->insert_record('format_flexible_icon', $newimagecontainer, true)) {
                throw new moodle_exception('invalidrecordid', 'format_flexible', '',
                'Could not create image container.  flexible format database is not ready.' .
                '  An admin must visit the notifications section.');
            }
            $sectionimage = $newimagecontainer;
        } else if ($sectionimage->courseid == 1) { // 1 is the default and is the 'site' course so cannot be the flexible format.
            // Note: Using a double equals in the test and not a triple as the latter does not work for some reason.
            $DB->set_field('format_flexible_icon', 'courseid', $courseid, array('sectionid' => $sectionid));
            $sectionimage->courseid = $courseid;
        }
        return $sectionimage;
    }

    // CONTRIB-4099 methods:....
    /**
     * Calculates the image container properties so that it can be rendered correctly.
     * @param int $imagecontainerratio The ratio array index of the container, see: $imagecontainerratios.
     * @param int $borderwidth The width of the border.
     * @return array with the key => value of 'height' for the container and 'margin-top' and 'margin-left' for the new activity
     *               image.
     */
    public function calculate_image_container_properties($imagecontainerratio, $borderwidth) {

        if (($imagecontainerratio !== self::$currentratio) ||
            ($borderwidth !== self::$currentborderwidth)) {
            $height = $this->calculate_height(self::$imagecontainerwidth, $imagecontainerratio);
            // This is: margin-top = image holder height - ( image height - border width)).
            // This is: margin-left = (image holder width - image width) + border width.

            $result = array(
                'height' => $height,
                'margin-top' => $height - (42 - $borderwidth),
                'margin-left' => (self::$imagecontainerwidth - 95) + $borderwidth
            );

            // Store:...
            self::$currentratio = $imagecontainerratio;
            self::$currentborderwidth = $borderwidth;
            self::$currentheight = $result['height'];
            self::$activitymargintop = $result['margin-top'];
            self::$activitymarginleft = $result['margin-left'];
        } else {
            $result = array(
                'height' => self::$currentheight,
                'margin-top' => self::$activitymargintop,
                'margin-left' => self::$activitymarginleft
            );
        }

        return $result;
    }

    /**
     * Gets the image container properties given the settings.
     * @param array $settings Must have integer keys for 'imagecontainerwidth' and 'imagecontainerratio'.
     * @return array with the key => value of 'height' and 'width' for the container.
     */
    private function get_displayed_image_container_properties($settings) {
        return array('height' => $this->calculate_height(self::$imagecontainerwidth, $settings['imagecontainerratio']),
            'width' => self::$imagecontainerwidth);
    }

    /**
     * Calculates height given the width and ratio.
     * @param int $width.
     * @param int $ratio.
     * @return int Height.
     */
    private function calculate_height($width, $ratio) {
        $basewidth = $width;

        switch ($ratio) {
            // Ratios 1 => '3-2', 2 => '3-1', 3 => '3-3', 4 => '2-3', 5 => '1-3', 6 => '4-3', 7 => '3-4', 8 => '5-3'.
            case 1: // 3-2.
            case 2: // 3-1.
            case 3: // 3-3.
            case 7: // 3-4.
                $basewidth = $width / 3;
                break;
            case 4: // 2-3.
                $basewidth = $width / 2;
                break;
            case 5: // 1-3.
                $basewidth = $width;
                break;
            case 6: // 4-3.
                $basewidth = $width / 4;
                break;
            case 8: // 5-3.
                $basewidth = $width / 5;
                break;
        }

        $height = $basewidth;
        switch ($ratio) {
            // Ratios 1 => '3-2', 2 => '3-1', 3 => '3-3', 4 => '2-3', 5 => '1-3', 6 => '4-3', 7 => '3-4', 8 => '5-3'.
            case 2: // 3-1.
                $height = $basewidth;
                break;
            case 1: // 3-2.
                $height = $basewidth * 2;
                break;
            case 3: // 3-3.
            case 4: // 2-3.
            case 5: // 1-3.
            case 6: // 4-3.
            case 8: // 5-3.
                $height = $basewidth * 3;
                break;
            case 7: // 3-4.
                $height = $basewidth * 4;
                break;
        }

        return round($height);
    }

    public function create_original_image_record($contextid, $sectionid, $filename) {
        $created = time();
        $storedfilerecord = array(
            'contextid' => $contextid,
            'component' => 'course',
            'filearea' => 'section',
            'itemid' => $sectionid,
            'filepath' => '/',
            // CONTRIB-5001 - Avoid clashes with the same image in the section summary by using a different name.
            'filename' => 'foi_' . $filename, // foi = tla = flexible original image.
            'timecreated' => $created,
            'timemodified' => $created);

        return $storedfilerecord;
    }

    public function create_section_image($tempfile, $storedfilerecord, $sectionimage) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        $fs = get_file_storage();

        try {
            $convertsuccess = true;

            // Ensure the right quality setting...
            $mime = $tempfile->get_mimetype();

            $imageinfo = $tempfile->get_imageinfo();
            $imagemaxwidth = self::get_maximum_image_width();
            if ($imageinfo['width'] > $imagemaxwidth) { // Maximum width as defined in lib.php.
                // Recalculate height:...
                $ratio = $imagemaxwidth / $imageinfo['width'];
                $imageinfo['height'] = $imageinfo['height'] * $ratio; // Maintain image's aspect ratio.
                // Set width:...
                $imageinfo['width'] = $imagemaxwidth;
            }

            $storedfilerecord['mimetype'] = $mime;

            // Note: It appears that this works with transparent Gif's to, so simplifying.
            $tmproot = make_temp_directory('flexibleformaticon');
            $tmpfilepath = $tmproot . '/' . $tempfile->get_contenthash();
            $tempfile->copy_content_to($tmpfilepath);

            $data = generate_image_thumbnail($tmpfilepath, $imageinfo['width'], $imageinfo['height']);
            if (!empty($data)) {
                $fs->create_file_from_string($storedfilerecord, $data);
            } else {
                $convertsuccess = false;
            }
            unlink($tmpfilepath);

            $tempfile->delete();
            unset($tempfile);

            if ($convertsuccess == true) {
                $DB->set_field('format_flexible_icon', 'image', $storedfilerecord['filename'],
                    array('sectionid' => $storedfilerecord['itemid']));

                // Set up the displayed image:...
                $sectionimage->newimage = $storedfilerecord['filename'];
                $icbc = self::hex2rgb($this->get_settings()['imagecontainerbackgroundcolour']);
                $this->setup_displayed_image($sectionimage, $storedfilerecord['contextid'], $this->get_settings(), $icbc, $mime);
            } else {
                print_error('imagecannotbeused', 'format_flexible', $CFG->wwwroot . "/course/view.php?id=" . $this->courseid);
            }
        } catch (Exception $e) {
            if (isset($tempfile)) {
                $tempfile->delete();
                unset($tempfile);
            }
            print('Flexible Format Image Exception:...');
            debugging($e->getMessage());
        }
    }

    /**
     * Set up the displayed image.
     * @param array $sectionimage Section information from its row in the 'format_flexible_icon' table.
     * @param array $contextid The context id to which the image relates.
     * @param array $settings The course settings to apply.
     * @param array $icbc The 'imagecontainerbackgroundcolour' as an RGB array.
     * @param string $mime The mime type if already known.
     * @return array The updated $sectionimage data.
     */
    public function setup_displayed_image($sectionimage, $contextid, $settings, $icbc, $mime = null) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/repository/lib.php');
        require_once($CFG->libdir . '/gdlib.php');

        // Set up the displayed image:...
        $fs = get_file_storage();
        if ($imagecontainerpathfile = $fs->get_file($contextid, 'course', 'section', $sectionimage->sectionid, '/',
                $sectionimage->newimage)) {
            $flexibleimagepath = $this->get_image_path();
            $convertsuccess = true;
            if (!$mime) {
                $mime = $imagecontainerpathfile->get_mimetype();
            }

            $displayedimageinfo = $this->get_displayed_image_container_properties($settings);

            $tmproot = make_temp_directory('flexibleformatdisplayedimagecontainer');
            $tmpfilepath = $tmproot . '/' . $imagecontainerpathfile->get_contenthash();
            $imagecontainerpathfile->copy_content_to($tmpfilepath);

            if ($settings['imageresizemethod'] == 1) {
                $crop = false;
            } else {
                $crop = true;
            }
            $iswebp = (get_config('format_flexible', 'defaultdisplayedimagefiletype') == 2);
            if ($iswebp) { // WebP.
                $newmime = 'image/webp';
            } else {
                $newmime = $mime;
            }
            $data = self::generate_image($tmpfilepath, $displayedimageinfo['width'], $displayedimageinfo['height'], $crop, $icbc, $newmime);
            if (!empty($data)) {
                // Updated image.
                $sectionimage->displayedimageindex++;
                $created = time();
                $displayedimagefilerecord = array(
                    'contextid' => $contextid,
                    'component' => 'course',
                    'filearea' => 'section',
                    'itemid' => $sectionimage->sectionid,
                    'filepath' => $flexibleimagepath,
                    'filename' => $sectionimage->displayedimageindex . '_' . $sectionimage->newimage,
                    'timecreated' => $created,
                    'timemodified' => $created,
                    'mimetype' => $mime);

                $this->remove_existing_new_displayed_image($displayedimagefilerecord, $fs);

                if ($iswebp) { // WebP.
                    // Displayed image is a webp image from the original, so change a few things.
                    $displayedimagefilerecord['filename'] = $sectionimage->displayedimageindex . '_' . $sectionimage->newimage.'.webp';
                    $displayedimagefilerecord['mimetype'] = $newmime;
                }
                $fs->create_file_from_string($displayedimagefilerecord, $data);
            } else {
                $convertsuccess = false;
            }
            unlink($tmpfilepath);

            if ($convertsuccess == true) {
                // Now safe to delete old file(s) if they exist.
                if ($oldfile = $fs->get_file($contextid, 'course', 'section', $sectionimage->sectionid, $flexibleimagepath,
                        ($sectionimage->displayedimageindex - 1) . '_' . $sectionimage->image)) {
                    $oldfile->delete();
                }
                if ($oldfile = $fs->get_file($contextid, 'course', 'section', $sectionimage->sectionid, $flexibleimagepath,
                        ($sectionimage->displayedimageindex - 1) . '_' . $sectionimage->image.'.webp')) {
                    $oldfile->delete();
                }
                $DB->set_field('format_flexible_icon', 'displayedimageindex', $sectionimage->displayedimageindex,
                    array('sectionid' => $sectionimage->sectionid));
            } else {
                print_error('cannotconvertuploadedimagetodisplayedimage', 'format_flexible',
                        $CFG->wwwroot . "/course/view.php?id=" . $this->courseid);
            }
        } else {
            $DB->set_field('format_flexible_icon', 'image', null, array('sectionid' => $sectionimage->sectionid));
        }

        return $sectionimage;  // So that the caller can know the new value of displayedimageindex.
    }

    protected function remove_existing_new_displayed_image($displayedimagefilerecord, $fs) {
        // Can happen if previously updating the section name did not delete the displayed image.
        if ($fs->file_exists($displayedimagefilerecord['contextid'], $displayedimagefilerecord['component'],
            $displayedimagefilerecord['filearea'], $displayedimagefilerecord['itemid'],
            $displayedimagefilerecord['filepath'], $displayedimagefilerecord['filename'])) {
            /* This can happen with previous CONTRIB-4099 versions where it was possible for the backup file to
               have the 'flexibleimage' files too.  Therefore without this, then 'create_file_from_string' below will
               baulk as the file already exists.   Unfortunately has to be here as the restore mechanism restores
               the flexible format data for the database and then the files.  And the Flexible code is called at the 'data'
               stage. */
            if ($oldfile = $fs->get_file($displayedimagefilerecord['contextid'], $displayedimagefilerecord['component'],
                $displayedimagefilerecord['filearea'], $displayedimagefilerecord['itemid'],
                $displayedimagefilerecord['filepath'], $displayedimagefilerecord['filename'])) {
                // Delete old file.
                $oldfile->delete();
            }
        }
        // WebP version.
        if ($fs->file_exists($displayedimagefilerecord['contextid'], $displayedimagefilerecord['component'],
            $displayedimagefilerecord['filearea'], $displayedimagefilerecord['itemid'],
            $displayedimagefilerecord['filepath'], $displayedimagefilerecord['filename'].'.webp')) {
            /* This can happen with previous CONTRIB-4099 versions where it was possible for the backup file to
               have the 'flexibleimage' files too.  Therefore without this, then 'create_file_from_string' below will
               baulk as the file already exists.   Unfortunately has to be here as the restore mechanism restores
               the flexible format data for the database and then the files.  And the Flexible code is called at the 'data'
               stage. */
            if ($oldfile = $fs->get_file($displayedimagefilerecord['contextid'], $displayedimagefilerecord['component'],
                $displayedimagefilerecord['filearea'], $displayedimagefilerecord['itemid'],
                $displayedimagefilerecord['filepath'], $displayedimagefilerecord['filename'].'.webp')) {
                // Delete old file.
                $oldfile->delete();
            }
        }
    }

    public function output_section_image($sectionname, $sectionimage, $contextid, $thissection,
        $flexibleimagepath, $output, $iswebp) {
        $content = '';
        if (is_object($sectionimage) && ($sectionimage->displayedimageindex > 0)) {
            $filename = $sectionimage->displayedimageindex . '_' . $sectionimage->image;
            if ($iswebp) {
                $filename .= '.webp';
            }
            $imgurl = moodle_url::make_pluginfile_url(
                $contextid, 'course', 'section', $thissection->id, $flexibleimagepath,
                $filename
            );
            $content = html_writer::empty_tag('img', array(
                'src' => $imgurl,
                'alt' => $sectionname,
                'role' => 'img',
                'aria-label' => $sectionname));
        } else {
            $geopattern = new \format_flexible\geopattern();
            // Id is unique and not the actual section number.
            try {
                $geopattern->setColor(self::sectioncolour($thissection->id, false));
            } catch (InvalidArgumentException $e) {
                $geopattern->setColor(self::sectioncolour($thissection->id, true));
            }
            $geopattern->patternbyid($thissection->id);
            $displayedimageinfo = $this->get_displayed_image_container_properties($this->get_settings());
            $imgurl = $geopattern->formaturi($displayedimageinfo);
            $content = html_writer::empty_tag('img', array(
                'src' => $imgurl,
                'alt' => $sectionname,
                'class' => 'info',
                'style' => 'background-image: '.$geopattern->toDataURL().';',
                'role' => 'img',
                'aria-label' => $sectionname));
        }
        return $content;
    }

    /**
     * Get the section colour.
     *
     * Here so that a theme can override it and state its own colours.
     *
     * @param int $num Number to use.
     * @param boolean $usedefaults Use the defaults instead of the setting, used for fallback.
     * @return string hex color code.
     */
    protected static function sectioncolour($num, $usedefaults) {
        if ($usedefaults) {
            $colours = self::get_default_generated_section_image_colours();
        } else {
            $colours = get_config('format_flexible', 'defaultgeneratedsectionimagecolours');
        }
        $basecolours = explode(',', $colours);
        $colour = trim($basecolours[$num % count($basecolours)]);

        return $colour;
    }

    public function delete_image($sectionid, $contextid) {
        $sectionimage = $this->get_image($this->courseid, $sectionid);
        if ($sectionimage) {
            global $DB;
            if (!empty($sectionimage->image)) {
                $fs = get_file_storage();

                // Delete the image.
                if ($file = $fs->get_file($contextid, 'course', 'section', $sectionid, '/', $sectionimage->image)) {
                    $file->delete();
                    $DB->set_field('format_flexible_icon', 'image', null, array('sectionid' => $sectionimage->sectionid));
                    // Delete the displayed image(s).
                    $flexibleimagepath = $this->get_image_path();
                    if ($file = $fs->get_file($contextid, 'course', 'section', $sectionid, $flexibleimagepath,
                            $sectionimage->displayedimageindex . '_' . $sectionimage->image)) {
                        $file->delete();
                    }
                    if ($file = $fs->get_file($contextid, 'course', 'section', $sectionid, $flexibleimagepath,
                            $sectionimage->displayedimageindex . '_' . $sectionimage->image.'.webp')) {
                        $file->delete();
                    }
                }
            }
            $DB->delete_records("format_flexible_icon", array('courseid' => $this->courseid,
                'sectionid' => $sectionimage->sectionid));
        }
    }

    public function delete_images() {
        $sectionimages = $this->get_images($this->courseid);

        if (is_array($sectionimages)) {
            global $DB;
            $context = $this->get_context();
            $fs = get_file_storage();
            $flexibleimagepath = $this->get_image_path();

            foreach ($sectionimages as $sectionimage) {
                // Delete the image if there is one.
                if (!empty($sectionimage->image)) {
                    if ($file = $fs->get_file($context->id, 'course', 'section', $sectionimage->sectionid, '/',
                            $sectionimage->image)) {
                        $file->delete();
                        // Delete the displayed image(s).
                        if ($file = $fs->get_file($context->id, 'course', 'section', $sectionimage->sectionid, $flexibleimagepath,
                                $sectionimage->displayedimageindex . '_' . $sectionimage->image)) {
                            $file->delete();
                        }
                        if ($file = $fs->get_file($context->id, 'course', 'section', $sectionimage->sectionid, $flexibleimagepath,
                                $sectionimage->displayedimageindex . '_' . $sectionimage->image.'.webp')) {
                            $file->delete();
                        }
                    }
                }
            }
            $DB->delete_records("format_flexible_icon", array('courseid' => $this->courseid));
        }
    }

    public function delete_displayed_images() {
        $sectionimages = $this->get_images($this->courseid);

        if (is_array($sectionimages)) {
            global $DB;
            $contextid = $this->get_context()->id;
            $fs = get_file_storage();
            $flexibleimagepath = $this->get_image_path();
            $t = $DB->start_delegated_transaction();

            foreach ($sectionimages as $sectionimage) {
                // Delete the displayed image.
                $this->delete_displayed_image($contextid, $sectionimage, $flexibleimagepath, $fs);
            }
            $t->allow_commit();
        }
    }

    protected function delete_displayed_image($contextid, $sectionimage, $flexibleimagepath, $fs) {
        global $DB;

        if ($file = $fs->get_file($contextid, 'course', 'section', $sectionimage->sectionid, $flexibleimagepath,
            $sectionimage->displayedimageindex . '_' . $sectionimage->image)) {
            $file->delete();
            $DB->set_field('format_flexible_icon', 'displayedimageindex', 0, array('sectionid' => $sectionimage->sectionid));
        }
        if ($file = $fs->get_file($contextid, 'course', 'section', $sectionimage->sectionid, $flexibleimagepath,
            $sectionimage->displayedimageindex . '_' . $sectionimage->image.'.webp')) {
            $file->delete();
            $DB->set_field('format_flexible_icon', 'displayedimageindex', 0, array('sectionid' => $sectionimage->sectionid));
        }
    }

    /**
     * Updates the displayed images because the settings have changed.
     * @param int $courseid The course id.
     * @param int $us The instance of format_flexible to use.
     * @param array $settings The settings to use.
     * @param int $ignorenorecords True we should not worry about no records existing, possibly down to a restore of a course.
     */
    private function update_displayed_images($courseid, $us, $settings, $ignorenorecords) {
        global $DB;

        $sectionimages = $us->get_images($courseid);
        if (is_array($sectionimages)) {
            $context = $this->get_context();

            $icbc = self::hex2rgb($this->get_settings()['imagecontainerbackgroundcolour']);
            $t = $DB->start_delegated_transaction();
            foreach ($sectionimages as $sectionimage) {
                if ($sectionimage->displayedimageindex > 0) {
                    $sectionimage->newimage = $sectionimage->image;
                    $sectionimage = $us->setup_displayed_image($sectionimage, $context->id, $settings, $icbc);
                }
            }
            $t->allow_commit();
        } else if (!$ignorenorecords) { // Only report error if it's ok not to have records.
            print_error('cannotgetimagesforcourse', 'format_flexible', '', null, "update_displayed_images - Course id: " . $courseid);
        }
    }

    /**
     * Generates a thumbnail for the given image
     *
     * If the GD library has at least version 2 and PNG support is available, the returned data
     * is the content of a transparent PNG file containing the thumbnail. Otherwise, the function
     * returns contents of a JPEG file with black background containing the thumbnail.
     *
     * @param string $filepath the full path to the original image file
     * @param int $requestedwidth the width of the requested image.
     * @param int $requestedheight the height of the requested image.
     * @param bool $crop false = scale, true = crop.
     * @param array $icbc The 'imagecontainerbackgroundcolour' as an RGB array.
     * @param string $mime The mime type.
     * @return string|bool false if a problem occurs or the image data.
     */
    private static function generate_image($filepath, $requestedwidth, $requestedheight, $crop, $icbc, $mime) {
        if (empty($filepath) or empty($requestedwidth) or empty($requestedheight)) {
            return false;
        }

        $imageinfo = getimagesize($filepath);

        if (empty($imageinfo)) {
            return false;
        }

        $originalwidth = $imageinfo[0];
        $originalheight = $imageinfo[1];

        if (empty($originalwidth) or empty($originalheight)) {
            return false;
        }

        $original = imagecreatefromstring(file_get_contents($filepath)); // Need to alter / check for webp support.

        switch ($mime) {
            case 'image/png':
                if (function_exists('imagepng')) {
                    $imagefnc = 'imagepng';
                    $filters = PNG_NO_FILTER;
                    $quality = 1;
                } else {
                    debugging('PNG\'s are not supported at this server, please fix the system configuration'.
                        ' to have the GD PHP extension installed.');
                    return false;
                }
                break;
            case 'image/jpeg':
                if (function_exists('imagejpeg')) {
                    $imagefnc = 'imagejpeg';
                    $filters = null;
                    $quality = 90;
                } else {
                    debugging('JPG\'s are not supported at this server, please fix the system configuration'.
                        ' to have the GD PHP extension installed.');
                    return false;
                }
                break;
            /* Moodle does not yet natively support webp as a mime type, but have here for us on the displayed image and
               not yet as a source image. */
            case 'image/webp':
                if (function_exists('imagewebp')) {
                    $imagefnc = 'imagewebp';
                    $filters = null;
                    $quality = 90;
                } else {
                    debugging('WEBP\'s are not supported at this server, please fix the system configuration'.
                        ' to have the GD PHP extension installed.');
                    return false;
                }
                break;
            case 'image/gif':
                if (function_exists('imagegif')) {
                    $imagefnc = 'imagegif';
                    $filters = null;
                    $quality = null;
                } else {
                    debugging('GIF\'s are not supported at this server, please fix the system configuration'.
                        ' to have the GD PHP extension installed.');
                    return false;
                }
                break;
            default:
                debugging('Mime type \''.$mime.'\' is not supported as an image format in the Flexible format.');
                return false;
        }

        $width = $requestedwidth;
        $height = $requestedheight;

        // Note: Code transformed from original 'resizeAndCrop' in 'imagelib.php' in the Moodle 1.9 version.
        if ($crop) {
            $ratio = $width / $height;
            $originalratio = $originalwidth / $originalheight;
            if ($originalratio < $ratio) {
                // Change the supplied height - 'resizeToWidth'.
                $ratio = $width / $originalwidth;
                $height = $originalheight * $ratio;
                $cropheight = true;
            } else {
                // Change the supplied width - 'resizeToHeight'.
                $ratio = $height / $originalheight;
                $width = $originalwidth * $ratio;
                $cropheight = false;
            }
        }

        if (function_exists('imagecreatetruecolor')) {
            $tempimage = imagecreatetruecolor($width, $height);
            if ($imagefnc === 'imagepng') {
                imagealphablending($tempimage, false);
                imagefill($tempimage, 0, 0, imagecolorallocatealpha($tempimage, 0, 0, 0, 127));
                imagesavealpha($tempimage, true);
            } else if (($imagefnc === 'imagejpeg') || ($imagefnc === 'imagewebp') || ($imagefnc === 'imagegif')) {
                imagealphablending($tempimage, false);
                imagefill($tempimage, 0, 0, imagecolorallocate($tempimage, $icbc['r'], $icbc['g'], $icbc['b']));
            }
        } else {
            $tempimage = imagecreate($width, $height);
        }

        if ($crop) {
            // First step, resize.
            imagecopybicubic($tempimage, $original, 0, 0, 0, 0, $width, $height, $originalwidth, $originalheight);
            imagedestroy($original);
            $original = $tempimage;

            // Second step, crop.
            if ($cropheight) {
                // Reset after change for resizeToWidth.
                $height = $requestedheight;
                // This is 'cropCenterHeight'.
                $width = imagesx($original);
                $srcoffset = (imagesy($original) / 2) - ($height / 2);
            } else {
                // Reset after change for resizeToHeight.
                $width = $requestedwidth;
                // This is 'cropCenterWidth'.
                $height = imagesy($original);
                $srcoffset = (imagesx($original) / 2) - ($width / 2);
            }

            if (function_exists('imagecreatetruecolor')) {
                $finalimage = imagecreatetruecolor($width, $height);
                if ($imagefnc === 'imagepng') {
                    imagealphablending($finalimage, false);
                    imagefill($finalimage, 0, 0, imagecolorallocatealpha($finalimage, 0, 0, 0, 127));
                    imagesavealpha($finalimage, true);
                } else if (($imagefnc === 'imagejpeg') || ($imagefnc === 'imagegif')) {
                    imagealphablending($tempimage, false);
                    imagefill($finalimage, 0, 0, imagecolorallocate($finalimage, $icbc['r'], $icbc['g'], $icbc['b']));
                }
            } else {
                $finalimage = imagecreate($width, $height);
            }

            if ($cropheight) {
                // This is 'cropCenterHeight'.
                imagecopybicubic($finalimage, $original, 0, 0, 0, $srcoffset, $width, $height, $width, $height);
            } else {
                // This is 'cropCenterWidth'.
                imagecopybicubic($finalimage, $original, 0, 0, $srcoffset, 0, $width, $height, $width, $height);
            }
        } else {
            $finalimage = $tempimage;
            $ratio = min($width / $originalwidth, $height / $originalheight);

            if ($ratio < 1) {
                $targetwidth = floor($originalwidth * $ratio);
                $targetheight = floor($originalheight * $ratio);
            } else {
                // Do not enlarge the original file if it is smaller than the requested thumbnail size.
                $targetwidth = $originalwidth;
                $targetheight = $originalheight;
            }

            $dstx = floor(($width - $targetwidth) / 2);
            $dsty = floor(($height - $targetheight) / 2);

            imagecopybicubic($finalimage, $original, $dstx, $dsty, 0, 0, $targetwidth, $targetheight, $originalwidth,
                $originalheight);
        }

        ob_start();
        if (!$imagefnc($finalimage, null, $quality, $filters)) {
            ob_end_clean();
            return false;
        }
        $data = ob_get_clean();

        imagedestroy($original);
        imagedestroy($finalimage);

        return $data;
    }

    /**
     * Returns the RGB for the given hex.
     *
     * @param string $hex
     * @return array
     */
    public static function hex2rgb($hex) {
        if (strlen($hex) == 3) {
            $r = substr($hex, 0, 1);
            $r .= $r;
            $g = substr($hex, 1, 1);
            $g .= $g;
            $b = substr($hex, 2, 1);
            $b .= $b;
        } else {
            $r = substr($hex, 0, 2);
            $g = substr($hex, 2, 2);
            $b = substr($hex, 4, 2);
        }
        return array('r' => hexdec($r), 'g' => hexdec($g), 'b' => hexdec($b));
    }

    public static function bstwothreetwo() {
        static $bs232 = null;
        if (is_null($bs232)) {
            global $PAGE;
            $bs232 = false;
            if (strcmp($PAGE->theme->name, 'clean') === 0) {
                $bs232 = true;
            } else if (!empty($PAGE->theme->parents)) {
                if (in_array('bootstrapbase', $PAGE->theme->parents) === true) {
                    $bs232 = true;
                }
            }
        }

        return $bs232;
    }

    /**
     * Returns a new instance of us so that specialised methods can be called.
     * @param int $courseid The course id of the course.
     * @return format_flexible object.
     */
    public static function get_instance($courseid) {
        return new format_flexible('flexible', $courseid);
    }

    /**
     * Prepares the templateable object to display section name.
     *
     * @param \section_info|\stdClass $section
     * @param bool $linkifneeded
     * @param bool $editable
     * @param null|lang_string|string $edithint
     * @param null|lang_string|string $editlabel
     * @return \core\output\inplace_editable
     */
    public function inplace_editable_render_section_name($section, $linkifneeded = true, $editable = null, $edithint = null,
            $editlabel = null) {
        if (empty($edithint)) {
            $edithint = new lang_string('editsectionname', 'format_flexible');
        }
        if (empty($editlabel)) {
            $title = $this->get_section_name($section);
            $editlabel = new lang_string('newsectionname', 'format_flexible', $title);
        }
        return parent::inplace_editable_render_section_name($section, $linkifneeded, $editable, $edithint, $editlabel);
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Returns whether this course format allows the activity to
     * have "triple visibility state" - visible always, hidden on course page but available, hidden.
     *
     * @param stdClass|cm_info $cm course module (may be null if we are displaying a form for adding a module)
     * @param stdClass|section_info $section section where this module is located or will be added to
     * @return bool
     */
    public function allow_stealth_module_visibility($cm, $section) {
        // Allow the third visibility state inside visible sections or in section 0, not allow in orphaned sections.
        return !$section->section || ($section->visible && $section->section <= $this->get_course()->numsections);
    }

    public function section_action($section, $action, $sr) {
        global $PAGE;

        if ($section->section && ($action === 'setmarker' || $action === 'removemarker')) {
            // Format 'flexible' allows to set and remove markers in addition to common section actions.
            require_capability('moodle/course:setcurrentsection', context_course::instance($this->courseid));
            course_set_marker($this->courseid, ($action === 'setmarker') ? $section->section : 0);
            return null;
        }

        // For show/hide actions call the parent method and return the new content for .section_availability element.
        $rv = parent::section_action($section, $action, $sr);
        $renderer = $PAGE->get_renderer('format_flexible');
        $rv['section_availability'] = $renderer->section_availability($this->get_section($section));
        return $rv;
    }

    private function get_context() {
        global $SITE;

        if ($SITE->id == $this->courseid) {
            // Use the context of the page which should be the course category.
            global $PAGE;
            return $PAGE->context;
        } else {
            return context_course::instance($this->courseid);
        }
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return \core\output\inplace_editable
 */
function format_flexible_inplace_editable($itemtype, $itemid, $newvalue) {
    global $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        global $DB;
        $section = $DB->get_record_sql(
                'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
                array($itemid, 'flexible'), MUST_EXIST);
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

/**
 * Indicates this format uses sections.
 *
 * @return bool Returns true
 */
function callback_flexible_uses_sections() {
    return true;
}

/**
 * Used to display the course structure for a course where format=flexible
 *
 * This is called automatically by {@link load_course()} if the current course
 * format = weeks.
 *
 * @param array $path An array of keys to the course node in the navigation
 * @param stdClass $modinfo The mod info object for the current course
 * @return bool Returns true
 */
function callback_flexible_load_content(&$navigation, $course, $coursenode) {
    return $navigation->load_generic_course_sections($course, $coursenode, 'flexible');
}

/**
 * The string that is used to describe a section of the course
 * e.g. Topic, Week...
 *
 * @return string
 */
function callback_flexible_definition() {
    return get_string('topic', 'format_flexible');
}

function flexible_format_update_displayed_images() {
    global $DB;

    if ($flexibleformatcourses = $DB->get_records('course', array('format' => 'flexible'), '', 'id')) {
        foreach ($flexibleformatcourses as $flexibleformatcourse) {
            $courseformat = course_get_format($flexibleformatcourse->id);
            $courseformat->delete_displayed_images();
        }
    }
}
