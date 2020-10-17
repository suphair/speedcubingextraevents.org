<?php

require_once 'configClass.php';
require_once 'corelogClass.php';

class smtp {

    protected static $config;

    CONST CONFIG = 'SMTP';
    CONST NAME = 'smtp';
    CONST VERSION = '2.2.0';

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

    static function send($to, $subject, $message) {

        self::$config = config::get_section(self::CONFIG,
                        [
                            'host',
                            'port',
                            'from',
                            'username',
                            'password'
                        ]
        );

        $contentMail = self::getContentMail($subject, $message);
        $result = self::_send($to, $contentMail);
        corelog::put(self::NAME,
                [
                    'version' => self::VERSION,
                    'to' => $to,
                    'subject' => $subject,
                    'message' => $message,
                    'result' => $result
                ]
        );
        return $result;
    }

    private static function _send($to, $contentMail) {
        $host = self::$config->host;
        $port = self::$config->port;
        $username = self::$config->username;
        $password = self::$config->password;

        if (!$socket = @fsockopen($host, $port, $errorNumber, $errorDescription, 30)) {
            return "$errorNumber:$errorDescription";
        }
        if (!self::_parseServer($socket, "220")) {
            return 'Connection error';
        }

        $server_name = $_SERVER["SERVER_NAME"];
        fputs($socket, "EHLO $server_name\r\n");
        if (!self::_parseServer($socket, "250")) {
            // если сервер не ответил на EHLO, то отправляем HELO
            fputs($socket, "HELO $server_name\r\n");
            if (!self::_parseServer($socket, "250")) {
                fclose($socket);
                return 'Error of command sending: HELO';
            }
        }

        fputs($socket, "AUTH LOGIN\r\n");
        if (!self::_parseServer($socket, "334")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, base64_encode($username) . "\r\n");
        if (!self::_parseServer($socket, "334")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, base64_encode($password) . "\r\n");
        if (!self::_parseServer($socket, "235")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, "MAIL FROM: <{$username}>\r\n");
        if (!self::_parseServer($socket, "250")) {
            fclose($socket);
            return 'Error of command sending: MAIL FROM';
        }

        $emails_to_array = explode(',', str_replace(" ", "", $to));
        foreach ($emails_to_array as $email) {
            fputs($socket, "RCPT TO: <{$email}>\r\n");
            if (!self::_parseServer($socket, "250")) {
                fclose($socket);
                return 'Error of command sending: RCPT TO';
            }
        }

        fputs($socket, "DATA\r\n");
        if (!self::_parseServer($socket, "354")) {
            fclose($socket);
            return 'Error of command sending: DATA';
        }

        fputs($socket, "$contentMail\r\n.\r\n");
        if (!self::_parseServer($socket, "250")) {
            fclose($socket);
            return 'E-mail didn\'t sent';
        }

        fputs($socket, "QUIT\r\n");
        fclose($socket);
        return true;
    }

    private static function _parseServer($socket, $response) {
        $responseServer = 'xxx';
        while (substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;
    }

    private static function getContentMail($subject, $message) {
        $from = self::$config->from;
        $username = self::$config->username;

        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "=?=\r\n";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: $from <$username>\r\n";
        $contentMail .= "$headers\r\n";
        $contentMail .= "$message\r\n";
        return $contentMail;
    }

}
