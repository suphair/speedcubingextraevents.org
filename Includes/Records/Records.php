<?php

$request = inputGet(
        [
            'event',
            'country',
            'continent'
        ]);

$event = new Event();

if ($request->event) {
    $event->getByCode($request->event);
}

$eventsID = Event::getEventsIdByFilter();
$events = Event::getEventsByEventsID($eventsID);


$results = array();
$formats = array();

if ($event->id) {
    $EventsIDForRecords = [$event->id];
} else {
    $EventsIDForRecords = $eventsID;
}

$records = [];
foreach ($event->id ? [$event] : $events as $eventAtt) {

    if ($request->country) {
        $eventAtt->getEventsRecordByCountry($request->country);
    } elseif ($request->continent) {
        $eventAtt->getEventsRecordByContinent($request->continent);
    } else {
        $eventAtt->getEventsRecord();
    }

    foreach ($eventAtt->eventsRecord as $eventsRecord) {
        if (!$eventsRecord->competitionTechnical) {
            $team = new Team();
            $team->getById($eventsRecord->teamID);
            $eventsRecord->team = $team;
            $eventsRecord->event = $eventsRecord->team->competitionEvent->event;
            unset ($eventsRecord->team->competitionEvent->event);
            $records[] = $eventsRecord;
        }
    }

    usort($records, function($a, $b) {
        if ($a->competitionDate == $b->competitionDate) {
            if ($a->event->code == $b->event->code) {
                return strcmp($a->format, $b->format);
            }
            return strcmp($a->event->code, $b->event->code);
        }
        return strcmp($b->competitionDate, $a->competitionDate);
    });
}

$countries = Country::getCountriesByCountriesCode(
                Competitor::getCountriesCodeByAllCompetitors());

$continents = Continent::getContinentsByCountries($countries);

$data = (object) [
            'event' => $event,
            'events' => $events,
            'request' => $request,
            'records' => $records,
            'countries' => $countries,
            'continents' => $continents,
];

IncludeClass::Template('Records', $data);
