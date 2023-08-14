<?php

define('CLI_SCRIPT', true);

$scripts = [
    'create_categories',
    'create_cohorts',
    'create_users'
];

$basePath = "/var/www/work/moodle-local/moodle/scripts/auto_moodle_admin/cli/";

foreach ($scripts as $script) {

    $command = "php" . " " . $basePath . $script . ".php";
    echo $command;

    $output = shell_exec($command);

    echo $output;
}