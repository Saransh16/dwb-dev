<?php

define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/scripts/auto_moodle_admin/params_config.php');
require_once(CLASS_PATH . 'cohort/cohort_creator.php');
require_once(CLASS_PATH . 'csv/csv_reader.php');
require_once(LOG_PROCESSOR_PATH . 'log_processor.php');

$csvFile = $CFG->dirroot . '/schools.csv';
$expectedHeaders = ['school_name'];

$logProcessor = new \scripts\auto_moodle_admin\log\LogProcessor('create_cohorts.log');
$cohortCreator = new \scripts\auto_moodle_admin\classes\cohort\CohortCreator($DB, $logProcessor);
$csvReader = new \scripts\auto_moodle_admin\classes\csv\CsvReader();

$csvData = $csvReader->readCsvFile($csvFile, $expectedHeaders);

// ToDo : Add in more feedback to this script.

foreach ($csvData as $data) {
    $cohortCreator->create_cohorts($data);
}
