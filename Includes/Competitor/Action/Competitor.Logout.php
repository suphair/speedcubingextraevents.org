<?php
if($Competitor=getCompetitor()){
    AddLog('WCA_Auth','Logout',$Competitor->name);
    unset($_SESSION['Competitor']);
    unset($_SESSION['competitorWid']);
}
header('Location: '.$_SERVER['HTTP_REFERER']);
exit();

