<?php

Class Delegate_data {

    static function getById($id) {
        if (is_numeric($id)) {
            return self::getBy("ID = $id");
        } else {
            return false;
        }
    }

    static function getByWid($wid) {
        if (is_numeric($wid)) {
            return self::getBy("WID = $wid");
        } else {
            return false;
        }
    }

    static function getByWcaid($wcaid) {
        return self::getBy("WCA_ID = '" . DataBaseClass::Escape($wcaid) . "'");
    }

    private static function getBy($where) {
        return DataBaseClass::getRowObject("
            SELECT 
                ID id,
                WCA_ID wcaid,
                WID wid,
                Status status,
                Contact contact,
                Secret secret
            FROM Delegate
            WHERE 
                $where
        ");
    }

}
