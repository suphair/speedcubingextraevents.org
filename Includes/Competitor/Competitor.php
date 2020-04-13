<?php

$competitorKey = getPathElement('Competitor', 1);

$competitor = new Competitor();
$competitor->getByKey($competitorKey);

$delegate = new Delegate();
$delegate->getByWcaid($competitor->wcaid);


$eventsWithAttempt = Event::getEventsByEventsID(
                $competitor->getEventsIdWithAttempt());

$events = Event::getEventsByEventsID(
                $competitor->getEventsId());


foreach ($eventsWithAttempt as &$event) {
    $event->rank = $competitor->getRankByEventId($event->id);
}

foreach ($events as &$event) {
    $event->getAttemptions();
    $teams = Team::getTeamsByTeamsId(
                    Team::getTeamsIdByEventIdCompetitorId($event->id, $competitor->id));
    foreach ($teams as &$team) {
        $team->getAttempts();
    }
    Team::sortByCompetition($teams);
    $event->teams = $teams;
}

$data = arrayToObject([
    'delegate' => $delegate,
    'competitor' => $competitor,
    'eventsRank' => $eventsWithAttempt,
    'eventsResult' => $events,
    'reload' => [
        'access' => CheckAccess('Competitor.Reload'),
        'message' => GetMessage('Competitor.Reload')
    ]
        ]);

IncludeClass::Template("Competitor", $data);
