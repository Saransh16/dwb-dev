<?php

namespace scripts\auto_moodle_admin\classes\course;

require_once($CFG->dirroot . '/course/lib.php');

class CourseCreator {

    private $DB;
    private $logProcessor;

    public function __construct($DB, $logProcessor) {
        $this->DB = $DB;
        $this->logProcessor = $logProcessor;
    }

    public function createCoursesForCategories($data) {

        $categories = $this->get_categories();

        $this->create_courses($data, $categories);
    }

    private function get_categories() {

        $subjects = unserialize(SUBJECTS);
        $years = unserialize(YEARS);
        $terms = unserialize(TERMS);
        $grades = unserialize(GRADES);

        $categories = [];

        foreach ($subjects as $subject) {
            foreach ($years as $year) {
                foreach ($terms as $term) {
                    foreach ($grades as $grade) {

                        $idnumber = $subject . '_' . $grade;
                        $categoryId = $this->DB->get_field('course_categories', 'id', ['idnumber' => $idnumber]);

                        if($categoryId) {
                            array_push($categories, ['id' => $categoryId, 'name' => $idnumber]);
                        }
                    }
                }
            }
        }

        return $categories;
    }


    private function create_courses($schools, $categories) {

        $terms = unserialize(TERMS);
        $subjects = unserialize(SUBJECTS);

        foreach ($schools as $school) {

            $grades = explode(',', $school['grades']);

            foreach ($grades as $grade) {
                foreach ($subjects as $subject) {
                    foreach($terms as $term) {
                        foreach($categories as $category) {
                            $name = $subject . '_Grade_' . $grade;
                            if($category['name'] == $name) {
                                $course_path = "/var/moodledata/backups/backup-science-g9.mbz";

                                if(strpos($course_path, strtolower($subject))) {

                                    if(strpos($course_path, '-g'.$grade)) {

                                        try {
                                            $fname = $category['name'] . '_' . $term . '_' . str_replace(" ", "_", $school['school_short_name']);
                                            $sname = $category['name'] . '_' . $term . '_' . $school['school_code'];
                                            $subject_code = $subject == 'Maths' ? 'M' : 'S';

echo "$fname\n";
echo "$sname\n";
//die;
                                            // Todo: change the below hard coded term for 2nd cycle.
                                            $idnumber = '2023_1_' . $subject_code . '_' .$grade . '_'. $school['school_code'];

                                            $command = "php ".RESTORE_BACKUP_SCRIPT_PATH .
                                                    " --file=" . $course_path .
                                                    " --categoryid=" . $category['id'] .
                                                    ' --fullname=' . $fname .
                                                    ' --shortname=' . $sname .
                                                    ' --idnumber=' . $idnumber;

var_dump($command);
//die;

                                            $output = shell_exec($command);
                                            echo "$output\n";
                                            $this->logProcessor->log("Created course: {$fname}");

                                        } catch (\Exception $e) {
var_dump($e);
                                            $this->logProcessor->log("Failed course: {$fname}");
                                            $this->logProcessor->log("Failed to create course {$e}");
                                            $this->logProcessor->handleException($e);
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
//die;
    }
}
