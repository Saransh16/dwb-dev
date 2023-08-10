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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace format_flexible;

defined('MOODLE_INTERNAL') || die();

/**
 * Flexible format geopattern.
 */
class geopattern extends \core_geopattern {
    /**
     * Create a transparent base 64 encoded svg of the dimensions required.
     */
    public function formaturi($dimensions) {
        $uri = 'data:image/svg+xml;base64,';
        $this->svg = new \RedeyeVentures\GeoPattern\SVG();
        $this->svg->setHeight($dimensions['height']);
        $this->svg->setWidth($dimensions['width']);

        $uri .= base64_encode((string)$this->svg);

        return $uri;
    }

}
