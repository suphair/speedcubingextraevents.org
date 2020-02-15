<?php
function LinkDiscipline($code){
    return PageIndex()."Event/$code";
}


function setLinkEvent($CompetitionWCA,$EventCode,$eventRound){
    if($eventRound>1){
        return PageIndex()."Competition/$CompetitionWCA/$EventCode/$eventRound";
    }else{
        return PageIndex()."Competition/$CompetitionWCA/$EventCode";
    }
}

function LinkEvent($ID){
    DataBaseClass::FromTable('Event',"ID=$ID");
    DataBaseClass::Join('Event', 'Competition');
    DataBaseClass::Join('Event', 'DisciplineFormat');
    DataBaseClass::Join_current( 'Discipline');
    $event=DataBaseClass::QueryGenerate(false);
    return PageIndex()."Competition/".$event['Competition_WCA']."/".$event['Discipline_Code']."/".$event['Event_Round'];
}


function LinkCompetitor($ID,$WCAID=""){
    return PageIndex()."Competitor/".($WCAID?$WCAID:$ID);
}

function LinkCompetition($WCA){
    return PageIndex()."Competition/$WCA";
}


function LinkLogin(){
    return PageIndex()."Login";
}

function LinkDelegate($WCAID){
    return PageIndex()."Delegate/$WCAID";  
}

function LinkDelegateAdd(){
   return PageIndex()."Delegate/Add";  
}
function LinkSettingsBack(){
    return "http://".$_SERVER['HTTP_HOST'].str_replace("/Settings","",$_SERVER['REQUEST_URI']);
}

function GetUrlWCA(){
       
   $url_refer = PageIndex().GetIni('WCA_AUTH','url_refer');
   if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false){
       $url_refer="http:".$url_refer;
   }else{
       $url_refer="https:".$url_refer;
   }
    $client_id = GetIni('WCA_AUTH','client_id');
    $scope=GetIni('WCA_AUTH','scope');
    
    return "https://www.worldcubeassociation.org/oauth/authorize?client_id=$client_id&redirect_uri=".urlencode($url_refer)."&response_type=code&scope=$scope";
}