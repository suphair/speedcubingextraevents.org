<?php

if(isset($_POST['language']) and in_array($_POST['language'],getLanguages())){
    $Language= $_POST['language'];
    $_SESSION['language_select']=$Language;
    if($Competitor=getCompetitor()){
        DataBaseClass::Query("Update Competitor set Language='$Language' where ID=".$Competitor->local_id,true);
    }
}
    
header('Location: '.$_SERVER['HTTP_REFERER']);    
exit();