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
                    <td style="vertical-align: top"><span class="caption">Tutorial Name</span></td>
                    <td><input id="tutorial_name" type="text" name="tutorialName" value="">
                        <span id="tutorial_name_error"></span></td>
                </tr>
                <tr>
                    <td style="vertical-align: top"><span class="caption">Main Entry</span></td>
                    <td><input id="main_entry" type="text" name="mainEntry" value="">
                        <span id="main_entry_error"></span>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top"><span class="caption">Main URL</span></td>
                    <td>
                        <input id="main_url" type="text" name="mainURL" value="">
                        <span id="main_url_error"></span>
                    </td>
                </tr>
    <!--             <tr>
                    <td><span class="caption">Home URL</span></td>
                    <td><input type="text" name="homeURL" value=""></td>
                    <td><span></span></td>
                </tr> -->
                <tr>
                    <td style="vertical-align: top"><span class="caption">Tutorial Content</span></td>
                    <td>
                        <textarea id="sub_content" name ="subContent"></textarea>
                        <span id="sub_content_error"></span>
                    </td>
                </tr>
                <tr>
                    <td><span class="caption"></span></td>
                    <td><input id="submit_button" type="submit" value="submit"></td>
                    <td><span></span></td>
                </tr>
                <tr>
                    <td colspan="2"><span id="summary_error"></span></td>
                </tr>
            </table>
            <input type="hidden" val="startTutorial" name="act" />
        </form>
    <div>
<script type="text/javascript">
(function ($) {
    /******* TutorialInput Function Prototype*******/
    function TutorialInput(inputId) {
        if (inputId.charAt(0) != '#') inputId = '#'+inputId;
        var $this = this;
        this.input = $(inputId),
        this.error = $(inputId+"_error"),
        this.validators = [this.requiredField];

        this.input.on('blur', function(){
            $this.validate();
        });
        this.error.addClass('validation_error').hide();
    }

    TutorialInput.prototype.validate = function() {
        this.error.hide();
        var val = this.input.val(),
            result = true;
        for (var i in this.validators) {
            result = this.validators[i].call(this, val);
            if (result !== true) {
                this.error.text(result).show();
                return false;
            }
        }

        return true;
    };

    TutorialInput.prototype.text = function(value) {
        if (typeof value == 'string')
            this.input.val(value);
        else
            return this.input.val();
    }
    
    TutorialInput.prototype.requiredField = function(val) {
        if ($.trim(val).length > 0) return true;

        return "Required Field";
    };

    TutorialInput.prototype.addValidator = function(validator) {
        if (typeof validator === 'function')
            this.validators.push(validator);
        return this;
    };

    /******* TutorialInput Objects *******/
    var mainEntryInput = new TutorialInput('main_entry');
    var mainURLInput = new TutorialInput('main_url');
    var subContentInput = new TutorialInput('sub_content');

    var tutorialNameInput = new TutorialInput('tutorial_name');
    tutorialNameInput.addValidator(function (val) {
        var reg = /^[a-z][a-z0-9\-]*$/
        if (reg.test(val)) return true;
        return "Only allow lower characters, numbers and hyphen.";
    });
    tutorialNameInput.input.on('change', function () {
        var text = tutorialNameInput.text();
        text = $.trim(text.toLowerCase()).replace(/ +/g, '-');
        tutorialNameInput.text(text);
    });

    tutorialNameInput.input.on('blur', function () {
        if (tutorialNameInput.error.is(":visible")) return;
        var parameters = {
            act: "validateName",
            tutorialName: tutorialNameInput.text(),
        }
        $.post('start.php', parameters, function(data) {
            var result = $.parseJSON(data);
            if (result.state == 'error')
                tutorialNameInput.error.text(result.content[0]).show();
            else 
                console.log(data);
        });
    });

    var summary_error = $('#summary_error').addClass('validation_error').hide();

    $('#submit_button').on('click', function() {
        var validated = (tutorialNameInput.error.is(':visible') || tutorialNameInput.validate())
            & mainEntryInput.validate()
            & mainURLInput.validate()
            & subContentInput.validate();
        if (!validated) return false;
        var parameters = {
            act: "startTutorial",
            tutorialName: tutorialNameInput.text(),
            mainEntry: mainEntryInput.text(),
            mainURL: mainURLInput.text(),
            subContent: subContentInput.text(),
        }

        $.post('start.php', parameters, function(data) {
            console.log(data);
            var result = $.parseJSON(data);
            if (result.state == 'error')
                summary_error.text(result.content.join('<br />')).show();
            else if (result.state == 'ok')
                window.location = 'tutorialview.php?tutorial=' + parameters.tutorialName;
        });
        return false;
    });

})(jQuery);

</script>

</body>
</html>