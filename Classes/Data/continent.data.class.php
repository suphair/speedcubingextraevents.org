<?php

Class Continent_data {

    static function getByCode($code) {
        if (!$code) {
            return false;
        }
        return DataBaseClass::getRowObject("
            SELECT 
                Name name,
                LOWER(Code) code
            FROM Continent
            WHERE LOWER(Code) = LOWER('" . DataBaseClass::Escape($code) . "')
        ");
    }

}
