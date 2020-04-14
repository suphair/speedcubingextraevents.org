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
        foreach (Attempt::getRecordsByEventIdCountryCode($eventAtt->id, $request->country) as $record) {
            $records[] = $record;
        }
    } elseif ($request->continent) {
        foreach (Attempt::getRecordsByEventIdContinentCode($eventAtt->id, $request->continent) as $record) {
            $records[] = $record;
        }
    } else {
        foreach (Attempt::getRecordsByEventIdWorld($eventAtt->id) as $record) {
            $records[] = $record;
        }
    }

    usort($records, function($a, $b) {
        if ($a->team->competitionEvent->competition->endDate == $b->team->competitionEvent->competition->endDate) {
            if ($a->team->competitionEvent->event->code == $b->team->competitionEvent->event->code) {
                return strcmp($a->format, $b->format);
            }
            return strcmp($a->team->competitionEvent->event->code, $b->team->competitionEvent->event->code);
        }
        return strcmp($b->team->competitionEvent->competition->endDate, $a->team->competitionEvent->competition->endDate);
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
