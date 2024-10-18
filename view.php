<?php
// File: mod/learningbook/view.php
require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT); // Course Module ID

// Get the necessary data
if (!$cm = get_coursemodule_from_id('learningbook', $id)) {
    print_error('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}
if (!$learningbook = $DB->get_record('learningbook', array('id' => $cm->instance))) {
    print_error('invalidlearningbookid', 'learningbook');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Fetch chapters from the database
$chapters = $DB->get_records('learningbook_chapter', array('bookid' => $learningbook->id), 'id ASC');

$chapter_url = new moodle_url('/mod/learningbook/chapter.php', array('book' => $learningbook->id));

// Print the page header
$PAGE->set_url('/mod/learningbook/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($learningbook->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

// Add a button in the top right corner
echo html_writer::start_tag('div', array('class' => 'top-right-button'));
echo html_writer::tag('button', 'Add New Chapter', array(
    'class' => 'add-btn', 
    'onclick' => "window.location.href='" . $chapter_url->out(false) . "';",
));
echo html_writer::end_tag('div');

// Book sliding content
echo html_writer::start_tag('div', array('class' => 'learningbook-content'));
?>

<style>
    body {
        margin: 0;
        background-color: #ffecc6;
    }
    * {
        box-sizing: border-box;
    }
    .book-section {
        height: 70vh;
        width: 100%;
        padding: 40px 0;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .book-section > .container {
        height: 400px;
        width: 500px;
        position: relative;
        border-radius: 2%;
        perspective: 1200px;
        margin: 0 auto;
    }
    .container > .right {
        position: absolute;
        height: 100%;
        width: 50%;
        transition: 0.7s ease-in-out;
        transform-style: preserve-3d;
    }
    .book-section > .container > .right {
        right: 0;
        transform-origin: left;
        border-radius: 10px 0 0 10px;
    }
    .right > figure.front, .right > figure.back {
        margin: 0;
        height: 100%;
        width: 100%;
        position: absolute;
        left: 0;
        top: 0;
        background-size: 200%;
        background-repeat: no-repeat;
        backface-visibility: hidden;
        background-color: white;
        overflow: hidden;
        padding: 20px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }
    .right > figure.front {
        background-position: right;
        border-radius: 0 10px 10px 0;
        box-shadow: 2px 2px 15px -2px rgba(0,0,0,0.2);
    }
    .right > figure.back {
        background-position: left;
        border-radius: 10px 0 0 10px;
        box-shadow: -2px 2px 15px -2px rgba(0,0,0,0.2);
        transform: rotateY(180deg);
    }
    .flip {
        transform: rotateY(-180deg);
    }
    .flip::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        z-index: 10;
        width: 100%;
        height: 100%;
        border-radius: 0 10px 10px 0;
        background-color: rgba(0,0,0,0.1);
    }
    .book-section > button {
        border: 2px solid #ef9f00;
        background-color: transparent;
        color: #ef9f00;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        margin: 10px 5px;
        transition: 0.3s ease-in-out;
    }
    .book-section > button:focus, .book-section > button:active {
        outline: none;
    }
    .book-section > p {
        color: rgba(0,0,0,0.7);
        font-family: calibri;
        font-size: 24px;
    }
    .book-section > p > a {
        text-decoration: none;
        color: #ef9f00;
    }
    .book-section > button:hover {
        background-color: #ef9f00;
        color: #fff;
    }
    .front#cover, .back#back-cover {
        background-color: #ffcb63;
        font-family: calibri;
        text-align: left;
        padding: 30px;
    }
    .front#cover h1 {
        color: #fff;
        font-size: 24px;
        margin-bottom: 10px;
    }
    .front#cover p {
        color: rgba(0,0,0,0.8);
        font-size: 16px;
    }
    .btn-sec {
        display: flex;
        padding: 10px;
        margin: 20px;
        gap: 10px;
    }
    .btn-nav {
        display: inline-block;
        padding: 10px 25px;
        background-color: #ef9f00;
        border: none;
        color: white;
        font-size: 16px;
        font-family: 'Calibri', sans-serif;
        border-radius: 30px;
        cursor: pointer;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-nav:hover {
        background-color: #ffb33b;
        box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.3);
        transform: translateY(-2px);
    }
    .btn-nav:active {
        background-color: #e68a00;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        transform: translateY(0);
    }
    .add-btn {
        margin: 20px 0px;
        padding: 10px;
        border-radius: 10px;
        background-color: #0476D0;
        border: none;
        color: white;
        font-size: 16px;
        font-family: 'Calibri', sans-serif;
    }
    .no-overflow {
        display: none;
    }
</style>

<div class="book-section">
    <div class="container">
        <?php 
        $total_chapters = count($chapters);
        if ($total_chapters > 0) {
            $chapters_array = array_values($chapters);
            
            // Cover page (leftmost)
            echo '<div class="right">';
            echo '<figure class="back"></figure>';
            echo '<figure class="front" id="cover">';
            echo '<h1>' . format_string($learningbook->name) . '</h1>';
            echo '</figure>';
            echo '</div>';
            
            // Chapter pages
            for ($i = 0; $i < $total_chapters; $i += 2) {
                echo '<div class="right">';
                
                // Back of the page (even chapter)
                $chapter = $chapters_array[$i];
                $preview = substr(strip_tags($chapter->content), 0, 100) . '...';
                echo '<figure class="back">';
                echo '<h3>' . format_string($chapter->title) . '</h3>';
                echo $preview;
                echo '</figure>';
                
                // Front of the page (odd chapter, if exists)
                if ($i + 1 < $total_chapters) {
                    $next_chapter = $chapters_array[$i + 1];
                    $next_preview = substr(strip_tags($next_chapter->content), 0, 100) . '...';
                    echo '<figure class="front">';
                    echo '<h3>' . format_string($next_chapter->title) . '</h3>';
                    echo $next_preview;
                    echo '</figure>';
                } else {
                    // If there's no next chapter, display an empty front
                    echo '<figure class="front"></figure>';
                }
                
                echo '</div>';
            }
        } else {
            echo '<p>No chapters found for this book.</p>';
        }
        ?>
    </div>
    <div class="btn-sec">
       <button class="btn-nav" onclick="turnLeft()">Prev</button>
       <button class="btn-nav" onclick="turnRight()">Next</button>
    </div>
    <br/>
</div>

<script>
    var right = document.getElementsByClassName("right");
    var si = right.length;
    var z = 1;
    turnRight();

    function turnRight() {
        if (si >= 1) {
            si--;
        } else {
            si = right.length - 1;
            function sttmot(i) {
                setTimeout(function() {
                    right[i].style.zIndex = "auto";
                }, 300);
            }
            for (var i = 0; i < right.length; i++) {
                right[i].className = "right";
                sttmot(i);
                z = 1;
            }
        }
        right[si].classList.add("flip");
        z++;
        right[si].style.zIndex = z;
    }

    function turnLeft() {
        if (si < right.length) {
            si++;
        } else {
            si = 1;
            for (var i = right.length - 1; i > 0; i--) {
                right[i].classList.add("flip");
                right[i].style.zIndex = right.length + 1 - i;
            }
        }
        right[si - 1].className = "right";
        setTimeout(function() {
            right[si - 1].style.zIndex = "auto";
        }, 350);
    }
</script>

<?php
echo html_writer::end_tag('div');

// Finish the page
echo $OUTPUT->footer();
?>