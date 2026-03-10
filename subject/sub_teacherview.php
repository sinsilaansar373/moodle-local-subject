<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/mustache/src/Mustache/Autoloader.php');
Mustache_Autoloader::register();

$template = file_get_contents($CFG->dirroot . '/local/subject/template/subjectview.mustache');

global $class,$CFG;
$context = context_system::instance();
$linktext = "Subjects";
$linkurl = new moodle_url('/local/subject/sub_teacherview.php');
$css_link = new moodle_url('/local/css/style.css');
$logo1 = new moodle_url('/local/img/logo.svg');
$logo2 = new moodle_url('/local/img/grid.svg');
$logo3 = new moodle_url('/local/img/list.svg');
$logo4 = new moodle_url('/local/img/sub-math.jpg');
$logo5 = new moodle_url('/course/view.php?id');
$logo6 = new moodle_url('/local/img/tabler_dots.svg');
// $logo5 = new moodle_url('/course/view.php?id');

$PAGE->set_context($context);
$PAGE->set_url('/local/subject/sub_teacherview.php');
$PAGE->set_heading($linktext);
$PAGE->set_title($linktext);

echo $OUTPUT->header(); 
$current_user_id = $USER->id;
$data = array();
$rec1=$DB->get_records_sql("SELECT {course}.*
FROM {enrol} JOIN {user_enrolments} ON
{user_enrolments}.enrolid = {enrol}.id JOIN {course}
  ON {course}.id = {enrol}.courseid where {user_enrolments}.userid=$current_user_id");
$mustache = new Mustache_Engine();
foreach($rec1 as $record1){
  $id = $record1->id;
  $fullname = $record1->fullname;
  $startdate = $record1->startdate;
  $enddate = $record1->enddate;
  $summary = $record1->summary;
    $data[] = array('id' => $id,'fullname' => $fullname, 'startdate' => $startdate, 'enddate' => $enddate, 'summary' => $summary);
}
//Multi-dimentional array
// $subjects = array();
echo $mustache->render($template,['sub' => $data,'logo6'=>$logo6,'logo1'=>$logo1,'logo2'=>$logo2,'logo3'=>$logo3,'logo4'=>$logo4,'logo5'=>$logo5,'css_link'=>$css_link]);
echo $OUTPUT->footer();

  ?>

  
    

