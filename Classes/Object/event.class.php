<?php

Class Event {

    public $id = false;
    public $code = false;
    public $name = false;
    public $image = false;
    public $codes = false;
    public $isTeam = false;
    public $isArchive = false;
    public $codeScript = false;
    public $eventsRecord = [];
    public $multiPuzzles = false;
    public $longInspection = false;
    public $countCompetitors = false;
    public $countCompetitions = false;

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
        $this->isTeam = $event->isTeam;
        $this->isArchive = $event->isArchive;
        $this->codeScript = $event->codeScript;
        $this->multiPuzzles = $event->multiPuzzles;
        $this->longInspection = $event->longInspection;


        $this->getCompetitorsCount();
        $this->getCompetitionsCount();

        if (file_exists("./Svg/{$this->codeScript}.svg")) {
            $this->image = "<i title='{$this->name}' class='fas ee-{$this->codeScript}'></i>";
        } else {
            $this->image = "<i title='{$this->name}' class='fas fa-question-circle'></i>";
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

    static function getEventsCodeByCompetitionID($competitionID) {
        return Event_data::getEventsCodeByCompetitionID($competitionID);
    }

    static function getEventsIdByFilter($filter = []) {
        return Event_data::getEventsIdByFilter(arrayToObject($filter));
    }

    static function getEventsByEventsID($eventsId) {
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

    function getEventsRecord($filter = []) {
        $eventsRecord = Event_data::getEventsRecordByEventId($this->id, $filter);
        $valuesCut = (object) [
                    'average' => false,
                    'single' => false
        ];
        foreach ($eventsRecord as $eventRecordKey => $eventRecord) {
            if (!isset($valuesCut->{$eventRecord->format})) {
                unset($eventsRecord->$eventRecordKey);
                continue;
            }
            $format = $eventRecord->format;
            if ($valuesCut->$format
                    and $valuesCut->$format < $eventRecord->value) {
                unset($eventsRecord->$eventRecordKey);
            } else {
                $valuesCut->$format = $eventRecord->value;
            }
            $eventRecord->date = date_range($eventRecord->competitionDate);
        }

        $this->eventsRecord = $eventsRecord;
    }

    function getEventsRecordByCountry($countryCode) {
        return $this->getEventsRecord(['country' => $countryCode]);
    }

    function getEventsRecordByContinent($continentCode) {
        return $this->getEventsRecord(['continent' => $continentCode]);
    }

    function getAttemptions() {
        $this->attemptionCount = Event_data::getAttemptions($this->id);
    }

}
