<?php

Class Team {

    public $id = false;
    public $video = false;
    public $place = false;
    public $attempts = [];
    public $competitors = [];
    public $attemptSingle;
    public $attemptAverage;
    public $competitionEvent;

    function __construct() {
        $this->competitionEvent = new CompetitionEvent();
        $this->attemptSingle = new Attempt();
        $this->attemptAverage = new Attempt();
    }

    static function getTeamsIdByEventId($eventId) {
        return Team_data::getTeamsIdByEventId($eventId);
    }

    static function getTeamsByTeamsId($teamsEventId) {
        $teams = [];
        foreach ($teamsEventId as $teamId) {
            $team = new Team();
            $team->getById($teamId);
            $teams[] = $team;
        }
        return $teams;
    }

    function getById($teamId) {
        $team = Team_data::getById($teamId);

        if ($team and $team != new stdClass()) {
            $this->id = $team->id;
            $this->video = $team->video;
            $this->place = $team->place;
            $competitorsId = Competitor::getCompetitorsIdByTeamId($teamId);
            $competitors = [];
            foreach ($competitorsId as $competitorId) {
                $competitor = new Competitor();
                $competitor->getById($competitorId);
                $competitors[] = $competitor;
            }
            usort($competitors, function($a, $b) {
                return strcmp($a->name, $b->name);
            });


            $this->competitors = $competitors;
            $this->competitionEvent->getById($team->competitionEventId);
        }
    }

    function getAttempts() {
        $this->getAttemptsSpecial(Attempt::SINGLE);
        $this->getAttemptsSpecial(Attempt::AVERAGE);
        $this->getAttemptsNumeric();
    }

    function getAttemptsSpecial($special) {
        $unofficial = $this->competitionEvent->competition->unofficial;
        $this->attemptSpecial = new Attempt();
        if ($special == Attempt::SINGLE) {
            $this->attemptSingle->getByTeamIdFormat($this->id, Attempt::SINGLE, $unofficial);
            $this->attemptSpecial = &$this->attemptSingle;
        }
        if ($special == Attempt::AVERAGE) {
            $this->attemptAverage->getByTeamIdFormat($this->id, Attempt::AVERAGE, $unofficial);
            $this->attemptSpecial = &$this->attemptAverage;
        }
    }

    function getAttemptsNumeric() {
        $this->competitionEvent->event->getAttemptions();
        $attemptionCount = $this->competitionEvent->event->attemptionCount;
        $attempts = [];
        for ($number = 1; $number <= $attemptionCount; $number++) {
            $attempt = new Attempt();
            $attempt->getByTeamIdNumber($this->id, $number);
            $attempts[$number] = $attempt;
        }
        $this->attempts = $attempts;
    }

    static function getTeamsIdByEventIdCompetitorId($eventID, $competitorId) {
        return Team_data::getTeamsIdByEventIdCompetitorId($eventID, $competitorId);
    }

    static function sortByCompetition(&$teams) {
        usort($teams, function($a, $b) {
            return strcmp($b->competitionEvent->competition->startDate, $a->competitionEvent->competition->startDate);
        });
    }

}
