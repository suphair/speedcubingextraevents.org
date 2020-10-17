<?php

function mlb($name) {
    return "<!--ML[$name]-->";
}

function ml($name, $arr = [], $b = true) {
    if (!is_array($arr) and $arr === false) {
        $arr = [];
        $b = false;
    }

    if (!is_array($arr)) {
        $arr = [$arr];
    }

    if (!isset($_SESSION['language_select'])) {
        $_SESSION['language_select'] = getLanguages()[0];
    }
    $Language = $_SESSION['language_select'];

    if (!$MultiLanguage = ObjectClass::getObject('MultiLanguage')) {
        DataBaseClass::Query("Select * from MultiLanguage where Language='" . getLanguages()[0] . "'");
        foreach (DataBaseClass::getRows() as $row) {
            $MultiLanguage[$row['Name']] = $row['Value'];
        }

        DataBaseClass::Query("Select * from MultiLanguage where Language='$Language'");
        foreach (DataBaseClass::getRows() as $row) {
            $MultiLanguage[$row['Name']] = $row['Value'];
        }
        ObjectClass::setObjects('MultiLanguage', $MultiLanguage);
    }

    if (isset($MultiLanguage[$name])) {
        $result = $MultiLanguage[$name];
        $N = sizeof($arr);
        for ($i = 1; $i <= $N; $i++) {
            $result = str_replace("%$i", $arr[$i - 1], $result);
        }
    } else {
        $result = "{" . $Language . ":" . $name . ($arr ? ("; " . print_r($arr, true)) : '') . "}";
    }

    if ($b) {
        return "<!--ML[$name]-->$result";
    } else {
        return "$result";
    }
}

function getLanguages() {
    return explode(",", config :: get('LANGUAGE', 'languages'));
}

function getObjLanguages() {
    foreach (getLanguages() as $language_element) {
        $languages[] = [
            'image' => ImageCountry($language_element),
            'code' => $language_element,
            'name' => CountryName($language_element, true)
        ];
    }
    return arrayToObject($languages);
}

function ml_json($json) {
    $str = json_decode($json, true);
    if (!is_array($str)) {
        return $json;
    }
    $Language = $_SESSION['language_select'];
    if (isset($str[$Language])) {
        return $str[$Language];
    }

    $Language_default = getLanguages()[0];

    if (isset($str[$Language_default])) {
        return $str[$Language_default];
    }

    if (is_array($str)) {
        return current($str);
    }

    return "{$json}";
}
