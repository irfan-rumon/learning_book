<?php
// File: mod/learningbook/chapter.php

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/formslib.php');

$id = required_param('id', PARAM_INT); // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID (0 if creating a new chapter)

$cm = get_coursemodule_from_id('learningbook', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$learningbook = $DB->get_record('learningbook', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
//require_capability('mod/learningbook:managechapters', $context);

// Set up page
$PAGE->set_url('/mod/learningbook/chapter.php', array('id' => $cm->id, 'chapterid' => $chapterid));
$PAGE->set_title(format_string($learningbook->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Chapter form definition
class chapter_form extends moodleform {
    function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'title', get_string('chaptertitle', 'learningbook'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

         // Subtitle field (changed from textarea to text input)
         $mform->addElement('text', 'subtitle', get_string('chaptersubtitle', 'learningbook'));
         $mform->setType('subtitle', PARAM_TEXT);
         $mform->addRule('subtitle', null, 'required', null, 'client'); // Adding a rule to make it required (optional)
 
         // Starting page (integer input)
         $mform->addElement('text', 'starting_page', get_string('startingpage', 'learningbook'));
         $mform->setType('starting_page', PARAM_INT);
         $mform->addRule('starting_page', null, 'required', null, 'client');
 
         // Lines per page (integer input)
         $mform->addElement('text', 'lines_per_page', get_string('linesperpage', 'learningbook'));
         $mform->setType('lines_per_page', PARAM_INT);
         $mform->addRule('lines_per_page', null, 'required', null, 'client');

       
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'chapterid');
        $mform->setType('chapterid', PARAM_INT);

        $this->add_action_buttons();
    }
}

// Set up form
$chapter = new stdClass();
$chapter->id = $cm->id;
$chapter->chapterid = $chapterid;

if ($chapterid) {
    $chapter = $DB->get_record('book_chapter', array('id' => $chapterid), '*', MUST_EXIST);
    $chapter->id = $cm->id;  // Overwrite chapter id with course module id for the form
}

$mform = new chapter_form();
$mform->set_data($chapter);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/learningbook/view.php', array('id' => $cm->id)));
} else if ($fromform = $mform->get_data()) {
    if ($chapterid) {
        // Update existing chapter
        $fromform->id = $chapterid;
        $fromform->timemodified = time();
        $DB->update_record('book_chapter', $fromform);
    } else {
        // Add new chapter
        $fromform->bookid = $learningbook->id;
        $fromform->timecreated = time();
        $fromform->timemodified = $fromform->timecreated;
        $DB->insert_record('book_chapter', $fromform);
    }
    redirect(new moodle_url('/mod/learningbook/view.php', array('id' => $cm->id)));
}

// Display form
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('chapter', 'learningbook'));
$mform->display();
echo $OUTPUT->footer();