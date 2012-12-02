<?php

include_once 'src/services/connector.php';
include_once 'src/utilities/attachment.php';
include_once 'src/services/displayer.php';
include_once 'src/models/entry.php';


function assertError() {
    if (Util::hasErrors()) {
        echo '<link rel="stylesheet" type="text/css" href="./css/style.css" />';
        $displayer = new ErrorDisplayer();
        $displayer->show();
        exit();
    }
}

$tutorialName = $_REQUEST['tutorial'];
$connector = new FileConnector($tutorialName);
$rootEntry = $connector->loadEntries();

assertError();

if (isset($_REQUEST['entryId'])) {
    $entryId = $_REQUEST['entryId'];
    $indics = explode('_', $entryId);
    array_shift($indics);
    $rootEntry = TutorialEntry::findByIndexOf($rootEntry, $indics);

    if ($rootEntry == null) {
        Util::addError('Cannot find entry with ID: '.$entryId);
    }
    assertError();
}

$attachmentName = $rootEntry->text;
if (empty($attachmentName)) $attachmentName = $tutorialName;


$attachment = new Attachment($rootEntry->text, 'txt', json_encode($rootEntry));
$attachment->attach();