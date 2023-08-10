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
 * Lib unit tests for the Flexible course format.
 * @group format_flexible
 */
class format_flexible_courseformatlib_testcase extends advanced_testcase {
    protected $courseone;
    protected $coursetwo;
    protected $courseformatone;
    protected $courseformattwo;

    protected function setUp() {
        $this->resetAfterTest(true);

        set_config('theme', 'clean');
        // Ref: https://docs.moodle.org/dev/Writing_PHPUnit_tests.
        $this->courseone = $this->getDataGenerator()->create_course(
            array('format' => 'flexible',
                'numsections' => 1,
                'hidesectiontitle' => 2,
                'numcolumns' => 2,
                'imagecontaineralignment' => 'left',
                'imagecontainerratio' => 1,
                'imageresizemethod' => 1,
                'sectiontitleflexiblelengthmaxoption' => 24,
                'sectiontitleboxposition' => 1,
                'sectiontitleboxinsideposition' => 2,
                'sectiontitleboxheight' => 42,
                'insideboxopacity' => '.3',
                'sectiontitlefontsize' => 24,
                'sectiontitlealignment' => 'left',
                'boxinsidetextcolour' => '#ffffff',
                'boxinsidetextbackgroundcolour' => '#000000'),
            array('createsections' => true));
        $this->courseformatone = course_get_format($this->courseone);
        $this->coursetwo = $this->getDataGenerator()->create_course(
            array('format' => 'flexible',
                'numsections' => 1,
                'hidesectiontitle' => 2,
                'numcolumns' => 4,
                'imagecontaineralignment' => 'right',
                'imagecontainerratio' => 2,
                'imageresizemethod' => 2,
                'sectiontitleflexiblelengthmaxoption' => 12,
                'sectiontitleboxposition' => 1,
                'sectiontitleboxinsideposition' => 3,
                'sectiontitleboxheight' => 34,
                'insideboxopacity' => '.7',
                'sectiontitlefontsize' => 12,
                'sectiontitlealignment' => 'right',
                'boxinsidetextcolour' => '#ffffff',
                'boxinsidetextbackgroundcolour' => '#000000'),
            array('createsections' => true));
        $this->courseformattwo = course_get_format($this->coursetwo);
    }

    public function test_reset_num_columns() {
        $this->setAdminUser();
        $data = new stdClass;
        $data->resetimagecontainercolumns = true;
        $this->courseformatone->update_course_format_options($data);
        $cfo1 = $this->courseformatone->get_format_options();
        $cfo2 = $this->courseformattwo->get_format_options();

        $this->assertEquals(3, $cfo1['numcolumns']);
        $this->assertEquals(4, $cfo2['numcolumns']);
    }

    public function test_reset_all_num_columns() {
        $this->setAdminUser();
        $data = new stdClass;
        $data->resetallimagecontainercolumns = true;
        $this->courseformatone->update_course_format_options($data);
        $cfo1 = $this->courseformatone->get_format_options();
        $cfo2 = $this->courseformattwo->get_format_options();

        $this->assertEquals(3, $cfo1['numcolumns']);
        $this->assertEquals(3, $cfo2['numcolumns']);
    }

    public function test_reset_image_container_alignment() {
        $this->setAdminUser();
        $data = new stdClass;
        $data->resetimagecontaineralignment = true;
        $this->courseformatone->update_course_format_options($data);
        $cfo1 = $this->courseformatone->get_format_options();
        $cfo2 = $this->courseformattwo->get_format_options();

        $this->assertEquals('center', $cfo1['imagecontaineralignment']);
        $this->assertEquals('right', $cfo2['imagecontaineralignment']);
    }

    public function test_reset_all_image_container_alignments() {
        $this->setAdminUser();
        $data = new stdClass;
        $data->resetallimagecontaineralignment = true;
        $this->courseformatone->update_course_format_options($data);
        $cfo1 = $this->courseformatone->get_format_options();
        $cfo2 = $this->courseformattwo->get_format_options();

        $this->assertEquals('center', $cfo1['imagecontaineralignment']);
        $this->assertEquals('center', $cfo2['imagecontaineralignment']);
    }

    public function test_reset_section_title_options() {
        $this->setAdminUser();
        $data = new stdClass;
        $data->resetsectiontitleoptions = true;
        $this->courseformatone->update_course_format_options($data);
        $cfo = $this->courseformatone->get_format_options();

        $this->assertEquals(1, $cfo['hidesectiontitle']);
        $this->assertEquals(0, $cfo['sectiontitleflexiblelengthmaxoption']);
        $this->assertEquals(2, $cfo['sectiontitleboxposition']);
        $this->assertEquals(1, $cfo['sectiontitleboxinsideposition']);
        $this->assertEquals(0, $cfo['sectiontitleboxheight']);
        $this->assertEquals('.8', $cfo['insideboxopacity']);
        $this->assertEquals(0, $cfo['sectiontitlefontsize']);
        $this->assertEquals('center', $cfo['sectiontitlealignment']);
        $this->assertEquals('000000', $cfo['boxinsidetextcolour']);
        $this->assertEquals('ffffff', $cfo['boxinsidetextbackgroundcolour']);
    }

    public function test_reset_all_section_title_options() {
        $this->setAdminUser();
        $data = new stdClass;
        $data->resetallsectiontitleoptions = true;
        $this->courseformatone->update_course_format_options($data);
        $cfo1 = $this->courseformatone->get_format_options();
        $cfo2 = $this->courseformattwo->get_format_options();

        $this->assertEquals(1, $cfo1['hidesectiontitle']);
        $this->assertEquals(0, $cfo1['sectiontitleflexiblelengthmaxoption']);
        $this->assertEquals(2, $cfo1['sectiontitleboxposition']);
        $this->assertEquals(1, $cfo1['sectiontitleboxinsideposition']);
        $this->assertEquals(0, $cfo1['sectiontitleboxheight']);
        $this->assertEquals('.8', $cfo1['insideboxopacity']);
        $this->assertEquals(0, $cfo1['sectiontitlefontsize']);
        $this->assertEquals('center', $cfo1['sectiontitlealignment']);
        $this->assertEquals('000000', $cfo1['boxinsidetextcolour']);
        $this->assertEquals('ffffff', $cfo1['boxinsidetextbackgroundcolour']);

        $this->assertEquals(1, $cfo2['hidesectiontitle']);
        $this->assertEquals(0, $cfo2['sectiontitleflexiblelengthmaxoption']);
        $this->assertEquals(2, $cfo2['sectiontitleboxposition']);
        $this->assertEquals(1, $cfo2['sectiontitleboxinsideposition']);
        $this->assertEquals(0, $cfo2['sectiontitleboxheight']);
        $this->assertEquals('.8', $cfo2['insideboxopacity']);
        $this->assertEquals(0, $cfo2['sectiontitlefontsize']);
        $this->assertEquals('center', $cfo2['sectiontitlealignment']);
        $this->assertEquals('000000', $cfo2['boxinsidetextcolour']);
        $this->assertEquals('ffffff', $cfo2['boxinsidetextbackgroundcolour']);
    }
}
