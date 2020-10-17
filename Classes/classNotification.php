<?php

require_once 'smtpClass.php';

class notification {

    CONST VERSION = '1.0.0';
    CONST NAME = 'notification';
    CONST CONFIG_EVENT = 'NOTIFICATION_EVENT';
    CONST CONFIG_MAIL = 'NOTIFICATION_MAIL';

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

    public static function put($event, $subject, $message) {
        $mail_values = [];
        $mail_keys = config::get_array(self::CONFIG_EVENT, $event);
        $mails_unique = [];
        foreach ($mail_keys as $mail_key) {
            foreach (config::get_array(self::CONFIG_MAIL, $mail_key) as $mail_value) {
                $mail_values[$mail_key][] = $mail_value;
                $mails_unique[] = $mail_value;
            }
        }

        corelog::put(self::NAME,
                [
                    'event' => $event,
                    'mails' => $mail_values,
                    'message' => $message
                ]
        );
        foreach (array_unique($mails_unique) as $to) {
            smtp::send($to, $subject, $message);
        }
    }

}
