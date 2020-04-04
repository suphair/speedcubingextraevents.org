<?php

Class CompetitionEvent_data {

    static function getByID($id) {
        if (!is_numeric($id)) {
            return [];
        }

        return DataBaseClass::getRowObject("
            SELECT 
                Event.ID id,
                Event.Competition competitionId,
                DisciplineFormat.Discipline eventId,
                Event.Round round
            FROM Event
            JOIN DisciplineFormat on DisciplineFormat.ID = Event.DisciplineFormat
            WHERE Event.ID=$id
        ");
    }

}
