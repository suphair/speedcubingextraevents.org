<?php

Class CompetitionEvent {

    public $id = false;
    public $link = false;
    public $event;
    public $round = false;
    public $secret = false;
    public $formats = [];
    public $formatsAmount = 0;
    public $limit;
    public $cutoff;
    public $cumulative = false;
    public $competition;
    public $attemptions = false;
    public $attemptionsCutoff = false;

    const FORMAT_SUM = 'Sum';
    const FORMAT_BEST = 'Best';
    const FORMAT_MEAN = 'Mean';
    const FORMAT_AVERAGE = 'Average';
    
    function __construct() {
        $this->competition = new Competition();
        $this->event = new Event();
        $this->cutoff = new Time();
        $this->limit = new Time();
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
        
        if(isset($competitionEvent->format1)){
           $this->formats[]= $competitionEvent->format1;
           $this->formatsAmount=1;
        }
        if(isset($competitionEvent->format2)){
           $this->formats[]= $competitionEvent->format2;
           $this->formatsAmount=2;
        }
        
        
        $this->cumulative = $competitionEvent->cumulative;
        $this->competition->getById($competitionEvent->competitionId);
        $this->attemptions = $competitionEvent->attemptions;

        switch ($competitionEvent->attemptions) {
            case 5:
                $this->attemptionsCutoff = 2;
                break;
            case 3:
                $this->attemptionsCutoff = 1;
                break;
            case 2:
                $this->attemptionsCutoff = 1;
                break;
            default:
                $this->attemptionsCutoff = 2;
        }

        $this->link = PageIndex() .
                "Competition/{$this->competition->wca}" .
                "/{$this->event->code}/{$this->round}";

        $this->limit->set($competitionEvent->limitMinute, $competitionEvent->limitSecond);
        $this->cutoff->set($competitionEvent->cutoffMinute, $competitionEvent->cutoffSecond);
    }

    static function getCompetitionEventIdIdByCompetitionId($competitionId) {
        return CompetitionEvent_data::getCompetitionEventIdIdByCompetitionId($competitionId);
    }

}
