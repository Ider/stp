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
                'arrangeEntry' => true,
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
            $displayer->setDeletable($GLOBALS['deletable']);
            $displayer->generate();
            $result_content = $displayer->layout;
        }
        
        break;
//TODO: deleteEntry and arrayEntry should be exclusive, to prevent from conflict
    case 'deleteEntry':
        $indics = explode('_', $entryId);
        $childIndex = array_pop($indics);
        $parentId = implode('_', $indics);

        $entry = $reviser->deleteEntry($parentId, $childIndex);
        $displayer = new ReviseSubViewDisplayer($entry, $parentId);
        $displayer->setDeletable($GLOBALS['deletable']);
        $displayer->generate();
        $result_content = $displayer->layout;
        break;
    case 'arrangeEntry':
        $newEntryId = $_REQUEST['newEntryId'];

        $oldIndics = explode('_', $entryId);
        $oldIndex = array_pop($oldIndics);
        $oldParentId = implode('_', $oldIndics);

        $newIndics = explode('_', $newEntryId);
        $newIndex = array_pop($newIndics);
        $newParentId = implode('_', $newIndics);

        $entryArray = $reviser->arrangeEntry($oldParentId, $oldIndex, $newParentId, $newIndex);

        //TODO: check containing relationship
        $displayer1 = new ReviseSubViewDisplayer($entryArray[0], $oldParentId);
        $displayer1->setDeletable($GLOBALS['deletable']);
        $displayer1->generate();

        $displayer2 = new ReviseSubViewDisplayer($entryArray[1], $newParentId);
        $displayer2->setDeletable($GLOBALS['deletable']);
        $displayer2->generate();
        $result_content = array($displayer1->layout, $displayer2->layout);

        //$revised = false;

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

