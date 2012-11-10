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
    $containerClass = 'tutorial_entris_view';
} else {
    $displayer = new TutorialListDisplayer($_SERVER['PHP_SELF']);
    $containerId = 'tutorials_list_container';  
    $containerClass = 'tutorials_list_container';
}

?>


<html>
<head>
    <title>
<?php 
    if ($showFrame) {
        echo Util::decodeFileName($tutorialName);
    } else {
       echo 'Tutorial List';
    }
?>
    </title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>

<?php
    require_once 'menu.php';

    echo '<div id="'.$containerId.'" class="'.$containerClass.'">';

    if (!$displayer->generate()) {
        $showFrame = false;
        $displayer = new ErrorDisplayer();
    }

    $displayer->show();

    if ($showFrame) {
        $frame = sprintf('<iframe id="tutorial_frame" class="tutorial_frame" name="%s" src="%s"></iframe>'
            , $displayer->frameName, $displayer->rootEntry->link);
        echo $frame;
    }

    echo '</div>';
?>
</body>
</html>