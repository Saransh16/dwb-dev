<?php

namespace scripts\auto_moodle_admin\classes\user;

// require_once($CFG->dirroot . '/config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/lib/moodlelib.php');

class UserCreator {

    private $DB;
    private $logProcessor;

    public function __construct($DB, $logProcessor) {
        $this->DB = $DB;
        $this->logProcessor = $logProcessor;
    }

    public function create_user($data) {

        $user = $this->save_user($data);

        $this->add_to_cohort($user);
    }

    private function save_user($data) {
        try {
            $user = new \stdClass();
            $user->firstname = $data['first_name'];
            $user->lastname = $data['last_name'];
            $user->email = $data['email'];
            $user->school = $data['school_name'];
            $user->grade = $data['grade'];
            $user->section = $data['section'];
            $user->country = 'IN';

            // Generating a unique username using first name, last name, and school name
            $user->username = strtolower($user->firstname) . "_" . strtolower($user->lastname) . "_" . strtolower(str_replace(' ', '_', $user->school));

            // Set manual accounts and force password change
            $user->auth = 'manual';
            $user->password = generate_password(10);
            $user->confirmed = 1;
            $user->suspended = 0;
            $user->preferences = array(array('name' =>'auth_forcepasswordchange', 'value' => 1));

            $user = (array) $user;
            // Save the new user to the database using Moodle's API
            $newuserid = user_create_user($user, true, false);            

            $data['user_id'] = $newuserid;

            return $data;

        } catch(\Exception $e) {

            $this->logProcessor->log("User not created.");
            $this->logProcessor->handleException($e);     
            return false;       
        }
    }

    private function add_to_cohort($user) {

        $school_name = $user['school_name'];

        $subjects = unserialize(SUBJECTS);
        $years = unserialize(YEARS);
        $terms = unserialize(TERMS);
        $grades = unserialize(GRADES);
        $roles = unserialize(ROLES);

        foreach ($subjects as $subject) {
            foreach ($years as $year) {
                foreach ($terms as $term) {
                    foreach ($grades as $grade) {
                        foreach ($roles as $role) {

                            $cohort = $subject . '_' . $grade . '_' . $term . '_' . $school_name . '_' . $role . '_' . $year;

                            try {
                                $cohortId = $this->DB->get_field('cohort', 'id', array('idnumber' => $cohort));
                                if($cohortId) {
                                    cohort_add_member($cohortId, $user['user_id']);
                                    echo "User with ID " . $user['user_id'] . " added to cohort " . $cohort . "\n";
                                    // $this->logProcessor->log("Cohort {$cohort} created successfully with ID {$cohort_id}");                                    
                                }                                
                            } catch (Exception $e) {
                                $this->logProcessor->log("Cohort {$cohort} not found.");
                                $this->logProcessor->handleException($e);
                                echo $cohort . " not found\n";
                            }

                            if($cohortId) {
                                //TODO: add according to grade and school. Two separate csvs would be provided.                             
                                cohort_add_member($cohortId, $user['user_id']);
                                echo "User with ID " . $user['user_id'] . " added to cohort " . $cohort . "\n";
                            } else {

                            }

                        }
                    }
                }
            }
        }        
    }

}
