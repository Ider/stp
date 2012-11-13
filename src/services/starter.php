<?php
include_once 'src/config.php';
include_once 'src/utilities/util.php';
include_once 'src/models/entry.php';
include_once 'src/services/connector.php';

class Starter {
    public static function validateName($tutorialName) {
        if (!FileConnector::isValidName($tutorialName)) return false;
        if (FileConnector::hasFile(CONTENT_DIR.$tutorialName)) {
            Util::addError('File already exists.');
            return false;
        }
        return true;                    
    }

    public static function getTutorialEntry($tutorialName, $mainEntry, $mainURL, $subContent) {
        if (!self::validateName($tutorialName)) return null;

        $subEntries = TutorialEntry::getEntriesFromContent($subContent);
        if (!$subEntries) return null;

            //TODO: validate URL in future

        $rootEntry = new TutorialEntry();
        $rootEntry->text = $mainEntry;
        $rootEntry->link = $mainURL;
        $rootEntry->subEntries = $subEntries;
        return $rootEntry;
    }

}