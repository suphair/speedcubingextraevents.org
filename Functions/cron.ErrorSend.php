<?php

function errorSend($daily = 0) {

    $errors = errors :: getAll();
    $counts = [
        errors :: _NEW => 0,
        errors :: _WORK => 0,
        errors :: _SKIP => 0,
        errors :: _DONE => 0];
    foreach ($errors as $error) {
        $counts[$error['status']]++;
    }

    $new = $counts[errors :: _NEW];
    $work = $counts[errors :: _WORK];
    $skip = $counts[errors :: _SKIP];
    $done = $counts[errors :: _DONE];

    if (!$daily) {
        if ($new) {
            notification::put('error', "SEE error: $new"
                    , "New errors on the site http:" . PageIndex() . " $new<br><a href='http:" . PageIndex() . "/Classes/errors'>http:" . PageIndex() . "/Classes/errors</a>"
            );
        }
    } elseif ($counts[errors :: _NEW] == 0) {
        if ($counts[errors :: _WORK] == 0) {
            notification::put('error', "SEE NO ERROR"
                    , "No new errors on the site http:" . PageIndex() . "<br><a href='http:" . PageIndex() . "/Classes/errors'>http:" . PageIndex() . "/Classes/errors</a>"
            );
        } else {
            notification::put('error', "SEE error in work: $work"
                    , "Errors in work on site http:" . PageIndex() . " $work<br><a href='http:" . PageIndex() . "/Classes/errors'>http:" . PageIndex() . "/Classes/errors</a>"
            );
        }
    }
    return json_encode($counts);
}
