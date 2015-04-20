<?php

require_once 'config.php';
require_once 'singleton_trait.php';

class DataConnection {
    use Singleton;

    private static $conn;

    protected function _init() {
        if (!static::$conn) {
            global $db_host, $db_user, $db_pass, $db_name;
            static::$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

            if (static::$conn->connect_error) {
                die(static::$conn->connect_error);
            }
        }
    }

    public function getConnection() {
        return static::$conn;
    }
};

?>

