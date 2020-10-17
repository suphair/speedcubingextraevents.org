<?php

class corelog {

    CONST VERSION = '1.0.0';
    CONST DIR = 'Corelog';
    CONST TABLE_PREFIX = 'corelog_';

    protected static $dir;
    protected static $connection;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

    public static function init($connection) {
        self::$connection = $connection;
    }

    public static function put($source, $message) {
        self::put_file($source, $message);
        self::put_db($source, $message);
    }

    private static function put_file($source, $message) {
        $log_dir = self::DIR . "/" . $source;
        $log_file = $log_dir . "/" . $source . '_' . date('Ymd') . ".log";
        if (!file_exists(self::DIR)) {
            mkdir(self::DIR);
        }
        if (!file_exists($log_dir)) {
            mkdir($log_dir);
        }
        $text = date("Y-m-d H:i:s") . ' ' . json_encode($message) . PHP_EOL;
        $fp = fopen($log_file, "a");
        fwrite($fp, $text);
        fclose($fp);
    }

    private static function put_db($source, $message) {
        $table = self::TABLE_PREFIX . $source;
        $sql_create = "
            CREATE TABLE `$table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `message` text DEFAULT NULL,
                `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='" . SELF::VERSION . "';
        ";

        $result = mysqli_query(self::$connection, "SHOW TABLES like '$table'");
        if (!mysqli_num_rows($result) and!mysqli_query(self::$connection, $sql_create)) {
            trigger_error(json_encode(mysqli_error(self::$connection)), E_USER_ERROR);
        }

        $query = " INSERT INTO `$table` (`message`) VALUES"
                . "('" . mysqli_escape_string(self::$connection, json_encode($message)) . "')";
        mysqli_query(self::$connection, $query);
    }

}
