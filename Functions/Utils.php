<?php

function random_string($length) {
    $key = '';
    $keys = [2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z'];


    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

function PageIndex() {
    return "//" . $_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['PHP_SELF']);
}

function PageAction($file) {
    return PageIndex() . "Actions/" . $file;
}

function PageLocal() {
    return str_replace("index.php", "", $_SERVER['PHP_SELF']);
}

Function HeaderExit() {
    if (isset($_SERVER['HTTP_REFERER']) and str_replace(PageIndex(), "", $_SERVER['HTTP_REFERER']) != $_SERVER['HTTP_REFERER']) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        echo 'access denied';
    }
    exit();
}

function getTermination($num, $str, $o1, $o2, $o5) {
    $number = substr($num, -2);
    if ($number > 10 and $number < 15) {
        $term = $o5;
    } else {
        $number = substr($number, -1);

        if ($number == 0)
            $term = $o5;
        if ($number == 1)
            $term = $o1;
        if ($number > 1)
            $term = $o2;
        if ($number > 4)
            $term = $o5;
    }
    echo $num . ' ' . $str . $term;
}
