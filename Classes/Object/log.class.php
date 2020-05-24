<?php

Class Log {

    CONST DEEP = 14;

    static function getLogsAuthorisations() {
        $logs = Log_data::getLogsAuthorisations(self::DEEP);
        foreach ($logs as &$log) {
            $competitor = new Competitor();
            $competitor->getByWid($log->competitorWid);
            $log->competitor = $competitor;
        }
        return $logs;
    }

    static function getLogsRegistrations() {
        $logs = Log_data::getLogsRegistrations(self::DEEP);

        foreach ($logs as &$log) {
            $competitionEvent = new CompetitionEvent();
            $competitionEvent->getById($log->competitionEventID);
            $competitionEvent->getCompetition();
            $log->competitionEvent = $competitionEvent;
        }
        return $logs;
    }

    static function getLogsScrambles() {
        $logs = Log_data::getLogsScrambles(self::DEEP);
        foreach ($logs as &$log) {
            $competitionEvent = new CompetitionEvent();
            $competitionEvent->getById($log->competitionEvent);
            $competitionEvent->getCompetition();
            $log->competitionEvent = $competitionEvent;

            $delegate = new Delegate();
            $delegate->getById($log->delegateID);
            $log->delegate = $delegate;

            if (file_exists("Image/Scramble/{$log->secret}.pdf")) {
                $log->fileScramble = PageIndex() . "Scramble/{$log->secret}";
            } else {
                $log->fileScramble = false;
            }
        }
        return $logs;
    }

    static function getLogsCron() {
        $logs = Log_data::getLogsCron(self::DEEP);
        return $logs;
    }
    
    static function getLogsCronNames() {
        $names = Log_data::getLogsCronNames();
        sort($names);
        return $names;
    }
    

    static function getLogsMail() {
        $logs = Log_data::getLogsMail(self::DEEP);
        return $logs;
    }

}
