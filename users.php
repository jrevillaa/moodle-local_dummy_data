<?php
require_once("../../config.php");


global $DB, $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir.'/adminlib.php');
include('forms.php');
include('lib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());
admin_externalpage_setup('generateusers', '', null);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$main_url = new moodle_url('/local/dummy_data/users.php');
$PAGE->set_url($main_url);
$title = get_string('indexconfigurationusers','local_dummy_data');
$PAGE->set_title($title);
$PAGE->set_heading($title);
print $OUTPUT->header();

$mform_user = new dummy_data_users();

if ($mform_user->is_cancelled()) {
  $returnurl = new moodle_url('/local/dummy_data/users.php');
  redirect($returnurl);
}

$data = $mform_user->get_data();

if(!$data){
	$mform_user->display();
}else{
	$out = '';
	if($data->numberuser > 0){
		$output = dummyCreateUser($data->numberuser);
		$table = new html_table();
		$table->head = array('N°','Id','Username','Password', 'Email');
		$count = 1;
		foreach ($output as $value) {
			$table->data[] = array($count++,$value['id'], $value['username'], $value['password'], $value['email']);
		}
		$out .= html_writer::tag('h3',get_string('usercreated','local_dummy_data'));
		$out .= html_writer::table($table);
	}else{
		$out .= html_writer::tag('h3',get_string('usernocreated','local_dummy_data'));
	}

	$out .= html_writer::link($main_url,get_string('continue'),array('class'=>'btn btn-default'));
	echo $out;
}



print $OUTPUT->footer();