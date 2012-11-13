<?php
include_once 'src/services/displayer.php';
include_once 'src/utilities/util.php';

$showFrame = false;
if (!isset($_GET["tutorial"])) {
    header('Location: tutorialsview.php');
    exit();
}
$tutorialName = $_GET["tutorial"];
?>


<html>
<head>
    <title>
<?php 
    echo Util::decodeFileName($tutorialName);
?>
    </title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>

<?php
    require_once 'menu.php';
    $displayer = new TutorialViewDisplayer($tutorialName);
    $showFrame = true;
    
    echo '<div id="tutorial_view_container" class="tutorial_entris_view">';

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