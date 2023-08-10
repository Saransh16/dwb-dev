<?php
defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_redirect_students_external' => array(
        'classname' => 'local_keybo_plugin_external',
        'methodname' => 'redirect_students',
        'classpath'   => 'local/keybo_plugin/externallib.php',
        'description' => 'Redirect the user to a specific course',
        'type' => 'write',
        'ajax' => true
    )
);

$services = array(
    'Redirect Students To Course' => array(
        'functions' => array('local_redirect_students_external'),
        'restrictedusers' => 0,
        'enabled' => 1
    )
);