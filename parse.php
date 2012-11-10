

<?php if(isset($_POST["HtmlContent"])) {
$con = $_POST["HtmlContent"];
$mainEntryText = $_POST["mainEntry"];
$mainURL = $_POST["mainURL"];

$parser = new AndroidParser();
$parser->homeURL = $_POST["homeURL"];
$entry = $parser->parse($con, $mainEntryText, $mainURL);

// $str = json_encode($entry);
// //echo <pre> . var_dump($entry) . </pre>

// file_put_contents(DIRPATH.$mainEntryText.'.txt', $str);

// $formatter = new JSONFormatter();
// $formatter->format($str);

} else {


?>

<form action="android_parser.php" method="post">
<table>
    <tr>
        <td><span>Main Entry: </span></td>
        <td><input type="text" name="mainEntry" value="" size="75"></td>
    </tr>
    <tr>
        <td><span>Main URL: </span></td>
        <td><input type="text" name="mainURL" value="" size="75"></td>
    </tr>
    <tr>
        <td><span>Home URL: </span></td>
        <td><input type="text" name="homeURL" value="" size="75"></td>
    </tr>
</table>

<textarea cols="100" rows="20" name ="HtmlContent"></textarea>
<br/>
<input  value="Parse" type="submit"/>
</form>
<?php
    }
?>