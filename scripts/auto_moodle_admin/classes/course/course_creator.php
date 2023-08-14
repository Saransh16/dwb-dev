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

    public function createCoursesForCategories($schoolNames) {

        $categories = $this->get_categories();

        $this->create_courses($schoolNames, $categories);
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


    private function create_courses($schoolNames, $categories) {

        $terms = unserialize(TERMS);
        
        foreach ($schoolNames as $school_name) {
            foreach($categories as $category) {
                foreach($terms as $term) {

                    // ToDo: Make changes to fetch all files from the backup directory. 
                    // TODo: Restore each course correctly to their respective location.

                    $fname = $category['name'] . '_' . $term . '_' . $school_name;
                    $sname = $school_name . '_' . $category['id'];
                    $course_path = "/var/www/work/moodle-local/moodledata/backup/backup-moodle2-course-13-dwb-10-spr23-science-20230627-0034.mbz";
                    $command = "php ".RESTORE_BACKUP_SCRIPT_PATH .
                            " --file=" . $course_path .
                            " --categoryid=" . $category['id'] . 
                            ' --fullname=' . $fname . 
                            ' --shortname=' . $sname ;
                    try {
                        $output = shell_exec($command); 
                        echo "$output\n";
                        $this->logProcessor->log("Created course: {$fname}");
                    } catch (\Exception $e) {
                        $this->logProcessor->log("Failed to create course {$e}");
                        $this->logProcessor->handleException($e);
                    }
                }
            }
        }         
    }    
}