<?php
$requests= getRequest();
if(!isset($requests[2]) or !is_numeric($requests[2])){
    echo 'Wrong event ID';
    exit();
}else{
   $ID=$requests[2];
}

DataBaseClass::FromTable("Event","ID=$ID");
$row=DataBaseClass::QueryGenerate(false);

if(isset($row['Event_Competition'])){
    $Competition=$row['Event_Competition'];
}else{
    $Competition=-1;
}

RequestClass::CheckAccessExit(__FILE__, "Competition.Settings",$Competition);

Databaseclass::FromTable('Event', "ID='$ID'");
DataBaseClass::Join_current('DisciplineFormat');
DataBaseClass::Join_current('Discipline');
DataBaseClass::Join('DisciplineFormat','Format');
Databaseclass::Join('Event','Competition');
$data=Databaseclass::QueryGenerate(false);
$Competition_WCA=str_replace('.','_',$data['Competition_WCA']);
$FileName=$Competition_WCA."_".$data['Discipline_Code']."_".$data['Event_Round'];
if(!$data['Discipline_GlueScrambles'] or !$data['Discipline_TNoodle']){
    exit();
} ?>
<head>
    <link rel="icon" href="<?= PageLocal()?><?= ImageEventFile($data['Discipline_CodeScript'])?>" >
    <title><?= $data['Discipline_Name']?><?= $data['Event_vRound']?></title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>
<h1><?= $data['Competition_Name'] ?> ▪ <?= $data['Discipline_Name']?><?= $data['Event_vRound']?></h1>
    <?php if($data['Format_Attemption']==5){
        $ex=2;
    }else{
        $ex=1;
    } ?>
    <h2>Set scrambles <?= $data['Discipline_TNoodle'] ?> for <?= $data['Event_Groups'] ?> groups ( <?= $data['Format_Attemption']." attempts + ".$ex?> extra )</h2>
    <br>
    <?php
    $event_request="('eventID'-'".$data['Discipline_TNoodle']."'_'round'-'1'_'scrambleSetCount'-".$data['Event_Groups']."_'scrambleCount'-".$data['Format_Attemption']."_'extraScrambleCount'-".$ex."_'copies'-1)";
    $link="http://localhost:2014/scramble-legacy/#competitionName=".str_replace('.','_',$data['Competition_WCA'])."_".$data['Discipline_Code']."_1&rounds=i".
    $event_request        
    ."!&version=1.0"; ?>
        
        
    1. Prepare TNoodle WCA Scrambler according to the <a target="_blank" href="https://www.worldcubeassociation.org/regulations/scrambles/">instructions</a><br><br>    
    2. Click the button "<b>Sramble!</b>" in the <a target="_blank" href="<?= $link ?>">TNoodle WCA Scrambler</a> (open at this link)<br><br>
    3. Click the "PDF" button and select the file with the same name in the download folder<br>
    [ <?= $FileName ?> / <b>Printing</b> / <?= $FileName ?> - All Scrambles.<b>pdf</b> ]<br><br>
    
    
    <form name="EventSetGlueScramblesTNoodlePDF" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/EventSetGlueScrambles.TNoodlePDF" ?>">           
        <div class="fileinputs">
            <input type="file" accept="application/pdf" class="file" name="file" multiple="true" onchange="document.forms['EventSetGlueScramblesTNoodlePDF'].submit();"/>
            <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
            <div class="fakefile" id="fkf">
                <button class="form_change">PDF</button> 
            </div>
        </div>
    </form>

    <?php
exit();