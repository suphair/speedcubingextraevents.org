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
                Event.Round round,
                Event.vRound view_round,
                Event.Secret secret,
                Event.Cumulative cumulative,
                Event.LimitSecond limitSecond,
                Event.LimitMinute limitMinute,
                Event.Competition competitionId,
                Event.CutoffSecond cutoffSecond,
                Event.CutoffMinute cutoffMinute,
                case Format.Result
                    when 'Sum' then '".CompetitionEvent::FORMAT_SUM."'
                    when 'Best' then '".CompetitionEvent::FORMAT_BEST."'
                    when 'Mean' then '".CompetitionEvent::FORMAT_MEAN."'
                    when 'Average' then '".CompetitionEvent::FORMAT_AVERAGE."'
                end format1,
                case Format.ExtResult
                    when 'Sum' then '".CompetitionEvent::FORMAT_SUM."'
                    when 'Best' then '".CompetitionEvent::FORMAT_BEST."'
                    when 'Mean' then '".CompetitionEvent::FORMAT_MEAN."'
                    when 'Average' then '".CompetitionEvent::FORMAT_AVERAGE."'
                end format2,
                Format.Attemption attemptions,
                DisciplineFormat.Discipline eventId
            FROM Event
            JOIN DisciplineFormat ON DisciplineFormat.ID = Event.DisciplineFormat
            JOIN Format ON Format.ID = DisciplineFormat.Format 
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
    
    public static function getScrambles($id){
        return DataBaseClass::getRowObject(" SELECT scrambles FROM Event WHERE ID=$id ")->scrambles ?? FALSE;
    }
    
}
