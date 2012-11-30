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

    public static function getEntriesFromContent($content) {
        $jsonEntries = json_decode($content);
        if (!$jsonEntries) {
            error_log(json_last_error ());
            Util::addError("String is not JSON format.");
            return null;
        }

        if (!is_array($jsonEntries)) {
            $jsonEntries = array($jsonEntries);
        }

        foreach ($jsonEntries as $jsonEntry) {
            if (!self::matchingProperties($jsonEntry)) {
                return null;
            }
        }

        return $jsonEntries;
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
    
    /**
     * Find sub entry that follow with the $index path in $entry, this static method is reverd
     *     stdClass that generated from json_encode
     * @param  mix   $entry: entry that index path looking into
     * @param  array $index: sub entry index path, $index will be cast to array if it is not
     * @return TutorialEntry: sub entry with the $index, or null if entry can not find
     */
    public static function findByIndexOf($entry, $index){
        if (empty($index)) return null;
        $indics = (array)$index;
        $count = count($indics);
        for ($i=0; $i < $count; $i++) { 
            $index = intval($indics[$i]);
            
            if ($index < 0 || count($entry->subEntries)-1 < $index) return null;

            $entry = $entry->subEntries[$index];
        }

        return $entry;
    }

    /**
     * Find sub entry that follow with the $index path in this entry
     * @param  array $index: sub entry index path, $index will be cast to array if it is not
     * @return TutorialEntry: return self::findByIndexOf($this, $index);
     */
    public function findByIndex($index) {
        return self::findByIndexOf($this, $index);
    }
}
