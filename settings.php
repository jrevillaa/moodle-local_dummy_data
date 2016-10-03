<?php 

$settings = null;

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    
    $ADMIN->add('modules', new admin_category('categorydummy', get_string('categorytitle','local_dummy_data')));
    //generate Users
    $ADMIN->add('categorydummy', new admin_externalpage('generateusers', 
    							get_string('menutitleusers', 'local_dummy_data'), 
    							new moodle_url('/local/dummy_data/users.php')));

    //generate Courses
    $ADMIN->add('categorydummy', new admin_externalpage('generatecourses', 
    							get_string('menutitlecourses', 'local_dummy_data'), 
    							new moodle_url('/local/dummy_data/courses.php')));


}