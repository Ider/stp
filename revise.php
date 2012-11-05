<?php

include_once 'src/services/connector.php';
include_once 'src/services/displayer.php';
include_once 'src/services/reviser.php';
include_once 'src/config.php';

$tutorialName = $_REQUEST['tutorial'];
$act = $_REQUEST['act'];
$entryId = $_REQUEST['entryId'];
$display = false;
if(isset($_REQUEST['display'])) {
    $display = $_REQUEST['display'];
}

$connector = new FileConnector($tutorialName);
$rootEntry = $connector->loadEntries();

if (!$rootEntry) {
    $display = new ErrorDisplayer();
    $display->show();
    return;
}

$reviser = new Reviser($rootEntry);
$revised = false;

if ($act == 'setText') {
    $val = $_REQUEST['text'];
    $reviser->setText($entryId, $val);
    $revised = true;
} else if ($act == 'setLink') {
    error_log($act);
    $val = $_REQUEST['link'];
    $reviser->setLink($entryId, $val);
    $revised = true;
} else if ($act == 'setDescription') {
    error_log($act);
    $val = $_REQUEST['description'];
    $reviser->setDescription($entryId, $val);
    $revised = true;
} else if ($act == 'setAttributes') {
    $val = $_REQUEST['attributes'];
    $reviser->setAttibutes($entryId, $val);
    $revised = true;
}

if ($display) {
    echo var_dump($rootEntry);
}

if ($revised) {
    $connector->saveEntries($rootEntry);
}


