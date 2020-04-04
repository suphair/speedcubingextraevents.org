<?php

$data->accessEventSetting = CheckAccess('Event.Settings');
if (false == isset($data->eventTitle)) {
    $data->eventTitle = false;
}

IncludeClass::Template('EventLinks', $data);
