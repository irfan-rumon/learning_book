<?php
// File: mod/learningbook/chapter.php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/learningbook/lib.php');
require_once($CFG->libdir . '/formslib.php');

global $DB, $PAGE, $OUTPUT, $USER;


$bookId = required_param('book', PARAM_INT); // book ID
$chapterId = optional_param('chapter', 0, PARAM_INT); // OPTIONAL CHAPTER ID FOR EDITING

$learningbook = $DB->get_record('learningbook', array('id' => $bookId), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $learningbook->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('learningbook', $learningbook->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/learningbook/chapter.php', array('book' => $bookId, 'chapter' => $chapterId));
$PAGE->set_title(format_string($learningbook->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Define the form
class chapter_form extends moodleform {
    protected function definition() {
        global $CFG;
        $mform = $this->_form;

        $mform->addElement('text', 'title', get_string('title', 'mod_learningbook'), array('size'=>'64'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('text', 'subtitle', get_string('subtitle', 'mod_learningbook'), array('size'=>'64'));
        $mform->setType('subtitle', PARAM_TEXT);

        $editoroptions = array('subdirs'=>0, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'changeformat'=>1, 'context'=>$this->_customdata['context'], 'noclean'=>0, 'trusttext'=>0);
        $mform->addElement('editor', 'content_editor', get_string('content', 'mod_learningbook'), null, $editoroptions);
        $mform->setType('content_editor', PARAM_RAW);
        $mform->addRule('content_editor', null, 'required', null, 'client');

        $mform->addElement('hidden', 'cm', $this->_customdata['cm']);
        $mform->setType('cm', PARAM_INT);

        $mform->addElement('hidden', 'course', $this->_customdata['course']);
        $mform->setType('course', PARAM_INT);

        $mform->addElement('hidden', 'chapter', $this->_customdata['chapter']);
        $mform->setType('chapter', PARAM_INT);

        $this->add_action_buttons();
    }
}

// Instantiate form
$mform = new chapter_form(null, array('context' => $context, 'cm' => $cmId, 'course' => $courseId, 'chapter' => $chapterId));

if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present on form
    redirect(new moodle_url('/mod/learningbook/view.php', array('id' => $cmId)));
} else if ($fromform = $mform->get_data()) {
    // Handle form processing if data is submitted
    $chapter = new stdClass();
    $chapter->bookid = $cmId;
    $chapter->title = $fromform->title;
    $chapter->subtitle = $fromform->subtitle;
    $chapter->content = $fromform->content_editor['text'];
    $chapter->timemodified = time();

    //var_dump( $chapter ); die();

    if ($chapterId) {
        // Update existing chapter
        //$chapter->id = $chapterId;
       // $DB->update_record('learningbook_chapter', $chapter);
    } else {
        // Add new chapter
        $chapter->timecreated = time();
        $chapterId = $DB->insert_record('learningbook_chapter', $chapter);
    }

    // Handle files
    // $draftitemid = $fromform->content_editor['itemid'];
    // $chapter->content = file_save_draft_area_files($draftitemid, $context->id, 'mod_learningbook', 'chapter', $chapterId, array('subdirs'=>0, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1), $chapter->content);
    // $DB->update_record('learningbook_chapter', $chapter);

    redirect(new moodle_url('/mod/learningbook/view.php', array('id' => $cmId)));
} else {
    // Form is being displayed for the first time or there were errors
    if ($chapterId) {
        // Editing existing chapter
        $chapter = $DB->get_record('learningbook_chapter', array('id' => $chapterId), '*', MUST_EXIST);
        $chapter = file_prepare_standard_editor($chapter, 'content', array('subdirs'=>0, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'changeformat'=>1, 'context'=>$context, 'noclean'=>0, 'trusttext'=>0), $context, 'mod_learningbook', 'chapter', $chapterId);
        $mform->set_data($chapter);
    }
}

echo $OUTPUT->header();


$mform->display();

echo $OUTPUT->footer();

?>

<!-- Move the style outside the PHP block -->
<style>
    .no-overflow {
        display: none;
    }
</style>