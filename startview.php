<?php
include_once 'src/utilities/util.php';
require_once './menu.php';

?>

<html>
<head>
    <title>
        Start New Tutorial Progress
    </title>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>

    <h1 class="main_title">Start New Tutorial Progress</h1>

    <div class="start_view_container">
        <form action="startview.php" method="post">
            <table>
                <tr>
                    <td><span class="caption">Main Entry</span></td>
                    <td><input id="main_entry" type="text" name="mainEntry" value="main"></td>
                    <td><span id="main_entry_error"></span></td>
                </tr>
                <tr>
                    <td><span class="caption">Main URL</span></td>
                    <td><input id="main_url" type="text" name="mainURL" value=""></td>
                    <td><span id="main_url_error"></span></td>
                </tr>
    <!--             <tr>
                    <td><span class="caption">Home URL</span></td>
                    <td><input type="text" name="homeURL" value=""></td>
                    <td><span></span></td>
                </tr> -->
                <tr>
                    <td style="vertical-align: top"><span class="caption">Tutorial Content</span></td>
                    <td><textarea id="sub_content" name ="subContent"></textarea></td>
                    <td><span id="sub_content_error"></span></td>
                </tr>
                <tr>
                    <td><span class="caption"></span></td>
                    <td></td>
                    <td><span></span></td>
                </tr>
                <tr>
                    <td><span class="caption"></span></td>
                    <td></td>
                    <td><span></span></td>
                </tr>
                <tr>
                    <td><span class="caption"></span></td>
                    <td><input id="submit_button" type="submit" value="submit"></td>
                    <td><span></span></td>
                </tr>
            </table>
        </form>
    <div>
<script type="text/javascript">
(function ($) {
    function TutorialInput(inputId) {
        this.input = $(inputId);
        this.error = $(inputId+"_error");
        this.validators = [TutorialInput.prototype.requiredField];
    }

    TutorialInput.prototype.validate = function() {
        var val = this.input.val();
        var result = true;
        for (var i in this.validators) {
            result = this.validators[i](val);
            if (result !== true) {
                this.error.text(result).show();
                return false;
            }
        }

        return true;
    };

    TutorialInput.prototype.requiredField= function(val) {
        if ($.trim(val).length > 0) return true;

        return "*";
    };

    var mainEntry = new TutorialInput('#main_entry');
    mainEntry.validate();

    var submit_button = $('#submit_button'),
        main_entry_input

    submit_button.on('click', function() {
        

        return true;
    });

})(jQuery);

</script>

</body>
</html>