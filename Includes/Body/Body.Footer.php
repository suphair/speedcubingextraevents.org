<?php

$mails = [
    'seniors' => [
        'mail' => urlencode(config :: get('EMAIL', 'seniors')),
        'subject' => $data->title
    ],
    'support' => [
        'mail' => urlencode(config :: get('EMAIL','support')),
        'subject' => "Support: {$data->title}"
    ]
];
$data->contacts = arrayToObject($mails);
IncludeClass::Template('Body.Footer', $data);
