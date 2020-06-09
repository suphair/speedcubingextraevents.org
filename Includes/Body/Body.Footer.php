<?php

$mails = [
    'seniors' => [
        'mail' => urlencode(Suphair \ Config :: get('Seniors', 'email')),
        'subject' => $data->title
    ],
    'support' => [
        'mail' => urlencode(Suphair \ Config :: get('Support', 'email')),
        'subject' => "Support: {$data->title}"
    ]
];
$data->contacts = arrayToObject($mails);
IncludeClass::Template('Body.Footer', $data);
