<?php

class config {

    const VERSION = '2.1.0';
    const DEFAULT = 'default';
    private const TEMPLATE = 'config_template';
    private const LOCALHOST = 'localhost';

    protected static $server;
    protected static $config;

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

    static function init($dir) {
        $server = str_replace('www.', '', strtolower(filter_input(INPUT_SERVER, 'SERVER_NAME')));
        $default = self::DEFAULT;

        if (self::check_file("$dir/.key") !== $server) {
            self::error("wrong key");
        }

        self::$config = self::get_config(["$dir/$server", "$dir/$default"]);
        self::$server = $server;
    }

    static function get($section, $param) {
        $value = trim(self::$config[$section][$param] ?? FALSE);
        if (!$value) {
            self::error("value [$section/$param] not found");
        }
        return $value;
    }
    
    static function get_array($section, $param) {
        $value = trim(self::$config[$section][$param] ?? FALSE);
        return array_map("trim",  explode(',',$value));
    }

    static function get_section($section, $keys) {
        $config = new stdClass();
        foreach ($keys as $key) {
            $config->$key = self::get($section, $key);
        }
        return $config;
    }

    static function info() {
        return 'config [' . self::VERSION . '/' . self::$server . ']';
    }

    static function isLocalhost() {
        return self::$server == self::LOCALHOST;
    }

    static private function get_config($folders) {
        $config = [];
        foreach ($folders as $folder) {
            self::check_file($folder);
            foreach (glob($folder . "/*.ini") as $file) {
                $config = array_merge($config, parse_ini_file($file, true));
            }
        }
        return $config;
    }

    static private function check_file($file) {
        if (!file_exists($file)) {
            self::error("[$file] not exists");
        }
        if (is_file($file)) {
            return file_get_contents($file);
        }
    }

    static private function error($error) {
        trigger_error(self::info() . ' : ' . $error, E_USER_ERROR);
    }

    static function template($dir) {
        $config = '';
        $config .= ';version ' . self::VERSION . "\n";
        $config .= ';' . date('d M Y') . "\n";
        foreach (self::$config as $section => $values) {
            $config .= "[$section]\n";
            foreach ($values as $key => $value) {
                $config .= "    $key=\n";
            }
        }
        $handle = fopen("$dir/" . self::TEMPLATE . ".ini", "w+");
        fwrite($handle, $config);
        fclose($handle);
    }

}
