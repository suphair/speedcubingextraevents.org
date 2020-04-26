<?php

Class CompetitionEvent {

    public $id = false;
    public $link = false;
    public $event;
    public $round = false;
    public $secret = false;
    public $competition;
    public $competitionId = false;

    function __construct() {
        $this->competition = new Competition();
        $this->event = new Event();
    }

    function getById($id) {
        $competitionEvent = CompetitionEvent_data::getById($id);
        if ($competitionEvent) {
            $this->SetbyRow($competitionEvent);
        }
    }

    function getBySecret($secret) {
        $competitionEvent = CompetitionEvent_data::getBySecret($secret);
        if ($competitionEvent) {
            $this->SetbyRow($competitionEvent);
        }
    }

    function SetbyRow($competitionEvent) {
        $this->id = $competitionEvent->id;
        $this->round = $competitionEvent->round;
        $this->event->getbyID($competitionEvent->eventId);
        $this->secret = $competitionEvent->secret;
         $this->competition->getById($competitionEvent->competitionId);
        $this->link = PageIndex() .
                "Competition/{$this->competition->wca}" .
                "/{$this->event->code}/{$this->round}";
    }
    
    static function getCompetitionEventIdIdByCompetitionId($competitionId){
        return CompetitionEvent_data::getCompetitionEventIdIdByCompetitionId($competitionId);
    }

}
