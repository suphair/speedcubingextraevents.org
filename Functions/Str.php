<?php
function html_spellcount($num, $one, $two = false, $many = false) {
    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }
	if (!$num){
		return $many;
	}
    return $num.' '.html_spellcount_only($num, $one, $two, $many);
}
function html_spellcount_only($num, $one, $two = false, $many = false) {
    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }
	if (strpos($num, '.') !== false){
		return $two;
	}
    if ($num%10 == 1 && $num%100 != 11){
        return $one;
    }
    elseif($num%10 >= 2 && $num%10 <= 4 && ($num%100 < 10 || $num%100 >= 20)){
        return $two;
    }
    else{
        return $many;
    }
    return $one;
}

function Parsedown($str,$out=true){
    $Parsedown = new Parsedown();
    if($out){
        echo $Parsedown->text($str);
    }else{
        return $Parsedown->text($str);
    }
}

function Short_Name($str){
    
    return trim(explode("(",$str)[0]);
    
}


function getTimeStrFromValue($value){
    if($value=='-1') return 'DNF';
    if($value=='-2') return 'DNS';
    if($value=='999999') return '-';
    $minute=floor($value/60/100); 
    $second=floor(($value-$minute*60*100)/100); 
    $milisecond=floor($value-$minute*60*100-$second*100); 
    if($minute){
        return sprintf("%d:%02d.%02d",$minute,$second,$milisecond);
    }else{
        return sprintf("%d.%02d",$second,$milisecond);
    }
}
