<?php

require_once($CFG->dirroot . "/lib/clilib.php");

class BackupCreator {

    private $DB;
    private $logProcessor;

    public function __construct($DB, $logProcessor) {
        $this->DB = $DB;
        $this->logProcessor = $logProcessor;
    }

    public function execute() 
    {
        $course_ids = cli_input("Enter course IDs (comma-separated): ");

        if (empty($course_ids)) {
            echo "No course IDs provided.\n";
            exit(1);
        }

        $course_ids = explode(',', $course_ids);

        // Process the course IDs
        foreach ($course_ids as $course_id) {
            // Perform necessary operations for each course ID
            echo "Processing course ID: $course_id\n";

            $this->create_backup($course_id);
        }

    }

    private function create_backup($course_id)
    {
        $destination = BACKUP_STORAGE_PATH;

        $backup_script_path = BACKUP_SCRIPT_PATH;

        $script = "php " . $backup_script_path . 
                " --courseid=" . $course_id . 
                " --destination=" . $destination;

        try {
            $output = shell_exec($script);
            echo "$output\n";
        } catch (Exception $e) {
            $this->logProcessor->log("Course {$course_id} not backed up.");
            $this->logProcessor->handleException($e);
        }
    }
}

?>