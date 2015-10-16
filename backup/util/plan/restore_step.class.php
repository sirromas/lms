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
 * @package moodlecore
 * @subpackage backup-plan
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Abstract class defining the needed stuf for one restore step
 *
 * TODO: Finish phpdocs
 */
abstract class restore_step extends base_step {

    /**
     * Constructor - instantiates one object of this class
     */
    public function __construct($name, $task = null) {
        if (!is_null($task) && !($task instanceof restore_task)) {
            throw new restore_step_exception('wrong_restore_task_specified');
        }
        parent::__construct($name, $task);
    }

    protected function get_restoreid() {
        if (is_null($this->task)) {
            throw new restore_step_exception('not_specified_restore_task');
        }
        return $this->task->get_restoreid();
    }

    /**
     * Apply course startdate offset based in original course startdate and course_offset_startdate setting
     * Note we are using one static cache here, but *by restoreid*, so it's ok for concurrence/multiple
     * executions in the same request
     *
     * @param int $value Time value (seconds since epoch), or empty for nothing
     * @return int Time value after applying the date offset, or empty for nothing
     */
    public function apply_date_offset($value) {

        // Empties don't offset - zeros (int and string), false and nulls return original value.
        if (empty($value)) {
            return $value;
        }

        static $cache = array();
        // Lookup cache.
        if (isset($cache[$this->get_restoreid()])) {
            return $value + $cache[$this->get_restoreid()];
        }
        // No cache, let's calculate the offset.
        $original = $this->task->get_info()->original_course_startdate;
        $setting = 0;
        if ($this->setting_exists('course_startdate')) { // Seting may not exist (MDL-25019).
            $setting  = $this->get_setting_value('course_startdate');
        }

        if (empty($original) || empty($setting)) {
            // Original course has not startdate or setting doesn't exist, offset = 0.
            $cache[$this->get_restoreid()] = 0;

        } else if (abs($setting - $original) < 24 * 60 * 60) {
            // Less than 24h of difference, offset = 0 (this avoids some problems with timezones).
            $cache[$this->get_restoreid()] = 0;

        } else if (!has_capability('moodle/restore:rolldates',
               context_course::instance($this->get_courseid()), $this->task->get_userid())) {
            // Re-enforce 'moodle/restore:rolldates' capability for the user in the course, just in case.
            $cache[$this->get_restoreid()] = 0;

        } else {
            // Arrived here, let's calculate the real offset.
            $cache[$this->get_restoreid()] = $setting - $original;
        }

        // Return the passed value with cached offset applied.
        return $value + $cache[$this->get_restoreid()];
    }
}

/*
 * Exception class used by all the @restore_step stuff
 */
class restore_step_exception extends base_step_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
