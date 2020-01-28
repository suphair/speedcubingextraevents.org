<?php


function Generate_fto(){
    
    $Sides=['U','L','R'];
    $Moves=["","'"];
    $flip='flip';
    
    $layers_lock='';
    $tmp='';
    
    $pattern=[rand(4,6),$flip,rand(4,6),$flip,rand(4,6),$flip,rand(4,6),$flip,rand(4,6)];
    
    #$pattern=[rand(2,2),$flip,rand(2,2)];
    
    foreach($pattern as $p){
        if($p==$flip){
            $tmp.=$flip.' ';
        }else{
            for($i=0;$i<$p;$i++){
                $Side=$Sides[array_rand($Sides)];
                if($Side!=$layers_lock){
                    $Move=$Moves[array_rand($Moves)];
                    $tmp.=$Side.$Move.' '; 
                    $layers_lock=$Side;
                }else{
                    $i--;
                }
            }
        }
    }
    return trim($tmp);
}


function Generate_fto_(){
      
    $Sides=['U','D','L','R','F','B','BR','BL'];
    $Moves=["","'"];
    
    $layers_lock=[];
    $tmp='';
    for($i=0;$i<25;$i++){
        $Side=$Sides[array_rand($Sides)];
        if(!in_array($Side,$layers_lock)){
            $Move=$Moves[array_rand($Moves)];
            $tmp.=$Side.$Move.' '; 
            if($Side=='U' or $Side=='D'){$layers_lock=['U','D'];}
            if($Side=='F' or $Side=='B'){$layers_lock=['F','B'];}
            if($Side=='L' or $Side=='BR'){$layers_lock=['L','BR'];}
            if($Side=='R' or $Side=='BL'){$layers_lock=['R','BL'];}
        }else{
            $i--;
        }
    }
    return trim($tmp);
}