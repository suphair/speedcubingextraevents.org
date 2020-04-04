<?php

Class Competitor_data {

    static function getByWcaid($wcaid) {
        return self::getBy("WCAID = '" . DataBaseClass::Escape($wcaid) . "'");
    }

    static function getByWid($wid) {
        if (is_numeric($wid)) {
            return self::getBy("WID = $wid");
        } else {
            return [];
        }
    }

    private static function getBy($where) {
        return DataBaseClass::getRowObject("
            SELECT 
                ID id,
                WID wid,
                WCAID wcaid,
                Name name,
                Country countryCode
            FROM Competitor
            WHERE $where
        ");
    }

    static function getListCompetitions($id) {
        if (is_numeric($id)) {
            
        } else {
            return [];
        }
    }

    static function getCountriesCodeByAllCompetitors(){
        
        return DataBaseClass::getColumn("
            SELECT DISTINCT
                Country.ISO2 countryCode
            FROM Competitor
            JOIN CommandCompetitor ON CommandCompetitor.Competitor = Competitor.ID 
            JOIN Command ON CommandCompetitor.Command = Command.ID 
            JOIN Country ON Country.ISO2 = Competitor.Country 
            JOIN Event ON Event.ID = Command.Event 
            JOIN Competition ON Competition.ID = Event.Competition 
            WHERE 1 = 1
            " . Command_data::COMMAND_BASE_FILTER . "
            " . Competition_data::COMPETIITON_BASE_FILTER . " 
            AND (COALESCE(Competitor.WCAID,0) != 0 OR COALESCE(Competitor.WID,0) != 0)  
        ");
    }
    
}
