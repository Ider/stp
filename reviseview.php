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
                        <input name="importance" type="radio" id="radio_trivia" value="trivia" /><label for="radio_trivia" class="trivia">trivia</label>
                        <input name="importance" type="radio" id="radio_minor"  value="minor" /><label for="radio_minor" class="minor">minor</label>
                        <input name="importance" type="radio" id="radio_normal"  value="normal" /><label for="radio_normal" class="normal">normal</label>
                        <input name="importance" type="radio" id="radio_major"  value="major" /><label for="radio_major" class="major">major</label>
                        <input name="importance" type="radio" id="radio_vital"  value="vital" /><label for="radio_vital" class="vital">vital</label>
                    </div>
                </div>
            </td>
        </tr>
         <tr>
            <td class="caption">Reading</td>
            <td>
                <div id="reading_selection">
                    <input name="reading" type="radio" id="radio_unread" value="unread" /><label for="radio_unread" class="unread">unread</label>
                    <input name="reading" type="radio" id="radio_glance" value="glance" /><label for="radio_glance" class="glance">glance</label>
                    <input name="reading" type="radio" id="radio_scan"  value="scan" /><label for="radio_scan" class="scan">scan</label>
                    <input name="reading" type="radio" id="radio_comprenhend"  value="comprenhend" /><label for="radio_comprenhend" class="comprenhend">comprenhend</label>
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
<div id="action_buttons_panel">
    <input type="image" src="res/images/edit.gif" id="edit_button"/>
    <input type="image" src="res/images/add.gif" id="add_button"/>
    <input type="image" src="res/images/delete.gif" id="delete_button"/>
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
    var action_buttons = {
        entry: null,
        panel: $('#action_buttons_panel').css('display', 'inline'),
        edit_button: $('#edit_button'),
        add_button: $('#add_button'),
        delete_button: $('#delete_button').hide(),

        
    },

    revise_properties = {
        entry: null,
        panel: $('#revise_properties_panel'),
        entry_text: $('#entry_text'),
        entry_link: $('#entry_link'),
        entry_description: $('#entry_description'),
        radio_normal: $('#radio_normal'),
        radio_unread: $('#radio_unread'),
    },
    
    add_subs = {
        entry: null,
        panel: $('#add_subs_panel'),
        entry_subContent: $('#entry_subContent'),
    };

    //hook events on action buttons
    action_buttons.edit_button.on('click', function() {
        var entry = action_buttons.entry;
        revise_properties.entry = entry;
        revise_properties.entry_text.val(entry.text());
        revise_properties.entry_link.val(entry.data('link'));
        revise_properties.entry_description.val(entry.data('description'));

        revise_properties.radio_normal.attr('checked', 'checked');
        revise_properties.radio_unread.attr('checked', 'checked');

        var attributes = entry.attr('class').split(' ');
        for (var index in attributes) {
            var radioInput = $('#' + attributes[index]);
            if (radioInput.length > 0) {
                radioInput.attr('checked', 'checked');
            }
        }

        revise_properties.panel.show();
        revise_properties.panel.animate({ top: $(this).offset().top}, "slow", "swing");
    
    });

    action_buttons.add_button.on('click', function() {
        var entry = action_buttons.entry;
        add_subs.entry = entry;
        add_subs.panel.show();
        add_subs.panel.animate({ top: entry.offset().top}, "slow", "swing");
    });

    $('#revise_view_container').find('span').on('hover', function() {
        var entry = $(this);
        action_buttons.entry = entry;
        entry.after(action_buttons.panel);
    });

    //hook events on revise properties
    revise_properties.entry_text.on('change', function() {
        var val = $.trim(this.value);
        if (val.length <= 0) return; //show some error.
        revise_properties.entry.text(val);
        sendServiceQuest('setText', {text: val});
    });

    revise_properties.entry_link.on('change', function() {
        var val = $.trim(this.value)
        revise_properties.entry.data('link', val);
        sendServiceQuest('setLink', {link: val});
    });

    revise_properties.entry_description.on('change', function() {
        var val = $.trim(this.value)
        revise_properties.entry.data('description', val);
        sendServiceQuest('setDescription', {description: val});
    });

    $('input:radio').on('click', function () {
        var importance = $('#importance_selection input:radio:checked').val();
        var reading = $('#reading_selection input:radio:checked').val();
        var entry = revise_properties.entry;
        entry.attr('class', '');
        if (importance != 'normal' ) entry.addClass(importance)
        if (reading != 'unread' ) entry.addClass(reading);

        sendServiceQuest('setAttributes', { attributes: entry.attr('class')});
    });

    $('#btnAddSubs').on('click', function() {
        var val = $.trim(add_subs.entry_subContent.val());
        if (val.length<= 0) return; //show some error.
        sendServiceQuest('addSubEntries', {subContent: val});
    });

    function sendServiceQuest(action, parameters) {
                        console.log(action);

        var entry = null;
        if (action == 'addSubEntries')
            entry = add_subs.entry;
        else 
            entry = revise_properties.entry;

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