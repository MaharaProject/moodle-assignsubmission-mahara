<?PHP
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
 * This file contains the moodle hooks for the submission Mahara plugin
 *
 * @package    assignsubmission_mahara
 * @copyright  2012 Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Statuses for locking setting.
define('ASSIGNSUBMISSION_MAHARA_SETTING_DONTLOCK', 0);
define('ASSIGNSUBMISSION_MAHARA_SETTING_KEEPLOCKED', 1);
define('ASSIGNSUBMISSION_MAHARA_SETTING_UNLOCK', 2);

/**
 * Get the list of MNet hosts that we are allowed to retrieve Mahara pages from.
 * (Only hosts that subscribe to the correct flags)
 *
 * @return array
 */
function assignsubmission_mahara_sitelist() {
    global $DB, $CFG;

    $sql = "
             SELECT DISTINCT
                 h.id,
                 h.name
             FROM
                 {mnet_host} h,
                 {mnet_application} a,
                 {mnet_host2service} h2s_IDP,
                 {mnet_service} s_IDP,
                 {mnet_host2service} h2s_SP,
                 {mnet_service} s_SP
             WHERE
                 h.id != :mnet_localhost_id AND
                 h.id = h2s_IDP.hostid AND
                 h.deleted = 0 AND
                 h.applicationid = a.id AND
                 h2s_IDP.serviceid = s_IDP.id AND
                 s_IDP.name = 'sso_idp' AND
                 h2s_IDP.publish = '1' AND
                 h.id = h2s_SP.hostid AND
                 h2s_SP.serviceid = s_SP.id AND
                 s_SP.name = 'sso_idp' AND
                 h2s_SP.publish = '1' AND
                 a.name = 'mahara'
             ORDER BY
                 h.name";

    return $DB->get_records_sql_menu($sql, array('mnet_localhost_id'=>$CFG->mnet_localhost_id));
}

/**
 * Determines whether or not the specified mnethost is publishing the function needed to
 * do Mahara page permissions via MNet.
 * TODO: Update this function if MDL-52172 gets merged
 *
 * @param int $mnethostid
 * @return boolean
 */
function assignsubmission_mahara_is_mnet_acl_enabled($mnethostid) {
    global $DB, $CFG;
    $sql = '
            SELECT r.xmlrpcpath
            FROM
                {mnet_host} h
                INNER JOIN {mnet_host2service} h2s
                    ON h.id = h2s.hostid
                INNER JOIN {mnet_service} s
                    ON s.id = h2s.serviceid
                    AND s.name=\'assign_submission_mahara\'
                INNER JOIN {mnet_service2rpc} s2r
                    ON s.id = s2r.serviceid
                INNER JOIN {mnet_rpc} r
                    on r.id = s2r.rpcid
                    AND r.functionname = \'can_view_view\'
                    AND r.pluginname = \'mahara\'
                    AND r.plugintype = \'local\'
            WHERE
                h.id = :mnethostid
                AND h.deleted = 0
                AND h2s.publish = 1
                AND r.enabled = 1
    ';

    $ret = $DB->record_exists_sql($sql, array('mnethostid' => $mnethostid));
    // Seemed like a good idea, but it fills up the logs too much.
//     if (!$ret) {
//         debugging('You should install the Mahara local plugin and publish its service, for better'
//                 .' interoperability with the Mahara assignment submission plugin.',
//                 DEBUG_DEVELOPER
//         );
//     }
    return $ret;
}
