<?php

namespace scripts\auto_moodle_admin\classes\cohort;

require_once($CFG->dirroot . '/cohort/lib.php');

class CohortCreator {

    private $DB;
    private $logProcessor;

    public function __construct($DB, $logProcessor) {
        $this->DB = $DB;
        $this->logProcessor = $logProcessor;
    }

    public function create_cohorts($data) {

        $subjects = unserialize(SUBJECTS);
        $years = unserialize(YEARS);
        $terms = unserialize(TERMS);
        $grades = unserialize(GRADES);
        $roles = unserialize(ROLES);

        $grades = explode(',', $data['grades']);

        foreach ($subjects as $subject) {
            foreach ($years as $year) {
                foreach ($terms as $term) {
                    foreach ($grades as $grade) {
                        foreach ($roles as $role) {
                            $cohort = $subject . '_Grade_' . $grade . '_' . $term . '_' . $data['school_name'] . '_' . $role . '_' . $year;
                            $this->store_cohorts($cohort);
                        }
                    }
                }
            }
        }
    }

    public function store_cohorts($cohort) {
        try {
            $record = new \stdClass();
            $record->contextid = \context_system::instance()->id;
            $record->name = $cohort;
            $record->idnumber = $cohort; //same as the name of the cohort.
            $record->visible = 1;
            $cohort_id = cohort_add_cohort($record);
            $this->logProcessor->log("Cohort {$cohort} created successfully with ID {$cohort_id}");
        } catch (Exception $e) {
            $this->logProcessor->log("Cohort {$cohort} not created.");
            $this->logProcessor->handleException($e);
        }
    }
}
?>
