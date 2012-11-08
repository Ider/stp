<?php

include_once 'src/services/connector.php';
include_once 'src/services/displayer.php';
include_once 'src/services/reviser.php';
include_once 'src/config.php';

$tutorialName = $_REQUEST['tutorial'];
$act = $_REQUEST['act'];
$entryId = $_REQUEST['entryId'];
$show = false;
if(isset($_REQUEST['show'])) {
    $show = $_REQUEST['show'];
}

$connector = new FileConnector($tutorialName);
$rootEntry = $connector->loadEntries();

$content_format = ResponseContentFormat::HTML;
$result_content = '';

if (!$rootEntry) {
    $displayer = new ErrorDisplayer();
    echo ResponseResult::create(ResponseResultState::ERROR, $displayer->layout, $content_format);
    return;
}
//$entryId = 'id_1_1_1_1_1_1_1_1'; //test

$reviser = new Reviser($rootEntry);
$revised = true;

switch ($act) {
    case 'setText':
        $val = $_REQUEST['text'];
        $reviser->setText($entryId, $val);
        break;

    case 'setLink':
        $val = $_REQUEST['link'];
        $reviser->setLink($entryId, $val);
        break;

    case 'setDescription':
        $val = $_REQUEST['description'];
        $reviser->setDescription($entryId, $val);
        break;

    case 'setAttributes':
        $val = $_REQUEST['attributes'];
        $reviser->setAttibutes($entryId, $val);
        break;

    case 'addSubEntries':
        $val = $_REQUEST['subContent'];
        $subEntry = $reviser->addSubEntries($entryId, $val);
        $displayer = new ReviseSubViewDisplayer($subEntry, $entryId);
        $displayer->generate();
        $result_content = $displayer->layout;

        break;

    default:
        $revised = false;
        break;
}

if ($show) {
    echo var_dump($rootEntry);
}

if (Util::hasErrors()) {
    $displayer = new ErrorDisplayer();
    echo ResponseResult::create(ResponseResultState::ERROR, $displayer->layout, $content_format);
    $revised = false;
}

if ($revised) {
    $connector->saveEntries($rootEntry);
}

echo ResponseResult::create(ResponseResultState::OK, $result_content, $content_format);

