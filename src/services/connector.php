<?php
include_once 'src/config.php';
include_once 'src/utilities/util.php';
include_once 'src/models/tutorial.php';

class FileConnector {

    private $tutorialName;
    function __construct($tutorialName = '') {
        $this->tutorialName = basename($tutorialName);
    }

    //TODO: should using regular expression to match.  
    public static function isValidName($name) {
        if (empty($name)) {
            Util::addError('Tutorial name is empty!');
            return false;
        }

        if ($name[0] == '.') {
            Util::addError('Invalid tutorial name!');
            return false;
        }
        return true;
    }

    public static function hasFile($name) {
        return is_file($name);
    }

    public static function getTutorialsFromFile() {
        $tutorials = array();
        $fileNames = scandir(CONTENT_DIR);
        foreach ($fileNames as $fileName) {
            if ($fileName[0] == '.') continue;
            $filePath = CONTENT_DIR.$fileName;
            if (!is_file($filePath)) continue;

            $tutorial = new Tutorial();
            $tutorial->name = $fileName;
            $tutorial->create_time = Util::formatDatetime(filectime($filePath));
            $tutorial->updated_time = Util::formatDatetime(filemtime($filePath));

            $tutorials[] = $tutorial;
        }
        return $tutorials;
    }

    public static function removeTutorial($tutorialName) {
        if (!self::isValidName($tutorialName)) return false;
        if (!unlink(CONTENT_DIR.$tutorialName)) {
            Util::addError('Unable to remove tutorial: '.$tutorialName.'. The file may no longer exists');
            return false;
        }

        return true;
    }

    public function loadEntries() {
        if (!self::isValidName($this->tutorialName)) {
            return null;
        }

        $filePath = CONTENT_DIR . $this->tutorialName;
        if (!self::hasFile($filePath)) {
            Util::addError('No tutorial file named: ' . $this->tutorialName);
            return null;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            Util::addError('Cannot get content of tutorial file named: ' . $this->tutorialName);
            return null;
        }
        
        return json_decode($content);
    }

    public function saveEntries($rootEntry) {
        $content = json_encode($rootEntry);
        $bytes = file_put_contents(CONTENT_DIR . $this->tutorialName, $content);
        return $bytes;
    }
}

class DatabaseConnector{
    public static function getMysqli() {
        $mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABSE);
        return $mysqli;
    }

    /**
     * Get simple informations of all tutorials in database, 
     * content would not be include as the data may be too big
     * @return array  of Tutorial
     */
    public static function  getTutorialsFromDatabase() {
        $connector = new DatabaseConnector('Tutorial');
        $db = DB_TABLE;
        $query = "SELECT id, name, created_time, updated_time FROM $db order by name";
        return $connector->getArray($query);   
    }

    private $modelType;
    public function __construct($type) {
        $this->modelType = $type;
    }

    public function query($query) {
        $mysqli = self::getMysqli();
        $result = $mysqli->query($query);
        $mysqli->close();
        return $result;
    }

    public function getArray($query) {
        $models = array();

        if ($result = $this->query($query)) {
            while ($row = $result->fetch_assoc()) {
                $model = new $this->modelType;
                foreach ($row as $key => $value) {
                    $model->$key = $value;
                }
                $models[] = $model;
            }
            $result->free();
        }

        return $models;
    }

    public function getObject($query) {
        $model = null;

        if ($result = $this->query($query)) {
            if ($row = $result->fetch_assoc()) {
                $model = new $this->modelType;
                foreach ($row as $key => $value) {
                    $model->$key = $value;
                }
            }
            $result->free();
        }
        return $model;
    }
}