<?php
function myErrorHandler($errno,$errstr,$errfile,$errline){

    if(!(error_reporting() & $errno)){
        return false;
    }
    
    if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false){
        echo "<p>[PHPError] $errfile ($errline): $errstr</p>";
    }
    
    $time = date("Y-m-d H:i:s");
    $handle = fopen("PHPError.txt", "a");
    fwrite($handle, "\r\n$time\r\n$errfile ($errline): $errstr");
    fclose($handle);
    
    if($errno==E_USER_ERROR){
        exit(1);
    }
    return true;      
}
    