<?php
function time_withzone($time,$timezone,$format='d.m.Y H:i:s'){
    $timestamp = strtotime($time);
    $dt = new DateTime("now", new DateTimeZone($timezone)); 
    $dt->setTimestamp($timestamp); 
    return $dt->format($format);
}