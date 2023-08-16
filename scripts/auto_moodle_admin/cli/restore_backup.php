<?php

define('CLI_SCRIPT', true);

require_once('../../../config.php');

require_once($CFG->dirroot . '/lib/clilib.php');
require_once($CFG->dirroot . "/backup/util/includes/restore_includes.php");

list($options, $unrecognized) = cli_get_params([
    'file' => '',
    'categoryid' => '',
    'showdebugging' => false,
    'help' => false,
    'fullname' => '',
    'shortname' => '',
    'idnumber' => ''
], [
    'f' => 'file',
    'c' => 'categoryid',
    's' => 'showdebugging',
    'h' => 'help',
    'fn' => 'fullname',
    'sn' => 'shortname',
    'idnum' => 'idnumber'
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if (!$admin = get_admin()) {
    throw new \moodle_exception('noadmins');
}

if (!file_exists($options['file'])) {
    throw new \moodle_exception('filenotfound');
}

if (!$category = $DB->get_record('course_categories', ['id' => $options['categoryid']], 'id')) {
    throw new \moodle_exception('invalidcategoryid');
}

$backupdir = "restore_" . uniqid();
$path = $CFG->tempdir . DIRECTORY_SEPARATOR . "backup" . DIRECTORY_SEPARATOR . $backupdir;

cli_heading(get_string('extractingbackupfileto', 'backup', $path));

$fp = get_file_packer('application/vnd.moodle.backup');
$fp->extract_to_pathname($options['file'], $path);

cli_heading(get_string('preprocessingbackupfile'));

try {

    $fullname = $options['fullname'];
    $shortname = $options['shortname'];
    $idnumber = $options['idnumber'];

    $courseid = restore_dbops::create_new_course($fullname, $shortname, $category->id);

    $rc = new restore_controller($backupdir, $courseid, backup::INTERACTIVE_NO,
        backup::MODE_GENERAL, $admin->id, backup::TARGET_NEW_COURSE);

    $rc->execute_precheck();

    $rc->execute_plan();

    $rc->destroy();


    // Update the new course name
    $course = $DB->get_record('course', ['id' => $courseid], 'id');
    $course->fullname = $fullname;
    $course->shortname = $shortname;
    $course->idnumber = $idnumber;
    $DB->update_record('course', $course);    

} catch (Exception $e) {
    cli_heading(get_string('cleaningtempdata'));
    fulldelete($path);
    throw new \moodle_exception('generalexceptionmessage', 'error', '', $e->getMessage());
}

cli_heading(get_string('restoredcourseid', 'backup', $courseid));
exit(0);