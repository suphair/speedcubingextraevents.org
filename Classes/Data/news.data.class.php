<?php

Class News_data {

    static function getNewsIDbyDepth($deepDays) {

        if ($deepDays) {
            $where = "AND TO_DAYS(now()) - TO_DAYS(Date) < $deepDays";
        } else {
            $where = '';
        }
        return DataBaseClass::getColumn("
        SELECT ID id
        FROM News
        WHERE 1 = 1
        $where
    ");
    }

    static function getById($aNewsId) {
        return DataBaseClass::getRowObject("
            SELECT
                ID id,
                Text text,
                Date date,
                Delegate delegateWid
            FROM News N 
            WHERE ID=$aNewsId
        ");
    }

}
