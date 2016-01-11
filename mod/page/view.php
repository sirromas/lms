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
error_reporting(E_ALL);
ini_set('display_errors', 1);


require('../../config.php');
require_once ('../../course/courseSections.php');
require_once($CFG->dirroot . '/mod/page/locallib.php');
require_once($CFG->libdir . '/completionlib.php');


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

// Instantiate course section object to get some additional info
$cs = new courseSections($context, $COURSE->id, $USER->id);
$roleid = $cs->getCourseRoles();
$forumid = false;
$forumid = $cs->getForumId();

$quizid = false;
$quizid = $cs->getQuizId();


// Trigger module viewed event.
$event = \mod_page\event\course_module_viewed::create(array(
            'objectid' => $page->id,
            'context' => $context
        ));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('page', $page);
$event->trigger();

// Update 'viewed' state if required by completion system
require_once($CFG->libdir . '/completionlib.php');
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

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
echo $OUTPUT->box($content, "generalbox center clearfix");

$strlastmodified = get_string("lastmodified");
echo "<div class=\"modified\">$strlastmodified: " . userdate($page->timemodified) . "</div>";

/* ******************************************************************************
 *  Here should be added forum functionality directly after page * 
 * **************************************************************************** */
//echo "Role id: ".$roleid."<br/>";
if ($roleid == 5) {

    if ($forumid != false) {
        $url = 'http://' . $_SERVER['SERVER_NAME'] . '/lms/mod/forum/view.php?id=' . $forumid;
        ?>
        <iframe src="<?php echo $url; ?>" onload="this.width = screen.width * 0.9;
                this.height = screen.height;" frameBorder="0"></iframe>
        <?php
    }


    /* ******************************************************************************
     *  Here should be added link to quiz * 
     * **************************************************************************** */
    if ($quizid != false) {
        $qizurl = "http://" . $_SERVER['SERVER_NAME'] . "/lms/mod/quiz/view.php?id=" . $quizid . "";
        ?>
        <br/><br/>
        <div style="text-align: center;"><a href="<?php echo $qizurl; ?>" >Go to Quiz</a></div>
        <?php
    }
} // end if $roleid==5

?>


<?php
echo $OUTPUT->footer();
