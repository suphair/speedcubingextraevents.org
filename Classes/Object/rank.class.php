<?php

Class Rank {

    public $countrySingle = false;
    public $countryAverage = false;
    public $continentSingle = false;
    public $continentAverage = false;
    public $wordSingle = false;
    public $wordAverage = false;
    public $attemptSingle;
    public $attemptAverage;

    function __construct() {
        $this->attemptSingle = new Attempt();
        $this->attemptAverage = new Attempt();
    }

    function getRankByCompetitorAndEventId($competitor, $eventId) {

        $this->attemptSingle = Rank_data::getAttempt($eventId, $competitor->id, 'single');
        $this->attemptAverage = Rank_data::getAttempt($eventId, $competitor->id, 'average');

        $countryFilter = ['country' => $competitor->country->code];
        $continentFilter = ['continent' => $competitor->country->continent->code];
        $valueSingle = $this->attemptSingle->value;
        $valueAverage = $this->attemptAverage->value;

        $this->countrySingle = Rank_data::getRank(
                        $eventId, 'single', $valueSingle, $countryFilter);

        $this->continentSingle = Rank_data::getRank(
                        $eventId, 'single', $valueSingle, $continentFilter);

        $this->wordSingle = Rank_data::getRank(
                        $eventId, 'single', $valueSingle);

        $this->countryAverage = Rank_data::getRank(
                        $eventId, 'average', $valueAverage, $countryFilter);

        $this->continentAverage = Rank_data::getRank(
                        $eventId, 'average', $valueAverage, $continentFilter);

        $this->wordAverage = Rank_data::getRank(
                        $eventId, 'average', $valueAverage);
    }

}
