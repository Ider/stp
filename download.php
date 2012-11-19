<?php

include_once 'src/services/connector.php';
include_once 'src/utilities/attachment.php';
include_once 'src/services/displayer.php';

echo '';
$tutorialName = $_REQUEST['tutorial'];
$connector = new FileConnector($tutorialName);
$rootEntry = $connector->loadEntries();

if (!$rootEntry) {
    echo '<link rel="stylesheet" type="text/css" href="./css/style.css" />';
    $displayer = new ErrorDisplayer();
    $displayer->show();
    return;
}

$attachment = new Attachment($tutorialName, 'txt', json_encode($rootEntry));
$attachment->attach();