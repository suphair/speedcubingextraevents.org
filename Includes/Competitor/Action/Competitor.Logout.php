<?php
if($Competitor=getCompetitor()){
    AddLog('WCA_Auth','Logout',$Competitor->name);
    unset($_SESSION['Competitor']);
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();

