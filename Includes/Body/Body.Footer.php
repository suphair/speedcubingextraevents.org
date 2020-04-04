<?php
$mails = [
    'seniors' => [
        'mail' => urlencode(getini('Seniors', 'email')),
        'subject' => $data->title
    ],
    'support' => [
        'mail' => urlencode(getini('Support', 'email')),
        'subject' => "Support: {$data->title}"
    ]
];
$data->contacts = arrayToObject($mails);
IncludeClass::Template('Body.Footer', $data);
