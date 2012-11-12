<link rel="stylesheet" type="text/css" href="./css/menu.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>


<div class="menu">
    <ul>
        <li><a href="tutorialsview.php">Tutorials</a></li>
        <?php
            if (isset($_GET['tutorial'])) {
                $tutorial = 'tutorial='.$_GET['tutorial'];

                echo '<li><a href="acquireview.php?'.$tutorial.'">Acquire</a></li>';
                echo '<li><a href="reviseview.php?'.$tutorial.'">Revise</a></li>';
            }
        ?>
        <li><a href="startview.php">Start</a></li>
    </ul>
</div>

