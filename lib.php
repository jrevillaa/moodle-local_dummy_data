<?php

function dummyCreateUser($cant){
	global $CFG;

	$zero = getMaxId('user') + 1;
	$max = $cant + $zero;

	$userids = array();
	$transaction = $DB->start_delegated_transaction();
	for($i = $zero ; $i < $max ; $i++){
		$user = array();
		$user['username'] = 'dummy_user_' . $i;
		$user['password'] = 'dummy_user_' . $i;
		$user['firstname'] = $i . ' Dummy';
		$user['lastname'] = 'User';
		$user['auth'] = 'manual';
		$user['email'] = 'dummy_user_' . $i . '@dummy-data.com';
	    $user['confirmed'] = true;
	    $user['mnethostid'] = $CFG->mnet_localhost_id;

	    $user['id'] = user_create_user($user);

	    // Custom fields.
	    /*if (!empty($user['customfields'])) {
	        foreach ($user['customfields'] as $customfield) {
	            $user["profile_field_".$customfield['type']] = $customfield['value'];
	        }
	        profile_save_data((object) $user);
	    }*/

	    // Trigger event.
	    \core\event\user_created::create_from_userid($user['id'])->trigger();

	    // Preferences.
	    /*if (!empty($user['preferences'])) {
	        foreach ($user['preferences'] as $preference) {
	            set_user_preference($preference['type'], $preference['value'], $user['id']);
	        }
	    }*/
	    $userids[] = array('id' => $user['id'], 
	    				   'username' => $user['username'],
	    				   'password' => $user['password'],
	    				   'email' => $user['email'],
	    				   );
	}

	$transaction->allow_commit();
	return $userids;
	
        
}


function dummyCreateCourse($cant,$categoryid,$sections,$enrolcant){
	global $CFG, $DB;
    require_once($CFG->dirroot . "/course/lib.php");
    require_once($CFG->libdir . '/completionlib.php');
    require_once($CFG->libdir . '/enrollib.php');
    require_once($CFG->libdir . '/../user/lib.php');
    require_once($CFG->libdir . "/../lib/weblib.php");

    $zero = getMaxId('course') + 1;
	$max = $cant + $zero;

	$resultcourses = array();
	$transaction = $DB->start_delegated_transaction();
	for($i = $zero ; $i < $max ; $i++){
		$course = array();
		$course['fullname'] = $i . ' Dummy Course';
		$course['shortname'] = 'dummycourse_' . $i;
		$course['categoryid'] = $categoryid;
		$course['summary'] = (string)random_lipsum();
		
		if($sections > 0){
			$course['numsections'] = $sections;
		}

		//force visibility if ws user doesn't have the permission to set it
		$category = $DB->get_record('course_categories', array('id' => $course['categoryid']));

		$course['visible'] = $category->visible;

		$courseconfig = get_config('moodlecourse');
		if (completion_info::is_enabled_for_site()) {
		    if (!array_key_exists('enablecompletion', $course)) {
		        $course['enablecompletion'] = $courseconfig->enablecompletion;
		    }
		} else {
		    $course['enablecompletion'] = 0;
		}
		$course['category'] = $course['categoryid'];
		$course['id'] = create_course((object) $course)->id;

		$resultcourses[] = array('id' => $course['id'], 'shortname' => $course['shortname']);
    }


    
    //START ENROLS!
    foreach ($resultcourses as $key => $value) {
    	$course = get_course($value['id']);
        $enrol = enrol_get_plugin('manual');

        $context = context_course::instance($course->id);

        $roles = get_assignable_roles($context);

        if (!array_key_exists(5, $roles)) {
            throw new moodle_exception('Rol no encontrado ', 'dummy_data');
        }

        $enrolinstances = enrol_get_instances($course->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance = $courseenrolinstance;
                //throw new moodle_exception('No se pudo enlazar matrícula al curso ' . json_encode($enrolinstances), 'ucic_enrol_peoplesoft');
                break;
            }
        }

        $enrolment['timestart'] = isset($enrolment['timestart']) ? $enrolment['timestart'] : 0;
        $enrolment['timeend'] = isset($enrolment['timeend']) ? $enrolment['timeend'] : 0;
        $enrolment['status'] = (isset($enrolment['suspend']) && !empty($enrolment['suspend'])) ?
                ENROL_USER_SUSPENDED : ENROL_USER_ACTIVE;

        $users = getRandomQuery('user',$enrolcant);
        
        $enrol_users = array();
        foreach ($users as $user) {
	        $enrol->enrol_user($instance, 
	                           $user->id, 
	                           5, 
	                           $enrolment['timestart'], 
	                           $enrolment['timeend'], 
	                           $enrolment['status']);
        	$enrol_users[] = array('id' => $user->id,
        						   'name' => $user->firstname . ' ' . $user->lastname);
        }

        $resultcourses[$key]['enrols'] = $enrol_users;
    }
    $transaction->allow_commit();
    return $resultcourses;
}


function getMaxId($table){
	global $DB;
	$sql = "SELECT MAX(id) as 'maxid' FROM {" . $table . "}";
	
	return $DB->get_record_sql($sql)->maxid;
}

function getRandomQuery($table,$number){
	global $DB;
	$sql = "SELECT * FROM {" . $table . "}
			";

	if($table == 'user'){
		$sql .= " WHERE deleted = 0";
	}

	$sql .= " ORDER BY RAND() limit " . (int)$number;

	return $DB->get_records_sql($sql);
}

function random_lipsum($amount = 1, $what = 'paras', $start = 0) {
    return simplexml_load_file("http://www.lipsum.com/feed/xml?amount=$amount&what=$what&start=$start")->lipsum->__toString();
}