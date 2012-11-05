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
<div id="revise_more_toggler" class="action_more_toggler">less</div>
<table width="100%">
    <tbody id="revise_more_container" class="more">
        <tr>
            <td class="caption">Name</td>
            <td>
                <div><input type="text" name="text" id="entry_text" size="50"></div>
            </td>
        </tr>
        <tr>
            <td class="caption">Website URL</td>
            <td>
                <div><input type="text" name="link" id="entry_link" size="50"></div>
            </td>
        </tr>
        <tr>
            <td class="caption">Description</td>
            <td>
                <div><input type="text" name="description" id="entry_description" size="50"></div>
            </td>
        </tr>
    </tbody>
    <tbody class="attributes_container">
        <tr>
            <td class="caption">Importance</td>
            <td>
                <div>
                    <div id="importance_selection">
                        <input name="importance" type="radio" id="trivia" value="trivia" /><label for="trivia" class="trivia">trivia</label>
                        <input name="importance" type="radio" id="minor"  value="minor" /><label for="minor" class="minor">minor</label>
                        <input name="importance" type="radio" id="normal"  value="normal" /><label for="normal" class="normal">normal</label>
                        <input name="importance" type="radio" id="major"  value="major" /><label for="major" class="major">major</label>
                        <input name="importance" type="radio" id="vital"  value="vital" /><label for="vital" class="vital">vital</label>
                    </div>
                </div>
            </td>
        </tr>
         <tr>
            <td class="caption">Reading</td>
            <td>
                <div id="reading_selection">
                    <input name="reading" type="radio" id="unread" value="unread" /><label for="unread" class="unread">unread</label>
                    <input name="reading" type="radio" id="glance" value="glance" /><label for="glance" class="glance">glance</label>
                    <input name="reading" type="radio" id="scan"  value="scan" /><label for="scan" class="scan">scan</label>
                    <input name="reading" type="radio" id="comprenhend"  value="comprenhend" /><label for="comprenhend" class="comprenhend">comprenhend</label>
                </div>
            </td>
        </tr>
    </tbody>
    <!-- <tr>
        <td class="caption">Relative</td>
        <td>
            <div></div>
        </td>
    </tr> -->
</table>
</div>




<div id="reviseService">
    <input id="entry_Id" type="hidden" name="entryId" />
    <input id="act" type="hidden" name="act" value="setAttributes"/>
    <input id="entry_attributes" type="hidden" name="entry_attributes" value=""/>
<?php echo '<input id="tutorialName" type="hidden" name="tutorialName" value="' .$tutorialName. '"/>' ?>
</div>

<span id="test" style="position:fixed; top:10px; right:30px;"></span>
<script type="text/javascript">



(function () {
    var entry = null;
    var panel = {
        entry_Id : $('#entry_Id'),
        entry_text: $('#entry_text'),
        entry_link: $('#entry_link'),
        entry_description: $('#entry_description'),
    };

    $('#revise_view_container').find('li').on('click', function() {
        entry = $('#' + this.id);

        panel.entry_Id.val(this.id);
        panel.entry_text.val(entry.text());
        panel.entry_link.val(entry.data('link'));
        panel.entry_description.val(entry.data('description'));

        $('#normal').attr('checked', 'checked');
        $('#unread').attr('checked', 'checked');

        var attributes = entry.attr('class').split(' ');
        for (var index in attributes) {
            var radioInput = $('#' + attributes[index]);
            if (radioInput.length > 0) {
                radioInput.attr('checked', 'checked');
            }
        }
    });

    $('#entry_text').on('change', function() {
        var val = $.trim(this.value);
        if (val.length <= 0) return; //show some error.
        entry.text(this.value);
        sendServiceQuest('setText');
    });

    $('#entry_link').on('change', function() {
        entry.data('link', this.value);
        sendServiceQuest('setLink');
    });

    $('#entry_description').on('change', function() {
        entry.data('description', this.value);
        sendServiceQuest('setDescription');
    });

    $('input:radio').on('click', function () {
        var importance = $('#importance_selection input:radio:checked').val();
        var reading = $('#reading_selection input:radio:checked').val();

        entry.attr('class', '');
        if (importance != 'normal' ) entry.addClass(importance)
        if (reading != 'unread' ) entry.addClass(reading);

        sendServiceQuest('setAttributes');
    });

    function sendServiceQuest(action) {
        if (!entry) return;

        var parameters = {};
        parameters.tutorial = $('#tutorialName').val();
        parameters.act = action;
        parameters.entryId = entry.attr('id');
        parameters.text = entry.text();
        parameters.link = entry.data('link');
        parameters.description = entry.data('description');
        parameters.attributes = entry.attr('class');

        var url = 'revise.php';
        $.post(url, parameters);
    }

    var revise_more = $('#revise_more_container').hide();
    var toggler = $('#revise_more_toggler').text('more').on('click', function() {
        if(revise_more.is(':hidden')) {
            revise_more.show();
            toggler.text('less');
        } else {
            revise_more.hide();
            toggler.text('more');
        }
    });
    
})();
</script>
<?php } ?>
</body>
</html>