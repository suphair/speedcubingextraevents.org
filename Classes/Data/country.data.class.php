<?php

Class Country_data {

    static function getByCode($code) {
        if (!$code) {
            return false;
        }
        return DataBaseClass::getRowObject("
            SELECT 
                Name countryName,
                Continent continentCode
            FROM Country
            WHERE ISO2 = UPPER('" . DataBaseClass::Escape($code) . "')
        ");
    }

}
