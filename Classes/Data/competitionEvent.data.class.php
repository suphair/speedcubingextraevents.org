<?php

Class CompetitionEvent_data {

    static function getById($id) {
        if (!is_numeric($id)) {
            return false;
        }
        return self::getByFilter("AND Event.ID=$id");
    }

    static function getBySecret($secret) {
        if (!$secret) {
            return false;
        }
        return self::getByFilter("AND LOWER(Event.Secret) = LOWER('$secret')");
    }

    private static function getByFilter($filter) {
        return DataBaseClass::getRowObject("
            SELECT 
                Event.ID id,
                Event.Secret secret,
                Event.Competition competitionId,
                DisciplineFormat.Discipline eventId,
                Event.Round round
            FROM Event
            JOIN DisciplineFormat on DisciplineFormat.ID = Event.DisciplineFormat
            WHERE 1 = 1
            $filter
        ");
    }

    public static function getCompetitionEventIdIdByCompetitionId($competitionId){
        if (!is_numeric($competitionId)) {
            return [];
        }
        return DataBaseClass::getColumn("
            SELECT DISTINCT 
                Event.ID
            FROM Competition
            JOIN Event on Event.Competition=$competitionId
            JOIN DisciplineFormat on DisciplineFormat.ID = Event.DisciplineFormat
            JOIN Discipline on Discipline.ID = DisciplineFormat.Discipline
        ");
        
        
    }
    
}
