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
 * Language strings for tool_lccoursedelete.
 *
 * @package    tool_lccoursedelete
 * @copyright  2025 Gifty Wanzola (UCL)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname']        = 'Course delete trigger';
$string['plugindescription'] = 'Triggers deletion of archived (frozen) courses that have been inactive for a configurable period and are older than a configurable age.';

$string['inactivitydelay']      = 'Inactivity delay';
$string['inactivitydelay_help'] = 'Courses whose most recent enrolled-user access is older than this value (or have never been accessed) will be considered for deletion.';

$string['creationdelay']        = 'Course creation delay';
$string['creationdelay_help']   = 'Courses created more recently than this value will not be considered for deletion, regardless of access history.';

$string['privacy:metadata'] = 'This plugin does not store any personal data.';
