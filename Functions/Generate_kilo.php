<?php

function Generate_kilo($training = false) {
    if ($training) {
        $filename = "Script/kilo_training_out.txt";
        $file = file($filename);
        $i = rand(0, sizeof($file) - 1);
        $str = $file[$i];
        $str = trim($str);
        return $str;
    } else {
        $filename = "Script/kilo_out.txt";
        $file = file($filename);
        $i = rand(0, sizeof($file) - 1);
        $str = $file[$i];
        $fp = fopen($filename, "w");
        unset($file[$i]);
        fputs($fp, implode("", $file));
        fclose($fp);
        $str = trim($str);
        return $str;
    }
}
