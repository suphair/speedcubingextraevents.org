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

function ImageCountry($country){
    $country=str_replace('en','gb',strtolower($country));
    if($country){
        return '<span class="flag-icon flag-icon-'.$country.'"></span>';
    }else{
        return '<i class="fas fa-globe"></i>';
    }    
}
        
function countryExists($code) {
    if (strlen($code) != 2) {
        return false;
    }
    if (! DataBaseClass::exists("select * from Country where ISO2='$code'")) {
        return false;
    }
    return true;
}
