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

require_once(__DIR__ . '/../../config.php');

global $USER, $SESSION, $DB, $PAGE, $OUTPUT;

$context = context_system::instance();
require_login();
require_capability('local/subject:view', $context);

$linktext = get_string('view_subjects', 'local_subject');
$linkurl = new moodle_url('/local/subject/viewsubject.php');
$subjectaddurl = new moodle_url('/local/subject/subject.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);
$PAGE->navbar->add($linktext, $linkurl);

// Typically, $schoolid is configured per user or system. Use 0 as default if not configured.
$school_id = isset($SESSION->schoolid) ? $SESSION->schoolid : 0;
$academic = $DB->get_records('academic_year', array('school' => $school_id));

$options1 = array();
$options1[] = array('value' => '', 'label' => get_string('select_academic_year', 'local_subject'));
if ($academic) {
    foreach ($academic as $academic1) {
        $timestart1 = date("d/m/Y", $academic1->start_year);
        $timeend1 = date("d/m/Y", $academic1->end_year);
        $options1[] = array('value' => $academic1->id, 'label' => $timestart1 . ' -- ' . $timeend1);
    }
}

$templateData = array(
    'startYearOptions' => $options1,
    'subjectaddurl' => $subjectaddurl->out(false)
);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_subject/subject_view', $templateData);
echo $OUTPUT->footer();