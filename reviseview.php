<?php
include_once 'src/services/displayer.php';
include_once 'src/utilities/util.php';

$displayer = null;
$showPanel = false;
if (isset($_GET["tutorial"])) {
    $tutorialName = Util::encodeFileName($_GET["tutorial"]);
    $displayer = new ReviseViewDisplayer($tutorialName);
    $showPanel = true;
    $containerId = 'revise_view_container';
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
    if ($showPanel) {
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
    require_once './menu.php';
    echo '<div id="'.$containerId.'" class="'.$containerClass.'">';

    if (!$displayer->generate()) {
        $showPanel = false;
        $displayer = new ErrorDisplayer();
    }

    $displayer->show();

    echo '</div>';
?>

<?php if ($showPanel) { ?>

<div id="actionPanel" class="action_panel">
    <div id="importance_selection"> <span>Importance : </span>
        <input name="importance" type="radio" id="trivia" value="trivia" /><label for="trivia">trivia</label>
        <input name="importance" type="radio" id="minor"  value="minor" /><label for="minor">minor</label>
        <input name="importance" type="radio" id="normal"  value="normal" /><label for="normal">normal</label>
        <input name="importance" type="radio" id="major"  value="major" /><label for="major">major</label>
        <input name="importance" type="radio" id="vital"  value="vital" /><label for="vital">vital</label>
    </div> <br />
    <div id="reading_selection"> <span>Reading: </span>
        <input name="reading" type="radio" id="unread" value="unread" /><label for="unread">unread</label>
        <input name="reading" type="radio" id="glance" value="glance" /><label for="glance">glance</label>
        <input name="reading" type="radio" id="scan"  value="scan" /><label for="scan">scan</label>
        <input name="reading" type="radio" id="comprenhend"  value="comprenhend" /><label for="comprenhend">comprenhend</label>
    </div>
</div>
<span id="test" style="position:fixed; top:10px; right:30px;"></span>

<div id="reviseService">
    <input id="entryId" type="hidden" name="entryId" />
    <input id="act" type="hidden" name="act" value="setAttributes"/>
    <input id="attributes" type="hidden" name="attributes" value=""/>
<?php echo '<input id="tutorialName" type="hidden" name="tutorialName" value="' .$tutorialName. '"/>' ?>
</div>
<script type="text/javascript">
function sendServiceQuest() {
    var parameters = {};
    parameters.tutorial = $('#tutorialName').val();
    parameters.act = $('#act').val();
    parameters.entryId = $('#entryId').val();
    parameters.attributes = $('#attributes').val();

    var url = 'revise.php';
    $.post(url, parameters);
}

(function () {
    $('li').hover(function() {
        $('#normal').attr('checked', 'checked');
        $('#unread').attr('checked', 'checked');

        var attributes = $(this).attr('class').split(' ');

        for (var index in attributes) {
            var radioInput = $('#' + attributes[index]);
            if (radioInput.length > 0) {
                radioInput.attr('checked', 'checked');
            }
        }

        var panel = $('#actionPanel');
        panel.offset({ top: $(this).offset().top, left: 700 });

        $('#entryId').val(this.id);
    }, function() {

    });

    $('input:radio').click(function () {
        var importance = $('#importance_selection input:radio:checked').val();
        var reading = $('#reading_selection input:radio:checked').val();

        var listEntry = $('#' + $('#entryId').val());
        listEntry.attr('class', '');
        var attributes = '';
        if (importance != 'normal' ) {
            listEntry.addClass(importance);
            attributes += ' ' + importance;
        }
        if (reading != 'unread' ) {
            listEntry.addClass(reading);
            attributes += ' ' + reading;
        }

        $('#attributes').val(attributes);

        sendServiceQuest();
    });
})();
</script>
<?php } ?>
</body>
</html>