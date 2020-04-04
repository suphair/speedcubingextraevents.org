<?php

Class Continent_data {

    static function getByCode($code) {
        if (!$code) {
            return false;
        }
        return DataBaseClass::getRowObject("
            SELECT 
                Name name,
                Code code
            FROM Continent
            WHERE Code = UPPER('" . DataBaseClass::Escape($code) . "')
        ");
    }

}
