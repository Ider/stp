<?php

include_once 'src/services/connector.php';
include_once 'src/services/displayer.php';
include_once 'src/services/reviser.php';
include_once 'src/config.php';
include_once 'src/utilities/response.php';


$act = $_REQUEST['act'];

switch ($act) {
    case 'validateName':
        $tutorialName = $_REQUEST['tutorialName'];
        if (FileConnector::hasFile(CONTENT_DIR.$tutorialName))
            Util::addError('File already exists.');
        break;    
    case 'startTutorial':
        $tutorialName = $_REQUEST['tutorialName'];
        $subContent = $_REQUEST['subContent'];
        $mainEntry = $_REQUEST['mainEntry'];
        $mainURL = $_REQUEST['mainURL'];
        if (!FileConnector::isValidName($tutorialName)) break;
        if (FileConnector::hasFile(CONTENT_DIR.$tutorialName)) {
            Util::addError('File already exists.');
            break;
        }

        if (FileConnector::hasFile(CONTENT_DIR.$tutorialName)) {
            Util::addError('File already exists.');
            break;    
        }
        //TODO: validate URL in future

        $jsonEntry = TutorialEntry::getEntriesFromContent($subContent);
        if (!$jsonEntry) break;

        $rootEntry = new TutorialEntry();
        $rootEntry->text = $mainEntry;
        $rootEntry->link = $mainURL;
        $rootEntry->subEntries = $jsonEntry;

        $connector = new FileConnector($tutorialName);
        $connector->saveEntries($rootEntry);
        break;
    
    default:
        # code...
        break;
}

if (Util::hasErrors()) {
    echo ResponseResult::create(ResponseResultState::ERROR
                    , ResponseContentFormat::COLLECTION
                    , Util::getErrors());
    return;
}


echo ResponseResult::create(ResponseResultState::OK
                , ResponseContentFormat::PLAIN
                , "");







