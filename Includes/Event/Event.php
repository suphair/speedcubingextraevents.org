<?php

$event = new Event();
$event->getByCode(getPathElement('event', 1));
$event->getAttemptions();

$filterFormat = getPathElement('event', 2);
$filterCountry = getQueryElement('country');
$filterContinent = getQueryElement('continent');

$events = Event::getEventsByEventsId(
                Event::getEventsIdByFilter());

$countries = Country::getCountriesByCountriesCode(
                Attempt::getCountriesCodeForAttempts());
$continents = Continent::getContinentsByCountries($countries);


if ($filterCountry) {
    $competitors = Competitor::getCompetitorsByEventIdCountryCode(
                    $event->id, $filterCountry
    );
} elseif ($filterContinent) {
    $competitors = Competitor::getCompetitorsByEventIdContinentCode(
                    $event->id, $filterContinent
    );
} else {
    $competitors = Competitor::getCompetitorsByEventId($event->id);
}

$competitors = Competitor::getBestAttempts($competitors, $event->id, $filterFormat);

$data = arrayToObject([
    'event' => $event,
    'events' => $events,
    'competitors' => $competitors,
    'filter' => [
        'format' => [
            'values' => [
                Attempt::SINGLE,
                Attempt::AVERAGE
            ],
            'value' => $filterFormat
        ],
        'country' => [
            'values' => $countries,
            'value' => $filterCountry],
        'continent' => [
            'values' => $continents,
            'value' => $filterContinent],
    ]
        ]);

IncludeClass::Template('Event', $data);
