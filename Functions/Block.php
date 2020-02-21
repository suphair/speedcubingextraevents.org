<?php
function getBlockText($nameBlock,$language = false)
{
    $nameBlock = DataBaseClass::Escape($nameBlock);
    
    if($language){
        DataBaseClass::Query("Select * from BlockText where Name='$nameBlock' and Country='$language'");  
    }else{
        DataBaseClass::Query("Select * from BlockText where Name='$nameBlock'" );  
    }
    
    if(DataBaseClass::rowsCount()){
        $valueBlock= DataBaseClass::getRow()['Value'];      
    }else{
        $valueBlock=false;
    }  
    
    return $valueBlock;   
}
?>