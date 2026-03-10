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
 * Form for subject creation
 *
 * @package    local_subject
 * @copyright  2022 Your Name <you@example.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class editsubject_form extends moodleform
{

    public function definition()
    {
        global $DB;

        $mform = $this->_form;

        $mform->addElement('header', 'subjectheader', get_string('edit_subject', 'local_subject'));

        $id = optional_param('id', 0, PARAM_INT);
        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        // Academic Year 
        $academic = $DB->get_records('academic_year');
        $options1 = array('' => get_string('select_academic', 'local_subject'));
        foreach ($academic as $academic1) {
            $timestart1 = date("d/m/Y", $academic1->start_year);
            $timeend1 = date("d/m/Y", $academic1->end_year);
            $options1[$academic1->id] = $timestart1 . '-' . $timeend1;
        }

        $mform->addElement('select', 'academicyear', get_string('academic_year', 'local_subject'), $options1);
        $mform->freeze('academicyear'); // Moodle way to disable a form field

        // Class 
        $classes = $DB->get_records('class');
        $options1 = array('' => get_string('select_class', 'local_subject'));
        foreach ($classes as $class) {
            $options1[$class->id] = format_string($class->class_name);
        }
        $mform->addElement('select', 'class', get_string('class', 'local_subject'), $options1);
        $mform->freeze('class');

        // Division 
        $divisions = $DB->get_records('division');
        $options2 = array('' => get_string('select_division', 'local_subject'));
        foreach ($divisions as $division) {
            $options2[$division->id] = format_string($division->div_name);
        }
        $mform->addElement('select', 'division', get_string('division', 'local_subject'), $options2);
        $mform->freeze('division');

        // Subject name
        $mform->addElement('text', 'subname', get_string('subject_name', 'local_subject'), array('size' => '30'));
        $mform->setType('subname', PARAM_TEXT);
        $mform->addRule('subname', get_string('subnamemissing', 'local_subject'), 'required', null);

        // Subject short name
        $mform->addElement('text', 'shortname', get_string('subject_shortname', 'local_subject'), array('size' => '30'));
        $mform->setType('shortname', PARAM_TEXT);
        $mform->addRule('shortname', get_string('shortnamemissing', 'local_subject'), 'required', null);

        // Description 
        $mform->addElement('textarea', 'description', get_string('description'), 'wrap="virtual" rows="5" cols="45"');
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', get_string('descriptionmissing', 'local_subject'), 'required', null);

        // Set defaults if in edit mode
        if ($id) {
            $sql = "SELECT s.*, c.fullname, c.shortname AS course_shortname, c.summary AS summary
                      FROM {subject} s
                      JOIN {course} c ON s.course_id = c.id
                     WHERE s.course_id = :courseid";
            if ($model = $DB->get_record_sql($sql, array('courseid' => $id))) {
                if ($class_record = $DB->get_record('class', array('id' => $model->sub_class))) {
                    $mform->setDefault('academicyear', $class_record->academic_id);
                }
                $mform->setDefault('id', $model->id);
                $mform->setDefault('class', $model->sub_class);
                $mform->setDefault('division', $model->sub_division);
                $mform->setDefault('subname', $model->sub_name);
                $mform->setDefault('shortname', $model->sub_shortname);
                $mform->setDefault('description', $model->sub_description);
            }
        }

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function get_data()
    {
        $data = parent::get_data();
        if ($data) {
        // Unfreeze fields to ensure they are available in get_data() if needed
        // However, freeze() means they won't be submitted by the user.
        // If we need the values, it's better to fetch from DB on save, 
        // but for editing, we only really update name and description.
        }
        return $data;
    }
}