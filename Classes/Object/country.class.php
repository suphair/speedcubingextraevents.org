<?php

Class Country {

    public $code = false;
    public $name = false;
    public $image = false;
    public $continent;
    public $isLanguage = false;

    function __construct($isLanguage = false) {
        $this->continent = new Continent();
        $this->isLanguage = $isLanguage;
    }

    function getByCode($code) {

        if ($this->isLanguage) {
            if (strtolower($code) == 'en') {
                $this->name = 'English';
                $this->code = 'gb';
            }
            if (strtolower($code) == 'ru') {
                $this->name = 'Русский';
                $this->code = 'ru';
            }
            $this->getImage();
            return;
        }
        $country = Country_data::getByCode($code);
        if ($country) {
            $this->code = $country->code;
            $this->name = $country->countryName;
            $this->continent->getByCode($country->continentCode);
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
