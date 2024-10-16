<?php


require_once('../../config.php');

$id = required_param('id', PARAM_INT);   // course id

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

$PAGE->set_url('/mod/learningbook/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'learningbook'));

// Get all the appropriate data.
if (!$learningbooks = get_all_instances_in_course('learningbook', $course)) {
    notice(get_string('nolearningbooks', 'learningbook'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

// Print the list of instances.
$table = new html_table();
$table->head = array(get_string('name'), get_string('description'));
$table->align = array('left', 'left');

foreach ($learningbooks as $learningbook) {
    $link = html_writer::link(
        new moodle_url('/mod/learningbook/view.php', array('id' => $learningbook->coursemodule)),
        format_string($learningbook->name)
    );
    $table->data[] = array($link, format_module_intro('learningbook', $learningbook, $learningbook->coursemodule));
}

echo html_writer::table($table);
echo $OUTPUT->footer();

// File: mod/learningbook/view.php

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);    // Course Module ID

if (!$cm = get_coursemodule_from_id('learningbook', $id)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}

if (!$learningbook = $DB->get_record('learningbook', array('id' => $cm->instance))) {
    //print_error('invalidlearningbookid', 'learningbook');
}

require_login($course, true, $cm);

$PAGE->set_url('/mod/learningbook/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($learningbook->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($learningbook->name));

// Replace this with actual content display logic
echo html_writer::tag('p', 'This is where the content of your Learning Book will be displayed.');

echo $OUTPUT->footer();