<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/mustache/src/Mustache/Autoloader.php');
Mustache_Autoloader::register();

$template = file_get_contents($CFG->dirroot . '/local/subject/template/parentview.mustache');

global $class, $CFG;
$context = context_system::instance();
require_login();

$linktext = "Subjects";
$linkurl = new moodle_url('/local/subject/sub_studentview.php');
$course_view = new moodle_url('/course/view.php?id');
$css_link = new moodle_url('/local/css/style.css');
$logo4 = new moodle_url('/local/img/sub-math.jpg');
$logo6 = new moodle_url('/local/img/tabler_dots.svg');

$PAGE->set_context($context);
$PAGE->set_url('/local/subject/sub_parentview.php');
$PAGE->set_heading($linktext);
$PAGE->set_title($linktext);

echo $OUTPUT->header();

$current_user_id = $USER->id;
$user1= optional_param('id', 0, PARAM_INT);

// Assuming there is a table named mdl_parent with a field user_id
$parent = $DB->get_record('parent', array('child_id' => $user1));
if ($parent) {
$rec1 = $DB->get_records_sql("
    SELECT {course}.*
    FROM {course}
    JOIN {enrol} ON {enrol}.courseid = {course}.id
    JOIN {user_enrolments} ON {user_enrolments}.enrolid = {enrol}.id
    JOIN {student} ON {student}.user_id = {user_enrolments}.userid
    JOIN {parent} ON {parent}.child_id = {student}.user_id
    WHERE {parent}.child_id = :current_user_id
", ['current_user_id' =>  $user1]);


//print_r($rec1);exit();

$data = array();
$mustache = new Mustache_Engine();

foreach ($rec1 as $record1) {
    $id = $record1->id;
    // print_r($id);
    $fullname = $record1->fullname;
    $startdate = $record1->startdate;
    $enddate = $record1->enddate;
    $summary = $record1->summary;
    $teacher_assignments = $DB->get_records_sql("SELECT * FROM {teacher_assign} WHERE t_subject = ?", array($id));
    // print_r($teacher_assignments);
    foreach ($teacher_assignments as $teacher_assignment) {
        $teacher1 = $teacher_assignment->user_id;
        // print_r($teacher1);
        $teacher_info = $DB->get_record_sql("SELECT * FROM {teacher} WHERE user_id = ?", array($teacher1));
        $teachername = $teacher_info->t_fname.''.$teacher_info->t_mname.' '.$teacher_info->t_lname;
            //  print_r($teachername);
        // Now you can use $teachername for further processing.
    }
// print_r($teachername);exit();


    $data[] = array('id' => $id, 'fullname' => $fullname, 'startdate' => $startdate, 'enddate' => $enddate, 'summary' => $summary ,'teacher'=>$teachername);
}

echo $mustache->render($template, ['sub' => $data, 'course_view' => $course_view ,'css_link'=>$css_link,'logo4'=>$logo4,'logo6'=>$logo6]);
} else {
    echo "Parent not found for the current user.";
}
echo $OUTPUT->footer();
?>
