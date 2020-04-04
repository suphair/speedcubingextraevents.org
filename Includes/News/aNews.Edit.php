<?php

$aNews = ObjectClass::getObject('PageaNews');
$Text = json_decode($aNews['Text'], true);

$languages = getObjLanguages();
foreach ($languages as &$language) {
    if (isset($Text[$language->code])) {
        $language->text = $Text[$language->code];
    } else {
        $language->text = false;
    }
}
$data = (object) [
            'id' => $aNews['ID'],
            'languages' => $languages
];

IncludeClass::Template('aNews.Edit', $data);
