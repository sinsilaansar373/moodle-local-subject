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
require_once($CFG->dirroot . '/local/subject/subject_form.php');
require_once($CFG->dirroot . '/course/lib.php');

global $USER, $DB, $PAGE, $OUTPUT;

$context = context_system::instance();
require_login();
require_capability('local/subject:manage', $context);

$linktext = get_string('subject', 'local_subject');
$linkurl = new moodle_url('/local/subject/subject.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_title($linktext);
$PAGE->set_heading($linktext);
$PAGE->navbar->add($linktext, $linkurl);

// Initialize form
$mform = new subject_form();

if ($mform->is_cancelled()) {
    $cancelurl = new moodle_url('/local/subject/viewsubject.php');
    redirect($cancelurl);
}
else if ($formdata = $mform->get_data()) {
    $subjectdata = new stdClass();
    $academic = $formdata->academic;
    $subjectdata->sub_class = $formdata->class;
    $subjectdata->sub_division = $formdata->division;
    $subjectdata->sub_name = $formdata->subname;
    $subjectdata->sub_shortname = $formdata->shortname;
    $subjectdata->sub_description = $formdata->description;

    $classdate = $DB->get_record('academic_year', array('id' => $academic));

    // Moodle course creation
    $defaultcategory = $DB->get_field_select('course_categories', 'MIN(id)', 'parent=0');

    $course = new stdClass();
    $course->fullname = $formdata->subname;
    $course->shortname = $formdata->shortname;
    $course->summary = $formdata->description;
    $course->summaryformat = FORMAT_HTML;
    $course->format = 'topics';
    if ($classdate) {
        $course->startdate = $classdate->start_year;
        $course->enddate = $classdate->end_year;
    }
    $course->enablecompletion = 1;
    $course->showactivitydates = 1;
    $course->newsitems = 0;
    $course->category = $defaultcategory;

    $createdcourse = create_course($course);
    $subjectdata->course_id = $createdcourse->id;

    $DB->insert_record('subject', $subjectdata);
    $urlto = new moodle_url('/local/subject/subject.php');
    redirect($urlto, get_string('datasaved', 'local_subject'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Ensure JS for dynamic dropdowns is included.
// In a real plugin, this would be an AMD module.
$js = "
require(['jquery'], function($) {
    $(document).ready(function() {
        if (!$('#id_academic').val()) {
            $('#id_class').prop('disabled', true);
        }
        if (!$('#id_class').val()) {
            $('#id_division').prop('disabled', true);
        }

        $('#id_academic').change(function() {
            var val = $(this).val();
            if (val) {
                $.ajax({
                    url: M.cfg.wwwroot + '/local/subject/ajax.php',
                    data: { action: 'getclasses', academicid: val, sesskey: M.cfg.sesskey },
                    type: 'POST',
                    success: function(data) {
                        $('#id_class').prop('disabled', false).html(data);
                        $('#id_division').prop('disabled', true).html('<option value=\"\">---- Select Division ----</option>');
                    }
                });
            } else {
                $('#id_class').prop('disabled', true);
                $('#id_division').prop('disabled', true);
            }
        });

        $('#id_class').change(function() {
            var val = $(this).val();
            if (val) {
                $.ajax({
                    url: M.cfg.wwwroot + '/local/subject/ajax.php',
                    data: { action: 'getdivisions', classid: val, sesskey: M.cfg.sesskey },
                    type: 'POST',
                    success: function(data) {
                        $('#id_division').prop('disabled', false).html(data);
                    }
                });
            } else {
                $('#id_division').prop('disabled', true);
            }
        });
    });
});
";
$PAGE->requires->js_amd_inline($js);

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();