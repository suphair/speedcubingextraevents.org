<?php

Class Event_data {

    CONST EVENT_BASE_FILTER = "
         AND Discipline.Status = 'Active'  
    ";

    static function getByID($id) {
        if (is_numeric($id)) {
            return self::getBy("ID = $id");
        } else {
            return [];
        }
    }

    static function getByCode($code) {
        return self::getBy("Code = '" . DataBaseClass::Escape($code) . "'");
    }

    static function getBy($where) {
        return DataBaseClass::getRowObject("
            SELECT 
                CodeScript codeScript,
                Name name,
                ID id,
                Code code,
                CASE
                    WHEN Status='Archive' then true
                    ELSE false
                END isArchive,
                CASE
                    WHEN TNoodles > 1 then true
                    ELSE false
                END multiPuzzles,
                CASE
                    WHEN Competitors > 1 then true
                    ELSE false
                END isTeam,
                CASE
                    WHEN Inspection = 20 then true
                    ELSE false
                END longInspection
            FROM Discipline
            WHERE 
                $where
        ");
    }

    static function getEventsCodeByCompetitionID($competitionId) {
        if (!is_numeric($competitionId)) {
            return [];
        }
        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                Discipline.Code
            FROM Competition
            JOIN Event on Event.Competition=$competitionId
            JOIN DisciplineFormat on DisciplineFormat.ID = Event.DisciplineFormat
            JOIN Discipline on Discipline.ID = DisciplineFormat.Discipline
        ");
    }

    static function getEventsCodeByCompetitionsId($listCompetitions) {

        if (!sizeof($listCompetitions)) {
            return [];
        }

        $where = " Competition.ID in ( " . implode(',', $listCompetitions) . ')';

        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                Discipline.Code
            FROM Competition
            JOIN Event on Event.Competition = Competition.ID
            JOIN DisciplineFormat on DisciplineFormat.ID = Event.DisciplineFormat
            JOIN Discipline on Discipline.ID = DisciplineFormat.Discipline
            WHERE 1 = 1 
            " . Event_data::EVENT_BASE_FILTER . "
            %f
        ", $where);
    }

    private static function buildWherebyFilter($filter) {
        $where = [];
        foreach ($filter as $value) {
            if ($value->type == 'simple') {
                $where[] = "Simple=1";
            }

            if ($value->type == 'nonsimple') {
                $where[] = "Simple=0";
            }

            if ($value->type == 'team') {
                $where[] = "Competitors>1";
            }

            if ($value->type == 'puzzles') {
                $where[] = "coalesce(TNoodles,'') <>''";
            }

            if ($value->type == 'wcapuzzle') {
                $where[] = "coalesce(GlueScrambles,0)=1";
            }

            if ($value->type == 'nonwcapuzzle') {
                $where[] = "coalesce(GlueScrambles,0)<>1";
            }

            if ($value->type == 'inscpection20') {
                $where[] = "Inspection=20";
            }

            if ($value->type == '333cube') {
                $where[] = "(TNoodle='333' or TNoodles='333') and GlueScrambles=1";
            }
        }


        if (!CheckAccess('Events.Archive')) {
            $where[] = "Status != 'Archive'";
        }
        return $where;
    }

    static function getEventsIdByFilter($filter) {
        return DataBaseClass::getColumn("
            SELECT ID id
            FROM `Discipline`
            WHERE 1=1 
            %f
        ", self::buildWherebyFilter($filter));
    }

    static function getCountCompetitorsByEventId($eventId) {
        return DataBaseClass::getValue("
            SELECT COUNT(DISTINCT CommandCompetitor.Competitor)
            FROM Event
            JOIN Command ON Command.Event = Event.ID  
            JOIN CommandCompetitor ON CommandCompetitor.Command = Command.ID
            JOIN Competition ON Competition.ID = Event.Competition 
            JOIN DisciplineFormat ON DisciplineFormat.ID = Event.DisciplineFormat
            WHERE DisciplineFormat.Discipline = $eventId
                " . Competition_data::COMPETIITON_BASE_FILTER . "
                " . Command_data::COMMAND_BASE_FILTER . "
         ");
    }

    static function getCountCompetitionsByEventId($eventId) {
        return DataBaseClass::getValue("
            SELECT COUNT(DISTINCT Event.Competition)
            FROM Event
            JOIN Competition ON Competition.ID = Event.Competition 
            JOIN DisciplineFormat ON DisciplineFormat.ID = Event.DisciplineFormat
            WHERE DisciplineFormat.Discipline = $eventId
            " . Competition_data::COMPETIITON_BASE_FILTER . "
         ");
    }

    private static function getWordRecord($type, $eventId) {
        return DataBaseClass::getRowObject("
            SELECT 
                Attempt.vOut result,
                Command.vCountry countryCode,
                Attempt.Special 
            FROM Attempt 
            JOIN Command ON Command.ID = Attempt.Command 
                AND Attempt.Special IN $type 
            JOIN Event on Event.ID = Command.Event 
            JOIN Competition ON Competition.ID = Event.Competition 
                AND Competition.Unofficial = 0 
            JOIN CommandCompetitor ON CommandCompetitor.Command = Command.ID 
            JOIN Competitor ON Competitor.ID = CommandCompetitor.Competitor 
            JOIN DisciplineFormat ON DisciplineFormat.ID = Event.DisciplineFormat 
                AND DisciplineFormat.Discipline = $eventId 
            ORDER BY Attempt.vOrder
            LIMIT 1 
        ");
    }

    static function getWordRecordSingle($eventId) {
        return self::getWordRecord("('Best', 'Sum')", $eventId);
    }

    static function getWordRecordAverage($eventId) {
        return self::getWordRecord("('Average', 'Mean')", $eventId);
    }

}
