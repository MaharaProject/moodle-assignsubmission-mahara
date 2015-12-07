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
 * This file contains the definition for the mnetservice class for Mahara submission plugin
 *
 * This class is just required for mnet services subscription registration
 * during plugin installation. It does not do anything.
 *
 * @package    assignsubmission_mahara
 * @copyright  2013 Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class mnetservice_assign_submission_mahara {

    public function donothing() {
    }

    /**
     * Determines whether the specified Moodle user should be able to view the specified
     * Mahara page (or collection), because it has been submitted to a Moodle assignment
     * and the user is grading it.
     *
     * // TODO: Currently, this function allows anyone with gradebook access in the
     * assignment, to view the page once it has been selected by the student. We may
     * want to change this to be more selective. For instance, only granting view access
     * if the submission record in mdl_assign_submission is in ASSIGN_SUBMISSION_STATUS_SUBMITTED
     *
     * @param int $viewid
     * @param string $username
     * @param int $assignment
     * @param bool $iscollection
     * @return boolean
     */
    public function can_view_view($viewid, $username, $assignment, $iscollection) {
        global $DB;

        $submissionid = $DB->get_field("assignsubmission_mahara",
                                       "submission",
                                        array("viewid"       => $viewid,
                                              "assignment"   => $assignment,
                                              "iscollection" => $iscollection));
        if (!$submissionid) {
            return false;
        }

        $userid = $DB->get_field("assign_submission", "userid", array("id" => $submissionid));
        $context = context_module::instance($assignment);
        $user = $DB->get_record("user", array("username" => $username));
        return $user->id == $userid || has_any_capability(array('mod/assign:grade', 'mod/assign:viewgrades'), $context, $user);
    }

}
