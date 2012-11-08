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



<div id="revise_properties_panel" class="action_panel">
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

<div id="add_subs_panel" class="action_panel">
    <table>
        <tr>
            <td class="caption">Sub Entries</td>
        </tr>
        <tr>
            <td><textarea id="entry_subContent" rows="7" cols="62" name="subContent"></textarea></td>
        </tr>
        <tr>
            <td align="right"><button id="btnAddSubs">Add Subs</button></td>
        </tr>
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



(function ($) {
    var entry = null;
    var panel = {
        revise_properties_panel: $('#revise_properties_panel'),
        add_subs_panel: $('#add_subs_panel'),

        entry_Id: $('#entry_Id'),
        entry_text: $('#entry_text'),
        entry_link: $('#entry_link'),
        entry_description: $('#entry_description'),
        entry_subContent: $('#entry_subContent'),
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
        panel.add_subs_panel.show();
        panel.add_subs_panel.animate({ top: $(this).offset().top}, "slow", "swing");
    });

    $('#entry_text').on('change', function() {
        var val = $.trim(this.value);
        if (val.length <= 0) return; //show some error.
        entry.text(val);
        sendServiceQuest('setText', {text: val});
    });

    $('#entry_link').on('change', function() {
        var val = $.trim(this.value)
        entry.data('link', val);
        sendServiceQuest('setLink', {link: val});
    });

    $('#entry_description').on('change', function() {
        var val = $.trim(this.value)
        entry.data('description', val);
        sendServiceQuest('setDescription', {description: val});
    });

    $('input:radio').on('click', function () {
        var importance = $('#importance_selection input:radio:checked').val();
        var reading = $('#reading_selection input:radio:checked').val();

        entry.attr('class', '');
        if (importance != 'normal' ) entry.addClass(importance)
        if (reading != 'unread' ) entry.addClass(reading);

        sendServiceQuest('setAttributes', { attributes: entry.attr('class')});
    });

    $('#btnAddSubs').on('click', function() {
        var val = $.trim(panel.entry_subContent.val());
        if (val.length<= 0) return; //show some error.
        sendServiceQuest('addSubEntries', {subContent: val});
    });

    function sendServiceQuest(action, parameters) {
        if (!entry) return;
        parameters.tutorial = $('#tutorialName').val();
        parameters.act = action;
        parameters.entryId = entry.attr('id');

        var url = 'revise.php';
        $.post(url, parameters, responseResult);
    }

    var revise_more = $('#revise_more_container').hide();
    var toggler = $('#revise_more_toggler').text('more').on('click', function() {
        if(revise_more.is(':hidden')) {
            revise_more.fadeIn();
            toggler.text('less');
        } else {
            revise_more.fadeOut();
            toggler.text('more');
        }
    });

    function responseResult(data) {
        var result = $.parseJSON(data);
        console.log(result);
    }
    
})(jQuery);

</script>
<?php } ?>
</body>
</html>