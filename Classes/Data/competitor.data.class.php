<?php

Class Competitor_data {

    CONST COUNTRY = 'country';
    CONST CONTINENT = 'continent';

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

    static function getCompetitorsIdByEventId($eventId, $type = false, $filter = false) {
        if (!is_numeric($eventId)) {
            return [];
        }

        switch ($type) {
            case self::COUNTRY:

                $where = " AND LOWER(Country.ISO2) = LOWER('" . DataBaseClass::Escape($filter) . "')";
                break;

            case self::CONTINENT:
                $where = " AND LOWER(Continent.Code) = LOWER('" . DataBaseClass::Escape($filter) . "')";
                break;

            default: $where = '';
        }

        return DataBaseClass::getColumn("
            SELECT DISTINCT
                Competitor.ID
            FROM Competitor
            JOIN CommandCompetitor ON CommandCompetitor.Competitor = Competitor.ID 
            JOIN Command ON CommandCompetitor.Command = Command.ID 
            JOIN Attempt ON Attempt.Command = Command.ID 
            LEFT OUTER JOIN Country ON Country.ISO2 = Competitor.Country 
            LEFT OUTER JOIN Continent ON Continent.Code = Country.Continent 
            JOIN Event ON Event.ID = Command.Event 
            JOIN DisciplineFormat on DisciplineFormat.ID=Event.DisciplineFormat
            JOIN Competition ON Competition.ID = Event.Competition 
            WHERE 1 = 1
            $where
            " . Team_data::TEAM_BASE_FILTER . "
            " . Competition_data::COMPETIITON_BASE_FILTER . " 
            " . Attempt_data::ATTEMPT_BASE_FILTER . " 
            AND DisciplineFormat.Discipline=$eventId 
        ");
    }

}
