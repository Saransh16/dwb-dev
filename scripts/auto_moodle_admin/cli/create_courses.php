<?php

define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/scripts/auto_moodle_admin/params_config.php');
require_once(CLASS_PATH . 'course/course_creator.php');
require_once(CLASS_PATH . 'csv/csv_reader.php');
require_once(LOG_PROCESSOR_PATH . 'log_processor.php');

$csvFile = $CFG->dirroot . '/schools.csv';
$expectedHeaders = ['school_name'];

$logProcessor = new \scripts\auto_moodle_admin\log\LogProcessor('create_courses.log');
$courseCreator = new \scripts\auto_moodle_admin\classes\course\CourseCreator($DB, $logProcessor);
$csvReader = new \scripts\auto_moodle_admin\classes\csv\CsvReader();

$csvData = $csvReader->readCsvFile($csvFile, $expectedHeaders);

$schoolNames = [];

foreach ($csvData as $data) {
    array_push($schoolNames, $data['school_name']);
}

$courseCreator->createCoursesForCategories($schoolNames);