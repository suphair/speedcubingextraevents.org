<?php

Class Competition {

    public $id = false;
    public $wca = false;
    public $name = false;
    public $city = false;
    public $date = false;
    public $startDate = false;
    public $country = false;
    public $unofficial = false;
    public $status = false;
    public $events = [];

    public function getByID($id) {
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
        $this->link = PageIndex() . "Competition/$competition->wca";
        $this->city = $competition->city;
        $this->date = date_range($competition->startDate, $competition->endDate);
        $this->startDate = $competition->startDate;
        $this->unofficial = $competition->unofficial;
        $this->status = $competition->status;
        $this->country = new Country();
        $this->country->getByCode($competition->countryCode);
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

        usort($competitions, function($a, $b) {
            return strcmp($b->startDate, $a->startDate);
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

}
