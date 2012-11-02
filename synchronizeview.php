<html>
    <head>
        <title>Synchronize</title>
        <link rel="stylesheet" type="text/css" href="./css/style.css" />
    </head>
    <body>
<pre>
<?php

include_once 'src/services/synchronizer.php';
include_once 'src/services/displayer.php';
include_once 'src/utilities/util.php';

?>
<table width="100%" border="1" cellpadding="10">
    <tr><th width="40%">Files</th><th width="20%">Action</th><th width="40%">Database</th><tr>

    <tr><td  valign="top">
        <div id="tutorialsInFile">
<?php

$displayer = new FileOptionssDisplayer();
$displayer->show();


?>
        </div>
    </td>
    <td align="center" valign="middle">
        <div style="height:70px;"> 
            <button id="btnToDB" class="punch">Sync to Database &gt;&gt;&gt;</button>
        </div>
        <div style="height:70px;"> 
            <button id="btnToFile" class="punch">&lt;&lt;&lt; Sync to File</button>
        </div>
    </td>
    <td valign="top">
        <div id="tutorialsInDB">
<?php
$displayer = new DatabaseOptionssDisplayer();
$displayer->show();

?>
        </div>
    </td></tr>
</table>
</pre>

<span id="conTest" class="warn_message"></span>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
function sync(syncDB) {
    var act = 'syncFile';
    var fromContainerId = '#tutorialsInDB';
    var toContainerId = '#tutorialsInFile';

    if (syncDB) {
        act = 'syncDatabase';
        fromContainerId = '#tutorialsInFile';
        toContainerId = '#tutorialsInDB';
    }

    var test = $('#conTest');
    var postData = { 'tutorials[]':[], 'act': act };
    var options = $(fromContainerId).find('input:checked');
    if (options.length <= 0) {
        test.text('Please select tutorials for ' + act);
        test.show();
        return;
    }

    options.each(function () {
        postData['tutorials[]'].push($(this).val());
    });

    $.post('synchronize.php', postData, function(responseData) {
        $(toContainerId).html(responseData);
    });
}

(function () {
    var test = $(conTest);
    test.on('click', function() {
        $(this).fadeOut('fast');
    }).hide();
    $('#btnToDB').on('click', function() {
        sync(true);
    });

    $('#btnToFile').on('click',function() {
        sync(false);
    });

    $('label').addClass('tutorialName');
})();

</script>
    </body>
</html>