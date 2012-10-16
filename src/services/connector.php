<?php
include_once 'src/config.php';
include_once 'src/utilities/util.php';

class FileConnector {

    private $tutorialName;
    function __construct($tutorialName = '') {
        $this->tutorialName = $tutorialName;
    }

    public function loadEntries() {
        $filePath = CONTENT_DIR . $this->tutorialName;
        $content = file_get_contents($filePath);
        if ($content === false) {
            Util::addError('No tutorial file named: ' . $this->tutorialName);
            return null;
        }
        
        return json_decode($content);
    }

    public function saveEntries($rootEntry) {
        $content = json_encode($rootEntry);
        $bytes = file_put_contents(CONTENT_DIR . $this->tutorialName, $content);
        return $bytes;
    }
}


class DatabaseConnector{
    public function loadEntry() {
       return null;
    }
}