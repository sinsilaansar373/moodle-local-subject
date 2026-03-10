<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/mustache/src/Mustache/Autoloader.php');
Mustache_Autoloader::register();

$template = file_get_contents($CFG->dirroot . '/local/subject/template/sub_view.mustache');
global $class,$CFG;
$context = context_system::instance();
require_login();
// $classid = $class->id;
$linktext = "Subjects";
$linkurl = new moodle_url('/local/subject/sub_studentview.php');
$course_view = new moodle_url('/course/view.php?id');
$css_link = new moodle_url('/local/css/style.css');
$logo4 = new moodle_url('/local/img/sub-math.jpg');
$logo6 = new moodle_url('/local/img/tabler_dots.svg');
$PAGE->set_context($context);

$PAGE->set_url('/local/subject/sub_studentview.php');
// $PAGE->set_heading($linktext);
// $PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

echo $OUTPUT->header();
$current_user_id = $USER->id;

$data = array();

$rec1=$DB->get_records_sql("SELECT {course}.* FROM {course} JOIN {enrol} ON
  {enrol}.courseid = {course}.id JOIN {user_enrolments}
    ON {user_enrolments}.enrolid = {enrol}.id where {user_enrolments}.userid=$current_user_id");
$mustache = new Mustache_Engine();
foreach($rec1 as $record1){
    $id = $record1->id;
    $fullname = $record1->fullname;
    $startdate = $record1->startdate;
    $enddate = $record1->enddate;
    $summary = $record1->summary;
    $teacher_assignments = $DB->get_records_sql("SELECT * FROM {teacher_assign} WHERE t_subject = ?", array($id));
    // print_r($teacher_assignments);exit();
    foreach ($teacher_assignments as $teacher_assignment) {
        $teacher1 = $teacher_assignment->user_id;
        // print_r($teacher1);
        $teacher_info = $DB->get_record_sql("SELECT * FROM {teacher} WHERE user_id = ?", array($teacher1));
        $teachername = $teacher_info->t_fname.''.$teacher_info->t_mname.' '.$teacher_info->t_lname;
            // print_r($teachername);
        // Now you can use $teachername for further processing.
    }
    $data[] = array('id' => $id,'fullname' => $fullname, 'startdate' => $startdate, 'enddate' => $enddate, 'summary' => $summary ,'teacher'=>$teachername);
}
echo $mustache->render($template,['sub' => $data,'course_view'=>$course_view,'css_link'=>$css_link,'logo4'=>$logo4,'logo6'=>$logo6,'empty_course'=>!empty($rec1)]);
  ?>

  <?php
    echo $OUTPUT->footer();
    ?>
