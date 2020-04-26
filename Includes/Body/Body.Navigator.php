<?php

$type = RequestClass::getPage();
$prefixType = explode('.', $type)[0];
if ($prefixType == 'aNews') {
    $type = 'News';
}
if ($prefixType == 'Delegate'
        or $prefixType == 'Delegates') {
    $type = 'Delegates';
}
if ($prefixType == 'Competitor') {
    $type = 'Competitors';
}
if ($prefixType == 'Event') {
    $type = 'Events';
}
if ($prefixType == 'Competition'
        or $type == 'index') {
    $type = 'Competitions';
}
if ($prefixType == 'Mainregulations') {
    $type = 'Regulations';
}

$data->type = $type;

IncludeClass::Template('Body.Navigator', $data);
