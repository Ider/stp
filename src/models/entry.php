<?php
include_once 'src/utilities/util.php';

class TutorialEntry {
    public $text;           //string
    public $link;           //string
    public $description;    //string
    public $attributes;     //string, each attribute seperated by space
    public $relatives;      //dictionary
    public $subEntries;     //array

    function __construct() {
        $this->text = "";
        $this->link = "";
        $this->description = "";
        $this->attributes = "";
        $this->relatives = array();
        $this->subEntries = array();
   }


    public static function matchingProperties($jsonEntry) {
        static $prototype = null;
        if (!$prototype) {
            $prototype = new TutorialEntry();
        }

        foreach ($prototype as $key => $value) {
            if (!isset($jsonEntry->$key)) {
                Util::addError("Flowing entry does not contains property($key):\n"
                    .json_encode($jsonEntry));
                return false;
            }

            $type = gettype($value);
            if ($type != gettype($jsonEntry->$key)) {
                Util::addError("Flowing entry does not matching property($key) type($type):\n"
                    .json_encode($jsonEntry));
                return false;
            }
        }

        foreach ($jsonEntry->subEntries as $subEntry) {
            if (!self::matchingProperties($subEntry)) {
                return false;
            }
        }

        return true;
    }
}
