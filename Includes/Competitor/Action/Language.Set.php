<?php

if(isset($_POST['language']) and in_array($_POST['language'],getLanguages())){
    $Language= $_POST['language'];
    $_SESSION['language_select']=$Language;
    if($Competitor=getCompetitor()){
        DataBaseClass::Query("Update Competitor set Language='$Language' where ID=".$Competitor->local_id);
    }
}

    if(isset($_SERVER['HTTP_REFERER']) and strpos($_SERVER['HTTP_REFERER'],'favicon.ico')===false){
        header('Location: '.$_SERVER['HTTP_REFERER']);
    }else{
        header('Location: '. PageIndex());
    }
    
exit();