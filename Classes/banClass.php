<?php

class ban {

    const VERSION = '1.1.1';
    const SESSION_NAME = 'ban.ban_user';

    public static function add_db($connection, $wcaid, $reason, $start_date, $end_date) {
        $wcaid_escape = strtoupper(mysqli_real_escape_string($connection, $wcaid));
        $reason_escape = mysqli_real_escape_string($connection, $reason);
        $start_date_sql = date('Y-m-d', strtotime($start_date));
        $end_date_sql = date('Y-m-d', strtotime($end_date));

        self::remove_db($wcaid_escape);
        $query = "
            INSERT into ban_users (wca_id, reason,start_date, end_date)
            values ('$wcaid_escape','$reason_escape','$start_date_sql','$end_date_sql')
        ";
        mysqli_query($connection, $query);
    }

    public static function remove_db($connection, $wcaid) {
        $wcaid_escape = strtoupper(mysqli_real_escape_string($connection, $wcaid));
        $query = "DELETE FROM ban_users WHERE wca_id='$wcaid_escape'";
        mysqli_query($connection, $query);
    }

    public static function get_db($connection, $wcaid) {
        if (!$wcaid) {
            return FALSE;
        }
        $wcaid_escape = strtoupper(mysqli_real_escape_string($connection, $wcaid));
        $query = "
            SELECT 
                wca_id,
                reason,
                start_date,
                end_date
            FROM ban_users
            WHERE wca_id = '$wcaid_escape' and end_date >= current_date()
        ";

        $result = mysqli_query($connection, $query);
        return $result->fetch_assoc();
    }

    public static function is_ban() {
        return $_SESSION[self::SESSION_NAME] ?? FALSE != FALSE;
    }

    public static function get_data() {
        return $_SESSION[self::SESSION_NAME] ?? FALSE;
    }

    public static function clear_data() {
        unset($_SESSION[self::SESSION_NAME]);
    }

    public static function set_data($connection, $wcaid, $competitor) {
        $ban_db = self::get_db($connection, $wcaid);
        if ($ban_db) {
            $_SESSION[self::SESSION_NAME] = $ban_db;
            $_SESSION[self::SESSION_NAME]['competitor'] = $competitor;
        }
    }

    public static function init($connection) {
        $queries = [];
        $errors = [];
        $queries['ban_users'] = "
            CREATE TABLE `ban_users` (
              `wca_id` varchar(10) NOT NULL,
              `reason` varchar(255) NOT NULL,
              `start_date` date NOT NULL,
              `end_date` date NOT NULL,
              UNIQUE KEY `wca_id` (`wca_id`) USING BTREE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";

        foreach ($queries as $table => $query) {
            if (!mysqli_query($connection, $query)) {
                $errors[$table] = mysqli_error($connection);
            }
        }

        if (sizeof($errors)) {
            trigger_error("ban.createTables: " . json_encode($errors), E_USER_ERROR);
        }
    }

}
