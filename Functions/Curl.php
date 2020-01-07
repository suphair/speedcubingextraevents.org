<?php

function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
    curl_setopt($ch, CURLOPT_COOKIESESSION, FALSE);  
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function file_get_contents_curl_PHPSESSID($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
    curl_setopt($ch, CURLOPT_COOKIESESSION, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: PHPSESSID=".$_COOKIE['PHPSESSID']));    
     
    session_write_close();
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}