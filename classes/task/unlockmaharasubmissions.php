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
 * Unlock Mahara portfolio submission pages / collections after a set date.
 *
 * @package   assignsubmission_mahara
 * @copyright 2020 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jackson D'souza (jackson.dsouza@catalyst-eu.net)
 */

namespace assignsubmission_mahara\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Syncing DB User role assignment task.
 *
 * @package   assignsubmission_mahara
 * @copyright 2020 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jackson D'souza (jackson.dsouza@catalyst-eu.net)
 */
class unlockmaharasubmissions extends \core\task\scheduled_task {

    /**
     * Name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('unlockmaharasubmissions', 'assignsubmission_mahara');
    }

    /**
     * Run task to unlock Mahara submission pages / collections.
     *
     * @return void
     */
    public function execute() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->dirroot . '/mod/assign/submission/mahara/lib.php');
        require_once($CFG->dirroot . '/mod/assign/submission/mahara/locallib.php');

        // Get records of all assignments where `Lock submitted pages` is set to `Yes, unlock after date`.
        $sql = "SELECT a.*
                  FROM {assign} a
                  JOIN {assign_plugin_config} acon ON a.id = acon.assignment
                 WHERE acon.plugin = :plugin1
                   AND (acon.name = :name1 and acon.value <= :value1)
                   AND a.id IN (SELECT a1.id
                                  FROM {assign} a1
                                  JOIN {assign_plugin_config} acon1 ON a1.id = acon1.assignment
                                 WHERE acon1.plugin = :plugin2
                                   AND (acon1.name = :name2 and acon1.value = :value2))
                    AND a.id IN (SELECT a2.id
                                   FROM {assign} a2
                                   JOIN {assign_plugin_config} acon2 ON a2.id = acon2.assignment
                                  WHERE acon2.plugin = :plugin3
                                    AND (acon2.name = :name3 and acon2.value = :value3))";
        $params['plugin1'] = 'mahara';
        $params['plugin2'] = 'mahara';
        $params['plugin3'] = 'mahara';
        $params['name1'] = 'unlockdate';
        $params['name2'] = 'lock';
        $params['name3'] = 'enabled';
        $params['value1'] = time();
        $params['value2'] = ASSIGNSUBMISSION_MAHARA_SETTING_UNLOCKDATE;
        $params['value3'] = 1;

        $records = $DB->get_records_sql($sql, $params);
        foreach ($records as $record) {
            $cm = get_coursemodule_from_instance('assign', $record->id, 0, false, MUST_EXIST);
            $context = \context_module::instance($cm->id);
            $assign = new \assign($context, null, null);
            $maharasubmissionplugin = $assign->get_submission_plugin_by_type('mahara');
            mtrace ('Processing assignment submission: ' . $record->id);

            // Get records of all Mahara assignments which have been submitted and locked in Mahara.
            $sql = "SELECT asubm.id, asubm.viewid, asubm.iscollection, asub.userid, u.username
                      FROM {assign_submission} asub
                      JOIN {assignsubmission_mahara} asubm ON asub.id = asubm.submission
                      JOIN {user} u ON asub.userid = u.id
                     WHERE asub.assignment = :assignment
                       AND asub.status = :status
                       AND asubm.viewstatus = :viewstatus";
            $params = array('assignment' => $record->id, 'status' => 'submitted', 'viewstatus' => 'submitted');
            $maharasubmissions = $DB->get_records_sql($sql, $params);
            foreach ($maharasubmissions as $maharasubmission) {
                $maharasubmissionplugin->mnet_release_submitted_view($maharasubmission->viewid, array(),
                    $maharasubmission->iscollection,
                    $maharasubmission->username);
                mtrace ('Unlocking Mahara pages/collections for: ' . $maharasubmission->userid);
            }
        }
    }
}
