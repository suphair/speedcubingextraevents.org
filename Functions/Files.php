<?php
function DeleteFiles($folder){
    foreach(glob("$folder/*") as $name){
        if(!is_dir($name)){
            unlink($name); 
        }
    } 
}

function DeleteFolder($folder){
    foreach(glob("$folder/*") as $name){
        if(!is_dir($name)){
            unlink($name); 
        }
    } 
    rmdir($folder);
}