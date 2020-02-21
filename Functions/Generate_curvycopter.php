<?php 
function Generate_curvycopter($training=false){
    if($training){
        $filename="Script/curvycopter_training_out.txt";
        $file=file($filename);
        $i=rand(0,sizeof($file));
        $str=$file[$i];
        $str=trim($str);
        $str_moves=explode(" ", $str);
        foreach($str_moves as $n=>$s){
            if(strpos($s,"-")!==false or strpos($s,"+")!==false){
                $str_moves[$n]='[verify] '.$s;
                break;
            }
        }
        $str=implode(" ",$str_moves);
        return $str;
   }else{
        $filename="Script/curvycopter_out.txt";
        $file=file($filename);
        $i=rand(0,sizeof($file));
        $str=$file[$i];
        $fp=fopen($filename,"w");
        unset($file[$i]);
        fputs($fp,implode("",$file));
        fclose($fp);
        $str=trim($str);
        $str_moves=explode(" ", $str);
        foreach($str_moves as $n=>$s){
            if(strpos($s,"-")!==false or strpos($s,"+")!==false){
                $str_moves[$n]='[verify] '.$s;
                break;
            }
        }
        $str=implode(" ",$str_moves);
        return $str;
   }
}