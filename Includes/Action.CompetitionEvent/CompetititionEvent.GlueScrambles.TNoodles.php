<?php
$requests= getRequest();
if(!isset($requests[2]) or !is_numeric($requests[2])){
    echo 'Wrong event ID';
    exit();
}else{
   $ID=$requests[2];
}

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

if(!$data['Discipline_GlueScrambles'] or !$data['Discipline_TNoodles']){
    exit();
} ?>
<head>
    <title>Set scrambles</title>
    <link rel="stylesheet" href="../../getStyle.css" type="text/css"/>
</head>
<h1><img width="30px" src="<?= PageIndex()?>Logo/Logo_Black.jpg"> Set scrambles</h1>
<h2><?= $data['Competition_Name']?> ▪ <?= date_range($data['Competition_StartDate'],$data['Competition_EndDate']) ?></h2>
<h2><img width="30px" align="top" src="<?= PageIndex()?><?= ImageEventFile($data['Discipline_CodeScript']) ?>"> <?= $data['Discipline_Name']?><?= $data['Event_vRound']?></h2>
<?php 

    $scrs=$data['Event_Groups']*($data['Format_Attemption']+1);
    
    if($data['Discipline_CodeScript']=='all_scr'){
        $scrs=$data['Event_Groups']*2;
    }
    
    $Pages_event=ceil($scrs/(5+2));
    ?>
    <h3><?= $scrs ?> scrambles for <?= $Pages_event==1?'page':"$Pages_event pages" ?> </h3>
    <center>

    <?php
    $Competition_WCA=str_replace('.','_',$data['Competition_WCA']);
    $FileName=$Competition_WCA."_".$data['Discipline_Code']."_".$data['Event_Round'];
    $event_requests=[];
    
    if(strpos($data['Discipline_Code'],'mguild')!==false){
        $data['Discipline_TNoodles']=GetIni('TNoodles','mguild');
    }
    
    
    foreach(explode(",",$data['Discipline_TNoodles']) as $event){
        if(in_array($event,['666','777'])){
            $event_requests[]="('eventID'-'".$event."'_'round'-'1'_'scrambleSetCount'-".$data['Discipline_TNoodlesMult']*$Pages_event."_'scrambleCount'-3_'extraScrambleCount'-1_'copies'-1)";
        }else{
            $event_requests[]="('eventID'-'".$event."'_'round'-'1'_'scrambleSetCount'-".$data['Discipline_TNoodlesMult']*$Pages_event."_'scrambleCount'-5_'extraScrambleCount'-2_'copies'-1)";
        }
    }
    $link="http://localhost:2014/scramble-legacy/#competitionName=".$FileName."&rounds=i".
    implode("_",$event_requests)        
    ."!&version=1.0"; ?>
    1. Prepare TNoodle WCA Scrambler according to the <a target="_blank" href="https://www.worldcubeassociation.org/regulations/scrambles/">instructions</a><br><br>    
    2. Click the button "Sramble!" in the <a target="_blank" href="<?= $link ?>">TNoodle WCA Scrambler</a> (open at this link) {<?= $data['Discipline_TNoodles']?>} <?=  $data['Discipline_TNoodlesMult']>1?(' * '.$data['Discipline_TNoodlesMult']):'' ?><br><br>
    3. Click the "PDF" button and select the file with the same name in the download folder<br>
    [ <?= $FileName ?> / <b>Printing</b> / <?= $FileName ?> - All Scrambles.<b>pdf</b> ]<br><br>
    <form name="EventSetGlueScramblesTNoodlesPDF" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/EventSetGlueScrambles.TNoodlesPDF" ?>">           
        <div class="fileinputs">
            <input type="file" accept="application/pdf" class="file" name="file" multiple="true" onchange="document.forms['EventSetGlueScramblesTNoodlesPDF'].submit();"/>
            <input name="ID" type="hidden" value="<?= $data['Event_ID'] ?>" />
             <div class="fakefile" id="fkf">
                <button class="form_change">PDF</button> 
            </div>
        </div>
    </form>

    </center>
<?php 
exit();