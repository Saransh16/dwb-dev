<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

use core_external\external_api;

class local_keybo_plugin_external extends \external_api {

    public static function redirect_students_parameters() {
        return new \external_function_parameters(
            array(
                'school_code' => new \external_value(PARAM_TEXT, 'School code', VALUE_REQUIRED),
                'grade' => new \external_value(PARAM_TEXT, 'Student grade', VALUE_REQUIRED),
                'subject' => new \external_value(PARAM_TEXT, 'Subject name', VALUE_REQUIRED)
            )
        );
    }
    
    public static function redirect_students($school_code, $grade, $subject) {
        global $DB;


        $params = external_api::validate_parameters(self::redirect_students_parameters(), array(
            'school_code' => $school_code,
            'grade' => $grade,
            'subject' => $subject
        ));        

        $inputs = [
            'school_code' => $params['school_code'],
            'grade' => $params['grade'],
            'subject' => $params['subject']
        ];

        $subject_code = $inputs['subject'] == 'Maths' ? 'M' : 'S';
        $course_id_number = '2023_1_'.$subject_code.'_'.$inputs['grade'].'_'.$inputs['school_code'];
        $course = $DB->get_record('course', ['idnumber' => $course_id_number], 'id');

        $response = [
            'redirect_url' => 'https://dwb-dev2.india.benesse.com/course/view.php?id='.$course->id
        ];

        return $response;
    }

    public static function redirect_students_returns() {
        return new \external_single_structure(array(
            'redirect_url' => new \external_value(PARAM_TEXT, 'Redirect Url')
        ));
    }
}
