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

    const FILENAME_DELIMETER = '-';

    //trim white space and change space to underline
    public static function encodeFileName($name) {
        return preg_replace('/[\t\r _]+/', self::FILENAME_DELIMETER, trim($name));
    }

    public static function decodeFileName($name) {
        return str_replace(self::FILENAME_DELIMETER, ' ', $name);
    }

    public static function hasTutorial($tutorialName) {
        //TODO
    }

    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    public static function formatDatetime($datetime) {
        return date(self::DATETIME_FORMAT, $datetime);
    }

    public static function stringEscape($str) {
        $str = str_replace("\x5C", "\x5C\x5C", $str); //back slash
        $str = str_replace("\x22", "\x5C\x22", $str); //double quote
        $str = str_replace("\x27", "\x5C\x27", $str); //single quote
        return $str;
    }

    public static function stringDescape($str) {
        $str = str_replace("\\\'", "\'", $str);
        $str = str_replace("\\\"", "\"", $str);
        $str = str_replace("\\\\", "\\", $str);

        return $str;
    }
}