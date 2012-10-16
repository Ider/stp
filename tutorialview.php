<?php
include_once 'src/services/displayer.php';
include_once 'src/utilities/util.php';

$displayer = null;
$showFrame = false;
$containerId = '';
if (isset($_GET["tutorial"])) {
    $tutorialName = Util::encodeFileName($_GET["tutorial"]);
    $displayer = new TutorialViewDisplayer($tutorialName);
    $showFrame = true;
    $containerId = 'tutorial_view_container';
} else {
    $displayer = new TutorialListDisplayer($_SERVER['PHP_SELF']);
    $containerId = 'tutorial_list_container';
}

?>


<html>
<head>
    <title>
<?php 
    if ($showFrame) {
        echo str_replace('_', ' ', $tutorialName);
    } else {
       echo 'Tutorial List';
    }
?>
    </title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>

<?php
    echo '<div id="'.$containerId.'">';

    if (!$displayer->generate()) {
        $showFrame = false;
        $displayer = new ErrorDisplayer();
    }

    $displayer->show();

    if ($showFrame) {
        $frame = sprintf('<iframe id="tutorial_frame" name="%s" src="%s"></iframe>'
            , $displayer->frameName, $displayer->rootEntry->link);
        echo $frame;
    }

    echo '</div>';
?>
</body>
</html>