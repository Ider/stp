<?php

include_once 'src/services/connector.php';
include_once 'src/services/displayer.php';
include_once 'src/services/reviser.php';
include_once 'src/utilities/response.php';
include_once 'src/config.php';
include_once 'security.php';

$tutorialName = $_REQUEST['tutorial'];
$act = $_REQUEST['act'];
if (isset($_REQUEST['entryId']))$entryId = $_REQUEST['entryId'];

$revised = false;
$needLoadEntry = array(
                'setText' => true,
                'setLink' => true,
                'setDescription' => true,
                'setAttributes' => true,
                'addSubEntries' => true,
                'deleteEntry' => true,
                );
if (isset($needLoadEntry[$act])) {
    $connector = new FileConnector($tutorialName);
    $rootEntry = $connector->loadEntries();

    if (!$rootEntry) {
        echo ResponseResult::create(ResponseResultState::ERROR
                        , ResponseContentFormat::COLLECTION
                        , Util::getErrors());
        return;
    }

    $reviser = new Reviser($rootEntry);
    $revised = true;
}

$result_content = '';


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
        $entry = $reviser->addSubEntries($entryId, $val);
        if ($entry) {
            $displayer = new ReviseSubViewDisplayer($entry, $entryId);
            $displayer->generate();
            $result_content = $displayer->layout;
        }
        
        break;
    case 'deleteEntry':
        $indics = explode('_', $entryId);
        $childIndex = array_pop($indics);
        $parentId = implode('_', $indics);

        $entry = $reviser->deleteEntry($parentId, $childIndex);
        $displayer = new ReviseSubViewDisplayer($entry, $parentId);
        $displayer->generate();
        $result_content = $displayer->layout;
        break;
    case 'removeTutorial':
        FileConnector::removeTutorial($tutorialName);
        break;

    default:
        Util::addError('Cannot response to act: '.$act);
        $revised = false;
        break;
}


if (Util::hasErrors()) {
    echo ResponseResult::create(ResponseResultState::ERROR
                    , ResponseContentFormat::COLLECTION
                    , Util::getErrors());
    $revised = false;
    return;
}

if ($revised) $connector->saveEntries($rootEntry);

echo ResponseResult::create(ResponseResultState::OK
    , ResponseContentFormat::HTML
    , $result_content);

