<?php

global $DB;

require('../config.php');

//var_dump($_GET['subject']);
//die;

//assessment year is required for both the situation. 
if (!(isset($_GET['subject'])) || !(isset($_GET['assessment_grade']))) {
    $redirect_url = 'https://dwb-dev2.india.benesse.com/my/courses.php';
    return redirect($redirect_url);
}

$subject_code = strtolower($_GET['subject']) == 'maths' ? 'M' : 'S';

$course_id_number = $_GET['assessment_year'].'_'.$_GET['assessment_cycle'].'_'.$subject_code.'_'.$_GET['assessment_grade'].'_'.$_GET['school_code'];

$course = $DB->get_record('course', ['idnumber' => $course_id_number], 'id');

$redirect_url = 'https://dwb-dev2.india.benesse.com/course/view.php?id='.$course->id;

return redirect($redirect_url);

// https://dwb-dev2.india.benesse.com/keybo/index.php?school_code=HR000001&assessment_year=2023&assessment_grade=10&assessment_cycle=1&subject=Maths
