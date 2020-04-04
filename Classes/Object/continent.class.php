<?php

Class Continent {

    public $code = false;
    public $name = false;
    public $codeLower = false;

    function getByCode($code) {
        $this->code = $code;
        $continent = Continent_data::getByCode($this->code);
        if ($continent) {
            $this->code = $continent->code;
            $this->name = $continent->name;
            $this->codeLower = strtolower($continent->code);
        }
    }

    static function getContinentsByCountries($countries) {
        $continents = [];
        foreach ($countries as $country) {
            $continents[$country->continent->code] = $country->continent;
        }

        usort($continents, function($a, $b) {
            return strcmp($a->name, $b->name);
        });
        return $continents;
    }

}