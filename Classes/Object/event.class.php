<?php

Class Event {

    public $id = false;
    public $code = false;
    public $name = false;
    public $image = false;
    public $codes = false;
    public $isCup = false;
    public $isTeam = false;
    public $isArchive = false;
    public $codeScript = false;
    public $formatResult;
    public $eventsRecord = [];
    public $multiPuzzles = false;
    public $formatResultId = false;
    public $longInspection = false;
    public $competitorsTeam = 0;
    public $countCompetitors = false;
    public $countCompetitions = false;

    function __construct() {
        $this->formatResult = new formatResult();
    }

    function getByCode($code) {
        $event = Event_data::getByCode($code);
        if ($event != new stdClass()) {
            $this->SetbyRow($event);
        }
    }

    function getById($id) {
        $event = Event_data::getById($id);
        if ($event != new stdClass()) {
            $this->SetbyRow($event);
        }
    }

    function SetbyRow($event) {
        $this->id = $event->id;
        $this->code = $event->code;
        $this->name = $event->name;
        $codes = explode(",", $event->codes);
        if ($codes[0]) {
            $this->codes = $codes;
        }
        $this->isCup = strpos($event->codeScript, '_cup');
        $this->isTeam = $event->isTeam;
        $this->isArchive = $event->isArchive;
        $this->codeScript = $event->codeScript;
        $this->multiPuzzles = $event->multiPuzzles;
        $this->formatResultId = $event->formatResultId;
        $this->longInspection = $event->longInspection;
        $this->competitorsTeam = $event->competitorsTeam;
        
        $this->getCompetitorsCount();
        $this->getCompetitionsCount();

        if (file_exists("./Svg/{$this->codeScript}.svg")) {
            $this->image = "<i title='{$this->name}' class='fas ee-{$this->codeScript}'></i>";
        } else {
            $this->image = "<i title='{$this->name}' class='fas fa-question-circle'></i>";
        }
    }
    
    function getFormatResult(){
        if($this->formatResultId){
            $formatResult = new formatResult();
            $formatResult->getById($this->formatResultId);
            $this->formatResult = $formatResult;
        }
    }

    function getCompetitorsCount() {
        $this->countCompetitors = Event_data::getCountCompetitorsByEventId($this->id);
    }

    function getCompetitionsCount() {
        $this->countCompetitions = Event_data::getCountCompetitionsByEventId($this->id);
    }

    static function getEventsByEventsCode($eventsCodes) {
        $events = [];
        foreach ($eventsCodes as $code) {
            $event = new Event;
            $event->getbyCode($code);
            $events[] = $event;
        }
        usort($events, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $events;
    }

    static function getEventsCodeByCompetitionsId($competitionsID) {
        return Event_data::getEventsCodeByCompetitionsId($competitionsID);
    }

    static function getEventsCodeByCompetitionId($competitionID) {
        return Event_data::getEventsCodeByCompetitionId($competitionID);
    }

    static function getEventsIdByFilter($filter = []) {
        return Event_data::getEventsIdByFilter(arrayToObject($filter));
    }

    static function getEventsByEventsId($eventsId) {
        $events = [];
        foreach ($eventsId as $id) {
            $event = new Event();
            $event->getById($id);
            $events[] = $event;
        }

        usort($events, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $events;
    }

    function getWordRecord() {
        $records = (object) [];
        $records->single = Event_data::getWordRecordSingle($this->id);
        $records->average = Event_data::getWordRecordAverage($this->id);

        foreach ($records as &$record) {
            $record->country = new Country();
            if (!isset($record->result)) {
                $record->result = false;
            } else {
                $record->country->getByCode($record->countryCode);
            }
        }
        $this->wordRecords = $records;
    }

    function getAttemptions() {
        $this->attemptionCount = Event_data::getAttemptions($this->id);
    }

    function getRegulation($language) {
        $this->regulation = false;
        $this->regulation_language = false;
        $regulations = Event_data::getRegulationsByEventId($this->id);

        if (isset($regulations[$language]) and $regulations[$language]['regulation']) {
            $this->regulation = $regulations[$language]['regulation'];
            return;
        }


        foreach ($regulations as $regulation) {
            if ($regulation['regulation']) {
                $this->regulation = $regulation['regulation'];
                $this->regulation_language = new Country(true);
                $this->regulation_language->getByCode($regulation['language']);
                return;
            }
        }
    }

}
