<?php

namespace scripts\auto_moodle_admin\classes\csv;

class CsvReader {

    public function readCsvFile($filename, $expectedHeaders) {
        if (!file_exists($filename) || !is_readable($filename)) {
            die("File not found or not readable");
        }

        $handle = fopen($filename, 'r');
        if ($handle === false) {
            die("Error opening the file");
        }

        $headers = fgetcsv($handle, 1000, ",");
        foreach ($expectedHeaders as $header) {
            if (!in_array($header, $headers)) {
                die("CSV file does not contain the expected headers");
            }
        }

        $csvData = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = $data[$index];
            }
            $csvData[] = $row;
        }

        fclose($handle);

        return $csvData;
    }
}
