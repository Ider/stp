<?php

include_once 'src/services/connector.php';
include_once 'src/services/displayer.php';
include_once 'src/services/starter.php';
include_once 'src/config.php';
include_once 'src/utilities/response.php';


$act = $_REQUEST['act'];

$tutorialName = $_REQUEST['tutorialName'];
switch ($act) {
    case 'validateName':
        Starter::validateName($tutorialName);
        break;    
    case 'startTutorial':
        $subContent = $_REQUEST['subContent'];
        $mainEntry = $_REQUEST['mainEntry'];
        $mainURL = $_REQUEST['mainURL'];
        
        $rootEntry = Starter::getTutorialEntry($tutorialName, $mainEntry, $mainURL, $subContent);
        if (!$rootEntry) break;

        $connector = new FileConnector($tutorialName);
        $connector->saveEntries($rootEntry);
        break;
    
    case 'removeTutorial':        
        FileConnector::removeTutorial($tutorialName);
        break;

    default:
        Util::addError('Cannot response to act: '.$act);
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







