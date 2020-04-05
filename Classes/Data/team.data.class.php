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
                Event competitionEventId,
                Secret secret,
                Video video
            FROM Command
            WHERE ID = $teamId 
        ");
    }

}
