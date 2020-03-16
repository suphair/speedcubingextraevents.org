<!DOCTYPE HTML>
<html>
<?php 
$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$Event=$request[2];

DataBaseClass::Query("Select E.vRound, D.Name Discipline, C.Name Competition, C.ID Competition_ID,C.WCA Competition_WCA, D.Code Discipline_Code,D.CodeScript Discipline_CodeScript, C.ID CompetitionID, E.ID EventID, E.Groups EventGroups "
        . " from `Event` E join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join  Discipline D on D.ID=DF.Discipline"
        . " join `Competition` C on C.ID=E.Competition Where E.ID='". $Event."'");

if(DataBaseClass::rowsCount()==0){
    exit();
}
    
$data=DataBaseClass::getRow();

?>
<head>
    <title><?= $data['Discipline']?><?= $data['vRound']?></title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
    <link rel="stylesheet" href="../../fontawesome-free-5.12.0-web/css/all.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="../../icons-extra-event/css/Extra-Events.css?t=3" type="text/css"/>    
    <link rel="stylesheet" href="../../jQuery/chosen_v1/chosen.css" type="text/css"/>
</head>  
<body>
<?php

$Competition=$data['Competition'];
$Discipline=$data['Discipline'];

RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$data['Competition_ID']); ?>
    <h1><?= $data['Competition'] ?> ▪ <?= $data['Discipline']?><?= $data['vRound']?></h1>
    <h2>Distribution of competitors by groups</h2>

    <?php 
    
    DataBaseClass::FromTable('Command',"Event='".$Event."'");
    
    $commands=DataBaseClass::QueryGenerate();?>
        <form method="POST" action="<?= PageAction('CompetitionEvent.Groups.Edit')?>">
        <input name="ID" type="hidden" value="<?= $Event ?>" />    
        <table class="table_new">
            <thead>
            <tr>
                <td/>
                <?php 
                $Group=array(-1=>0);
                for($i=0;$i<$data['EventGroups'];$i++){ 
                    $Group[$i]=0; ?>
                    <td><?= Group_Name($i)?></td>
                <?php } ?>
                 <td></td>   
            </tr>
            </thead>
            <tbody>
                <?php foreach($commands as $command){ 
                    $Group[$command['Command_Group']]++; ?>
                <tr>
                    <td class='left'>
                        <?php DataBaseClass::FromTable('CommandCompetitor',"Command=".$command['Command_ID']);
                        DataBaseClass::Join_current('Competitor');
                        DataBaseClass::OrderClear('Competitor', 'Name');
                        $names=array();
                        foreach(DataBaseClass::QueryGenerate() as $competitor){  
                            $names[]=$competitor['Competitor_Name']?>
                                <p><?= $competitor['Competitor_Name'] ?> &#9642; <?= $competitor['Competitor_WCAID']; ?></p>
                        <?php } ?>
                    </td>
                    <?php for($i=0;$i<$data['EventGroups'];$i++){  ?>
                        <td <?= ($command['Command_Group']==$i)?"class='backgroundcolor_green'":"" ?> >
                            <input type="radio" <?= ($command['Command_Group']==$i)?"checked":"" ?> name="Command_ID[<?= $command['Command_ID'] ?>]" value="<?= $i ?>">
                        </td>
                    <?php } ?>
                        <td <?= ($command['Command_Group']==-1)?"class='backgroundcolor_red'":"" ?> >
                            <input type="radio" <?= ($command['Command_Group']==-1)?"checked":"" ?> name="Command_ID[<?= $command['Command_ID'] ?>]" value="-1">
                        </td>            
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <td/>
                <?php 
                for($i=0;$i<$data['EventGroups'];$i++){ ?>
                    <td><?= $Group[$i] ?></td>
                <?php } ?>
                 <td><?= $Group[-1] ?></td>   
            </tr>
            </tfoot>
        </table>
        <button><i class="fas fa-save"></i> Save</button>
    </form>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Groups.Delete')?>" onsubmit="return confirm('Attention: Confirm Reset')">
        <input name="ID" type="hidden" value="<?= $Event ?>" />   
        <button class="delete"><i class="fas fa-eraser"></i> Reset</button>
    </form>

<?php exit(); ?>
</body>
</html>