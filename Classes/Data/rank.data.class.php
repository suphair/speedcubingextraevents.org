<?php

Class Rank_data {

    static function getAttempt($eventId, $competitorId, $special) {

        $select = 'Attempt.vOrder "value",
                   Attempt.vOut "out"
                   FROM Attempt WHERE ID = (
                   SELECT Attempt.ID';


        $where = Attempt::getFormat($special) . "
        " . Competition_data::COMPETIITON_OFFICIAL . "
        AND Discipline.ID = $eventId 
        AND Competitor.ID = $competitorId
        ORDER by Attempt.vOrder
        LIMIT 1
        )";

        $attempt = DataBaseClass::getRowObject(
                        self::getSql($select, $where));
        return New Attempt($attempt);
    }

    static function getRank($eventId, $special, $value, $filter = false) {
        if (!$value) {
            return false;
        }

        $select = "COUNT(distinct Competitor.ID)";

        $where = Attempt::getFormat($special) . "
        " . Competition_data::COMPETIITON_OFFICIAL . "
        AND Discipline.ID = $eventId 
        AND Attempt.vOrder < $value";

        if (isset($filter['country'])) {
            $where .= " AND Country.ISO2 = '" . DataBaseClass::Escape($filter['country']) . "'";
        }
        if (isset($filter['continent'])) {
            $where .= " AND Continent.Code = '" . DataBaseClass::Escape($filter['continent']) . "'";
        }

        return DataBaseClass::getValue(
                        self::getSql($select, $where)) + 1;
    }

    private static function getSql($select, $where) {
        return "
        SELECT 
            $select
        FROM Command 
        LEFT OUTER JOIN Country ON Country.ISO2 = Command.vCountry
        LEFT OUTER JOIN Continent ON Continent.Code = Country.Continent
        JOIN CommandCompetitor ON CommandCompetitor.Command = Command.ID 
        JOIN Attempt ON Attempt.Command = Command.ID
        JOIN Competitor ON Competitor.ID = CommandCompetitor.Competitor 
        JOIN Event ON Event.ID = Command.Event 
        JOIN Competition ON Competition.ID = Event.Competition
        JOIN DisciplineFormat ON Event.DisciplineFormat = DisciplineFormat.ID 
        JOIN Discipline ON Discipline.ID = DisciplineFormat.Discipline 
        WHERE 1 = 1
        $where 
    ";
    }

}
