<?php

define('CLI_SCRIPT', true);

require_once('../../../config.php');

require_once($CFG->dirroot . '/scripts/auto_moodle_admin/params_config.php');
require_once(CLASS_PATH . 'category/category_creator.php');
require_once(LOG_PROCESSOR_PATH . 'log_processor.php');

$logFileName = 'create_categories.log';

$logFile = LOG_PATH . $logFileName;

$logProcessor = new \scripts\auto_moodle_admin\log\LogProcessor($logFileName);

echo "Logs for this script can be found at {$logFile}\n";

echo "Process started\n";
$categoryCreator = new CategoryCreator($DB, $logProcessor);

$categoryCreator->create_categories_hierarchy();

echo "Process finished\n";

?>
