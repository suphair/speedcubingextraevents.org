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
    <link rel="icon" href="<?= PageLocal()?><?= ImageEventFile($data['Discipline_CodeScript'])?>" >
    <title><?= $data['Discipline']?><?= $data['vRound']?></title>
    <link rel="stylesheet" href="../../style.css" type="text/css"/>
</head>  
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
        <table>
            <tr class="tr_title">
                <td/>
                <?php 
                $Group=array(-1=>0);
                for($i=0;$i<$data['EventGroups'];$i++){ 
                    $Group[$i]=0; ?>
                    <td><?= Group_Name($i)?></td>
                <?php } ?>
                 <td></td>   
            </tr>
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
                            <div class="competitor_td">
                                <nobr><?= $competitor['Competitor_Name'] ?> &#9642; <?= $competitor['Competitor_WCAID']; ?></nobr>
                            </div>
                        <?php } ?>
                    </td>
                    <?php for($i=0;$i<$data['EventGroups'];$i++){  ?>
                        <td <?= ($command['Command_Group']==$i)?"style='background:var(--light_green)'":"" ?> >
                            <input type="radio" <?= ($command['Command_Group']==$i)?"checked":"" ?> name="Command_ID[<?= $command['Command_ID'] ?>]" value="<?= $i ?>">
                        </td>
                    <?php } ?>
                        <td class="border-left-solid" <?= ($command['Command_Group']==-1)?"style='background:var(--light_red)'":"" ?> >
                            <input type="radio" <?= ($command['Command_Group']==-1)?"checked":"" ?> name="Command_ID[<?= $command['Command_ID'] ?>]" value="-1">
                        </td>            
                </tr>
                <?php } ?>
            <tr class="tr_title">
                <td/>
                <?php 
                for($i=0;$i<$data['EventGroups'];$i++){ ?>
                    <td><?= $Group[$i] ?></td>
                <?php } ?>
                 <td><?= $Group[-1] ?></td>   
            </tr>

        </table>
        <input type="submit" value="Save group distribution">
    </form>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Groups.Delete')?>">
        <input name="ID" type="hidden" value="<?= $Event ?>" />   
        <input class="delete" type="submit" value="Reset group distribution">
    </form>

<?php exit();