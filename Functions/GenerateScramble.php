<?php

function GenerateScramble($Event,$training=false){
    if($Event=='kilo'){
        return Generate_kilo($training);
    }elseif($Event=='ivy'){
        return Generate_ivy();
    }elseif($Event=='223'){
        return Generate_223($training);
    }elseif($Event=='332'){
        return Generate_332();
    }elseif($Event=='redi'){
        return Generate_redi();
    }elseif($Event=='dino'){
        return Generate_dino();
    }elseif($Event=='888'){
        return GenerateNxN(8,100,10);
    }elseif($Event=='999'){
        return GenerateNxN(9,100,10);
    }elseif($Event=='pyra444'){
        return Generate_pyra444();
    }elseif($Event=='fto'){
        return Generate_fto();
    }else{
        return false;
    }

}

function DeleteScramble($ID){
    foreach(DataBaseClass::SelectTableRows('Scramble', "Event=$ID") as $scramble ){
        $filename="Image/Scramble/".$scramble['Scramble_ID'].".png";
        
        if(file_exists($filename)){
            unlink($filename);     
        }
        DataBaseClass::Query("Delete from `Scramble` where `ID`='".$scramble['Scramble_ID']."' ");    
        
    }
    
}