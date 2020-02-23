<?php 
function Generate_curvycopter($training=false){
    if($training){
        $filename="Script/curvycopter_training_out.txt";
        $file=file($filename);
        $i=rand(0,sizeof($file)-1);
        $str=$file[$i];
        $str=trim($str);
        $str= str_replace("*","[verify]",$str);
        return $str;
   }else{
        $filename="Script/curvycopter_out.txt";
        $file=file($filename);
        $i=rand(0,sizeof($file)-1);
        $str=$file[$i];
        $fp=fopen($filename,"w");
        unset($file[$i]);
        fputs($fp,implode("",$file));
        fclose($fp);
        $str=trim($str);
        $str= str_replace("*","[verify]",$str);
        return $str;
   }
}