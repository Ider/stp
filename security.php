<?php

$userRole = '';
if(isset($_COOKIE['user_role'])) $userRole = $_COOKIE['user_role'];

define('IS_ADMIN', ($userRole == 'admin'));
$deletable = false;
if(isset($_COOKIE['deletable'])) $deletable = !!$_COOKIE['deletable'];
if(isset($_GET['deletable'])) $deletable = !!$_GET['deletable'];