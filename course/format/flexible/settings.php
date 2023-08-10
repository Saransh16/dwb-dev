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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/format/flexible/lib.php'); // For format_flexible static constants.

if ($ADMIN->fulltree) {

    // Number of columns.
    $name = 'format_flexible/defaultnumcolumns';
    $title = get_string('defaultnumcolumns', 'format_flexible');
    $description = get_string('defaultnumcolumns_desc', 'format_flexible');
    $default = format_flexible::get_default_num_columns();
    $choices = format_flexible::get_num_columns();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Container alignment.
    $name = 'format_flexible/defaultimagecontaineralignment';
    $title = get_string('defaultimagecontaineralignment', 'format_flexible');
    $description = get_string('defaultimagecontaineralignment_desc', 'format_flexible');
    $default = format_flexible::get_default_image_container_alignment();
    $choices = format_flexible::get_horizontal_alignments();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Image ratio.
    $name = 'format_flexible/defaultimagecontainerratio';
    $title = get_string('defaultimagecontainerratio', 'format_flexible');
    $description = get_string('defaultimagecontainerratio_desc', 'format_flexible');
    $default = format_flexible::get_default_image_container_ratio();
    $choices = format_flexible::get_image_container_ratios();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Resize method - 1 = scale, 2 = crop.
    $name = 'format_flexible/defaultimageresizemethod';
    $title = get_string('defaultimageresizemethod', 'format_flexible');
    $description = get_string('defaultimageresizemethod_desc', 'format_flexible');
    $default = format_flexible::get_default_image_resize_method();
    $choices = array(
        1 => new lang_string('scale', 'format_flexible'),
        2 => new lang_string('crop', 'format_flexible')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Section zero at top.
    $name = 'format_flexible/defaultsection0attop';
    $title = get_string('defaultsection0attop', 'format_flexible');
    $description = get_string('defaultsection0attop_desc', 'format_flexible');
    $default = format_flexible::get_default_section0attop();
    $choices = array(
        1 => new lang_string('top', 'format_flexible'),
        2 => new lang_string('grid', 'format_flexible')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Generated section image colours.
    require_once($CFG->dirroot.'/course/format/flexible/flexible_admin_setting_sectioncolours.php');
    $name = 'format_flexible/defaultgeneratedsectionimagecolours';
    $title = get_string('defaultgeneratedsectionimagecolours', 'format_flexible');
    $description = get_string('defaultgeneratedsectionimagecolours_desc', 'format_flexible');
    $default = format_flexible::get_default_generated_section_image_colours();
    $settings->add(new flexible_admin_setting_sectioncolours($name, $title, $description, $default, PARAM_TEXT));

    // Default border colour in hexadecimal RGB with preceding '#'.
    $name = 'format_flexible/defaultbordercolour';
    $title = get_string('defaultbordercolour', 'format_flexible');
    $description = get_string('defaultbordercolour_desc', 'format_flexible');
    $default = format_flexible::get_default_border_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Border width.
    $name = 'format_flexible/defaultborderwidth';
    $title = get_string('defaultborderwidth', 'format_flexible');
    $description = get_string('defaultborderwidth_desc', 'format_flexible');
    $default = format_flexible::get_default_border_width();
    $choices = format_flexible::get_border_widths();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Border radius on / off.
    $name = 'format_flexible/defaultborderradius';
    $title = get_string('defaultborderradius', 'format_flexible');
    $description = get_string('defaultborderradius_desc', 'format_flexible');
    $default = format_flexible::get_default_border_radius();
    $choices = array(
        1 => new lang_string('off', 'format_flexible'),   // Off.
        2 => new lang_string('on', 'format_flexible')   // On.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default imagecontainer background colour in hexadecimal RGB with preceding '#'.
    $name = 'format_flexible/defaultimagecontainerbackgroundcolour';
    $title = get_string('defaultimagecontainerbackgroundcolour', 'format_flexible');
    $description = get_string('defaultimagecontainerbackgroundcolour_desc', 'format_flexible');
    $default = format_flexible::get_default_image_container_background_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default current selected section colour in hexadecimal RGB with preceding '#'.
    $name = 'format_flexible/defaultcurrentselectedsectioncolour';
    $title = get_string('defaultcurrentselectedsectioncolour', 'format_flexible');
    $description = get_string('defaultcurrentselectedsectioncolour_desc', 'format_flexible');
    $default = format_flexible::get_default_current_selected_section_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Hide section title - 1 = no, 2 = yes.
    $name = 'format_flexible/defaulthidesectiontitle';
    $title = get_string('defaulthidesectiontitle', 'format_flexible');
    $description = get_string('defaulthidesectiontitle_desc', 'format_flexible');
    $default = format_flexible::get_default_hide_section_title();
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Section title in flexible box maximum length with 0 for no truncation.
    $name = 'format_flexible/defaultsectiontitleflexiblelengthmaxoption';
    $title = get_string('defaultsectiontitleflexiblelengthmaxoption', 'format_flexible');
    $description = get_string('defaultsectiontitleflexiblelengthmaxoption_desc', 'format_flexible');
    $default = format_flexible::get_default_section_title_flexible_length_max_option();
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    // Section title box position - 1 = Inside, 2 = Outside.
    $name = 'format_flexible/defaultsectiontitleboxposition';
    $title = get_string('defaultsectiontitleboxposition', 'format_flexible');
    $description = get_string('defaultsectiontitleboxposition_desc', 'format_flexible');
    $default = format_flexible::get_default_section_title_box_position();
    $choices = array(
        1 => new lang_string('sectiontitleboxpositioninside', 'format_flexible'),
        2 => new lang_string('sectiontitleboxpositionoutside', 'format_flexible')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Section title box inside position - 1 = Top, 2 = Middle, 3 = Bottom.
    $name = 'format_flexible/defaultsectiontitleboxinsideposition';
    $title = get_string('defaultsectiontitleboxinsideposition', 'format_flexible');
    $description = get_string('defaultsectiontitleboxinsideposition_desc', 'format_flexible');
    $default = format_flexible::get_default_section_title_box_inside_position();
    $choices = array(
        1 => new lang_string('sectiontitleboxinsidepositiontop', 'format_flexible'),
        2 => new lang_string('sectiontitleboxinsidepositionmiddle', 'format_flexible'),
        3 => new lang_string('sectiontitleboxinsidepositionbottom', 'format_flexible')
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Section title box height.
    $name = 'format_flexible/defaultsectiontitleboxheight';
    $title = get_string('defaultsectiontitleboxheight', 'format_flexible');
    $description = get_string('defaultsectiontitleboxheight_desc', 'format_flexible');
    $default = format_flexible::get_default_section_title_box_height();
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_INT));

    // Inside flexible box text box opacity.
    $name = 'format_flexible/defaultinsideboxopacity';
    $title = get_string('defaultinsideboxopacity', 'format_flexible');
    $description = get_string('defaultinsideboxopacity_desc', 'format_flexible');
    $default = format_flexible::get_default_inside_box_opacity();
    $choices = format_flexible::get_default_opacities();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Section title font size.
    $name = 'format_flexible/defaultsectiontitlefontsize';
    $title = get_string('defaultsectiontitlefontsize', 'format_flexible');
    $description = get_string('defaultsectiontitlefontsize_desc', 'format_flexible');
    $default = format_flexible::get_default_section_title_font_size();
    $choices = format_flexible::get_default_section_font_sizes();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Section title alignment.
    $name = 'format_flexible/defaultsectiontitlealignment';
    $title = get_string('defaultsectiontitlealignment', 'format_flexible');
    $description = get_string('defaultsectiontitlealignment_desc', 'format_flexible');
    $default = format_flexible::get_default_section_title_alignment();
    $choices = format_flexible::get_horizontal_alignments();
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Default text and icon colour when inside the flexible box in hexadecimal RGB with preceding '#'.
    $name = 'format_flexible/defaultboxinsidetextcolour';
    $title = get_string('defaultboxinsidetextcolour', 'format_flexible');
    $description = get_string('defaultboxinsidetextcolour_desc', 'format_flexible');
    $default = format_flexible::get_default_box_inside_text_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Default text and icon background colour when inside the flexible box in hexadecimal RGB with preceding '#'.
    $name = 'format_flexible/defaultboxinsidetextbackgroundcolour';
    $title = get_string('defaultboxinsidetextbackgroundcolour', 'format_flexible');
    $description = get_string('defaultboxinsidetextbackgroundcolour_desc', 'format_flexible');
    $default = format_flexible::get_default_box_inside_text_background_colour();
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);

    // Section summary icon.
    $name = 'format_flexible/defaultsectionsummaryicon';
    $title = get_string('defaultsectionsummaryicon', 'format_flexible');
    $description = get_string('defaultsectionsummaryicon_desc', 'format_flexible');
    $default = format_flexible::get_default_section_summary_icon();
    $settings->add(new admin_setting_configtext($name, $title, $description, $default, PARAM_ALPHANUMEXT));

    // Show new activity notification image - 1 = no, 2 = yes.
    $name = 'format_flexible/defaultnewactivity';
    $title = get_string('defaultnewactivity', 'format_flexible');
    $description = get_string('defaultnewactivity_desc', 'format_flexible');
    $default = 2;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Fix the section container popup to the screen. 1 = no, 2 = yes.
    $name = 'format_flexible/defaultfitsectioncontainertowindow';
    $title = get_string('defaultfitsectioncontainertowindow', 'format_flexible');
    $description = get_string('defaultfitsectioncontainertowindow_desc', 'format_flexible');
    $default = 1;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    // Displayed image file type - 1 = original, 2 = webp.
    $name = 'format_flexible/defaultdisplayedimagefiletype';
    $title = get_string('defaultdisplayedimagefiletype', 'format_flexible');
    $description = get_string('defaultdisplayedimagefiletype_desc', 'format_flexible');
    $default = format_flexible::get_default_image_file_type();
    $choices = array(
        1 => new lang_string('original', 'format_flexible'),
        2 => new lang_string('webp', 'format_flexible')
    );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('flexible_format_update_displayed_images');
    $settings->add($setting);

    // Grey out hidden sections.
    $name = 'format_flexible/defaultgreyouthidden';
    $title = get_string('greyouthidden', 'format_flexible');
    $description = get_string('greyouthidden_desc', 'format_flexible');
    $default = 1;
    $choices = array(
        1 => new lang_string('no'),   // No.
        2 => new lang_string('yes')   // Yes.
    );
    $settings->add(new admin_setting_configselect($name, $title, $description, $default, $choices));
}
