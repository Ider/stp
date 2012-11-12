<html>
<head>
    <title>Tutorials List</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>
<h1 class="main_title">Tutorial List</h1>
<div id="tutorials_list" class="tutorials_list_container">

<?php
include_once 'src/services/displayer.php';
require_once 'menu.php';

$deletable = false;
if (isset($_GET['deletable']))
    $deletable = $_GET['deletable'];

$displayer = new TutorialListDisplayer($deletable);
if (!$displayer->generate())
    $displayer = new ErrorDisplayer();

$displayer->show();

?>
<div id="error_sumary">What?</div>
<script type="text/javascript">
(function ($) {
    var error_sumary = $('#error_sumary').addClass('error_message').hide();
    var seletedRow = null;
    $('#tutorials_list').on('click', 'input[name="remove"]', function(){
        var parameters = {
            act: "removeTutorial",
            tutorial: this.value,
        }
        seletedRow = $(this).closest('tr');
        var url = 'revise.php';
        error_sumary.hide();
        $.post(url, parameters, function(data) {
            var result = $.parseJSON(data);
            if (result.state == 'ok') {
                seletedRow.css('background-color', '#FFC6CA').animate({ opacity: 0.0 },  "slow", function() {
                    seletedRow.remove();
                });
            } else if (result.state == 'error') {
                error_sumary.text(result.content.join('<br />')).show();
            }
        });
    });
})(jQuery);
</script>
</div>
</body>
</html>