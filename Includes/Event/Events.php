<?php

$filter = getPathElement('events', 1);

$events = Event::getEventsByEventsID(
                Event::getEventsIdByFilter(
                        [['type' => getPathElement('events', 1)]]));

foreach ($events as &$event) {
    $event->getWordRecord();
}

$data = (object) [
            'filter' => $filter,
            'events' => $events,
            'accessEventAdd' => CheckAccess('Event.Add'),
            'accessEventSettings' => CheckAccess("Event.Settings")
];

IncludeClass::Template('Events', $data);
