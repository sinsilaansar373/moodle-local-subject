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

global $DB, $CFG;

$id = required_param('id', PARAM_INT);
require_login();
require_sesskey();

$context = context_system::instance();
require_capability('local/subject:manage', $context);

$id1 = $DB->get_record('subject', array('id' => $id), 'course_id');

if ($id1) {
   if ($id1->course_id) {
      $DB->delete_records('course', array('id' => $id1->course_id));
   }
   $DB->delete_records('subject', array('id' => $id));
}

$delete = new moodle_url('/local/subject/viewsubject.php');
redirect($delete, get_string('deleted', 'local_subject'), null, \core\output\notification::NOTIFY_SUCCESS);