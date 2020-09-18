<?php

class ban {

    const VERSION = '1.0.0';

    protected $connection;

    function __construct($connection) {
        $this->connection = $connection;
    }

    public function add($wcaid, $reason, $start_date, $end_date) {
        $wcaid_escape = strtoupper(mysqli_real_escape_string($this->connection, $wcaid));
        $reason_escape = mysqli_real_escape_string($this->connection, $reason);
        $start_date_sql = date('Y-m-d', strtotime($start_date));
        $end_date_sql = date('Y-m-d', strtotime($end_date));

        $query = "
            DELETE FROM ban_users
            WHERE wca_id='$wcaid_escape' AND end_date < current_date()
        ";
        mysqli_query($this->connection, $query);

        $query = "
            INSERT into ban_users (wca_id,reason,start_date,end_date)
            values ('$wcaid_escape','$reason_escape','$start_date_sql','$end_date_sql')
        ";
        mysqli_query($this->connection, $query);

        return self::get($wcaid_escape);
    }

    public function remove($wcaid) {
        $wcaid_escape = strtoupper(mysqli_real_escape_string($this->connection, $wcaid));
        $query = "
            DELETE FROM ban_users
            WHERE wca_id='$wcaid_escape'
        ";
        mysqli_query($this->connection, $query);
        return self::get($wcaid_escape);
    }

    public function get($wcaid) {
        $wcaid_escape = strtoupper(mysqli_real_escape_string($this->connection, $wcaid));
        $query = "
            SELECT 
                wca_id,
                reason,
                start_date,
                end_date
            FROM ban_users
            WHERE wca_id = '$wcaid_escape' and end_date>=current_date()
        ";

        $result = mysqli_query($this->connection, $query);
        $row = $result->fetch_assoc();
        if (!$row) {
            $row = ['ban' => false];
        } else {
            $row['ban'] = true;
        }
        return json_encode($row);
    }

    public function init() {
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
            if (!mysqli_query($this->connection, $query)) {
                $errors[$table] = mysqli_error($this->connection);
            }
        }

        if (sizeof($errors)) {
            trigger_error("ban.createTables: " . json_encode($errors), E_USER_ERROR);
        }
    }

}
