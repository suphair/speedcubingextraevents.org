<?php

class Log_data {

    static function getLogsAuthorisations($deep) {
        return DataBaseClass::getRowsObject(" 
            SELECT * FROM (
                SELECT 
                CASE
                    WHEN Action='Login' AND Object='WCA_Auth' THEN 'login' 
                    WHEN Action='Login' AND Object='Alternative' THEN 'alternative' 
                    WHEN Action='Logout' AND Object='WCA_Auth' THEN 'logout'
                END action,
                DateTime timestamp,
                Competitor competitorWid
                FROM Logs 
                WHERE DATE(DateTime) >= DATE_ADD(current_date(),INTERVAL -$deep Day)
                )t 
            WHERE action IS NOT NULL
            ORDER BY Timestamp DESC
        ");
    }

    static function getLogsRegistrations($deep) {
        return DataBaseClass::getRowsObject("
    
            Select 
                Timestamp timestamp,
                Action,
                Event competitionEventID,            
                CASE
                    WHEN Action like '%D%' THEN 'delegate' 
                    WHEN Action like '%C%' THEN 'competitor' 
                    WHEN Action like '%S%' THEN 'scoretaker' 
                END actionIcon,  
                CASE
                    WHEN Action like '%x%' THEN 'delete' 
                    WHEN Action like '%-%' THEN 'remove' 
                    WHEN Action like '%*%' THEN 'new' 
                    WHEN Action like '%+%' THEN 'add' 
                    WHEN Action like '%!%' THEN 'link' 
                END action,  
                CASE
                    WHEN Doing like '%Delegate:%' THEN 'delegate' 
                    WHEN Doing like '%Competitor:%' THEN 'competitor' 
                    WHEN Doing like '%ScoreTaker%' THEN 'scoreTaker' 
                END activistIcon,  
                REPLACE(
                    REPLACE(Doing,'Delegate:',''),
                    'Competitor:','') activist,  
                REPLACE(
                    REPLACE(Details,': ',':<br>'),
                    ',','<br>') details

                FROM LogsRegistration
                where date(Timestamp)>=DATE_ADD(current_date(),INTERVAL -$deep Day)  
                order by Timestamp desc
        ");
    }

    static function getLogsScrambles($deep) {
        return DataBaseClass::getRowsObject("
            SELECT 
            ScramblePdf.Timestamp timestamp,
            ScramblePdf.Secret secret,
            CASE ScramblePdf.Secret
               WHEN Event.ScrambleSalt THEN 'actual'
               WHEN Event.ScramblePublic THEN 'published'
            END status,
            ScramblePdf.Event competitionEvent,
            ScramblePdf.Delegate delegateID,
            ScramblePdf.Action action

            FROM ScramblePdf 
            JOIN Event ON ScramblePdf.Event = Event.ID 
            WHERE DATE(ScramblePdf.Timestamp) >= DATE_ADD(current_date(), INTERVAL -$deep Day)  
            ORDER BY ScramblePdf.Timestamp DESC");
    }

    static function getLogsCron($deep) {
        return DataBaseClass::getRowsObject("
                SELECT 
                Object object,
                DateTime timestamp,
                Details details
                FROM Logs 
                WHERE DATE(DateTime) >= DATE_ADD(current_date(),INTERVAL -$deep Day)
                AND Action='Cron'
                ORDER BY DateTime DESC
           ");
    }
    
    static function getLogsCronObjects($deep) {
        return DataBaseClass::getColumn("
                SELECT DISTINCT Object
                FROM Logs 
                WHERE DATE(DateTime) >= DATE_ADD(current_date(),INTERVAL -$deep Day)
                AND Action='Cron'
           ");
    }
    
    
    

    static function getLogsMail($deep) {
        return DataBaseClass::getRowsObject("
                SELECT 
                Id id,
                `To` 'to',
                Subject subject,
                Body body,
                DateTime timestamp,
                CASE Result
                    WHEN 1 THEN null
                    ELSE Result
                END result,
                CASE result
                    WHEN 1 THEN 'send'
                    ELSE 'error'
                END status
                FROM LogMail 
                WHERE DATE(DateTime) >= DATE_ADD(current_date(),INTERVAL -$deep Day)
                ORDER BY DateTime DESC
           ");
    }

}
