<?php

include_once dirname(__FILE__).'/../config.php';

class Util {
    protected static $errors = array();

    public static function addError($error) {
        self::$errors[] = $error;
        if (LOG_ERROR) error_log($error);
    }


    public static function getErrors() {
        return self::$errors;
    }

    //trim white space and change space to underline
    public static function encodeFileName($name) {
        return preg_replace('/[\t\r ]+/', '_', trim($name));
    }

    public static function hasTutorial($tutorialName) {

    }
}