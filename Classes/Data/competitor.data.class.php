<?php

Class Competitor_data {

    static function getByWcaid($wcaid) {
        return self::getBy("WCAID = '" . DataBaseClass::Escape($wcaid) . "'");
    }

    static function getById($id) {
        if (is_numeric($id)) {
            return self::getBy("ID = $id");
        } else {
            return [];
        }
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
                Email email,
                Name name,
                Country countryCode
            FROM Competitor
            WHERE $where
        ");
    }

    static function getCountriesCodeByAllCompetitors() {

        return DataBaseClass::getColumn("
            SELECT DISTINCT
                LOWER(Country.ISO2) countryCode
            FROM Competitor
            JOIN CommandCompetitor ON CommandCompetitor.Competitor = Competitor.ID 
            JOIN Command ON CommandCompetitor.Command = Command.ID 
            JOIN Country ON Country.ISO2 = Competitor.Country 
            JOIN Event ON Event.ID = Command.Event 
            JOIN Competition ON Competition.ID = Event.Competition 
            WHERE 1 = 1
            " . Team_data::TEAM_BASE_FILTER . "
            " . Competition_data::COMPETIITON_BASE_FILTER . " 
            AND (COALESCE(Competitor.WCAID,0) != 0 OR COALESCE(Competitor.WID,0) != 0)  
        ");
    }

    static function getCompetitorsIdByTeamId($teamId) {
        if (!is_numeric($teamId)) {
            return false;
        }

        return DataBaseClass::getColumn("
            SELECT Competitor
            FROM CommandCompetitor 
            WHERE Command=$teamId
        ");
    }

}
