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

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

$context = context_system::instance();

$action = required_param('action', PARAM_ALPHA);

switch ($action) {
    case 'getclasses':
        require_capability('local/subject:view', $context);
        $academicid = required_param('academicid', PARAM_INT);
        $classes = $DB->get_records('class', array('academic_id' => $academicid));

        $output = '<option value="" selected disabled>---- Select Class ----</option>';
        if ($classes) {
            foreach ($classes as $class) {
                $output .= '<option value="' . $class->id . '">' . s($class->class_name) . '</option>';
            }
        }
        echo $output;
        break;

    case 'getdivisions':
        require_capability('local/subject:view', $context);
        $classid = required_param('classid', PARAM_INT);
        $output = '<option value="" selected disabled>---- Select Division----</option>';
        if ($classid > 0) {
            $divisions = $DB->get_records('division', array('div_class' => $classid));
            if ($divisions) {
                foreach ($divisions as $division) {
                    $output .= '<option value="' . $division->id . '">' . s($division->div_name) . '</option>';
                }
            }
        }
        echo $output;
        break;

    case 'getsubjects':
        require_capability('local/subject:view', $context);
        $divisionid = required_param('divisionid', PARAM_INT);
        $output = '';
        if ($divisionid > 0) {
            $subjects = $DB->get_records('subject', array('sub_division' => $divisionid));
            if ($subjects) {
                $output .= '<table class="table table-striped table-bordered">';
                $output .= '<thead><tr><th>' . get_string('subject_name', 'local_subject') . '</th><th>' . get_string('action', 'local_subject') . '</th></tr></thead>';
                $output .= '<tbody>';
                foreach ($subjects as $sub) {
                    $editurl = new moodle_url('/local/subject/editsubject.php', array('id' => $sub->id));
                    $deleteurl = new moodle_url('/local/subject/deletesubject.php', array('id' => $sub->id, 'sesskey' => sesskey()));
                    $output .= '<tr>';
                    $output .= '<td>' . s($sub->sub_name) . '</td>';
                    $output .= '<td>';
                    $output .= '<a href="' . $editurl . '" class="btn btn-primary btn-sm">' . get_string('edit', 'local_subject') . '</a> ';
                    $output .= '<a href="' . $deleteurl . '" class="btn btn-danger btn-sm" onclick="return confirm(\'' . get_string('confirmdelete', 'local_subject') . '\');">' . get_string('delete', 'local_subject') . '</a>';
                    $output .= '</td>';
                    $output .= '</tr>';
                }
                $output .= '</tbody></table>';
            }
            else {
                $output .= '<div class="alert alert-info">' . get_string('nosubjects', 'local_subject') . '</div>';
            }
        }
        echo $output;
        break;

    default:
        die('Invalid action');
}