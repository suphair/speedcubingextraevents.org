<?php
function DeleteFiles($folder){
    $deleted=0;
    foreach(glob("$folder/*") as $name){
        if(!is_dir($name)){
            unlink($name); 
            $deleted++;
        }
    } 
    return $deleted;
}

function DeleteFolder($folder){
    foreach(glob("$folder/*") as $name){
        if(!is_dir($name)){
            unlink($name); 
        }
    } 
    rmdir($folder);
}