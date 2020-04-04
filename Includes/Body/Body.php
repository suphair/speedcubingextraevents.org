<?php

$competitor = getObjCompetitor();
if (!$competitor) {
    $_SESSION['ReferAuth'] = $_SERVER['REQUEST_URI'];
}
$delegate = getObjDelegate();
$language = $_SESSION['language_select'];

$data = arrayToObject([
    'title' => GetIni('TEXT', 'title'),
    'competitor' => $competitor,
    'language' => [
        'code' => $language,
        'image' => ImageCountry($language)
    ],
    'languages' => getObjLanguages(),
    'delegate' => $delegate
        ]);

IncludeClass::Template('Body', $data);
