<?php

Class Team_data {

    CONST TEAM_BASE_FILTER = " 
        AND Command.Decline = 0
        ";

    static function getTeamsIdByEventId($eventId) {
        if (!is_numeric($eventId)) {
            return false;
        }

        return DataBaseClass::getColumn("
            SELECT Command.Id 
            FROM Event
            JOIN DisciplineFormat ON DisciplineFormat.ID = Event.DisciplineFormat
            JOIN Command ON Command.Event = Event.ID
            WHERE 
            DisciplineFormat.Discipline = $eventId 
            " . Team_data::TEAM_BASE_FILTER . "
        ");
    }

    static function getById($teamId) {
        if (!is_numeric($teamId)) {
            return false;
        }
        return DataBaseClass::getRowObject("
            SELECT 
                ID id,
                CardID cardId,
                Onsite onsite,
                Event competitionEventId,
                Secret secret,
                Video video,
                Place place,
                Warnings warnings
            FROM Command
            WHERE ID = $teamId 
        ");
    }

    static function getTeamsIdByEventIdCompetitorId($eventId, $competitorId) {
        if (!is_numeric($eventId) or ! is_numeric($competitorId)) {
            return false;
        }

        return DataBaseClass::getColumn("
            SELECT 
                Command.ID
            FROM Command
            JOIN CommandCompetitor ON CommandCompetitor.Command = Command.ID
            JOIN Event on Event.ID=Command.Event
            JOIN DisciplineFormat on DisciplineFormat.ID=Event.DisciplineFormat
            WHERE CommandCompetitor.Competitor = $competitorId 
                AND DisciplineFormat.Discipline = $eventId
        ");
    }

}
