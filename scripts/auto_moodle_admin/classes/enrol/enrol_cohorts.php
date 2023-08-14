<?php

namespace scripts\auto_moodle_admin\classes\enrol;

require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->dirroot . '/enrol/cohort/locallib.php');

class EnrolCohorts {

    private $DB;
    private $logProcessor;

    public function __construct($DB, $logProcessor) {
        $this->DB = $DB;
        $this->logProcessor = $logProcessor;
    }

    public function enroll() {

        // Fetch all the cohorts
        $cohorts = $DB->get_records('cohort', null, '', 'id, name');

        $courses = $DB->get_records('course', null, '', 'id, fullname');

        // Iterate through each cohort
        foreach ($cohorts as $cohort) {

            foreach($courses as $course) { 
            
                $check = str_contains($cohort->name, $course->fullname);

                if($check) {

                    // Get the cohort members
                    $cohortMembers = $DB->get_records('cohort_members', ['cohortid' => $cohort->id]);

                    // Retrieve user IDs
                    $userIds = array_column($cohortMembers, 'userid');  

                    foreach ($userIds as $userId) {

                        $enrol = enrol_get_plugin('manual');
                        
                        $instances = enrol_get_instances($course->id, true);
                        $instance = null;
                        
                        foreach ($instances as $inst) {
                            if ($inst->enrol === 'manual') {
                                $instance = $inst;
                                break;
                            }
                        }

                        $roleid = str_contains($cohort->name, "Students") ? STUDENT : TEACHER;
                        
                        $enrol->enrol_user($instance, $userId, $roleid);
                    }

                    echo "Enrolled cohort with ID $cohort->id into course with ID $course->id.\n";
                }    
            }
        }

    }
}
