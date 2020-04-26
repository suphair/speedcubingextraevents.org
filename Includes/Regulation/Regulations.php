<?php

$language = $_SESSION['language_select'];

$events = Event::getEventsByEventsId(
                Event::getEventsIdByFilter());

$event = new Event();
$event->getByCode(getPathElement('regulations', 1));
if (!$event->id) {
    $event = $events[0];
}

$event->getRegulation($language);

$text = (object) [
            'team' => false,
            'mguild' => false,
            'multiPuzzles' => false,
            'longInspection' => false,
];
if ($event->isTeam) {
    $text->team = Parsedown(
            str_replace(
                    "%1", $event->competitorsTeam, getBlockText(
                            'Regulation.Competitors', $language)
            )
            , false);
}

if ($event->longInspection) {
    $text->longInspection = Parsedown(
            getBlockText(
                    'Regulation.Inspect.20', $language
            )
            , false);
}

if ($event->multiPuzzles) {
    $text->multiPuzzles = Parsedown(
            getBlockText(
                    'Regulation.puzzles', $language
            )
            , false);
}

if (strpos($event->codeScript, 'mguild') !== false) {
    $text->mguild = Parsedown(
            getBlockText(
                    'Regulation.mguild', $language
            )
            , false);
}

$data = arrayToObject([
    'text' => $text,
    'event' => $event,
    'events' => $events,
    'language' => $language
        ]);

IncludeClass::Template('Regulations', $data);

