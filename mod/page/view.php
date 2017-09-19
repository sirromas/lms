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
 * Page module version information
 *
 * @package mod_page
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->dirroot . '/mod/page/lib.php');
require_once($CFG->dirroot . '/mod/page/locallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once $_SERVER['DOCUMENT_ROOT'] . '/lms/custom/navigation/classes/Navigation.php';

$nav = new Navigation();
$roleid = $nav->get_user_role();

$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$p = optional_param('p', 0, PARAM_INT);  // Page instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$page = $DB->get_record('page', array('id' => $p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('page', $page->id, $page->course, false, MUST_EXIST);
} else {
    if (!$cm = get_coursemodule_from_id('page', $id)) {
        print_error('invalidcoursemodule');
    }
    $page = $DB->get_record('page', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/page:view', $context);

// Completion and trigger events.
page_view($page, $course, $cm, $context);

$PAGE->set_url('/mod/page/view.php', array('id' => $cm->id));

$options = empty($page->displayoptions) ? array() : unserialize($page->displayoptions);

if ($inpopup and $page->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname . ': ' . $page->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname . ': ' . $page->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($page);
}
echo $OUTPUT->header();

if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($page->name), 2);
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($page->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'pageintro');
        echo format_module_intro('page', $page, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($page->content, 'pluginfile.php', $context->id, 'mod_page', 'content', $page->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, $page->contentformat, $formatoptions);
$attr = array('width' => '175px');
echo $OUTPUT->box($content, "generalbox center clearfix", 'assesment', $attr);

$forumid = $nav->get_forum_id();
$glossaryid = $nav->get_glossary_id();
$grades = $nav->get_student_grades();
$quizid = $nav->get_quiz_id();

if ($roleid == 4) {
    // Show discussion board for teachers under article
    if ($forumid > 0) {
        $forumurl = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/forum/view.php?id=$forumid";
        echo "<div style='width:877px;margin:auto;'><iframe src='$forumurl' id='forumframe' width='100%' style='border:0' onload='resizeIframe(this)'></iframe></div>";
    } // end if $forumid>0
}

if ($roleid == 5) {

    if ($quizid > 0) {
        echo "<div style='width:877px;margin:auto;text-align:center;'>";
        echo "<span class='span9' style='text-align:center;margin-left:10%'><a href='https://www.newsfactsandanalysis.com/lms/mod/quiz/view.php?id=$quizid' target='_blank' style='font-weight:bold;font-size:18px;'>☞ Take the News Quiz<br><hr></a></span>";
        echo "</div>";
    } // end if 

    if ($forumid > 0) {
        $forumurl = "https://www." . $_SERVER['SERVER_NAME'] . "/lms/mod/forum/view.php?id=$forumid";
        echo "<div style='width:877px;margin:auto;'><iframe src='$forumurl' id='forumframe' width='100%' style='border:0' onload='resizeIframe(this)'></iframe></div>";
    } // end if $forumid>0
    
    /*
    echo "<div class='row-fluid' style='width:877px;margin:auto;'>";
    echo "<span style='padding-left:2%'>";
    echo "<button id='show_grades' class='btn btn-primary'>My Grades</button>";
    echo "</span>";
    echo "</div>";
    */
    
    echo "<div style='width:877px;margin:auto;text-align:center;display:none;' id='student_grades'>";
    echo "<span class='span12' style='margin-left:15%' >$grades</span>";
    echo "</div>";
} // end if $roleid == 5

echo $OUTPUT->footer();
?>


<!-- jQuery library -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

<script type="text/javascript">

    function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    }


    $(document).ready(function () {

        $('.no-overflow').css("overflow-y", "hidden");
        $('.no-overflow').css("overflow-x", "hidden");

        $('#show_grades').click(function () {
            
        }); // end of click function

    }); // end of document ready


</script>