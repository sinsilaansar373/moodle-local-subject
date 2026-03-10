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

require(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/subject/editsubject_form.php');
require_once($CFG->dirroot . '/course/lib.php');

global $DB, $USER, $PAGE, $OUTPUT;

$context = context_system::instance();
require_login();
require_capability('local/subject:manage', $context);

$linktext = get_string('edit_subject', 'local_subject');
$linkurl = new moodle_url('/local/subject/editsubject.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);
$PAGE->navbar->add($linktext, $linkurl);

// Initialize form
$mform = new editsubject_form();

if ($mform->is_cancelled()) {
    $cancelurl = new moodle_url('/local/subject/viewsubject.php');
    redirect($cancelurl);
}
else if ($formdata = $mform->get_data()) {
    $subjectdata = new stdClass();
    $subjectdata->id = $formdata->id;
    $subjectdata->sub_name = $formdata->subname;
    $subjectdata->sub_shortname = $formdata->shortname;
    $subjectdata->sub_description = $formdata->description;

    $courseid_record = $DB->get_record('subject', array('id' => $subjectdata->id), 'course_id');

    if ($courseid_record) {
        $course = new stdClass();
        $course->id = $courseid_record->course_id;
        $course->fullname = $formdata->subname;
        $course->shortname = $formdata->shortname;
        $course->summary = $formdata->description;

        update_course($course);
    }

    $DB->update_record('subject', $subjectdata);

    $urlto = new moodle_url('/local/subject/viewsubject.php');
    redirect($urlto, get_string('datasaved', 'local_subject'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();