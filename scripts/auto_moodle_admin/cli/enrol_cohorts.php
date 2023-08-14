<?php

define('CLI_SCRIPT', true);

require_once('config.php');
require_once($CFG->dirroot . '/scripts/auto_moodle_admin/params_config.php');
require_once(CLASS_PATH . 'enrol/enrol_cohorts.php');
require_once(LOG_PROCESSOR_PATH . 'log_processor.php');

$logProcessor = new \scripts\auto_moodle_admin\log\LogProcessor('create_users.log');
$enrolCohort = new \scripts\auto_moodle_admin\classes\enrol\EnrolCohorts($DB, $logProcessor);

$enrolCohort->enrol();

