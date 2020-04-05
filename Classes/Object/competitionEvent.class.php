<?php

Class CompetitionEvent {

    public $id = false;
    public $competitionId = false;
    public $round = false;
    public $competition = false;
    public $event = false;
    

    function getByid($id) {
        $competitionEvent = CompetitionEvent_data::getById($id);
        if ($competitionEvent) {
            $this->id = $id;
            $this->competitionId = $competitionEvent->competitionId;
            $this->round = $competitionEvent->round;
            $event = new Event();
            $event->getbyID($competitionEvent->eventId);
            $this->event = $event;
        }
    }

    function getCompetition() {
        if ($this->competitionId) {
            $competition = new Competition();
            $competition->getById($this->competitionId);
            $this->competition = $competition;
        }
    }

}
