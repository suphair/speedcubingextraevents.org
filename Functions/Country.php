<?php

function CountryName($str,$language=false){
    if($language and $str=='EN'){
        return 'English';
    }
    
    if($language and $str=='RU'){
        return 'Русский';
    }
    
    if($str=='All')return 'All countries';
    DataBaseClass::Query("Select * from Country where ISO2='$str'");
    $country=DataBaseClass::GetRow();
    if(isset($country['Name'])){
        return $country['Name'];
    }else{
        return $str;    
    }
}

function CountryNames($str){
    
    DataBaseClass::Query("Select * from Country");
    $countries=[];
    foreach(DataBaseClass::GetRows() as $country){
        $countries[$country['ISO2']]=$country['Name'];
    }
    $strs=[];
    foreach(explode(",",$str) as $s){
       if(isset($countries[$s])){
            $strs[]=$countries[$s].' ('.$s.')';
       }else{
            $strs[]=$s;
       }
    }
    return implode(", ",$strs);
}

function ImageCountry($country,$width){ 
    $country=str_replace('EN','GB',$country);
    if($country){
        if(file_exists("Image/Flags/".strtolower($country).".png")){ ?>
            <img alt="<?= $country?>" width="<?= $width ?>" style="vertical-align: middle" src="<?= PageIndex() ?>Image/Flags/<?= strtolower($country)?>.png"><?php
         }else{ 
            echo  " &bull;";
           } 
     }else{
         ?><img alt="world" width="<?= $width ?>" style="vertical-align: middle" src="<?= PageIndex() ?>Image/Flags/world.png"><?php 
     }
}