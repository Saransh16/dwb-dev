<?php

define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/scripts/auto_moodle_admin/params_config.php');
require_once(CLASS_PATH . 'user/user_creator.php');
require_once(CLASS_PATH . 'csv/csv_reader.php');
require_once(LOG_PROCESSOR_PATH . 'log_processor.php');
require_once($CFG->dirroot . '/cohort/lib.php');

$csvFile = $CFG->dirroot . '/file.csv';
$expectedHeaders = ['first_name', 'last_name', 'email', 'school_name', 'grade', 'section'];

$logProcessor = new \scripts\auto_moodle_admin\log\LogProcessor('create_users.log');
$cohortCreator = new \scripts\auto_moodle_admin\classes\user\UserCreator($DB, $logProcessor);
$csvReader = new \scripts\auto_moodle_admin\classes\csv\CsvReader();

$csvData = $csvReader->readCsvFile($csvFile, $expectedHeaders);

foreach ($csvData as $data) {
    $cohortCreator->create_user($data);
}
