<?php

Class Competition_data {

    CONST COMPETIITON_BASE_FILTER = "
        AND Competition.Unofficial = 0  
        AND Competition.Technical = 0 
    ";
    CONST COMPETIITON_OFFICIAL = "
        AND Competition.Unofficial = 0  
    ";
    CONST COMPETIITON_PUBLIC = "
        AND Competition.Technical = 0 
    ";

    static function getById($id) {
        if (is_numeric($id)) {
            return self::getBy("ID = $id");
        } else {
            return false;
        }
    }

    static function getByWca($wca) {
        return self::getBy("WCA = '" . DataBaseClass::Escape($wca) . "'");
    }

    private static function getBy($where) {
        return DataBaseClass::getRowObject("
            SELECT 
                ID id,
                WCA wca,
                City city,
                Name name,
                EndDate endDate,
                Country countryCode,
                StartDate startDate,
                Unofficial unofficial,
                Onsite onsite,
                CASE
                    WHEN Technical = 1 THEN 'technical'
                    WHEN Status = -1 THEN 'covid-19'
                    WHEN Status = 0 THEN 'hidden'
                    WHEN current_date < StartDate THEN 'upcoming'
                    WHEN current_date >= StartDate 
                        AND current_date <= EndDate THEN 'running'
                    WHEN current_date > EndDate THEN 'past'
                END status
            FROM Competition
            WHERE $where
        ");
    }

    static function getCompetitionsIdbyDelegate($id, $filterValues) {

        if (!is_numeric($id)) {
            return [];
        }

        return DataBaseClass::getColumn("
            SELECT 
                Competition.ID
            FROM CompetitionDelegate 
            JOIN Competition ON Competition.ID = CompetitionDelegate.Competition 
            WHERE 1 = 1 
                %f
                AND CompetitionDelegate.Delegate = '$id'
            ORDER BY Competition.StartDate DESC
        ", self::buildWherebyFilter($filterValues));
    }

    static function getCompetitionsIdByFilter($filter) {

        return DataBaseClass::getColumn("
            SELECT 
                Competition.ID
            FROM Competition
            WHERE 1 = 1 
            %f
        ", self::buildWherebyFilter($filter));
    }

    static function getCompetitionsId() {

        return DataBaseClass::getColumn("
            SELECT 
                Competition.ID
            FROM Competition
        ");
    }

    private static function buildWherebyFilter($filter) {
        $where = [];
        foreach ($filter as $value) {
            if ($value->type == 'Country') {
                $where[] = "Competition.Country='" . DataBaseClass::Escape($value->value) . "'";
            }
            if ($value->type == 'Year') {
                $where[] = "YEAR(Competition.StartDate)='" . DataBaseClass::Escape($value->value) . "'";
            }
        }

        if (!CheckAccess('Competitions.Hidden')) {
            $where[] = "Competition.Status != 0";
        }

        if (!CheckAccess('Competitions.Secret')) {
            $where[] = "Technical = 0";
        }
        return $where;
    }

    static function getCountriesCodeByCompetitionsId($listCompetitions) {
        if (!$listCompetitions) {
            return [];
        }

        $where = " Competition.ID in ( " . implode(',', $listCompetitions) . ')';

        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                Competition.Country
            FROM Competition
            WHERE 1 = 1 
            AND Country!=''
            %f
        ", $where);
    }

    static function getYearsByCompetitionsId($listCompetitions) {
        if (!$listCompetitions) {
            return [];
        }

        $where = " Competition.ID in ( " . implode(',', $listCompetitions) . ')';

        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                YEAR(Competition.StartDate)
            FROM Competition
            WHERE 1 = 1 
            %f
        ", $where);
    }

    static function getCompetitionsIdByCompetitor($id, $filterValues) {

        if (!is_numeric($id)) {
            return [];
        }

        return DataBaseClass::getColumn("
            SELECT DISTINCT
                Competition.ID
            FROM Competition
            JOIN Event ON Event.Competition = Competition.ID
            JOIN Command on Command.Event = Event.ID
                AND Command.Decline = 0
            JOIN CommandCompetitor ON CommandCompetitor.Command = Command.ID 
            WHERE 1 = 1 
                %f
                AND CommandCompetitor.Competitor = '$id'
        ", self::buildWherebyFilter($filterValues));
    }

}
