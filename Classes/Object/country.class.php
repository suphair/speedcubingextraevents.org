<?php

Class Country {

    public $code = false;
    public $name = false;
    public $image = false;
    public $continent = false;

    function getByCode($code) {
        $this->code = $code;
        $country = Country_data::getByCode($this->code);
        if ($country) {
            $this->name = $country->countryName;
            $continent = new Continent();
            $continent->getByCode($country->continentCode);
            $this->continent = $continent;
            $this->getImage();
        }
    }

    function getImage() {
        if ($this->code) {
            $iconImage = strtolower($this->code);
            $this->image = "<span class='flag-icon flag-icon-$iconImage'></span>";
        } else {
            $this->image = "<i class='fas fa-globe'></i>";
        }
    }

    static function getCountriesByCountriesCode($listCode) {
        $countries = [];
        foreach ($listCode as $code) {
            $country = new Country;
            $country->getByCode($code);
            $countries[] = $country;
        }
        usort($countries, function($a, $b) {
            return strcmp($a->name, $b->name);
        });

        return $countries;
    }
}
