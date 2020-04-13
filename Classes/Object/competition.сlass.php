<?php

Class Competition {

    public $id = false;
    public $wca = false;
    public $name = false;
    public $city = false;
    public $date = false;
    public $link = false;
    public $status = false;
    public $events = [];
    public $endDate = false;
    public $country;
    public $startDate = false;
    public $unofficial = false;

    function __construct() {
        $this->country = new Country();
    }

    public function getById($id) {
        $competition = Competition_data::getById($id);
        if ($competition) {
            $this->SetbyRow($competition);
        }
    }

    function getByWca($wca) {
        $competition = Competition_data::getByWca($wca);
        if ($competition) {
            $this->SetbyRow($competition);
        }
    }

    private function SetbyRow($competition) {
        $this->id = $competition->id;
        $this->wca = $competition->wca;
        $this->name = $competition->name;
        $this->city = $competition->city;
        $this->date = date_range($competition->startDate, $competition->endDate);
        $this->link = PageIndex() . "Competition/$competition->wca";
        $this->status = $competition->status;
        $this->endDate = $competition->endDate;
        $this->country->getByCode($competition->countryCode);
        $this->startDate = $competition->startDate;
        $this->unofficial = $competition->unofficial;
    }

    function getEvents() {
        if ($this->id) {
            foreach (Event::getEventsCodeByCompetitionID($this->id) as $eventCode) {
                $event = new Event();
                $event->getbyCode($eventCode);
                $this->events[] = $event;
            }
        }
    }

    static function getCompetitionsIdbyDelegate($delegateID, $filterValues = []) {
        return Competition_data::getCompetitionsIdbyDelegate($delegateID, $filterValues);
    }

    static function getCompetitionsIdByCompetitor($competitorID, $filterValues = []) {
        return Competition_data::getCompetitionsIdByCompetitor($competitorID, $filterValues);
    }

    static function getCompetitionsByCompetitionsID($competitionsId) {
        $competitions = [];
        foreach ($competitionsId as $id) {
            $competition = new Competition();
            $competition->getById($id);
            $competition->getEvents();
            $competitions[] = $competition;
        }

        return self::SortByDateDesc($competitions);
    }

    static function SortByDateDesc($competitions) {
        usort($competitions, function($a, $b) {
            return strcmp($b->startDate, $a->startDate);
        });
        return $competitions;
    }

    static function SortByDateAsc($competitions) {
        usort($competitions, function($a, $b) {
            return strcmp($a->endDate, $b->endDate);
        });
        return $competitions;
    }

    static function getCompetitionsIdByFilter($filter = []) {
        return Competition_data::getCompetitionsIdByFilter(
                        arrayToObject($filter));
    }

    static function getCountriesCodeByCompetitionsId($competitionsId) {
        return Competition_data::getCountriesCodeByCompetitionsId($competitionsId);
    }

    static function getYearsByCompetitionsId($competitionsId) {
        $years = Competition_data::getYearsByCompetitionsId($competitionsId);
        rsort($years);
        return $years;
    }

    static function getCompetitionsId() {
        return Competition_data::getCompetitionsId();
    }

}
