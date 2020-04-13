<?php

Class CompetitionEvent {

    public $id = false;
    public $link = false;
    public $event;
    public $round = false;
    public $competition;
    public $competitionId = false;

    function __construct() {
        $this->competition = new Competition();
        $this->event = new Event();
    }

    function getByid($id) {
        $competitionEvent = CompetitionEvent_data::getById($id);
        if ($competitionEvent) {
            $this->id = $id;
            $this->competition->getById($competitionEvent->competitionId);
            $this->round = $competitionEvent->round;
            $this->event->getbyID($competitionEvent->eventId);
            $this->link = PageIndex() .
                    "Competition/{$this->competition->wca}" .
                    "/{$this->event->code}/{$this->round}";
        }
    }

}
