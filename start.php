<?php

include_once 'src/services/connector.php';
include_once 'src/services/displayer.php';
include_once 'src/services/starter.php';
include_once 'src/config.php';
include_once 'src/utilities/response.php';


$act = $_REQUEST['act'];

switch ($act) {
    case 'validateName':
        $tutorialName = $_REQUEST['tutorialName'];
        Starter::validateName($tutorialName);
        break;    
    case 'startTutorial':
        $tutorialName = $_REQUEST['tutorialName'];
        $subContent = $_REQUEST['subContent'];
        $mainEntry = $_REQUEST['mainEntry'];
        $mainURL = $_REQUEST['mainURL'];
        
        $rootEntry = Starter::getTutorialEntry($tutorialName, $mainEntry, $mainURL, $subContent);
        if (!$rootEntry) break;

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







