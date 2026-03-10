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

class subject_form extends moodleform
{

    public function definition()
    {
        global $DB, $SESSION;

        $mform = $this->_form;

        $school_id = isset($SESSION->schoolid) ? $SESSION->schoolid : 0; // Fallback if not set

        $mform->addElement('header', 'subjectheader', get_string('subject_creation', 'local_subject'));

        // Academic Year 
        $academic = $DB->get_records('academic_year', array('school' => $school_id));
        $options1 = array('' => get_string('select_academic', 'local_subject'));
        foreach ($academic as $academics) {
            $timestart1 = date("d/m/Y", $academics->start_year);
            $timeend1 = date("d/m/Y", $academics->end_year);
            $options1[$academics->id] = $timestart1 . '-' . $timeend1;
        }
        $mform->addElement('select', 'academic', get_string('academic_year', 'local_subject'), $options1);
        $mform->addRule('academic', get_string('required'), 'required', null);

        // Class 
        $classes = $DB->get_records('class'); // Usually filtered by school or academic year
        $options2 = array('' => get_string('select_class', 'local_subject'));
        foreach ($classes as $class) {
            $options2[$class->id] = format_string($class->class_name);
        }
        $mform->addElement('select', 'class', get_string('class', 'local_subject'), $options2);
        $mform->addRule('class', get_string('required'), 'required', null);

        // Division 
        $divisions = $DB->get_records('division'); // Should ideally be loaded via AJAX based on Class
        $options3 = array('' => get_string('select_division', 'local_subject'));
        foreach ($divisions as $division) {
            $options3[$division->id] = format_string($division->div_name);
        }
        $mform->addElement('select', 'division', get_string('division', 'local_subject'), $options3);
        $mform->addRule('division', get_string('required'), 'required', null);

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

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    public function validation($data, $files)
    {
        global $DB;

        $errors = parent::validation($data, $files);

        // Check for duplicate subject entry in the selected class and division.
        if (!empty($data['class']) && !empty($data['division']) && !empty($data['subname'])) {
            $existingSubject = $DB->get_record('subject', array(
                'sub_class' => $data['class'],
                'sub_division' => $data['division'],
                'sub_name' => $data['subname']
            ));

            if ($existingSubject) {
                $errors['subname'] = get_string('subject_exists', 'local_subject');
            }
        }

        return $errors;
    }
}