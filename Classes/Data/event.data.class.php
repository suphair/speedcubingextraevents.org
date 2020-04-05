<?php

Class Event_data {

    CONST EVENT_BASE_FILTER = "
         AND Discipline.Status = 'Active'  
    ";

    static function getById($id) {
        if (is_numeric($id)) {
            return self::getBy("ID = $id");
        } else {
            return [];
        }
    }

    static function getByCode($code) {
        return self::getBy("LOWER(Code) = LOWER('" . DataBaseClass::Escape($code) . "')");
    }

    static function getBy($where) {
        return DataBaseClass::getRowObject("
            SELECT 
                CodeScript codeScript,
                Name name,
                ID id,
                LOWER(Code) code,
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
                " . Team_data::TEAM_BASE_FILTER . "
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

    static function getEventsRecordByEventId($eventID, $filter) {
        $where = "";
        if (isset($filter['country'])) {
            $where = "AND LOWER(Command.vCountry) = LOWER('" . DataBaseClass::Escape($filter['country']) . "')";
        }
        if (isset($filter['continent'])) {
            $where = "AND LOWER(Country.Continent) = LOWER('" . DataBaseClass::Escape($filter['continent']) . "')";
        }

        return DataBaseClass::getRowsObject("
            SELECT 
                DisciplineFormat.Discipline eventID,
                t.Special format,
                Command.ID teamID,
                Competition.EndDate competitionDate,
                CASE 
                    WHEN Competition.WCA LIKE 't.%' THEN 1
                    ELSE 0
                END competitionTechnical,
                t.vOrder value,
                Attempt.vOut result
            FROM(
                    SELECT 
                            MIN(vOrder) vOrder,
                            Discipline,
                            Special,
                            EndDate
                    FROM(
                            SELECT 
                                    Attempt.vOrder,
                                    DisciplineFormat.Discipline,
                                    CASE Attempt.Special
                                            WHEN 'Mean' THEN 'average'
                                            WHEN 'Average' THEN 'average'
                                            WHEN 'Best' THEN 'single'
                                            WHEN 'Sum' THEN 'single'
                                    END Special,
                                    Competition.EndDate
                            FROM Command
                            JOIN Attempt ON Attempt.Command = Command.ID
                            JOIN Event ON Event.ID = Command.Event
                            JOIN Competition ON Competition.ID=Event.Competition
                            LEFT OUTER JOIN Country ON Country.ISO2=Command.vCountry
                            JOIN DisciplineFormat 
                                ON DisciplineFormat.ID = Event.DisciplineFormat
                            WHERE 
                                Attempt.Special IS NOT NULL
                                AND Attempt.IsDNF = 0
                                AND DisciplineFormat.Discipline = $eventID
                                $where
                    )t
                    GROUP BY 
                            Discipline,
                            Special,
                            EndDate
            )t
            JOIN Attempt
                    ON Attempt.vOrder = t.vOrder
                    AND (
                            (Attempt.Special in ('Mean', 'Average') 
                                    AND t.Special = 'average')
                            OR
                            (Attempt.Special in ('Best', 'Sum') 
                                    AND t.Special = 'single')
                    )
            JOIN Command
                    ON Command.ID = Attempt.Command       
            JOIN Event
                    ON Event.ID = Command.Event
            JOIN Competition
                    ON Competition.ID = Event.Competition
                    AND Competition.EndDate = t.EndDate
            LEFT OUTER JOIN Country ON Country.ISO2=Command.vCountry
            JOIN DisciplineFormat 
                    ON DisciplineFormat.ID = Event.DisciplineFormat
                    AND DisciplineFormat.Discipline = t.Discipline
            WHERE 1 = 1
                $where
    ");
    }

}
