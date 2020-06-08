<?php

function sendPhpErrors() {
    $_details = [];

    $errors = file_get_contents('PHPError.txt');
    if (trim($errors)) {
        $_details['count'] = substr_count($errors, '[PHPError]');
        $_details['mail'] = SendMail(getini('Support', 'email'), 'SEE:PHPError ', str_replace("\n", "<br>", $errors));
    }

    return json_encode($_details);
}
