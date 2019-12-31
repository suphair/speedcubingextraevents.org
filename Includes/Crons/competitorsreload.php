<?php
$start=date("H:i:s");
$limitApi=10;
$limitBd=1000;
$depth=14;

DataBaseClass::Query(" Select UpdateTimestamp, ID, WID from Competitor  where  WID is not null and WCAID='' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
order by UpdateTimestamp Limit $limitApi");

foreach(DataBaseClass::getRows() as $user){
    Competitors_Reload($user['ID'],$user['WID']);
}

DataBaseClass::Query(" Select UpdateTimestamp, ID, WCAID from Competitor  where  WID is null and WCAID<>'' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth
order by UpdateTimestamp Limit $limitApi");

foreach(DataBaseClass::getRows() as $user){
    Competitors_Reload($user['ID'],$user['WCAID']);
}
 
DataBaseClass::Query("select ID, WCAID, Country, Name from Competitor where WCAID<>'' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth 
order by UpdateTimestamp limit $limitBd");

foreach(DataBaseClass::getRows() as $row){
    DataBaseClassWCA::Query("select P.id,C.iso2, trim(SUBSTRING_INDEX(P.name,'(',1)) name from Persons P join Countries C on C.id=P.countryId where P.id='{$row['WCAID']}'");    
    $wca_row=DataBaseClassWCA::getRow();
    if(isset($wca_row['id'])){
        if($wca_row['iso2']!=$row['Country']){
            AddLog('CompetitorsReload', 'Cron',"Update country {$row['WCAID']}: {$row['Country']}->{$wca_row['iso2']}");
        }
        if($wca_row['name']!=$row['Name']){
            AddLog('CompetitorsReload', 'Cron',"Update name {$row['WCAID']}: {$row['Name']}->{$wca_row['name']}");
        }
        
        DataBaseClass::Query("Update Competitor set Country='{$wca_row['iso2']}', Name='".DataBaseClass::Escape($wca_row['name'])."', UpdateTimestamp=now() where ID='{$row['ID']}'");    
    }else{
        DataBaseClass::Query("Update Competitor set UpdateTimestamp=now()  where ID='{$row['ID']}'");    
    }
}
    
Competitors_RemoveDuplicates();

$end=date("H:i:s");


DataBaseClass::Query(" Select UpdateTimestamp, ID, WID from Competitor  where  WID is not null and WCAID='' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth ");
$count1=DataBaseClass::rowsCount();

DataBaseClass::Query(" Select UpdateTimestamp, ID, WID from Competitor  where  WID is not null and WCAID='' ");
$count1t=DataBaseClass::rowsCount();



DataBaseClass::Query(" Select UpdateTimestamp, ID, WCAID from Competitor  where  WID is null and WCAID<>'' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth");
$count2=DataBaseClass::rowsCount();

DataBaseClass::Query(" Select UpdateTimestamp, ID, WCAID from Competitor  where  WID is null and WCAID<>'' ");
$count2T=DataBaseClass::rowsCount();


DataBaseClass::Query("select ID, WCAID from Competitor where WCAID<>'' and TO_DAYS(now()) - TO_DAYS(UpdateTimestamp) > $depth ");
$count3=DataBaseClass::rowsCount();

AddLog('CompetitorsReload', 'Cron',"$count1/$count1t $count2/$count2T $count3 : $start - $end");

exit();  