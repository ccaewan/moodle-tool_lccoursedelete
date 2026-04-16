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
 * Lifecycle trigger: delete frozen courses based on long inactivity and age.
 *
 * @package    tool_lccoursedelete
 * @copyright  2025 Gifty Wanzola (UCL)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lccoursedelete\lifecycle;

global $CFG;
require_once($CFG->dirroot . '/admin/tool/lifecycle/trigger/lib.php');

use tool_lifecycle\local\manager\settings_manager;
use tool_lifecycle\local\response\trigger_response;
use tool_lifecycle\settings_type;
use tool_lifecycle\trigger\instance_setting;

defined('MOODLE_INTERNAL') || die();

/**
 * Trigger class for deleting archived (frozen) courses.
 *
 * Targets courses where:
 *  - Course context is locked (already archived/frozen)
 *  - Last access is older than inactivitydelay (default 48 months)
 *    OR course has never been accessed
 *  - Course creation is older than creationdelay (default 60 months)
 */
class trigger extends \tool_lifecycle\trigger\base_automatic {

    /**
     * Every decision is already in the WHERE statement.
     * @param \stdClass $course
     * @param int $triggerid
     * @return trigger_response
     */
    public function check_course($course, $triggerid) {
        return trigger_response::trigger();
    }

    /**
     * @return string full component name
     */
    public function get_subpluginname() {
        return 'tool_lccoursedelete';
    }

    /**
     * @return string human-readable plugin name
     */
    public function get_plugin_name() {
        return get_string('pluginname', 'tool_lccoursedelete');
    }

    /**
     * @return string human-readable plugin description
     */
    public function get_plugin_description() {
        return get_string('plugindescription', 'tool_lccoursedelete');
    }

    /**
     * @return instance_setting[]
     */
    public function instance_settings() {
        return [
            new instance_setting('inactivitydelay', PARAM_INT),
            new instance_setting('creationdelay', PARAM_INT),
        ];
    }

    /**
     * Returns WHERE clause and params for frozen courses eligible for deletion.
     *
     * @param int $triggerid
     * @return array [$where, $params]
     */
    public function get_course_recordset_where($triggerid) {
        $settings = settings_manager::get_settings($triggerid, settings_type::TRIGGER);

        $inactivitydelay = isset($settings['inactivitydelay']) ? (int)$settings['inactivitydelay'] : (4 * 365 * DAYSECS);
        $creationdelay   = isset($settings['creationdelay'])   ? (int)$settings['creationdelay']   : (5 * 365 * DAYSECS);

        $now = time();
        $lastaccessthreshold = $now - $inactivitydelay;
        $creationthreshold   = $now - $creationdelay;

        $where = "c.id <> 1
                  AND c.timecreated < :creationthreshold
                  AND EXISTS (
                        SELECT 1
                          FROM {context} ctx
                         WHERE ctx.contextlevel = 50
                           AND ctx.instanceid = c.id
                           AND ctx.locked = 1
                  )
                  AND (
                        NOT EXISTS (
                            SELECT 1
                              FROM {user_lastaccess} la
                             WHERE la.courseid = c.id
                        )
                        OR
                        EXISTS (
                            SELECT 1
                              FROM {user_lastaccess} la
                             WHERE la.courseid = c.id
                             GROUP BY la.courseid
                            HAVING MAX(la.timeaccess) < :lastaccessthreshold
                        )
                  )";

        $params = [
            'creationthreshold'   => $creationthreshold,
            'lastaccessthreshold' => $lastaccessthreshold,
        ];

        return [$where, $params];
    }

    /**
     * @param \MoodleQuickForm $mform
     */
    public function extend_add_instance_form_definition($mform) {
        $elementname = 'inactivitydelay';
        $mform->addElement('duration', $elementname,
            get_string($elementname, 'tool_lccoursedelete'));
        $mform->addHelpButton($elementname, $elementname, 'tool_lccoursedelete');
        $mform->setDefault($elementname, 4 * 365 * DAYSECS);

        $elementname = 'creationdelay';
        $mform->addElement('duration', $elementname,
            get_string($elementname, 'tool_lccoursedelete'));
        $mform->addHelpButton($elementname, $elementname, 'tool_lccoursedelete');
        $mform->setDefault($elementname, 5 * 365 * DAYSECS);
    }

    /**
     * @param \MoodleQuickForm $mform
     * @param array $settings
     */
    public function extend_add_instance_form_definition_after_data($mform, $settings) {
        if (!is_array($settings)) {
            return;
        }
        if (array_key_exists('inactivitydelay', $settings)) {
            $mform->setDefault('inactivitydelay', $settings['inactivitydelay']);
        }
        if (array_key_exists('creationdelay', $settings)) {
            $mform->setDefault('creationdelay', $settings['creationdelay']);
        }
    }
}
