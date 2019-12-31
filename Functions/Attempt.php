<?php
function AttemptUpdater($Com=0){
    if($Com){
        DataBaseClass::FromTable("Attempt","Command=$Com");
    }else{
        DataBaseClass::FromTable("Attempt");    
    }
    $Attempts= DataBaseClass::QueryGenerate();
   
    foreach($Attempts as $r){
        $string="";
        $order=1000*1000000+999999;
        if(isset($r['Attempt_IsDNF']) and $r['Attempt_IsDNF']){
            $string='DNF';
        }elseif(isset($r['Attempt_IsDNS']) and $r['Attempt_IsDNS']){
            $string='DNS';
        }else{
            $order= $r['Attempt_Minute']*60*100+$r['Attempt_Second']*100+$r['Attempt_Milisecond'];
            if($r['Attempt_Minute']){
                $string=sprintf( "%d:%02d.%02d", $r['Attempt_Minute'],$r['Attempt_Second'],$r['Attempt_Milisecond']);
            }elseif($r['Attempt_Second']){
                $string=sprintf( "%2d.%02d", $r['Attempt_Second'],$r['Attempt_Milisecond']);
            }else{
                $string=sprintf( "0.%02d", $r['Attempt_Milisecond']);
            }
        
            if($string=="0.00")$string="";
            if($r['Attempt_Amount']>0){
                if($r['Attempt_Special']!='Mean'){
                    $string=round($r['Attempt_Amount']).' ('.$string.')';
                }else{
                    $string=$r['Attempt_Amount'];
                }
            }
            
            $order=(1000-$r['Attempt_Amount'])*1000000+$order;
            
        }
        DataBaseClass::Query("Update Attempt set `vOut`='$string',`vOrder`=$order where ID=".$r['Attempt_ID']);
    }
}

