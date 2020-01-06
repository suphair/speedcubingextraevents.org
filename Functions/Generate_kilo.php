<?php 
function Generate_kilo(){
    
    $filename="Script/kilo_out.txt";
    $file=file($filename);
    
    $i=rand(0,sizeof($file));
    $fp=fopen($filename,"w");
    $str=$file[$i];
    unset($file[$i]);
    fputs($fp,implode("",$file));
    fclose($fp);
    $str=trim($str);
    return $str;
}