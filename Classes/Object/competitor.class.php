<?php

Class Competitor {

    public $id = false;
    public $wid = false;
    public $wcaid = false;
    public $name = false;
    public $country = false;
    public $link = false;
    public $linkWca = false;
    public $linkApiUser = false;

    function getCurrent() {
        if (isset($_SESSION['competitorWid'])) {
            $this->getByWid($_SESSION['competitorWid']);
        }
    }

    function getById($id) {
        $competitor = Competitor_data::getById($id);
        if ($competitor) {
            $this->SetbyRow($competitor);
        }
    }

    function getByWid($wid) {
        $competitor = Competitor_data::getByWid($wid);
        if ($competitor) {
            $this->SetbyRow($competitor);
        }
    }

    function getByWcaid($wcaid) {
        $competitor = Competitor_data::getByWcaid($wcaid);
        if ($competitor) {
            $this->SetbyRow($competitor);
        }
    }

    private function SetbyRow($competitor) {
        $this->id = $competitor->id;
        $this->wid = $competitor->wid;
        $this->wcaid = $competitor->wcaid;
        if ($this->wcaid) {
            $this->link = PageIndex() . "Competitor/$this->wcaid";
            $this->linkWca = "https://www.worldcubeassociation.org/persons/{$this->wcaid}";
        } else {
            $this->link = PageIndex() . "Competitor/$this->id";
        }

        if ($this->wid) {
            $this->linkApiUser = "https://www.worldcubeassociation.org/api/v0/users/{$this->wid}";
        }

        $this->name = $competitor->name;
        $this->country = new Country();
        $this->country->getByCode($competitor->countryCode);
    }

    function getListCompetitions() {
        if (!$this->id) {
            return [];
        }
        return Competitor_data::getListCompetitions($this->id);
    }

    static function getCountriesCodeByAllCompetitors() {
        return Competitor_data::getCountriesCodeByAllCompetitors();
    }

    static function getCompetitorsIdByTeamId($teamId) {
        return Competitor_data::getCompetitorsIdByTeamId($teamId);
    }

}
