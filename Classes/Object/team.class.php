<?php

Class Team {

    public $id = false;
    public $video = false;
    public $competitors = [];
    public $competition = false;
    public $competitionEvent = false;

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
            $competitorsId = Competitor::getCompetitorsIdByTeamId($teamId);
            $competitors = [];
            foreach ($competitorsId as $competitorId) {
                $competitor = new Competitor();
                $competitor->getById($competitorId);
                $competitors[] = $competitor;
            }
            $this->competitors = $competitors;

            $competitionEvent = new CompetitionEvent();
            $competitionEvent->getByid($team->competitionEventId);
            $this->competitionEvent = $competitionEvent;

            $competition = new Competition();
            $competition->getById($competitionEvent->competitionId);
            $this->competition = $competition;
        }
    }

}
