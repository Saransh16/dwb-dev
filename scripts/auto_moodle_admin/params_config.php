<?php

//paths
define('PLUGIN_PATH', $CFG->dirroot . '/scripts/auto_moodle_admin/');
define('CLASS_PATH', PLUGIN_PATH . 'classes/');
define('CLI_PATH', PLUGIN_PATH . 'cli/');
define('LOG_PROCESSOR_PATH', PLUGIN_PATH . 'log/');
define('LOG_PATH', PLUGIN_PATH . 'logs/');
define('BACKUP_STORAGE_PATH', $CFG->dataroot . '/backup/');
define('BACKUP_SCRIPT_PATH', $CFG->dirroot . '/admin/cli/backup.php');
define('RESTORE_BACKUP_SCRIPT_PATH', CLI_PATH . 'restore_backup.php');

//variables
define('PRODUCT_TYPE', serialize(['DWB']));
define('YEARS', serialize(['AY_2023']));
define('TERMS', serialize(['1st_Cycle'])); //'2nd_Cycle'
define('GRADES', serialize(['Grade_8', 'Grade_9', 'Grade_10']));
define('SUBJECTS', serialize(['Maths', 'Science']));
define('ROLES', serialize(['Students', 'Teachers']));

//roles
define('STUDENT', 5);
define('TEACHER', 4);