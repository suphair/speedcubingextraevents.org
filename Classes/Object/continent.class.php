<?php

Class Continent {

    public $code = false;
    public $name = false;

    function getByCode($code) {
        $continent = Continent_data::getByCode($code);
        if ($continent) {
            $this->code = $continent->code;
            $this->name = $continent->name;
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
