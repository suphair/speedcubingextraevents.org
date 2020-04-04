<?php

IncludeClass::Page('News.Announce');

$competitor = new Competitor;
$competitor->getCurrent();

$filterСountry = filter_input(INPUT_GET, 'country');
$filterYear = filter_input(INPUT_GET, 'year');

$filter = [];
if ($filterСountry) {
    $filter[] = [
        'type' => 'Country',
        'value' => $filterСountry
    ];
}

if ($filterYear) {
    $filter[] = [
        'type' => 'Year',
        'value' => $filterYear
    ];
}

$mine = (getPathElement('competitions', 1) == 'mine');
if ($mine) {
    $competitionsId = Competition::getCompetitionsIdByCompetitor($competitor->id);
} else {
    $competitionsId = Competition::getCompetitionsIdByFilter($filter);
}

$competitions = Competition::getCompetitionsByCompetitionsID($competitionsId);

$filerEvents = Event::getEventsByEventsCode(
                Event::getEventsCodeByCompetitionsId($competitionsId));

$competitionsAllId = Competition::getCompetitionsIdByFilter();

$countries = Country::getCountriesByCountriesCode(
                Competition::getCountriesCodeByCompetitionsId($competitionsAllId));

$years = Competition::getYearsByCompetitionsId($competitionsAllId);

$data = arrayToObject([
    'competitionAdd' => CheckAccess('Competition.Add'),
    'filter' => [
        'mine' => $mine,
        'country' => [
            'value' => $filterСountry,
            'options' => $countries
        ],
        'year' => [
            'value' => $filterYear,
            'options' => $years
        ],
    ],
    'competitor' => $competitor,
    'filerEvents' => $filerEvents,
    'competitions' => $competitions
        ]);

IncludeClass::Template('Competitions', $data);
IncludeClass::Template('CompetitionsList', $data);
