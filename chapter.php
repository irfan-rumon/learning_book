<?php
// File: mod/learningbook/chapter.php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/learningbook/lib.php');

global $DB, $PAGE, $OUTPUT, $USER;

$cmId = required_param('cm', PARAM_INT); // Course Module ID
$courseId = required_param('course', PARAM_INT); //COURSE ID
$chapterId = optional_param('chapter', '', PARAM_INT); //OPTIONAL CHAPTER ID FOR EDITING

var_dump($cmId);


require_login();
$context = context_system::instance();



var_dump("course id"); var_dump($courseId);
var_dump("chapter id"); var_dump($chapterId);


// Prepare data for mustache template
$data = [
    'cmId' => $cmId,
    'courseId' => $courseId ,
    'chapterId' => $chapterId ,
    'view_url' => new moodle_url('/mod/learningbook/chapter.php', array('cm' => $cmId, 'course' => $courseId) )
];

echo $OUTPUT->header();


// Render the mustache template
//echo $OUTPUT->render_from_template('mod_learningbook/chapter', $data);

// Include necessary JavaScript
//$PAGE->requires->js_call_amd('local_certificate/certificate_filter', 'init', array($CFG->wwwroot));

echo $OUTPUT->footer();