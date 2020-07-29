<?php

$events = Event::getEventsByEventsId(Event::getEventsIdByFilter());
$this_event = new Event();
$this_event->getByCode(getPathElement('event', 1));

foreach ($events as $e => $event) {
    $functionGenerateFunction = "Generate_{$event->codeScript}";
    $functionGenerateTrainigFunction = "GenerateTraining_{$event->codeScript}";
    $scriptGenerateFile = "Script/{$event->codeScript}_generator.js";
    $scrambleFile = "Scramble/{$event->codeScript}.php";

    $functionGenerate = (function_exists($functionGenerateFunction)
            or function_exists($functionGenerateTrainigFunction));

    $scriptGenerate = file_exists($scriptGenerateFile) ? $scriptGenerateFile : FALSE;

    if (!file_exists($scrambleFile)) {
        $functionGenerate = false;
        $scriptGenerate = false;
    }
    $event->generate = new stdClass();
    $event->generate->function = $functionGenerate;
    $event->generate->script = $scriptGenerate;
    $event->generate->scrambleFile = $scrambleFile;
    if (!$functionGenerate and!$scriptGenerate) {
        unset($events[$e]);
        continue;
    }

    if ($event->codeScript == $this_event->codeScript) {
        $this_event->generate = $event->generate;
        $this_event->getMaxAttempts();
    }
}

if (!($this_event->generate ?? FALSE)) {
    $this_event->generate = new stdClass();
    $this_event->generate->function = false;
    $this_event->generate->script = false;
}

if ($this_event->generate->function) {
    $this_event->generate->scrambleImage = 'Scramble/Training/' . session_id() . '_' . $this_event->codeScript . '.png';
    $this_event->generate->scramble = GenerateScramble($this_event->codeScript, true);
    include $this_event->generate->scrambleFile;
    $ScrambleImage = ScrambleImage($this_event->generate->scramble, true);
    imagepng($ScrambleImage, $this_event->generate->scrambleImage);
} else {
    $this_event->generate->scramble = false;
}

$data = (object) [
            'event' => $this_event,
            'events' => $events,
];
IncludeClass::Template('Event.Training', $data);
