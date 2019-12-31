<?php
RequestClass::CheckAccessExit(__FILE__, 'Competition.Add');
$Delegate = CashDelegate();

if(CheckAccess('Competition.Add.Ext')){
    CheckPostIsset('WCA','Delegate');
    CheckPostNotEmpty('WCA','Delegate');
    CheckPostIsNumeric('Delegate');
    $WCA=$_POST['WCA'];
    $DelegateID=$_POST['Delegate'];
}else{
    CheckPostIsset('WCA');
    CheckPostNotEmpty('WCA');
    $WCA=$_POST['WCA'];
    $DelegateID=$Delegate['Delegate_ID'];
}

$result=@file_get_contents(GetIni('WCA_API','competition')."/$WCA");
$competition=json_decode($result);
if(!$competition){
    SetMessageName('CompetitionCreate','WCA not load '.$WCA);
    HeaderExit();  
}

DataBaseClass::Query("Select ID from `Competition` where `WCA`='$WCA'");
$delegates=[];
foreach($competition->delegates as $delegate){
    $delegates[]=$delegate->wca_id;
}


$Name= DataBaseClass::Escape($competition->name);
$City=DataBaseClass::Escape($competition->city);
$Country=DataBaseClass::Escape($competition->country_iso2);
$StartDate=DataBaseClass::Escape($competition->start_date);
$EndDate=DataBaseClass::Escape($competition->end_date);
$WebSite=DataBaseClass::Escape($competition->website);
$DelegatesWCA=DataBaseClass::Escape(implode(", ",$delegates));

DataBaseClass::Query("Select ID from `Competition` where `WCA`='$WCA'");
if(!DataBaseClass::rowsCount()){
    DataBaseClass::Query("Insert into `Competition`"
            . " (`WCA`, `Name`, `StartDate`, `EndDate`,`City`,`Country`,`WebSite`,`Status`,`Registration`,`Onsite`,`DelegateWCA`) "
            . "values ('$WCA','$Name','$StartDate','$EndDate','$City','$Country','$WebSite',0,0,0,'$DelegatesWCA')");
    $ID=DataBaseClass::getID();   
    DataBaseClass::Query("Insert into `CompetitionDelegate` (Competition,Delegate) values ($ID,$DelegateID)");
    
    SendMail(getini('Seniors','email'), 'SEE: New competition '.$Name,
            "<pre>".$Delegate['Delegate_Name']." <br>SEE <a href='https://". PageIndex()."Competition/$WCA'>$Name</a><br>WCA <a href='https://www.worldcubeassociation.org/competitions/$WCA'>$WCA</a><br>".date_range($StartDate,$EndDate));
    AddLog("Competition","Create",$Delegate['Delegate_Name'].' / '.$WCA);
    
    CompetitionCompetitorsLoad($ID,$WCA,$Name,'Create');
    
}else{
   SetMessageName('CompetitionCreate','Competition exists on site '.$WCA); 
   HeaderExit(); 
}

SetMessage();
header('Location: '.PageIndex().'Competition/'.$WCA);
exit();  
