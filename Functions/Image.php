<?php

function ImageEvent($event,$size='',$name=""){
    $style="";
    if(is_float($size) and $size<2){
        $style=" style='font-size:{$size}em;'";
    }
  
    if(file_exists("./Svg/$event.svg")){        
        return "<i $style title='$name' class='fas fa-{$size}x ee-$event'></i>";
    }else{
        return  "<i $style title='$name' class='fas fa-{$size}x fa-question-circle'></i>";
    }
}


function ImageEventFile($event){     
    return "Image/Events/$event.jpg";
}

function ImageCompetition($competition,$size=100,$name=""){
    if(!$name)$name=$competition;
    $filenameLocal= "./Image/Competition/".$competition.".jpg";
    if(file_exists($filenameLocal)){
        return  "<img align='center' title='$name'  width='".$size."px' src='".PageIndex()."/Image/Competition/".$competition.".jpg'>";
    }else{
        return "";
    }
}




function ImageConfig($link,$config=false,$size=100){ 
        $class=$config?"config_enter":"config"; ?>
        <img  width='<?= $size ?>px' class="<?= $class ?>"
        onclick="this.className='<?= $class ?>'; document.location.href = '<?= $link ?>'"     
        onmouseover="this.className='config_select',
                    this.style.cursor='pointer';"
        onmouseout="this.className='<?= $class ?>'"
        src='<?= PageIndex() ?>/Image/Config.jpg'>
    <?php
}

function ImageAdd($size=100){
  return  "<img width='".$size."px' src='".PageIndex()."/Image/Add.png'>";  
}

function ImageConfigurate($size=100){
  return  "<img width='".$size."px' src='".PageIndex()."/Image/Config.jpg'>";  
}


function GetParam($size,$font,$text){
    $return=array();
    $textbox = imagettfbbox($size, 0, $font, $text);
    $return['height']=$textbox[1] - $textbox[7];
    $return['weith']=$textbox[2] - $textbox[0];
    $return['dy']=$textbox[1];
    $return['dx']=$textbox[0];
    return $return;
} 