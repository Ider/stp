<?php 

include_once 'src/models/tutorial.php';
include_once 'src/services/connector.php';

class Synchronizer {

    public function __construct() {
    
    }

    public function syncDatabase($tutorials) {
        $mysqli = DatabaseConnector::getMysqli();
        foreach ($tutorials as $tutorial) {
            $filePath = CONTENT_DIR.$tutorial;
            if (!is_file($filePath)) continue;

            $content = $mysqli->real_escape_string(file_get_contents($filePath));
            $name = $mysqli->real_escape_string($tutorial);
            $time = date(Util::DATETIME_FORMAT);
            $table = DB_TABLE;
            $query = "UPDATE $table SET content = '$content', updated_time ='$time' WHERE name = '$name'";
            $mysqli->query($query);

            if ($mysqli->affected_rows > 0) continue;

            //row not exists, insert it
            $query = "INSERT INTO $table (name, content, created_time, updated_time) "
                     ."VALUE('$name', '$content', '$time', '$time')";
            $mysqli->query($query);
        }
        $mysqli->close();
    }

    public function syncFile($tutorials) {
        $mysqli = DatabaseConnector::getMysqli();

        $condition = '(\'\'';
        foreach ($tutorials as $tutorial)
            $condition .= ',\''.$mysqli->real_escape_string($tutorial).'\'';
        $condition .=')';
        $mysqli->close();

        $table = DB_TABLE;
        $query = "SELECT id, name, content FROM $table WHERE name in $condition";

        $connector = new DatabaseConnector('Tutorial');
        $results = $connector->getArray($query);

        foreach ($results as $tutorial)
            file_put_contents(CONTENT_DIR.$tutorial->name, $tutorial->content);
    }
}