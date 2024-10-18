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
 * Book module core interaction API
 *
 * @package    mod_learningbook
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Add Learning Book instance.
 * Called when a new Learning Book is added.
 *
 * @param stdClass $data
 * @param mod_learningbook_mod_form $mform
 * @return int new learningbook instance id
 */
function learningbook_add_instance(stdClass $learningbook, mod_learningbook_mod_form $mform = null) {
    global $DB;

    $learningbook->timecreated = time();
    $learningbook->timemodified = $learningbook->timecreated;
    $learningbook->cm = $learningbook->coursemodule;

   
    if (!isset($learningbook->intro)) {
        $learningbook->intro = '';
    }
    if (!isset($learningbook->introformat)) {
        $learningbook->introformat = FORMAT_HTML;
    }

 
    $learningbook->id = $DB->insert_record('learningbook', $learningbook);

    
    $DB->set_field('course_modules', 'instance', $learningbook->id, array('id' => $cm));


    return $learningbook->id;
}

function learningbook_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

