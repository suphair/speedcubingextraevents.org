<?php

require_once 'configClass.php';

class wcaoauth {

    protected static $scope = 'public';
    protected static $config = [];
    protected static $clientId;
    protected static $urlRefer;
    protected static $connection;
    protected static $clientSecret;

    CONST ME = 'wcaoauth.me';
    CONST CONFIG = 'wcaoauth';
    CONST NAME = 'wcaoauth';
    CONST TABLE_LOGS = 'wca_oauth_logs';
    CONST VERSION = '2.1.0';
    CONST SESSION_REQUEST_URI = 'wcaoauth.request_uri';

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

    static function set($prefix_url_refer, $connection) {
        $keys = [
            'url_refer',
            'client_id',
            'client_secret',
            'scope',
            'wca_url_authorize',
            'wca_url_token',
            'wca_url_me'
        ];

        foreach ($keys as $key) {
            self::$config[$key] = config::get(self::CONFIG, $key);
        }

        $http = config::isLocalhost() ? "http" : "https";
        self::$urlRefer = $http . ':' . $prefix_url_refer . self::$config['url_refer'];
        self::$connection = $connection;
        self::check_db();
    }

    static function url() {
        $_SESSION[self::SESSION_REQUEST_URI] = filter_input(INPUT_SERVER, 'REQUEST_URI');

        return self::$config['wca_url_authorize'] . '?'
                . "client_id=" . self::$config['client_id'] . "&"
                . "redirect_uri=" . urlencode(self::$urlRefer) . "&"
                . "response_type=code&"
                . "scope=" . self::$config['scope'] . "";
    }

    static function location() {
        header("Location: {$_SESSION[self::SESSION_REQUEST_URI]}");
        exit();
    }

    static function authorize() {
        if (filter_input(INPUT_GET, 'error') == 'access_denied') {
            self::location();
        }

        $code = filter_input(INPUT_GET, 'code');
        if (!$code) {
            return;
        }

        $accessToken = self::getAccessTokenCurl($code);
        if (!$accessToken) {
            self::location();
        }

        self::getMeCurl($accessToken);
    }

    private static function buildQueryForAccessToken($code) {
        return http_build_query(
                [
                    'grant_type' => 'authorization_code',
                    'client_id' => self::$config['client_id'],
                    'client_secret' => self::$config['client_secret'],
                    'code' => $code,
                    'redirect_uri' => self::$urlRefer
        ]);
    }

    private static function getAccessTokenCurl($code) {
        $postdata = self::buildQueryForAccessToken($code);
        $ch = curl_init(self::$config['wca_url_token']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded', '"Accept-Language: en-us,en;q=0.5";']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if (json_decode($result)->error ?? FALSE) {
            trigger_error(self::NAME . ".getToken: $result <br>" . print_r($postdata, true), E_USER_ERROR);
        }

        if ($status != 200) {
            trigger_error(self::NAME . ".getToken: $status<br>$url", E_USER_ERROR);
        }

        return json_decode($result)->access_token;
    }

    private static function getMeCurl($accessToken) {
        $ch = curl_init(self::$config['wca_url_me']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Bearer $accessToken"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($status != 200) {
            trigger_error(self::NAME . ".getMe: $status<br>$url", E_USER_ERROR);
        }

        if (isset(json_decode($result)->me->id)) {
            $me = json_decode($result)->me;
            self::log($me);
            $_SESSION[self::ME] = $me;
            return $me;
        } else {
            $_SESSION[self::ME] = false;
            self::location();
        }
    }

    private static function log($details) {
        $query = " INSERT INTO `" . self::TABLE_LOGS . "` "
                . "(`details`,`version`) "
                . "VALUES ('" . json_encode($details) . "','" . self::VERSION . "')";
        mysqli_query(self::$connection, $query);
    }

    private static function check_db() {
        $errors = [];

        $tables[self::TABLE_LOGS] = "
            CREATE TABLE `" . self::TABLE_LOGS . "` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `details` text DEFAULT NULL,
                `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `version` varchar(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='" . SELF::VERSION . "';
        ";

        foreach ($tables as $table => $query) {
            $result = mysqli_query(self::$connection, "SHOW TABLES like '$table'");
            if (!mysqli_num_rows($result) and!mysqli_query(self::$connection, $query)) {
                trigger_error(self::NAME . ". " . json_encode(mysqli_error(self::$connection)), E_USER_ERROR);
            }
        }
    }

    static function out() {
        $_SESSION[self::ME] = FALSE;
    }

    static function me() {
        return $_SESSION[self::ME] ??= FALSE;
    }

}

/*
[wcaoauth]
    url_refer = 
    client_id = 
    client_secret = 
    scope = 
    wca_url_authorize = 
    wca_url_token = 
    wca_url_me = 
 */