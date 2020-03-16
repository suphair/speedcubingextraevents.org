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

RequestClass::CheckAccessExit(__FILE__,'Competition.Settings',$Competition);


DataBaseClass::Query("Select S.Timestamp, S.Scramble,S.Group,S.Attempt from `Scramble` S where S.`Event`='$ID' order by S.Group, S.Attempt");
$scrambles=array();
$scrambles_row=array();
foreach(DataBaseClass::getRows() as $row){
    $scrambles[$row['Group']][$row['Attempt']]=array('Scramble'=>$row['Scramble'],'Timestamp'=>$row['Timestamp']);
    $scrambles_row[]=$row['Scramble'];
}


Databaseclass::FromTable('Event', "ID='$ID'");
Databaseclass::Join_current('DisciplineFormat');
Databaseclass::Join_current('Discipline');
Databaseclass::Join('DisciplineFormat','Format');
Databaseclass::Join('Event','Competition');
$data=Databaseclass::QueryGenerate(false);
$Discipline=$data['Discipline_Code'];
$Attemption=$data['Format_Attemption'];

$exs=2;
if($Attemption<5){
        $exs=1;
}

?>
<head>
    <title><?= $data['Discipline_Name']?><?= $data['Event_vRound']?></title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>

<h1><?= $data['Competition_Name'] ?> ▪ <?= $data['Discipline_Name']?><?= $data['Event_vRound']?></h1>
<?php if(strpos($data['Discipline_CodeScript'],'_cup')!==FALSE){
        DataBaseClass::Query("Select count(*) count"
            . " from `Event` E join `Command` Com on Com.Event=E.ID "
            . " where E.ID='$ID'");
        $commands_count=DataBaseClass::getRow()['count'];
        if(!$commands_count)$commands_count=8;?>
    <h2>Set <?= ($commands_count-1) ?> * <?= $data['Discipline_Competitors'] ?> * 5 scrambles <?= $data['Discipline_TNoodle'] ?> for <?= $commands_count ?> teams</h2>
<?php }else{ ?>
    <h2>Set scrambles <?= $data['Discipline_TNoodle'] ?> for <?= $data['Event_Groups'] ?> groups ( <?= $data['Format_Attemption']." attempts + ".$exs?> extra )</h2>
<?php } ?>
<br>
    <?php $Competition_WCA=str_replace('.','_',$data['Competition_WCA']);
    $FileName=$Competition_WCA."_".$data['Discipline_Code']."_".$data['Event_Round']; 
    if(strpos($data['Discipline_CodeScript'],'_cup')!==FALSE){
        $count=($commands_count-1)* $data['Discipline_Competitors'] * 5;
        $link="http://localhost:2014/scramble-legacy/#competitionName=".$FileName."&rounds=i('eventID'-'".$data['Discipline_TNoodle']."'_'round'-'1'_'scrambleSetCount'-1_'scrambleCount'-".$count."_'extraScrambleCount'-0_'copies'-1)!&version=1.0";
     }else{ 
        $link="http://localhost:2014/scramble-legacy/#competitionName=".$FileName."&rounds=i('eventID'-'".$data['Discipline_TNoodle']."'_'round'-'1'_'scrambleSetCount'-".$data['Event_Groups']."_'scrambleCount'-".$data['Format_Attemption']."_'extraScrambleCount'-".$exs."_'copies'-1)!&version=1.0";
     }
    ?>
    1. Prepare TNoodle WCA Scrambler according to the <a target="_blank" href="https://www.worldcubeassociation.org/regulations/scrambles/">instructions</a><br><br>    
    2. Click the button "<b>Sramble!</b>" in the <a target="_blank" href="<?= $link ?>">TNoodle WCA Scrambler</a> (open at this link)<br><br>
    
    
    3. Click the "Json" button and select the file with the same name in the download folder<br>
    [ <?= $FileName ?> / <b>Interchange</b> / <?= $FileName ?>.<b>json</b> ]<br><br>
  
<form name="EventSetScrambleFile" enctype="multipart/form-data" method="POST" action="<?= PageAction('CompetitionEvent.Scramble.LoadFile')?>">           
    <div class="fileinputs">
        <input type="file" accept="application/json"  class="file" name="file" multiple="true" onchange="document.forms['EventSetScrambleFile'].submit();"/>
        <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
        <div class="fakefile" id="fkf">
            <button>JSON</button> 
        </div>
    </div>
</form>   
<?php exit();