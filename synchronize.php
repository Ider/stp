<?php

include_once 'src/services/synchronizer.php';
include_once 'src/services/displayer.php';

$act = $_REQUEST['act'];
$tutorials = $_REQUEST['tutorials'];

$sync = new Synchronizer();
$displayer = null;
if ($act == 'syncDatabase') {
    $sync->syncDatabase($tutorials);
    $displayer = new DatabaseOptionssDisplayer();
} else if ($act == 'syncFile') {
    $sync->syncFile($tutorials);
    $displayer = new FileOptionssDisplayer();
}

if ($displayer) {
    $displayer->show();
}