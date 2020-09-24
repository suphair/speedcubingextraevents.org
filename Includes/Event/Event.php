<?php

$max_attempt = Attempt::MAX_ATTEMPT;

$select_attempts = "";
$join_attempts = "";
foreach (range(1, $max_attempt) as $a) {
    $select_attempts .= "Attemp{$a}.ID attemp{$a}Id, Attemp{$a}.vOut attemp{$a}Out, Attemp{$a}.Except attemp{$a}Except,";
    $join_attempts .= " left outer join Attempt Attemp{$a} on Attemp{$a}.Command=Attempt.Command and Attemp{$a}.Attempt={$a}";
}

$sql = "
select
Competitor.Name competitorName, Competitor.WCAID competitorWcaid,  Competitor.ID competitorId,
Competitor2.Name competitor2Name, Competitor2.WCAID competitor2Wcaid,  Competitor2.ID competitor2Id,
Competitor3.Name competitor3Name, Competitor3.WCAID competitor3Wcaid,  Competitor3.ID competitor3Id,
Competitor4.Name competitor4Name, Competitor4.WCAID competitor4Wcaid,  Competitor4.ID competitor4Id,
Continent.Code continentCode,Continent.Name continentName,
CountryCompetitor.Name countryCompetitorName, CountryCompetitor.ISO2 countryCompetitorCode,
CountryCompetition.Name countryCompetitionName, CountryCompetition.ISO2 countryCompetitionCode,
Attempt.vOut attemptOut, Attempt.Special attemptSpecial, Attempt.vOrder attemptOrder,
$select_attempts
Competition.Name competitionName, Competition.WCA competitionWca,
Command.Video video
from Competitor
join CommandCompetitor on Competitor.ID=CommandCompetitor.Competitor
join Command on Command.ID=CommandCompetitor.Command
join Event on Event.ID=Command.Event
join Competition on Competition.ID=Event.Competition and Competition.Unofficial=0 and Competition.Technical=0  
join DisciplineFormat on DisciplineFormat.ID=Event.DisciplineFormat
join Discipline on Discipline.ID=DisciplineFormat.Discipline
join Attempt on Attempt.Command=Command.ID
join Country CountryCompetitor on Competitor.Country=CountryCompetitor.ISO2
join Continent Continent on Continent.Code=CountryCompetitor.Continent
left outer join Country CountryCompetition on Competition.Country=CountryCompetition.ISO2
$join_attempts
    
left outer join CommandCompetitor CommandCompetitor2 on CommandCompetitor2.Command=Command.ID and CommandCompetitor2.ID!=CommandCompetitor.ID
left outer join Competitor Competitor2 on Competitor2.ID = CommandCompetitor2.Competitor and Competitor.ID!=Competitor2.ID

left outer join CommandCompetitor CommandCompetitor3 on CommandCompetitor3.Command=Command.ID and CommandCompetitor3.ID!=CommandCompetitor.ID and CommandCompetitor3.ID!=CommandCompetitor2.ID
left outer join Competitor Competitor3 on Competitor3.ID = CommandCompetitor3.Competitor and Competitor.ID!=Competitor3.ID and Competitor2.Name<Competitor3.Name

left outer join CommandCompetitor CommandCompetitor4 on CommandCompetitor4.Command=Command.ID and CommandCompetitor4.ID!=CommandCompetitor.ID and CommandCompetitor4.ID!=CommandCompetitor2.ID and CommandCompetitor4.ID!=CommandCompetitor3.ID
left outer join Competitor Competitor4 on Competitor4.ID = CommandCompetitor4.Competitor and Competitor.ID!=Competitor4.ID and Competitor3.Name<Competitor4.Name

where Discipline.Code='{event.code}'
and Attempt.Special in ({attempt.special})
and( 
(Discipline.Competitors = 1) or 
(Discipline.Competitors = 2 and Competitor2.ID is not null) or 
(Discipline.Competitors = 3 and Competitor2.ID is not null and Competitor3.ID is not null) or 
(Discipline.Competitors = 4 and Competitor2.ID is not null and Competitor3.ID is not null and Competitor4.ID is not null)
)
{competitor.country}
order by Attempt.vOrder

";



$event = new Event();
$event->getByCode(getPathElement('event', 1));
$event->getAttemptions();

$filterFormat = getPathElement('event', 2);
$filterCountry = getQueryElement('country');
$filterContinent = getQueryElement('continent');

if (!$filterFormat) {
    $filterFormat = Attempt::SINGLE;
}

$events = Event::getEventsByEventsId(
                Event::getEventsIdByFilter());

$countries = Country::getCountriesByCountriesCode(
                Attempt::getCountriesCodeForAttempts());
$continents = Continent::getContinentsByCountries($countries);

if ($filterCountry) {
    $county = " AND UPPER(Competitor.Country)=UPPER('$filterCountry') ";
} elseif ($filterContinent) {
    $county = " AND UPPER(Continent.Code)=UPPER('$filterContinent') ";
} else {
    $county = "  ";
}

if ($filterFormat == Attempt::SINGLE) {
    $special = "'Best','Sum'";
} else {
    $special = "'Average','Mean'";
}
DataBaseClass::Query(
        str_replace(['{event.code}', '{attempt.special}', '{competitor.country}']
                , [$event->code, $special, $county]
                , $sql));
$rows = DataBaseClass::getRows();
$rows_uniq = [];
foreach ($rows as $row) {
    if (!isset($rows_uniq[$row['competitorId']])) {
        $rows_uniq[$row['competitorId']] = $row;
    }
}
$competitors = [];
foreach ($rows_uniq as $c => $row) {

    $competitor['country']['image'] = "<span class='flag-icon flag-icon-" . strtolower($row['countryCompetitorCode']) . "'></span>";
    $competitor['country']['name'] = $row['countryCompetitorName'];
    $competitor['team']['attemptSpecial']['value'] = $row['attemptOrder'];
    $competitor['team']['attemptSpecial']['out'] = $row['attemptOut'];
    $competitor['link'] = PageIndex() . "Competitor/" . ($row['competitorWcaid'] ?: $row['competitorId']);
    $competitor['name'] = $row['competitorName'];
    $competitor['id'] = $row['competitorId'];
    $competitor['team']['video'] = $row['video'];

    $competitor['team']['attempts'] = [];

    foreach (range(1, $max_attempt) as $a) {
        if ($row["attemp{$a}Id"]) {
            $competitor['team']['attempts'][] = ['except' => $row["attemp{$a}Except"], 'out' => $row["attemp{$a}Out"]];
        }
    }

    $competitor['team']['competitors'] = [];
    if ($row['competitor2Id']) {
        $competitor['team']['competitors'][] = [
            'id' => $row['competitor2Id'],
            'link' => PageIndex() . "Competitor/" . ($row['competitor2Wcaid'] ?: $row['competitor2Id']),
            'name' => $row['competitor2Name']];
    }
    if ($row['competitor3Id']) {
        $competitor['team']['competitors'][] = [
            'id' => $row['competitor3Id'],
            'link' => PageIndex() . "Competitor/" . ($row['competitor3Wcaid'] ?: $row['competitor3Id']),
            'name' => $row['competitor3Name']];
    }
    if ($row['competitor4Id']) {
        $competitor['team']['competitors'][] = [
            'id' => $row['competitor4Id'],
            'link' => PageIndex() . "Competitor/" . ($row['competitor4Wcaid'] ?: $row['competitor4Id']),
            'name' => $row['competitor4Name']];
    }

    $competitor['team']['competitionEvent']['competition']['link'] = PageIndex() . "Competition/" . $row['competitionWca'];
    $competitor['team']['competitionEvent']['competition']['name'] = $row['competitionName'];
    $competitor['team']['competitionEvent']['competition']['image'] = "<span class='flag-icon flag-icon-" . strtolower($row['countryCompetitionCode']) . "'></span>";
    ;

    $competitors[] = $competitor;
    unset($competitor);
}


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
