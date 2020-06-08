<?php

function errorSend($daily = 0 ) {

    $errors = Suphair \ Error :: getAll();
    $count = 0;
    foreach ($errors as $error) {
        if ($error['status'] == Suphair \ Error :: _NEW) {
            $count++;
        }
    }

    if($count){
        SendMail(
                GetIni('Support', 'email'), "SEE error: $count"
            , "New errors on the site ".PageIndex().": $count<br><a href='http:". PageIndex()."Classes/suphair_error'>http:". PageIndex()."Classes/suphair_error</a>"
        );
    }elseif($daily){
        SendMail(
            GetIni('Support', 'email'), "SEE NO ERROR"
            , "No new errors on the site ".PageIndex().": $count<br><a href='http:". PageIndex()."Classes/suphair_error'>http:". PageIndex()."Classes/suphair_error</a>"
        );
    }
    return $count;
}
