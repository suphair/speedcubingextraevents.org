<?php 
function Generate_curvycopter($training=false){
    if($training){
        $filename="Script/curvycopter_training_out.txt";
        $file=file($filename);
        $i=rand(0,sizeof($file)-1);
        $str=$file[$i];
        $str=trim($str);
        $str= str_replace("*","[verify]",$str);
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
        
   }
   
    $edges=['UL','UB','UR','UF','LF','LB','RB','RF','DL','DB','DR','DF'];
    $edgeblock=[[3,5,1,4],[0,6,2,5],[1,7,3,6],[2,4,0,7],[0,11,3,8],[1,8,0,9],
               [2,9,1,10],[3,10,2,11],[4,9,5,11],[5,10,6,8],[6,11,7,9],[7,8,4,10]];
    $jumblings=[];

    foreach($edgeblock as $n=>$e){
        $jumblings["J".$edges[$n]."+"]=$edges[$e[0]]."+ ".$edges[$e[1]]."+ ".$edges[$n]." ".$edges[$e[0]]."- ".$edges[$e[1]]."-";
        $jumblings["J".$edges[$n]."-"]=$edges[$e[2]]."- ".$edges[$e[3]]."- ".$edges[$n]." ".$edges[$e[2]]."+ ".$edges[$e[3]]."+";
    }

   $scramble=$str;
   foreach($jumblings as $jumblingname=>$jumbling){
        $scramble=str_replace($jumbling,$jumblingname,$scramble);
   }
  
   foreach($jumblings as $jumblingname=>$jumbling){
        $scramble=str_replace("($jumblingname) ($jumblingname)","",$scramble);
        $scramble=str_replace("  "," ",$scramble);
   }
   
   foreach($jumblings as $jumblingname=>$jumbling){
        $scramble=str_replace($jumblingname,$jumbling,$scramble);
   }
   $str=$scramble;
   
   return $str;
}