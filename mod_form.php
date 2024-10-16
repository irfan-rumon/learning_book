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
 * Instance add/edit form
 *
 * @package    mod_book
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
 
class mod_learningbook_mod_form extends moodleform_mod {
 
     public function definition() {
         global $CFG;
 
         $mform = $this->_form;
 
         // Adding the "general" fieldset, where all the common settings are shown.
         $mform->addElement('header', 'general', get_string('general', 'form'));
 
         // Adding the standard "name" field.
         $mform->addElement('text', 'name', get_string('learningbookname', 'learningbook'), array('size'=>'64'));
         $mform->setType('name', PARAM_TEXT);
         $mform->addRule('name', null, 'required', null, 'client');
 
         // Adding the standard "intro" and "introformat" fields.
         $this->standard_intro_elements();
 
         // Add standard elements, common to all modules.
         $this->standard_coursemodule_elements();
 
         // Add standard buttons, common to all modules.
         $this->add_action_buttons();
     }
}