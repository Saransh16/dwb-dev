<?php

namespace scripts\auto_moodle_admin\log;

class LogProcessor {
    private $logFile;

    public function __construct($fileName) {
        $logDir = LOG_PATH;

        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $this->logFile = $logDir . $fileName;
        $this->createLogFile();
    }

    private function createLogFile() {
        if (!file_exists($this->logFile)) {
            touch($this->logFile);
            chmod($this->logFile, 0644);
        }
    }

    public function log($message) {
        $timeStamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timeStamp}] {$message}\n";

        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    public function handleException(\Exception $e) {
        $this->log($e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    }
}
