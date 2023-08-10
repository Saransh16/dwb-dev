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

require_once($CFG->dirroot . '/course/format/renderer.php');
require_once($CFG->dirroot . '/course/format/flexible/lib.php');

class format_flexible_renderer extends format_section_renderer_base {

    protected $courseformat; // Our course format object as defined in lib.php.
    protected $settings; // Settings array.
    protected $initialsection = -1;

    /**
     * Constructor method, calls the parent constructor - MDL-21097
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courseformat = course_get_format($page->course);
        $this->settings = $this->courseformat->get_settings();

        /* Since format_flexible_renderer::section_edit_controls() only displays the 'Set current section' control when editing
           mode is on we need to be sure that the link 'Turn editing mode on' is available for a user who does not have any
           other managing capability. */
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'ftopics', 'id' => 'ftopics'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('sectionname', 'format_flexible');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render($this->courseformat->inplace_editable_render_section_name($section));
    }
    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render($this->courseformat->inplace_editable_render_section_name($section, false));
    }

    /**
     * Generate next/previous section links for naviation
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the coruse which is being dsiplayed
     * @return array associative array with previous and next section link
     */
    protected function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        if ($this->settings['section0attop'] == 2) {
            $buffer = -1;
        } else {
            $buffer = 0;
        }
        while ($back > $buffer and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array();
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$back]);
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $coursenumsections = $this->courseformat->get_last_section_number();
        $forward = $sectionno + 1;
        while ($forward <= $coursenumsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array();
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {
        $o = '';
        $sectionmenu = array();
        $sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage');
        $modinfo = get_fast_modinfo($course);
        $section = 1;
        if ($this->settings['section0attop'] == 2) {
            $section = 0;
        } else {
            $section = 1;
        }
        $coursenumsections = $this->courseformat->get_last_section_number();
        while ($section <= $coursenumsections) {
            $thissection = $modinfo->get_section_info($section);
            $showsection = $thissection->uservisible or !$course->hiddensections;
            if (($showsection) && ($section != $displaysection) && ($url = course_get_url($course, $section))) {
                $sectionmenu[$url->out(false)] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto')));
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @param bool $onsectionpage true if being printed on a section page.
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {
        $o = parent::section_right_content($section, $course, $onsectionpage);

        if ($this->page->user_is_editing()) {
            $o .= $this->get_section_rendered_as_icon($section);
        }

        return $o;
    }

    /**
     * Generate the render as icon markup.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param bool $ignoretiled Return empty if tiled.
     * @param string $containerclasses CSS classes to add to the container tag.
     * @return string HTML to output.
     */
    protected function get_section_rendered_as_icon($section, $ignoretiled = false, $containerclasses = '') {
        $o = '';

        if (!(($section->sectionrenderas == 1) && ($ignoretiled))) {
            switch ($section->sectionrenderas) {
                case 1:
                    $title = get_string('tile', 'format_flexible');
                    $icon = 'th-large';
                    break;
                case 2:
                    $title = get_string('expandablecollapsed', 'format_flexible');
                    $icon = 'level-up';
                    break;
                case 3:
                    $title = get_string('expandableexpanded', 'format_flexible');
                    $icon = 'level-down';
                    break;
                case 4:
                    $title = get_string('expanded', 'format_flexible');
                    $icon = 'share';
                    break;
            }
            if (($section->sectionrenderas > 0) && ($section->sectionrenderas < 5)) {
                if (!empty($containerclasses)) {
                    $classes = array('class' => $containerclasses);
                } else {
                    $classes = null;
                }
                $o .= html_writer::start_tag('div', $classes).
                    $this->getfontawesomemarkup($icon, null, array('title' => $title)).
                    html_writer::tag('span', $title, array('class' => 'sr-only')).
                    html_writer::end_tag('div');
            }
        }

        return $o;
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included for when section 0 is in the flexible
     * and a single section page.
     *
     * @param stdClass $thissection The course_section entry from DB.
     * @param stdClass $course The course entry from DB.
     * @return string HTML to output.
     */
    protected function section_header_onsectionpage_topic0notattop($thissection, $course) {
        $o = '';
        $sectionstyle = '';

        if ($thissection->section != 0) {
            // Only in the non-general sections.
            if (!$thissection->visible) {
                $sectionstyle = ' hidden';
            } else if (course_get_format($course)->is_section_current($thissection)) {
                $sectionstyle = ' current';
            }
        }

        $o .= html_writer::start_tag('li', array('id' => 'section-'.$thissection->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role' => 'region',
            'aria-label' => get_section_name($course, $thissection)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $thissection), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($thissection, $course, true);
        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($thissection, $course, true);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        $sectionname = html_writer::tag('span', $this->section_title($thissection, $course));
        $o .= $this->output->heading($sectionname, 3, 'sectionname accesshide');

        $showimageonsectionpage = ($thissection->showimageonsectionpage == 2);
        if ($showimageonsectionpage) {
            $bs232 = \format_flexible::bstwothreetwo();
            if ($bs232) {
                $o .= html_writer::start_tag('div', array('class' => 'row-fluid'));
                $o .= html_writer::start_tag('div', array('class' => 'span8'));
            } else {
                $o .= html_writer::start_tag('div', array('class' => 'row'));
                $o .= html_writer::start_tag('div', array('class' => 'col-sm-8 order-2 order-sm-1'));
            }
        }

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        $o .= $this->format_summary_text($thissection);
        $o .= html_writer::end_tag('div');

        if ($showimageonsectionpage) {
            $o .= html_writer::end_tag('div');
            if ($bs232) {
                $o .= html_writer::start_tag('div', array('class' => 'span4'));
            } else {
                $o .= html_writer::start_tag('div', array('class' => 'col-sm-4 order-1 order-sm-2 mt-2'));
            }

            $o .= html_writer::start_tag('div', array('class' => 'flexiblesectionwrapper'));

            $sectionimagecontent = '';
            if (($this->settings['newactivity'] == 2) && (isset($sectionupdated[$thissection->id]))) {
                $sectionimagecontent .= html_writer::empty_tag('img', array(
                    'class' => 'new_activity',
                    'src' => $urlpicnewactivity));
            }

            $sectionimagecontent .= html_writer::start_tag('div', array('class' => 'image_holder'));

            $thesectionname = $this->courseformat->get_section_name($thissection);

            // Method get_image has 'repair' functionality for when there are issues with the data.
            $sectionimage = $this->courseformat->get_image($course->id, $thissection->id);
            $contextid = context_course::instance($course->id)->id;
            /* If the image is set then check that displayedimageindex is greater than 0 otherwise create the displayed image.
               This is a catch-all for existing courses. */
            if (isset($sectionimage->image) && ($sectionimage->displayedimageindex < 1)) {
                // Set up the displayed image:...
                $sectionimage->newimage = $sectionimage->image;
                $icbc = $this->courseformat->hex2rgb($this->settings['imagecontainerbackgroundcolour']);
                $sectionimage = $this->courseformat->setup_displayed_image($sectionimage, $contextid,
                    $this->settings, $icbc);
            }
            $flexibleimagepath = $this->courseformat->get_image_path();

            // Are we using WebP for the displayed image?
            $iswebp = (get_config('format_flexible', 'defaultdisplayedimagefiletype') == 2);

            $sectionimagecontent .= $this->courseformat->output_section_image(
                $thesectionname, $sectionimage, $contextid, $thissection, $flexibleimagepath, $this->output, $iswebp);

            $sectionimagecontent .= html_writer::end_tag('div');

            // Need an enclosing 'span' for IE.
            $o .= html_writer::tag('span', $sectionimagecontent);

            // Expandable collapsed area graphic.
            $scgraphic = $this->section_completion_graphic($thissection, $course);
            if (!empty($scgraphic)) {
                $scgattr = array(
                    'class' => 'scexpandablegraphic',
                    'id' => 'flexiblesectioncompletiongraphicmore-'.$thissection->section
                );
                $o .= html_writer::start_tag('div', $scgattr);
                $o .= $scgraphic;
                $o .= html_writer::end_tag('div');
            }
            $o .= html_writer::end_tag('div');

            $o .= html_writer::end_tag('div');
            $o .= html_writer::end_tag('div');
        }

        $o .= $this->section_availability($thissection);

        return $o;
    }

    /**
     * Output the html for a single section page.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist.
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection, $course->id);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', get_section_name($course, $displaysection));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        $ulclasses = 'ftopics';
        if ($thissection->showimageonsectionpage == 2) {
            $ulclasses .= ' flexibleicons';
        }
        echo html_writer::start_tag('ul', array('class' => $ulclasses, 'id' => 'ftopics'));

        echo $this->section_header_onsectionpage_topic0notattop($thissection, $course);
        if ($course->enablecompletion) {
            // Show completion help icon.
            $completioninfo = new completion_info($course);
            echo $completioninfo->display_help_icon();
        }

        echo $this->courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag('div', $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align'));
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param array $mods
     * @param array $modnames
     * @param array $modnamesused
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $USER;
        if (!empty($USER->profile['accessible'])) {
            return parent::print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused);
        }

        $coursecontext = context_course::instance($course->id);
        $editing = $this->page->user_is_editing();
        $hascapvishidsect = has_capability('moodle/course:viewhiddensections', $coursecontext);

        if ($editing) {
            $streditsummary = get_string('editsummary');
            $urlpicedit = $this->output->image_url('t/edit');
        } else {
            $urlpicedit = false;
            $streditsummary = '';
        }

        echo html_writer::start_tag('div', array('id' => 'flexiblemiddle-column'));
        echo $this->output->skip_link_target();

        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();

        // Start at 1 to skip the summary block or include the summary block if it's in the flexible display.
        if ($this->settings['section0attop'] == 1) {
            $this->make_block_topic0($course, $sections[0], $editing, $urlpicedit,
                $streditsummary, false);
        }
        echo html_writer::start_tag('div', array('id' => 'flexibleiconcontainer', 'role' => 'navigation',
            'aria-label' => get_string('flexibleimagecontainer', 'format_flexible')));

        $bs232 = \format_flexible::bstwothreetwo();
        if ($bs232) {
            $flexibleiconsclass = 'flexibleicons row-fluid';
        } else {
            $flexibleiconsclass = 'flexibleicons row';
        }
        if ($this->settings['sectiontitleboxposition'] == 1) {
            $flexibleiconsclass .= ' content_inside';
        }

        echo html_writer::start_tag('ul', array('class' => $flexibleiconsclass));
        // Print all of the image containers.
        $this->make_block_icon_topics($coursecontext->id, $sections, $course, $editing, $hascapvishidsect, $urlpicedit, $bs232);
        echo html_writer::end_tag('ul');

        echo html_writer::end_tag('div');

        $rtl = right_to_left();

        $coursenumsections = $this->courseformat->get_last_section_number();

        if ($editing) {
            echo $this->start_section_list();
            // If currently moving a file then show the current clipboard.
            $this->make_block_show_clipboard_if_file_moving($course);

            // Print Section 0 with general activities.
            if ($this->settings['section0attop'] == 2) {
                $this->make_block_topic0($course, $sections[0], $editing, $urlpicedit, $streditsummary, false);
            }

            /* Now all the normal modules by topic.
               Everything below uses "section" terminology - each "section" is a topic/module. */
            $this->make_block_topics($course, $sections, $modinfo, $hascapvishidsect, $streditsummary,
                $urlpicedit, false);

            echo html_writer::tag('div', '&nbsp;', array('class' => 'clearer'));
        }
        echo html_writer::end_tag('div');

        $sectionredirect = null;
        // Get the redirect URL prefix for keyboard control with the 'Show one section per page' layout.
        $sectionredirect = $this->courseformat->get_view_url(null)->out(true);
    }

    /**
     * Generate the edit controls of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of links with edit controls
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {

        if (!$this->page->user_is_editing()) {
            return array();
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url($course, $section->section);
        } else {
            $url = course_get_url($course);
        }
        $url->param('sesskey', sesskey());

        $controls = array();
        if ($section->section && has_capability('moodle/course:setcurrentsection', $coursecontext)) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param('marker', 0);
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marked',
                                               'name' => $highlightoff,
                                               'pixattr' => array('class' => ''),
                                               'attr' => array('class' => 'editing_highlight',
                                               'data-action' => 'removemarker'));
                $url->param('marker', 0);
            } else {
                $url->param('marker', $section->section);
                $highlight = get_string('highlight');
                $controls['highlight'] = array('url' => $url, "icon" => 'i/marker',
                                               'name' => $highlight,
                                               'pixattr' => array('class' => ''),
                                               'attr' => array('class' => 'editing_highlight',
                                               'data-action' => 'setmarker'));
            }
        }

        $parentcontrols = parent::section_edit_control_items($course, $section, $onsectionpage);

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists("edit", $parentcontrols)) {
            $merged = array();
            /* We can't use splice because we are using associative arrays.
               Step through the array and merge the arrays. */
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge($merged, $controls);
                }
            }

            return $merged;
        } else {
            return array_merge($controls, $parentcontrols);
        }
    }

    // Flexible format specific code.
    /**
     * Makes section zero.
     */
    private function make_block_topic0($course, $sectionzero, $editing, $urlpicedit, $streditsummary,
            $onsectionpage) {

        if ($this->settings['section0attop'] == 1) {
            echo html_writer::start_tag('ul', array('class' => 'ftopics-0'));
        }

        $sectionname = $this->courseformat->get_section_name($sectionzero);
        echo html_writer::start_tag('li', array(
            'id' => 'section-0',
            'class' => 'section main' . (($this->settings['section0attop'] == 1) ? '' : ' flexible_section'),
            'role' => 'region',
            'aria-label' => $sectionname)
        );

        echo html_writer::start_tag('div', array('class' => 'content'));

        if (!$onsectionpage) {
            echo $this->output->heading($sectionname, 3, 'sectionname');
        }

        echo html_writer::start_tag('div', array('class' => 'summary'));

        echo $this->format_summary_text($sectionzero);

        if ($editing) {
            echo html_writer::link(
                new moodle_url('editsection.php', array('id' => $sectionzero->id)),
                    html_writer::empty_tag('img', array('src' => $urlpicedit,
                        'alt' => $streditsummary,
                        'class' => 'iconsmall edit')),
                    array('title' => $streditsummary));
        }
        echo html_writer::end_tag('div');

        echo $this->courserenderer->course_section_cm_list($course, $sectionzero, 0);

        if ($editing) {
            echo $this->courserenderer->course_section_add_cm_control($course, $sectionzero->section, 0, 0);

            if ($this->settings['section0attop'] == 1) {
                $strhidesummary = get_string('hide_summary', 'format_flexible');
                $strhidesummaryalt = get_string('hide_summary_alt', 'format_flexible');

                echo html_writer::link(
                    $this->courseformat->flexible_moodle_url('mod_summary.php', array(
                        'sesskey' => sesskey(),
                        'courseid' => $course->id,
                        'showsummary' => 2)), html_writer::empty_tag('img', array(
                        'src' => $this->output->image_url('into_flexible', 'format_flexible'),
                        'alt' => $strhidesummaryalt)) . '&nbsp;' . $strhidesummary, array('title' => $strhidesummaryalt));
            }
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('li');

        if ($this->settings['section0attop'] == 1) {
            echo html_writer::end_tag('ul');
        }
    }

    /**
     * States if the icon is to be greyed out.
     *
     * For logic see: section_availability_message() + a bit more!
     *
     * @param stdClass $course The course entry from DB
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return bool Grey out the section icon, true or false?
     */
    protected function section_greyedout($course, $section, $canviewhidden) {
        global $CFG;
        $sectiongreyedout = false;
        if (!$section->visible) {
            if ($canviewhidden) {
                $sectiongreyedout = true;
            } else if (!$course->hiddensections) { // Hidden sections in collapsed form.
                $sectiongreyedout = true;
            }
        } else if (!$section->uservisible) {
            if (($section->availableinfo) && ((!$course->hiddensections) || ($canviewhidden))) { // Hidden sections in collapsed form.
                /* Note: We only get to this function if availableinfo is non-empty,
                   so there is definitely something to print. */
                $sectiongreyedout = true;
            }
        } else if ($canviewhidden && !empty($CFG->enableavailability)) {
            // Check if there is an availability restriction.
            $ci = new \core_availability\info_section($section);
            $fullinfo = $ci->get_full_information();
            $information = '';
            if ($fullinfo && (!$ci->is_available($information))) {
                $sectiongreyedout = true;
                $information = '';
            }
        }
        return $sectiongreyedout;
    }

    /**
     * Makes the flexible image containers.
     */
    private function make_block_icon_topics($contextid, $sections, $course, $editing, $hascapvishidsect,
            $urlpicedit, $bs232) {
        global $CFG;

        if ($this->settings['newactivity'] == 2) {
            $currentlanguage = current_language();
            if (!file_exists("$CFG->dirroot/course/format/flexible/pix/new_activity_".$currentlanguage.".png")) {
                $currentlanguage = 'en';
            }
            $urlpicnewactivity = $this->output->image_url('new_activity_'.$currentlanguage, 'format_flexible');

            // Get all the section information about which items should be marked with the NEW picture.
            $sectionupdated = $this->new_activity($course);
        }

        // Get the section images for the course.
        $sectionimages = $this->courseformat->get_images($course->id);

        // CONTRIB-4099:...
        $flexibleimagepath = $this->courseformat->get_image_path();

        $singlepageurl = $this->courseformat->get_view_url(null)->out(true);

        // Start at 1 to skip the summary block or include the summary block if it's in the flexible display.
        $coursenumsections = $this->courseformat->get_last_section_number();

        // Are we using WebP for the displayed image?
        $iswebp = (get_config('format_flexible', 'defaultdisplayedimagefiletype') == 2);

        // Get the summary repeated items.
        $summaryicon = $this->getfontawesomemarkup(get_config('format_flexible', 'defaultsectionsummaryicon'));
        $closebuttontitle = get_string('closebuttontitle');

        $isexpandable = false;
        $hassectionsummary = false;
        $sectioncount = 0;
        foreach ($sections as $sectionnum => $thissection) {
            if ((($this->settings['section0attop'] == 1) && ($sectionnum == 0)) || ($sectionnum > $coursenumsections)) {
                continue;  // Section 0 at the top and not in the flexible / orphaned section.
            }

            // Check if section is visible to user.
            $showsection = $thissection->uservisible ||
                ($thissection->visible && !$thissection->available &&
                !empty($thissection->availableinfo) && ((!$course->hiddensections) || ($hascapvishidsect)));

            // If we should grey it out, flag that here.
            $sectiongreyedout = false;
            if ($this->settings['greyouthidden'] == 2) {
                $sectiongreyedout = $this->section_greyedout($course, $thissection, $hascapvishidsect);
            }

            if ($showsection || $sectiongreyedout) {
                if (($editing) || ($thissection->sectionrenderas == 1)) { // Tile.
                    if ($this->make_icon_only($thissection, $sectionnum, $course, $contextid, $flexibleimagepath, $editing,
                        $sectiongreyedout, $singlepageurl, $sectionimages, $iswebp, $urlpicedit, $summaryicon,
                        $closebuttontitle)) {
                        $hassectionsummary = true;
                    }
                    $sectioncount++;
                } else {
                    if (($bs232) && ($sectioncount > 0)) {
                        $this->bs232_break_grid();
                    }
                    $this->make_icon_content($thissection, $sectionnum, $course, $contextid, $flexibleimagepath, $editing,
                        $sectiongreyedout, $singlepageurl, $sectionimages, $iswebp);
                    if ($bs232) {
                        $this->bs232_break_grid();
                    }
                    if ($thissection->sectionrenderas != 4) {
                        $isexpandable = true;
                    }
                    $sectioncount = 0;
                }
                if (($bs232) && ($sectioncount >= $this->settings['numcolumns'])) {
                    $this->bs232_break_grid();
                    $sectioncount = 0;
                }
            }
        }
        if ($hassectionsummary) {
            $this->page->requires->js_call_amd('format_flexible/sectionsummary', 'init');
        }
        if ($isexpandable) {
            $this->page->requires->js_call_amd('format_flexible/sectionexpandable', 'init');
        }
    }

    private function bs232_break_grid() {
        echo html_writer::end_tag('ul');
        $flexibleiconsclass = 'flexibleicons row-fluid';
        if ($this->settings['sectiontitleboxposition'] == 1) {
            $flexibleiconsclass .= ' content_inside';
        }
        echo html_writer::start_tag('ul', array('class' => $flexibleiconsclass));
    }

    private function make_icon_only($thissection, $sectionnum, $course, $contextid, $flexibleimagepath, $editing,
        $sectiongreyedout, $singlepageurl, $sectionimages, $iswebp, $urlpicedit, $summaryicon, $closebuttontitle) {
        $sectionname = $this->courseformat->get_section_name($thissection);
        $sectiontitleattribues = array();
        if ($this->settings['hidesectiontitle'] == 1) {
            $displaysectionname = $sectionname;
        } else {
            $displaysectionname = '';
            $sectiontitleattribues['aria-label'] = $sectionname;
        }
        if ($this->settings['sectiontitleflexiblelengthmaxoption'] != 0) {
            $sectionnamelen = core_text::strlen($displaysectionname);
            if ($sectionnamelen !== false) {
                if ($sectionnamelen > $this->settings['sectiontitleflexiblelengthmaxoption']) {
                    $displaysectionname = core_text::substr($displaysectionname, 0, $this->settings['sectiontitleflexiblelengthmaxoption']).'...';
                }
            }
        }
        $sectiontitleclass = 'icon_content';
        if ($this->settings['sectiontitleboxposition'] == 1) { // Inside.
            // Only bother if there is a section name to show.
            $canshow = false;
            $sectionnamelen = core_text::strlen($displaysectionname);
            if (($sectionnamelen !== false) && ($sectionnamelen > 0)) {
                if ($sectionnamelen == 1) {
                    if ($displaysectionname[0] != ' ') {
                        $canshow = true;
                    }
                } else {
                    $canshow = true;
                }
            }
            if ($canshow) {
                $sectiontitleclass .= ' content_inside ';
                switch ($this->settings['sectiontitleboxinsideposition']) {
                    case 1:
                        $sectiontitleclass .= 'top';
                        break;
                    case 2:
                        $sectiontitleclass .= 'middle';
                        break;
                    case 3:
                        $sectiontitleclass .= 'bottom';
                        break;
                }
            }
        }

        $sectiontitleattribues['id'] = 'flexiblesectionname-'.$thissection->section;
        $sectiontitleattribues['class'] = $sectiontitleclass;

        if ($thissection->setsectionallowsummarymarkup == 2) { // Allow markup in the summary.
            $summary = $this->format_summary_text($thissection);
        } else {
            $summary = strip_tags($thissection->summary);
            $summary = str_replace("&nbsp;", ' ', $summary);
        }

        if (!empty($summary)) {
            $displayedsectionname = $displaysectionname;
            $displaysectionname .= html_writer::tag('div', $summaryicon, array(
                'class' => 'sectionsummaryiconcontainer',
                'data-target' => '#sectionsummarymodal-'.$thissection->section,
                'title' => get_string('showsectionsummary', 'format_flexible')
                )
            );
        }

        /* Roles info on based on: http://www.w3.org/TR/wai-aria/roles.
           Looked into the 'flexible' role but that requires 'row' before 'flexiblecell' and there are none as the flexible
           is responsive, so as the container is a 'navigation' then need to look into converting the containing
           'div' to a 'nav' tag (www.w3.org/TR/2010/WD-html5-20100624/sections.html#the-nav-element) when I'm
           that all browsers support it against the browser requirements of Moodle. */
        $liattributes = array(
            'class' => $this->courseformat->get_column_class(false),
            'role' => 'region',
            'aria-labelledby' => 'flexiblesectionname-'.$thissection->section
        );
        if ($this->courseformat->is_section_current($sectionnum)) {
            $liattributes['class'] .= ' currenticon';
        }
        if (!empty($summary)) {
            $liattributes['aria-describedby'] = 'flexiblesectionsummary-'.$thissection->section;
        }
        echo html_writer::start_tag('li', $liattributes);

        $wrapperclass = 'flexiblesectionwrapper';
        if ($sectiongreyedout) {
            $wrapperclass .= ' inaccessible';
        }
        echo html_writer::start_tag('div', array('class' => $wrapperclass));

        // Ensure the record exists.
        if (($sectionimages === false) || (!array_key_exists($thissection->id, $sectionimages))) {
            // Method get_image has 'repair' functionality for when there are issues with the data.
            $sectionimage = $this->courseformat->get_image($course->id, $thissection->id);
        } else {
            $sectionimage = $sectionimages[$thissection->id];
        }

        /* If the image is set then check that displayedimageindex is greater than 0 otherwise create the displayed image.
           This is a catch-all for existing courses. */
        if (isset($sectionimage->image) && ($sectionimage->displayedimageindex < 1)) {
            // Set up the displayed image:...
            $sectionimage->newimage = $sectionimage->image;
            $icbc = $this->courseformat->hex2rgb($this->settings['imagecontainerbackgroundcolour']);
            $sectionimage = $this->courseformat->setup_displayed_image($sectionimage, $contextid,
                $this->settings, $icbc);
        }

        $content = '';
        if ($this->settings['sectiontitleboxposition'] == 2) { // Outside.
            $content .= html_writer::tag('div', $displaysectionname, $sectiontitleattribues);
        }

        if (($this->settings['newactivity'] == 2) && (isset($sectionupdated[$thissection->id]))) {
            $content .= html_writer::empty_tag('img', array(
                'class' => 'new_activity',
                'src' => $urlpicnewactivity));
        }

        $content .= html_writer::start_tag('div', array('class' => 'image_holder'));

        if ($editing) {
            $classes = 'render_as ';
            if ($this->settings['sectiontitleboxinsideposition'] == 1) { // Top.
                $classes .= 'bottom';
            } else {
                $classes .= 'top';
            }
            $content .= $this->get_section_rendered_as_icon($thissection, true, $classes);
        }

        if ($this->settings['sectiontitleboxposition'] == 1) { // Inside.
            $content .= html_writer::tag('div', $displaysectionname, $sectiontitleattribues);
        }

        if (!empty($summary)) {
            $content .= html_writer::tag('div', '', array('id' => 'flexiblesectionsummary-'.$thissection->section,
                'hidden' => true, 'aria-label' => $summary));
        }

        $content .= $this->courseformat->output_section_image(
            $sectionname, $sectionimage, $contextid, $thissection,
            $flexibleimagepath, $this->output, $iswebp);

        $content .= html_writer::end_tag('div');

        if (!$sectiongreyedout) {
            echo html_writer::link($singlepageurl.'&sectionid='.$thissection->id, $content, array(
                'id' => 'flexiblesection-'.$thissection->section,
                'class' => 'flexibleicon_link',
                'role' => 'link'));
        } else {
            // Need an enclosing 'span' for IE.
            echo html_writer::tag('span', $content);
        }
        if ($editing) {
            $this->make_block_icon_topics_editing($thissection, $contextid, $urlpicedit);
            if ($sectionnum == 0) {
                $this->make_block_icon_topic0_editing($course);
            }
        }

        if (!empty($summary)) {
            $summarymodal = html_writer::start_tag('div', array(
                'aria-hidden' => 'true',
                'aria-labelledby' => 'sectionsummarymodaltitle-'.$thissection->section,
                'class' => 'modal fade',
                'id' => 'sectionsummarymodal-'.$thissection->section,
                'role' => 'dialog'
                )
            );
            $summarymodal .= html_writer::start_tag('div', array(
                'class' => 'modal-dialog',
                'role' => 'document'
                )
            );
            $summarymodal .= html_writer::start_tag('div', array(
                'class' => 'modal-content'
                )
            );
            $summarymodal .= html_writer::start_tag('div', array(
                'class' => 'modal-header'
                )
            );

            $bs232 = \format_flexible::bstwothreetwo();
            $modaltitle = html_writer::tag('h3', $displayedsectionname, array(
                'class' => 'modal-title',
                'id' => 'sectionsummarymodal-'.$thissection->section
                )
            );
            $modalclose = html_writer::tag('button', $this->getfontawesomemarkup('times-circle-o'), array(
                'aria-label' => $closebuttontitle,
                'class' => 'close',
                'data-dismiss' => 'modal',
                'type' => 'button'
                )
            );
            if ($bs232) {
                $summarymodal .= $modalclose.$modaltitle;
            } else {
                $summarymodal .= $modaltitle.$modalclose;
            }
            $summarymodal .= html_writer::end_tag('div');

            $summarymodal .= html_writer::start_tag('div', array(
                'class' => 'modal-body'
                )
            );
            $summarymodal .= html_writer::tag('p', $summary);
            $summarymodal .= html_writer::end_tag('div');

            $summarymodal .= html_writer::start_tag('div', array(
                'class' => 'modal-footer'
                )
            );
            $summarymodal .= html_writer::tag('button', $closebuttontitle, array(
                'data-dismiss' => 'modal',
                'class' => 'btn btn-primary',
                'type' => 'button'
                )
            );
            $summarymodal .= html_writer::end_tag('div');

            $summarymodal .= html_writer::end_tag('div');
            $summarymodal .= html_writer::end_tag('div');
            $summarymodal .= html_writer::end_tag('div');
            echo $summarymodal;
        }
        if (!$editing) {
            // Section completion graphic.
            $scgraphic = $this->section_completion_graphic($thissection, $course);
            if (!empty($scgraphic)) {
                echo html_writer::tag('div', $scgraphic, array('class' => 'sctilegraphic'));
            }
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('li');

        return (!empty($summary));
    }

    private function make_icon_content($thissection, $sectionnum, $course, $contextid, $flexibleimagepath, $editing, $sectiongreyedout, $singlepageurl, $sectionimages, $iswebp) {
        $sectionname = $this->courseformat->get_section_name($thissection);
        $sectiontitleattribues = array();
        if ($this->settings['hidesectiontitle'] == 1) {
            $displaysectionname = $sectionname;
        } else {
            $displaysectionname = '';
            $sectiontitleattribues['aria-label'] = $sectionname;
        }
        if ($this->settings['sectiontitleflexiblelengthmaxoption'] != 0) {
            $sectionnamelen = core_text::strlen($displaysectionname);
            if ($sectionnamelen !== false) {
                if ($sectionnamelen > $this->settings['sectiontitleflexiblelengthmaxoption']) {
                    $displaysectionname = core_text::substr($displaysectionname, 0, $this->settings['sectiontitleflexiblelengthmaxoption']).'...';
                }
            }
        }
        $sectiontitleattribues['id'] = 'flexiblesectionname-'.$thissection->section;

        if ($thissection->setsectionallowsummarymarkup == 2) { // Allow markup in the summary.
            $summary = $this->format_summary_text($thissection);
        } else {
            $summary = strip_tags($thissection->summary);
            $summary = str_replace("&nbsp;", ' ', $summary);
        }

        /* Roles info on based on: http://www.w3.org/TR/wai-aria/roles.
           Looked into the 'flexible' role but that requires 'row' before 'flexiblecell' and there are none as the flexible
           is responsive, so as the container is a 'navigation' then need to look into converting the containing
           'div' to a 'nav' tag (www.w3.org/TR/2010/WD-html5-20100624/sections.html#the-nav-element) when I'm
           that all browsers support it against the browser requirements of Moodle. */
        $liattributes = array(
            'class' => $this->courseformat->get_column_class(true),
            'role' => 'region',
            'aria-labelledby' => 'flexiblesectionname-'.$thissection->section
        );
        if ($this->courseformat->is_section_current($sectionnum)) {
            $liattributes['class'] = 'currenticon';
        }
        if (!empty($summary)) {
            $liattributes['aria-describedby'] = 'flexiblesectionsummary-'.$thissection->section;
        }
        echo html_writer::start_tag('li', $liattributes); // Section container.

        // Ensure the record exists.
        if (($sectionimages === false) || (!array_key_exists($thissection->id, $sectionimages))) {
            // Method get_image has 'repair' functionality for when there are issues with the data.
            $sectionimage = $this->courseformat->get_image($course->id, $thissection->id);
        } else {
            $sectionimage = $sectionimages[$thissection->id];
        }

        /* If the image is set then check that displayedimageindex is greater than 0 otherwise create the displayed image.
           This is a catch-all for existing courses. */
        if (isset($sectionimage->image) && ($sectionimage->displayedimageindex < 1)) {
            // Set up the displayed image:...
            $sectionimage->newimage = $sectionimage->image;
            $icbc = $this->courseformat->hex2rgb($this->settings['imagecontainerbackgroundcolour']);
            $sectionimage = $this->courseformat->setup_displayed_image($sectionimage, $contextid,
                $this->settings, $icbc);
        }

        // Section title.
        $bs232 = \format_flexible::bstwothreetwo();
        if ($bs232) {
            echo html_writer::start_tag('div', array('class' => 'row-fluid flexibleexpandabletitle'));
            echo html_writer::start_tag('div', array('class' => 'span12'));
        } else {
            echo html_writer::start_tag('div', array('class' => 'row flexibleexpandabletitle'));
            echo html_writer::start_tag('div', array('class' => 'col-sm-12'));
        }
        $sectiontitle = html_writer::tag('h3', $displaysectionname, $sectiontitleattribues);
        if (!$sectiongreyedout) {
            echo html_writer::link($singlepageurl.'&sectionid='.$thissection->id, $sectiontitle, array(
                'id' => 'flexiblesectiontitle-'.$thissection->section,
                'class' => 'flexibletitle_link',
                'role' => 'link'));
        } else {
            // Need an enclosing 'span' for IE.
            echo html_writer::tag('span', $sectiontitle);
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');

        // Start section content wrapper.
        if ($bs232) {
            echo html_writer::start_tag('div', array('class' => 'row-fluid flexibleexpandablecontent'));

            // Section summary.
            echo html_writer::start_tag('div', array('class' => 'span9'));
        } else {
            echo html_writer::start_tag('div', array('class' => 'row flexibleexpandablecontent'));

            // Section summary.
            echo html_writer::start_tag('div', array('class' => 'col-sm-9'));
        }

        if (!empty($summary)) {
            echo html_writer::tag('div', $summary, array('id' => 'flexiblesectionsummary-'.$thissection->section));
        }
        echo html_writer::end_tag('div');

        // Image holder.
        if ($bs232) {
            echo html_writer::start_tag('div', array('class' => 'span3'));
        } else {
            echo html_writer::start_tag('div', array('class' => 'col-sm-3'));
        }

        $wrapperclass = 'flexiblesectionwrapper';
        if ($sectiongreyedout) {
            $wrapperclass .= ' inaccessible';
        }

        echo html_writer::start_tag('div', array('class' => $wrapperclass));

        $sectionimagecontent = '';
        if (($this->settings['newactivity'] == 2) && (isset($sectionupdated[$thissection->id]))) {
            $sectionimagecontent .= html_writer::empty_tag('img', array(
                'class' => 'new_activity',
                'src' => $urlpicnewactivity));
        }

        $sectionimagecontent .= html_writer::start_tag('div', array('class' => 'image_holder'));
        $sectionimagecontent .= $this->courseformat->output_section_image(
            $sectionname, $sectionimage, $contextid, $thissection, $flexibleimagepath, $this->output, $iswebp);

        $sectionimagecontent .= html_writer::end_tag('div');
        if (!$sectiongreyedout) {
            echo html_writer::link($singlepageurl.'&sectionid='.$thissection->id, $sectionimagecontent, array(
                'id' => 'flexiblesectionimage-'.$thissection->section,
                'class' => 'flexibleimage_link',
                'role' => 'link'));
        } else {
            // Need an enclosing 'span' for IE.
            echo html_writer::tag('span', $sectionimagecontent);
        }

        // Expandable collapsed area graphic.
        $scgraphic = $this->section_completion_graphic($thissection, $course);
        if (!empty($scgraphic)) {
            $scgattr = array(
                'class' => 'scexpandablegraphic',
                'id' => 'flexiblesectioncompletiongraphicmore-'.$thissection->section
            );
            echo html_writer::start_tag('div', $scgattr);

            echo $scgraphic;

            echo html_writer::end_tag('div');
        }
        echo html_writer::end_tag('div');
        echo html_writer::end_tag('div');

        // End section content wrapper.
        echo html_writer::end_tag('div');

        if ($thissection->sectionrenderas == 4) {
            // Expanded section.
            if (!$sectiongreyedout) {
                // Show more button.
                if ($bs232) {
                    echo html_writer::start_tag('div', array('class' => 'row-fluid flexibleexpanded'));
                    echo html_writer::start_tag('div', array('class' => 'span12'));
                } else {
                    echo html_writer::start_tag('div', array('class' => 'row flexibleexpanded'));
                    echo html_writer::start_tag('div', array('class' => 'col-sm-12'));
                }
                echo html_writer::link($singlepageurl.'&sectionid='.$thissection->id, get_string('showmore', 'format_flexible'), array(
                    'id' => 'flexiblesectionshowmore-'.$thissection->section,
                    'class' => 'flexibleshowmore_link btn btn-primary',
                    'role' => 'link'));
                echo html_writer::end_tag('div');
                echo html_writer::end_tag('div');
            }
        } else {
            // Expandable section.
            $expanded = ($thissection->sectionrenderas == 3);  // Logically now can only be 2 is collapsed and 3 is expanded.
            // Show more button.
            if ($bs232) {
                echo html_writer::start_tag('div', array('class' => 'row-fluid flexibleexpandabletoggle'));
                echo html_writer::start_tag('div', array('class' => 'span12'));
            } else {
                echo html_writer::start_tag('div', array('class' => 'row flexibleexpandabletoggle'));
                echo html_writer::start_tag('div', array('class' => 'col-sm-12'));
            }
            $showbuttonclasses = 'btn btn-primary flexibleexpandabletoggleshowbutton';
            if ($expanded) {
                $showbuttonclasses .= ' hidden';
            }

            $showmoreattrs = array(
                'class' => $showbuttonclasses,
                'data-target' => '#expandablecontent-'.$thissection->section,
                'id' => 'flexibleexpandabletoggleshowbutton-'.$thissection->section,
                'type' => 'button'
            );
            echo html_writer::tag('button', get_string('showmore', 'format_flexible'), $showmoreattrs);
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');

            $expandablecontentclass = 'collapse flexibleexpandablecollapsecontainer';
            if ($expanded) {
                $expandablecontentclass .= ' show';
                $expandablecontentstate = 'expanded';
            } else {
                $expandablecontentstate = 'collapsed';
            }
            echo html_writer::start_tag('div', array(
                'class' => $expandablecontentclass,
                'data-state' => $expandablecontentstate,
                'id' => 'expandablecontent-'.$thissection->section)
            );

            if ($bs232) {
                echo html_writer::start_tag('div', array('class' => 'row-fluid flexibleexpandablesection'));
                echo html_writer::start_tag('div', array('class' => 'span12'));
            } else {
                echo html_writer::start_tag('div', array('class' => 'row flexibleexpandablesection'));
                echo html_writer::start_tag('div', array('class' => 'col-sm-12'));
            }
            echo $this->start_section_list();
            echo $this->expandable_section_header($thissection, $course, false);

            if ($course->enablecompletion) {
                // Show completion help icon.
                $completioninfo = new completion_info($course);
                echo $completioninfo->display_help_icon();
            }

            echo $this->courserenderer->course_section_cm_list($course, $thissection, $sectionnum);
            echo $this->section_footer();
            echo $this->end_section_list();
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');

            // Show less button.
            if ($bs232) {
                echo html_writer::start_tag('div', array('class' => 'row-fluid flexibleexpandabletoggle'));
                echo html_writer::start_tag('div', array('class' => 'span12'));
            } else {
                echo html_writer::start_tag('div', array('class' => 'row flexibleexpandabletoggle'));
                echo html_writer::start_tag('div', array('class' => 'col-sm-12'));
            }
            $showlessattrs = array(
                'class' => 'btn btn-primary flexibleexpandabletogglehidebutton',
                'data-show' => '#flexibleexpandabletoggleshowbutton-'.$thissection->section,
                'data-target' => '#expandablecontent-'.$thissection->section,
                'type' => 'button'
            );
            echo html_writer::tag('button', get_string('showless', 'format_flexible'), $showlessattrs);
            echo html_writer::end_tag('div');
            echo html_writer::end_tag('div');

            echo html_writer::end_tag('div');
        }

        echo html_writer::end_tag('li');
    }

    /**
     * Generate the section completion graphic if any.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course the course record from DB.
     * @return string the markup or empty if nothing to show.
     */
    protected function section_completion_graphic($section, $course) {
        $markup = '';
        if (($course->enablecompletion) && ($this->settings['showsectioncompletiongraphic'] == 2)) {
            $percentage = $this->section_activity_progress($section, $course);
            if ($percentage !== false) {
                $progress = new stdClass;
                $progress->progress = $percentage;
                $markup = $this->render_from_template('block_myoverview/progress-bar', $progress);
            }
        }
        return $markup;
    }

    /**
     * Calculate the section progress.
     *
     * Adapted from core section_activity_summary() method.
     *
     * @param stdClass $section The course_section entry from DB.
     * @param stdClass $course the course record from DB.
     * @return bool/int false if none or the actual progress.
     */
    protected function section_activity_progress($section, $course) {
        $modinfo = get_fast_modinfo($course);
        if (empty($modinfo->sections[$section->section])) {
            return false;
        }

        // Generate array with count of activities in this section:
        $sectionmods = array();
        $total = 0;
        $complete = 0;
        $cancomplete = isloggedin() && !isguestuser();
        $completioninfo = new completion_info($course);
        foreach ($modinfo->sections[$section->section] as $cmid) {
            $thismod = $modinfo->cms[$cmid];

            // Labels counted for now, see: https://tracker.moodle.org/browse/MDL-65853.

            if ($thismod->uservisible) {
                if (isset($sectionmods[$thismod->modname])) {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modplural;
                    $sectionmods[$thismod->modname]['count']++;
                } else {
                    $sectionmods[$thismod->modname]['name'] = $thismod->modfullname;
                    $sectionmods[$thismod->modname]['count'] = 1;
                }
                if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $total++;
                    $completiondata = $completioninfo->get_data($thismod, true);
                    if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                            $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                        $complete++;
                    }
                }
            }
        }

        if (empty($sectionmods)) {
            // No sections
            return false;
        }

        if ($total == 0) {
            return false;
        }

        return round(($complete / $total) * 100);
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function expandable_section_header($section, $course, $onsectionpage, $sectionreturn=null) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
            'aria-label'=> get_section_name($course, $section)));

        $o.= html_writer::start_tag('div', array('class' => 'content'));

        $o .= $this->section_availability($section);

        return $o;
    }

    private function make_block_icon_topics_editing($thissection, $contextid, $urlpicedit) {
        global $USER;

        $streditimage = get_string('editimage', 'format_flexible');
        $streditimagealt = get_string('editimage_alt', 'format_flexible');

        echo html_writer::link(
            $this->courseformat->flexible_moodle_url('editimage.php', array(
                'sectionid' => $thissection->id,
                'contextid' => $contextid,
                'userid' => $USER->id,
                'role' => 'link',
                'aria-label' => $streditimagealt)
            ),
            html_writer::empty_tag('img', array(
                'src' => $urlpicedit,
                'alt' => $streditimagealt,
                'role' => 'img',
                'aria-label' => $streditimagealt)).'&nbsp;'.$streditimage,
            array('title' => $streditimagealt)
        );
    }

    private function make_block_icon_topic0_editing($course) {
        $strdisplaysummary = get_string('display_summary', 'format_flexible');
        $strdisplaysummaryalt = get_string('display_summary_alt', 'format_flexible');

        echo html_writer::empty_tag('br').html_writer::link(
            $this->courseformat->flexible_moodle_url('mod_summary.php', array(
                'sesskey' => sesskey(),
                'courseid' => $course->id,
                'showsummary' => 1,
                'role' => 'link',
                'aria-label' => $strdisplaysummaryalt)
            ),
            html_writer::empty_tag('img', array(
                'src' => $this->output->image_url('out_of_flexible', 'format_flexible'),
                'alt' => $strdisplaysummaryalt,
                'role' => 'img',
                'aria-label' => $strdisplaysummaryalt)) . '&nbsp;' . $strdisplaysummary,
                array('title' => $strdisplaysummaryalt)
        );
    }

    /**
     * If currently moving a file then show the current clipboard.
     */
    private function make_block_show_clipboard_if_file_moving($course) {
        global $USER;

        if (is_object($course) && ismoving($course->id)) {
            $strcancel = get_string('cancel');

            $stractivityclipboard = clean_param(format_string(
                            get_string('activityclipboard', '', $USER->activitycopyname)), PARAM_NOTAGS);
            $stractivityclipboard .= '&nbsp;&nbsp;('
                    . html_writer::link(new moodle_url('/mod.php', array(
                        'cancelcopy' => 'true',
                        'sesskey' => sesskey())), $strcancel);

            echo html_writer::tag('li', $stractivityclipboard, array('class' => 'clipboard'));
        }
    }

    /**
     * Makes the list of sections to show.
     */
    private function make_block_topics($course, $sections, $modinfo, $hascapvishidsect, $streditsummary,
            $urlpicedit, $onsectionpage) {
        $coursecontext = context_course::instance($course->id);
        unset($sections[0]);

        $coursenumsections = $this->courseformat->get_last_section_number();

        foreach ($sections as $section => $thissection) {
            if (!$hascapvishidsect && !$thissection->visible && $course->hiddensections) {
                unset($sections[$section]);
                continue;
            }
            if ($section > $coursenumsections) {
                // Orphaned section.
                continue;
            }

            $sectionstyle = 'section main';
            if (!$thissection->visible) {
                $sectionstyle .= ' hidden';
            }
            if ($this->courseformat->is_section_current($section)) {
                $sectionstyle .= ' current';
            }
            $sectionstyle .= ' flexible_section'; // hide_section';

            $sectionname = $this->courseformat->get_section_name($thissection);
            $title = $this->section_title($thissection, $course);
            echo html_writer::start_tag('li', array(
                'id' => 'section-' . $section,
                'class' => $sectionstyle,
                'role' => 'region',
                'aria-label' => $sectionname)
            );

            // Note, 'left side' is BEFORE content.
            $leftcontent = $this->section_left_content($thissection, $course, $onsectionpage);
            echo html_writer::tag('div', $leftcontent, array('class' => 'left side'));
            // Note, 'right side' is BEFORE content.
            $rightcontent = $this->section_right_content($thissection, $course, $onsectionpage);
            echo html_writer::tag('div', $rightcontent, array('class' => 'right side'));

            echo html_writer::start_tag('div', array('class' => 'content'));
            if ($hascapvishidsect || ($thissection->visible && $thissection->available)) {
                // If visible.
                echo $this->output->heading($title, 3, 'sectionname');

                echo html_writer::start_tag('div', array('class' => 'summary'));

                echo $this->format_summary_text($thissection);

                echo html_writer::link(
                    new moodle_url('editsection.php', array('id' => $thissection->id)),
                    html_writer::empty_tag('img', array('src' => $urlpicedit, 'alt' => $streditsummary,
                        'class' => 'iconsmall edit')), array('title' => $streditsummary));
                echo html_writer::end_tag('div');

                echo $this->section_availability_message($thissection, has_capability('moodle/course:viewhiddensections',
                        $coursecontext));

                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->courserenderer->course_section_add_cm_control($course, $thissection->section, 0);
            } else {
                echo html_writer::tag('h2', $this->get_title($thissection));
                echo html_writer::tag('p', get_string('hidden_topic', 'format_flexible'));

                echo $this->section_availability_message($thissection, has_capability('moodle/course:viewhiddensections',
                        $coursecontext));
            }

            echo html_writer::end_tag('div');
            echo html_writer::end_tag('li');

            unset($sections[$section]);
        }

        // Print stealth sections if present.
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section <= $coursenumsections or empty($modinfo->sections[$section])) {
                // This is not stealth section or it is empty.
                continue;
            }
            echo $this->stealth_section_header($section);
            echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
            echo $this->stealth_section_footer();
        }

        echo $this->end_section_list();

        echo $this->change_number_sections($course, 0);
    }

    /**
     * Attempts to return a 40 character title for the section image container.
     * If section names are set, they are used. Otherwise it scans
     * the summary for what looks like the first line.
     */
    private function get_title($section) {
        $title = is_object($section) && isset($section->name) &&
                is_string($section->name) ? trim($section->name) : '';

        if (!empty($title)) {
            // Apply filters and clean tags.
            $title = trim(format_string($section->name, true));
        }

        if (empty($title)) {
            $title = trim(format_text($section->summary));

            // Finds first header content. If it is not found, then try to find the first paragraph.
            foreach (array('h[1-6]', 'p') as $tag) {
                if (preg_match('#<(' . $tag . ')\b[^>]*>(?P<text>.*?)</\1>#si', $title, $m)) {
                    if (!$this->is_empty_text($m['text'])) {
                        $title = $m['text'];
                        break;
                    }
                }
            }
            $title = trim(clean_param($title, PARAM_NOTAGS));
        }

        if (core_text::strlen($title) > 40) {
            $title = $this->text_limit($title, 40);
        }

        return $title;
    }

    /**
     * States if the text is empty.
     * @param type $text The text to test.
     * @return boolean Yes(true) or No(false).
     */
    public function is_empty_text($text) {
        return empty($text) ||
                preg_match('/^(?:\s|&nbsp;)*$/si', htmlentities($text, 0 /* ENT_HTML401 */, 'UTF-8', true));
    }

    /**
     * Cuts long texts up to certain length without breaking words.
     */
    private function text_limit($text, $length, $replacer = '...') {
        if (core_text::strlen($text) > $length) {
            $text = wordwrap($text, $length, "\n", true);
            $pos = strpos($text, "\n");
            if ($pos === false) {
                $pos = $length;
            }
            $text = trim(core_text::substr($text, 0, $pos)) . $replacer;
        }
        return $text;
    }

    /**
     * Checks whether there has been new activity.
     */
    private function new_activity($course) {
        global $CFG, $USER, $DB;

        $sectionsedited = array();
        if (isset($USER->lastcourseaccess[$course->id])) {
            $course->lastaccess = $USER->lastcourseaccess[$course->id];
        } else {
            $course->lastaccess = 0;
        }

        $sql = "SELECT id, section FROM {$CFG->prefix}course_modules " .
                "WHERE course = :courseid AND added > :lastaccess";

        $params = array(
            'courseid' => $course->id,
            'lastaccess' => $course->lastaccess);

        $activity = $DB->get_records_sql($sql, $params);
        foreach ($activity as $record) {
            $sectionsedited[$record->section] = true;
        }

        return $sectionsedited;
    }

    public function set_initialsection($initialsection) {
        $this->initialsection = $initialsection;
    }

    protected function getfontawesomemarkup($theicon, $classes = array(), $attributes = array(), $content = '') {
        $classes[] = 'fa fa-'.$theicon;
        $attributes['aria-hidden'] = 'true';
        $attributes['class'] = implode(' ', $classes);
        return html_writer::tag('span', $content, $attributes);
    }
}
