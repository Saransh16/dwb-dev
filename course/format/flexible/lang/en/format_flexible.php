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

$string['display_summary'] = 'Move out of the grid';
$string['display_summary_alt'] = 'Move this section out of the grid';
$string['editimage'] = 'Change image';
$string['editimage_alt'] = 'Set or change image';
$string['formatflexible'] = 'Flexible format'; // Name to display for format.
$string['general_information'] = 'General Information';  // No longer used kept for legacy versions.
$string['hidden_topic'] = 'This section has been hidden';
$string['hide_summary'] = 'Move section into the grid';
$string['hide_summary_alt'] = 'Move this section into the grid';
$string['nameflexible'] = 'Flexible view';
$string['title'] = 'Section title';
$string['topic'] = 'Section';
$string['topic0'] = 'General';
$string['topicoutline'] = 'Section';  // No longer used kept for legacy versions.

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages.
$string['sectionname'] = 'Section';
$string['pluginname'] = 'Flexible format';
$string['section0name'] = 'General';

// WAI-ARIA - http://www.w3.org/TR/wai-aria/roles.
$string['flexibleimagecontainer'] = 'Flexible images';
//$string['closeshadebox'] = 'Close shade box';
$string['previoussection'] = 'Previous section';
$string['nextsection'] = 'Next section';
$string['shadeboxcontent'] = 'Shade box content';

// MDL-26105.
$string['page-course-view-flexible'] = 'Any course main page in the flexible format';
$string['page-course-view-flexible-x'] = 'Any course page in the flexible format';

$string['addsection'] = 'Add section';
$string['hidefromothers'] = 'Hide section'; // No longer used kept for legacy versions.
$string['showfromothers'] = 'Show section'; // No longer used kept for legacy versions.
$string['currentsection'] = 'This section'; // No longer used kept for legacy versions.
$string['markedthissection'] = 'This section is highlighted as the current section';
$string['markthissection'] = 'Highlight this section as the current section';

// Moodle 3.0 Enhancement.
$string['editsection'] = 'Edit section';
$string['deletesection'] = 'Delete section';

// MDL-51802.
$string['editsectionname'] = 'Edit section name';
$string['newsectionname'] = 'New name for section {$a}';

// Moodle 2.4 Course format refactoring - MDL-35218.
$string['numbersections'] = 'Number of sections';

// Exception messages.
$string['imagecannotbeused'] = 'Image cannot be used, must be a PNG, JPG or GIF and the GD PHP extension must be installed.';
$string['cannotfinduploadedimage'] = 'Cannot find the uploaded original image.  Please report error details and the information contained in the php.log file to developer.  Refresh the page and upload a fresh copy of the image.';
$string['cannotconvertuploadedimagetodisplayedimage'] = 'Cannot convert uploaded image to displayed image.  Please report error details and the information contained in the php.log file to developer.';
$string['cannotgetimagesforcourse'] = 'Cannot get images for course.  Please report error details to developer.';

$string['off'] = 'Off';
$string['on'] = 'On';
$string['scale'] = 'Scale';
$string['crop'] = 'Crop';
$string['original'] = 'Original';
$string['webp'] = 'WebP';
$string['imagefile'] = 'Upload an image';
$string['imagefile_help'] = 'Upload an image of type PNG, JPG or GIF.  WEBP needs Moodle core support.';
$string['deleteimage'] = 'Delete image';
$string['deleteimage_help'] = "Delete the image for the section being edited.  If you've uploaded an image then it will not replace the deleted image.";
$string['defaultnumcolumns'] = 'Default number of columns';
$string['defaultnumcolumns_desc'] = 'The default number of columns.';
$string['defaultimagecontaineralignment'] = 'Default alignment of the image containers';
$string['defaultimagecontaineralignment_desc'] = 'The default alignment of the image containers.';
$string['defaultimagecontainerratio'] = 'Default ratio of the image container relative to the width';
$string['defaultimagecontainerratio_desc'] = 'The default ratio of the image container relative to the width.';
$string['defaultimageresizemethod'] = 'Default image resize method';
$string['defaultimageresizemethod_desc'] = 'The default method of resizing the image to fit the container.';
$string['defaultbordercolour'] = 'Default image container border colour';
$string['defaultbordercolour_desc'] = 'The default image container border colour.';
$string['defaultborderradius'] = 'Default border radius';
$string['defaultborderradius_desc'] = 'The default border radius on / off.';
$string['defaultborderwidth'] = 'Default border width';
$string['defaultborderwidth_desc'] = 'The default border width.';
$string['defaultdisplayedimagefiletype'] = 'Displayed image type';
$string['defaultdisplayedimagefiletype_desc'] = 'Set the displayed image type.';
$string['defaultimagecontainerbackgroundcolour'] = 'Default image container background colour';
$string['defaultimagecontainerbackgroundcolour_desc'] = 'The default image container background colour.';
$string['defaultcurrentselectedsectioncolour'] = 'Default current selected section colour';
$string['defaultcurrentselectedsectioncolour_desc'] = 'The default current selected section colour.';

$string['defaultfitsectioncontainertowindow'] = 'Fit section container to window by default';
$string['defaultfitsectioncontainertowindow_desc'] = 'The default setting for \'Fit section container to window\'.';

$string['defaultnewactivity'] = 'Show new activity notification image default';
$string['defaultnewactivity_desc'] = "Show the new activity notification image when a new activity or resource are added to a section default.";

$string['defaultsection0attop'] = 'Default position for section zero on new courses';
$string['defaultsection0attop_desc'] = 'Set the default position for section zero on new courses, top or grid.';
$string['grid'] = 'Grid';

$string['ffreset'] = 'Flexible reset options';
$string['ffreset_help'] = 'Reset to Flexible defaults.';

$string['setnumcolumns'] = 'Number of columns';
$string['setnumcolumns_help'] = 'Set the number of columns';
$string['setimagecontaineralignment'] = 'Set the image container alignment';
$string['setimagecontaineralignment_help'] = 'Set the image container width to one of: Left, Centre or Right';
$string['setimagecontainerratio'] = 'Set the image container ratio relative to the width';
$string['setimagecontainerratio_help'] = 'Set the image container ratio to one of: 3-2, 3-1, 3-3, 2-3, 1-3, 4-3, 3-4 or 5-3.';
$string['setimageresizemethod'] = 'Set the image resize method';
$string['setimageresizemethod_help'] = "Set the image resize method to: 'Scale' or 'Crop' when resizing the image to fit the container.";
$string['setbordercolour'] = 'Set the border colour';
$string['setbordercolour_help'] = 'Set the border colour in hexidecimal RGB.';
$string['setborderradius'] = 'Set the border radius on / off';
$string['setborderradius_help'] = 'Set the border radius on or off.';
$string['setborderwidth'] = 'Set the border width';
$string['setborderwidth_help'] = 'Set the border width between 1 and 10.';
$string['setimagecontainerbackgroundcolour'] = 'Set the image container background colour';
$string['setimagecontainerbackgroundcolour_help'] = 'Set the image container background colour in hexidecimal RGB.';
$string['setcurrentselectedsectioncolour'] = 'Set the current selected section colour';
$string['setcurrentselectedsectioncolour_help'] = 'Set the current selected section colour in hexidecimal RGB.';

$string['setnewactivity'] = 'Show new activity notification image';
$string['setnewactivity_help'] = "Show the new activity notification image when a new activity or resource are added to a section.";

$string['setfitsectioncontainertowindow'] = 'Fit the section popup to the window';
$string['setfitsectioncontainertowindow_help'] = 'If enabled, the popup box with the contents of the section will fit to the size of the window and will scroll inside if necessary.  If disabled, the entire page will scroll instead.';

$string['colourrule'] = "Please enter a valid RGB colour, six hexadecimal digits.";
$string['opacityrule'] = "Please enter a valid opacity, between 0 and 1 with 0.1 increments.";
$string['sectiontitlefontsizerule'] = "Please enter a valid section title font size, between 12 and 24 (pixels) or 0 for 'do not set'.";

// Section title text format options.
$string['hidesectiontitle'] = 'Hide section title option';
$string['hidesectiontitle_help'] = 'Hide the section title.';
$string['defaulthidesectiontitle'] = 'Hide section title option';
$string['defaulthidesectiontitle_desc'] = 'Hide the section title.';
$string['sectiontitleflexiblelengthmaxoption'] = 'Section title flexible length option';
$string['sectiontitleflexiblelengthmaxoption_help'] = 'Set the maximum length of the section title in the flexible box.  Enter \'0\' for no truncation.';
$string['defaultsectiontitleflexiblelengthmaxoption'] = 'Section title flexible length option';
$string['defaultsectiontitleflexiblelengthmaxoption_desc'] = 'Set the default maximum length of the section title in the flexible box.  Enter \'0\' for no truncation.';
$string['sectiontitleflexiblelengthmaxoptionrule'] = 'The maximum length of the section title in the flexible box must not be zero.  Enter \'0\' for no truncation.';
$string['sectiontitleboxposition'] = 'Section title box position option';
$string['sectiontitleboxposition_help'] = 'Set the position of the section title within the flexible box to one of: \'Inside\' or \'Outside\'.';
$string['defaultsectiontitleboxposition'] = 'Section title box position option';
$string['defaultsectiontitleboxposition_desc'] = 'Set the position of the section title within the flexible box to one of: \'Inside\' or \'Outside\'.';
$string['sectiontitleboxpositioninside'] = 'Inside';
$string['sectiontitleboxpositionoutside'] = 'Outside';
$string['sectiontitleboxinsideposition'] = 'Section title box position when \'Inside\' option';
$string['sectiontitleboxinsideposition_help'] = 'Set the position of the section title when \'Inside\' the flexible box to one of: \'Top\', \'Middle\' or \'Bottom\'.';
$string['defaultsectiontitleboxinsideposition'] = 'Section title box position when \'Inside\' option';
$string['defaultsectiontitleboxinsideposition_desc'] = 'Set the position of the section title when \'Inside\' the flexible box to one of: \'Top\', \'Middle\' or \'Bottom\'.';
$string['sectiontitleboxinsidepositiontop'] = 'Top';
$string['sectiontitleboxinsidepositionmiddle'] = 'Middle';
$string['sectiontitleboxinsidepositionbottom'] = 'Bottom';
$string['sectiontitleboxheight'] = 'Section title box height';
$string['sectiontitleboxheight_help'] = 'Section title box height in pixels or 0 for browser calculated.  When the box position is \'Inside\'.';
$string['defaultsectiontitleboxheight'] = 'Section title box height';
$string['defaultsectiontitleboxheight_desc'] = 'Section title box height in pixels or 0 for browser calculated.  When the box position is \'Inside\'.';
$string['insideboxopacity'] = 'Inside flexible box text box opacity';
$string['insideboxopacity_help'] = 'Inside flexible box text box opacity between 0 and 1 in 0.1 increments.  This is for text boxes that are inside the flexible box.';
$string['defaultinsideboxopacity'] = 'Inside flexible box text box opacity';
$string['defaultinsideboxopacity_desc'] = 'Inside flexible box text box opacity between 0 and 1 in 0.1 increments.  This is for text boxes that are inside the flexible box.';
$string['sectiontitlefontsize'] = 'Section title font size';
$string['sectiontitlefontsize_help'] = 'Section title font size between 12 and 24 pixels where 0 represents \'do not set but inherit from theme or any other applying CSS\'.';
$string['defaultsectiontitlefontsize'] = 'Section title font size';
$string['defaultsectiontitlefontsize_desc'] = 'Section title font size between 12 and 24 pixels where 0 represents \'do not set but inherit from theme or any other applying CSS\'.';
$string['sectiontitlealignment'] = 'Section title alignment';
$string['sectiontitlealignment_help'] = 'Set the section title alignment to one of \'Left\', \'Centre\' or \'Right\'.';
$string['defaultsectiontitlealignment'] = 'Section title alignment';
$string['defaultsectiontitlealignment_desc'] = 'Set the section title alignment to one of \'Left\', \'Centre\' or \'Right\'.';
$string['boxinsidetextcolour'] = 'Text and icon colour when inside the flexible box';
$string['boxinsidetextcolour_help'] = 'Set the text and icon colour when inside the flexible box.';
$string['defaultboxinsidetextcolour'] = 'Text and icon colour when inside the flexible box';
$string['defaultboxinsidetextcolour_desc'] = 'Set the text and icon colour when inside the flexible box.';
$string['boxinsidetextbackgroundcolour'] = 'Text and icon background colour when inside the flexible box';
$string['boxinsidetextbackgroundcolour_help'] = 'Set the text and icon background colour when inside the flexible box.';
$string['defaultboxinsidetextbackgroundcolour'] = 'Text and icon background colour when inside the flexible box';
$string['defaultboxinsidetextbackgroundcolour_desc'] = 'Set the text and icon background colour when inside the flexible box.';
$string['top'] = 'Top';
$string['bottom'] = 'Bottom';
$string['centre'] = 'Centre';
$string['left'] = 'Left';
$string['right'] = 'Right';

// Show section completion graphic.
$string['setshowsectioncompletiongraphic'] = 'Show the section completion graphic';
$string['setshowsectioncompletiongraphic_help'] = 'Show the section completion graphic when completion tracking is on.';

// Show image on section page.
$string['setshowimageonsectionpage'] = 'Show image on section page';
$string['setshowimageonsectionpage_help'] = 'Show the image on the section page.';

// Reset.
$string['resetgrp'] = 'Reset:';
$string['resetallgrp'] = 'Reset all:';
$string['resetimagecontaineralignment'] = 'Image container alignment';
$string['resetimagecontaineralignment_help'] = 'Resets the image container alignment to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallimagecontaineralignment'] = 'Image container alignments';
$string['resetallimagecontaineralignment_help'] = 'Resets the image container alignmentss to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetimagecontainersize'] = 'Image container size';
$string['resetimagecontainersize_help'] = 'Resets the image container size to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallimagecontainersize'] = 'Image container sizes';
$string['resetallimagecontainersize_help'] = 'Resets the image container sizes to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetimageresizemethod'] = 'Image resize method';
$string['resetimageresizemethod_help'] = 'Resets the image resize method to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallimageresizemethod'] = 'Image resize methods';
$string['resetallimageresizemethod_help'] = 'Resets the image resize methods to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetimagecontainerstyle'] = 'Image container style';
$string['resetimagecontainerstyle_help'] = 'Resets the image container style to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallimagecontainerstyle'] = 'Image container styles';
$string['resetallimagecontainerstyle_help'] = 'Resets the image container styles to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetsectiontitleoptions'] = 'Section title options';
$string['resetsectiontitleoptions_help'] = 'Resets the section title options to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallsectiontitleoptions'] = 'Section title options';
$string['resetallsectiontitleoptions_help'] = 'Resets the section title options to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetnewactivity'] = 'New activity';
$string['resetnewactivity_help'] = 'Resets the new activity notification image to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallnewactivity'] = 'New activities';
$string['resetallnewactivity_help'] = 'Resets the new activity notification images to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetfitpopup'] = 'Fit section popup to the window';
$string['resetfitpopup_help'] = 'Resets the \'Fit section popup to the window\' to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallfitpopup'] = 'Fit section popups to the window';
$string['resetallfitpopup_help'] = 'Resets the \'Fit section popup to the window\' to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';
$string['resetgreyouthidden'] = 'Grey out unavailable';
$string['resetgreyouthidden_desc'] = 'Resets the property \'Flexible display show unavailable section images in grey and unlinked.\'';
$string['resetgreyouthidden_help'] = 'Resets the property \'In Flexible display show unavailable section images in grey and unlinked.\'';

// Section 0 on own page when out of the flexible and course layout is 'Show one section per page'.
$string['resetimagecontainercolumns'] = 'Image container columns';
$string['resetimagecontainercolumns_help'] = 'Resets the image container columns to the default value so it will be the same as a course the first time it is in the Flexible format.';
$string['resetallimagecontainercolumns'] = 'All image container columns';
$string['resetallimagecontainercolumns_help'] = 'Resets the image container columns to the default value for all courses so it will be the same as a course the first time it is in the Flexible format.';

// Capabilities.
$string['flexible:changeimagecontaineralignment'] = 'Change or reset the image container alignment';
$string['flexible:changeimagecontainercolumns'] = 'Change or reset the image container columns';
$string['flexible:changeimagecontainersize'] = 'Change or reset the image container size';
$string['flexible:changeimageresizemethod'] = 'Change or reset the image resize method';
$string['flexible:changeimagecontainerstyle'] = 'Change or reset the image container style';
$string['flexible:changesectiontitleoptions'] = 'Change or reset the section title options';

// Expandable content sections.
$string['tile'] = 'Tile';
$string['expanded'] = 'Expanded';
$string['expandablecollapsed'] = 'Expandable content (collapsed by default)';
$string['expandableexpanded'] = 'Expandable content (expanded by default)';
$string['setsectionrenderas'] = 'Render as';
$string['setsectionrenderas_help'] = 'Render section as either \'Tile\', \'Expanded\', \'Expandable content (collapsed by default)\' or \'Expandable content (expanded by default)\'.';
$string['showless'] = 'Show less';
$string['showmore'] = 'Show more';
$string['setsectionallowsummarymarkup'] = 'Allow summary markup';
$string['setsectionallowsummarymarkup_help'] = 'Allow the summary to contain HTML markup on the main course page.';

// Other.
$string['greyouthidden'] = 'Grey out unavailable';
$string['greyouthidden_desc'] = 'In Flexible display show unavailable section images in grey and unlinked.';
$string['greyouthidden_help'] = 'In Flexible display show unavailable section images in grey and unlinked.';

// Section summary.
$string['showsectionsummary'] = 'Show the section summary';
$string['defaultsectionsummaryicon'] = 'Set section summary icon';
$string['defaultsectionsummaryicon_desc'] = 'Set the name (without the \'fa-\' prefix) of the FontAwesome 4.7.0 icon to be used as the section summary icon.  For a list of icons, please visit \'https://fontawesome.com/v4.7.0/icons/\'.';

// Generated section image colours.
$string['defaultgeneratedsectionimagecolours'] = 'List of generated section image colours';
$string['defaultgeneratedsectionimagecolours_desc'] = 'Set the list of colours the format should pick from when generating an image for a container without an image.  This needs to be comma separated.';
$string['invalidcolour'] = 'Invalid colour {$a}.';

// Privacy.
$string['privacy:nop'] = 'The Flexible format stores lots of settings that pertain to its configuration.  None of the settings are related to a specific user.  It is your responsibilty to ensure that no user data is entered in any of the free text fields.  Setting a setting will result in that action being logged within the core Moodle logging system against the user whom changed it, this is outside of the formats control, please see the core logging system for privacy compliance for this.  When uploading images, you should avoid uploading images with embedded location data (EXIF GPS) included or other such personal data.  It would be possible to extract any location / personal data from the images.  Please examine the code carefully to be sure that it complies with your interpretation of your privacy laws.  I am not a lawyer and my analysis is based on my interpretation.  If you have any doubt then remove the format forthwith.';
