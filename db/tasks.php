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
 * Task definition for assignsubmission_mahara.
 * @package   assignsubmission_mahara
 * @copyright 2020 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jackson D'souza (jackson.dsouza@catalyst-eu.net)
 */

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => '\assignsubmission_mahara\task\unlockmaharasubmissions',
        'blocking' => 0,
        'minute' => '5',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
        'disabled' => 0
    )
);