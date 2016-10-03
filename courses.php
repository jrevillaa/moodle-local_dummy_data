<?php
require_once("../../config.php");


global $DB, $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir.'/adminlib.php');
include('forms.php');
include('lib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
admin_externalpage_setup('generatecourses', '', null);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$main_url = new moodle_url('/local/dummy_data/courses.php');
$PAGE->set_url($main_url);
$title = get_string('indexconfigurationcourses','local_dummy_data');
$PAGE->set_title($title);
$PAGE->set_heading($title);
print $OUTPUT->header();

$mform_user = new dummy_data_courses();

if ($mform_user->is_cancelled()) {
  $returnurl = new moodle_url('/local/dummy_data/courses.php');
  redirect($returnurl);
}

$data = $mform_user->get_data();

if(!$data){
	$mform_user->display();
}else{
	/*echo "<pre>";
	print_r($data);
	echo "</pre>";*/
	$out = '';
	if($data->numbercourses > 0){
		$outpu = dummyCreateCourse($data->numbercourses, $data->category, $data->numbersections, $data->numberenrol);

		$table = new html_table();
		$table->head = array('N°','Id','Shortname Course','Enroled Users');
		$count = 1;
		foreach ($outpu as $value) {
			$enroles = '';
			foreach ($value['enrols'] as $val) {
				$enroles .= html_writer::tag('p','- ' . $val['name']);
			}
			$table->data[] = array($count++,$value['id'], $value['shortname'], $enroles);
		}
		$out .= html_writer::tag('h3',get_string('coursecreated','local_dummy_data'));
		$out .= html_writer::table($table);
	}else{
		$out .= html_writer::tag('h3',get_string('coursenocreated','local_dummy_data'));
	}

	$out .= html_writer::link($main_url,get_string('continue'),array('class'=>'btn btn-default'));
	echo $out;
}



print $OUTPUT->footer();