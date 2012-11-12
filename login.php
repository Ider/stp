<link rel="stylesheet" type="text/css" href="./css/style.css" />
<?php 
$users = array('ider' => '2107');
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Ider Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<div class="error_message">Login canceled!</div>';
    exit;
} else if (!isset($users[$_SERVER['PHP_AUTH_USER']])) {
    echo '<div class="error_message">No such user!</div>';
    exit;

} else if($users[$_SERVER['PHP_AUTH_USER']] != $_SERVER['PHP_AUTH_PW']) {
    echo '<div class="error_message">No such user!</div>';
} else {
    $timeout = time()+60*60*24*30;
    echo "Welcome, you are logged in";
    setcookie('user_role', 'admin');
    setcookie('deletable', '1');
}