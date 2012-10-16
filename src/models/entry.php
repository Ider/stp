<?php
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
}
