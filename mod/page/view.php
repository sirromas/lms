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

if ($roleid == 5) {

    echo "<br><div class='row-fluid' style='text-align:center;'>";

    $quizid = $nav->get_quiz_id();
    if ($quizid > 0) {
        $quizurl = "http://globalizationplus.com/lms/mod/quiz/view.php?id=$quizid";
        echo "<div class='span4' style='padding-left:12px;font-weight:bold;font-size:20px;color:black;'><img src='http://" . $_SERVER['SERVER_NAME'] . "/assets/images/checkmark.png' width='20' height='20' valign='middle'>&nbsp;<a href='$quizurl' target='_blank'>News Quiz</a></div>";
    } // end if $quizid>0

    $forumid = $nav->get_forum_id();
    if ($forumid > 0) {
        $forumurl = "http://globalizationplus.com/lms/mod/forum/view.php?id=$forumid";
        echo "<div class='span4' style='padding-left:12px;font-weight:bold;font-size:20px;color:black;'><img src='http://" . $_SERVER['SERVER_NAME'] . "/assets/images/checkmark.png' width='20' height='20' valign='middle'>&nbsp;</span><a href='$forumurl' target='_blank'>Discussion Board</a></div>";
    } // end if $forumid>0

    $glossaryid = $nav->get_glossary_id();
    if ($glossaryid > 0) {
        $glossaryurl = "http://globalizationplus.com/lms/mod/glossary/view.php?id=$glossaryid";
        echo "<div class='span4' style='padding-left:12px;font-weight:bold;font-size:20px;color:black;'><img src='http://" . $_SERVER['SERVER_NAME'] . "/assets/images/search.png' width='20' height='20' valign='middle'>&nbsp;<a href='$glossaryurl' target='_blank'>Political Dictionary</a></div>";
    } // end if 

    echo "</div>";

    echo "<div class='row-fluid' style='text-align:center;'>";
    echo "<div class='span12'><hr></div>";
    echo "</div>";
} // end if $roleid == 5

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
echo $OUTPUT->box($content, "generalbox center clearfix");


if ($roleid == 4) {
    die();
}

if ($roleid == 5) {
    if ($forumid > 0) {
        $forumurl = "http://globalizationplus.com/lms/mod/forum/view.php?id=$forumid";
        echo "<iframe src='$forumurl'  width='100%' height='600' frameBorder='0' scrolling='no'></iframe>";
    }
}

$strlastmodified = get_string("lastmodified");
echo "<div class=\"modified\">$strlastmodified: " . userdate($page->timemodified) . "</div>";

echo $OUTPUT->footer();
