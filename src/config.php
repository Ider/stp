<?php

define('IS_PROD', false);

if (IS_PROD) {
    define('DB_SERVER', 'localhost');
    define('DB_TABLE', 'stp');
    define('CONTENT_DIR', dirname(__FILE__).'/../res/content/');
} else {
    define('DB_SERVER', '66.147.244.142');
    define('DB_TABLE', 'stp_sample');
    define('CONTENT_DIR', '/tmp/todo/');
}

define('DB_USER', 'iderzhen_project');
define('DB_DATABSE', 'iderzhen_projects');
define('DB_PASSWORD', 'oxyXN2x%R9PT');


define('LOG_ERROR', false);

date_default_timezone_set('America/Los_Angeles');