<?php


defined('MOODLE_INTERNAL') || die();


require_once($CFG->libdir . '/formslib.php');


class dummy_data_users extends moodleform {

  function definition() {
    global $DB;
    $mform = & $this->_form;

    $mform->addElement('text', 'numberuser', get_string('textnumberuser','local_dummy_data')); // Add elements to your form
    $mform->addHelpButton('numberuser', 'textnumberuser','local_dummy_data');
    $mform->setType('numberuser', PARAM_INT);                   //Set type of element
    $mform->addRule('numberuser', get_string('required'), 'required', null, 'client');
    $mform->addRule('numberuser', get_string('onlynumbers','local_dummy_data'), 'numeric', null, 'client');
    $mform->setDefault('numberuser', 0);        //Default value
    
    $this->add_action_buttons('true',get_string('createusers','local_dummy_data'));
  }

    function validation($data, $files) {
        $errors= array();


        return $errors;
    }

}


class dummy_data_courses extends moodleform {

  function definition() {
    global $DB;
    $mform = & $this->_form;

    $mform->addElement('text', 'numbercourses', get_string('textnumbercourses','local_dummy_data'));
    $mform->addHelpButton('numbercourses', 'textnumbercourses','local_dummy_data');
    $mform->setType('numbercourses', PARAM_INT);
    $mform->addRule('numbercourses', get_string('required'), 'required', null, 'client');
    $mform->addRule('numbercourses', get_string('onlynumbers','local_dummy_data'), 'numeric', null, 'client');
    $mform->setDefault('numbercourses', 0);

    $mform->addElement('text', 'numberenrol', get_string('textnumberenrol','local_dummy_data'));
    $mform->addHelpButton('numberenrol', 'textnumberenrol','local_dummy_data');
    $mform->setType('numberenrol', PARAM_INT);
    $mform->addRule('numberenrol', get_string('onlynumbers','local_dummy_data'), 'numeric', null, 'client');
    $mform->setDefault('numberenrol', 0);

    $mform->addElement('text', 'numbersections', get_string('textnumbersections','local_dummy_data'));
    $mform->addHelpButton('numbersections', 'textnumbersections','local_dummy_data');
    $mform->setType('numbersections', PARAM_INT);
    $mform->addRule('numbersections', get_string('onlynumbers','local_dummy_data'), 'numeric', null, 'client');
    $mform->setDefault('numbersections', 0);

    $displaylist = coursecat::make_categories_list('moodle/course:create');
    $mform->addElement('select', 'category', get_string('coursecategory'), $displaylist);
    $mform->addHelpButton('category', 'coursecategory');

    
    $this->add_action_buttons('true',get_string('createcourses','local_dummy_data'));
  }

    function validation($data, $files) {
        $errors= array();

        
        return $errors;
    }

}