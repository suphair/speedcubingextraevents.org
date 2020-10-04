<?php

Class Competitor {

    public $id = false;
    public $wid = false;
    public $name = false;
    public $wcaid = false;
    public $email = false;
    public $country;
    public $link = false;
    public $linkWca = false;
    public $linkApiUser = false;

    function __construct() {
        $this->country = new Country();
    }

    function getCurrent() {
        if (isset($_SESSION['competitorWid'])) {
            $this->getByWid($_SESSION['competitorWid']);
        }
    }

    function getByKey($key) {
        if (is_numeric($key)) {
            $this->getById($key);
        } else {
            $this->getByWcaid($key);
        }
    }

    function getById($id) {
        $competitor = Competitor_data::getById($id);
        if ($competitor and $competitor != new stdClass()) {
            $this->SetbyRow($competitor);
        }
    }

    function getByWid($wid) {
        $competitor = Competitor_data::getByWid($wid);
        if ($competitor and $competitor != new stdClass()) {
            $this->SetbyRow($competitor);
        }
    }

    function getByWcaid($wcaid) {
        $competitor = Competitor_data::getByWcaid($wcaid);
        if ($competitor and $competitor != new stdClass()) {
            $this->SetbyRow($competitor);
        }
    }

    private function SetbyRow($competitor) {
        $this->id = $competitor->id;
        $this->wid = $competitor->wid;
        $this->wcaid = $competitor->wcaid;
        if (CheckAccess('Competitor.Email')) {
            $this->email = $competitor->email;
        }
        if ($this->wcaid) {
            $this->link = PageIndex() . "/Competitor/$this->wcaid";
            $this->linkWca = "https://www.worldcubeassociation.org/persons/{$this->wcaid}";
        } else {
            $this->link = PageIndex() . "/Competitor/$this->id";
        }

        if ($this->wid) {
            $this->linkApiUser = "https://www.worldcubeassociation.org/api/v0/users/{$this->wid}";
        }

        $this->name = $competitor->name;
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

    function getEventsIdWithAttempt() {
        return Event_data::getEventsIdWithAttemptByCompetitorID($this->id);
    }

    function getEventsId() {
        return Event_data::getEventsIdByCompetitorID($this->id);
    }

    function getRankByEventId($eventId) {
        $rank = new Rank();
        $rank->getRankByCompetitorAndEventId($this, $eventId);
        return $rank;
    }

    static function getCompetitorsByEventId($eventId) {
        return Competitor::getCompetitorsByCompetitorsId(
                        Competitor_data::getCompetitorsIdByEventId($eventId));
    }

    static function getCompetitorsByEventIdCountryCode($eventId, $contryCode) {
        return Competitor::getCompetitorsByCompetitorsId(
                        Competitor_data::getCompetitorsIdByEventId(
                                $eventId, Competitor_data::COUNTRY, $contryCode)
        );
    }

    static function getCompetitorsByEventIdContinentCode($eventId, $continentCode) {
        return Competitor::getCompetitorsByCompetitorsId(
                        Competitor_data::getCompetitorsIdByEventId(
                                $eventId, Competitor_data::CONTINENT, $continentCode)
        );
    }

    static function getCompetitorsByCompetitorsId($competitorsId) {
        $competitors = [];
        foreach ($competitorsId as $competitorId) {
            $competitor = new Competitor();
            $competitor->getById($competitorId);
            $competitors[] = $competitor;
        }
        return $competitors;
    }

    static function getBestAttempts($competitors, $eventId, $filterFormat) {

        foreach ($competitors as $c => $competitor) {
            $teams = Team::getTeamsByTeamsId(
                            Team::getTeamsIdByEventIdCompetitorId($eventId, $competitor->id)
            );

            foreach ($teams as $t => &$team) {
                if ($team->competitionEvent->competition->unofficial) {
                    unset($teams[$t]);
                } else {
                    if ($filterFormat == Attempt::SINGLE) {
                        $team->getAttemptsSpecial(Attempt::SINGLE);
                    } else {
                        $team->getAttemptsSpecial(Attempt::AVERAGE);
                        $team->getAttemptsNumeric();
                    }
                }
            }

            if (!($teams[0] ?? FALSE)) {
                unset($competitors[$c]);
                continue;
            }
            usort($teams, function($a, $b) {
                $value_a = $a->attemptSpecial->value;
                $value_b = $b->attemptSpecial->value;
                $strcmp1 = strcmp($value_a, $value_b);
                if ($strcmp1 !== 0) {
                    return $strcmp1;
                }

                $endDate_a = $a->competitionEvent->competition->endDate;
                $endDate_b = $b->competitionEvent->competition->endDate;
                $strcmp2 = strcmp($endDate_a, $endDate_b);
                return $strcmp2;
            });
            if ($teams[0]->attemptSpecial->value) {
                $competitors[$c]->team = $teams[0];
            } else {
                unset($competitors[$c]);
            }
        }

        
        usort($competitors, function($a, $b) {
            $value_a = $a->team->attemptSpecial->value;
            $value_b = $b->team->attemptSpecial->value;
            $strcmp1 = strcmp($value_a, $value_b);
            if ($strcmp1 !== 0) {
                return $strcmp1;
            }

            $name_a = $a->name;
            $name_b = $b->name;
            $strcmp2 = strcmp($name_a, $name_b);
            return $strcmp2;
        });

        return $competitors;
    }

}
