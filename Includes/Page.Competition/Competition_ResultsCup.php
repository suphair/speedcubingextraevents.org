<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$Competitor=getCompetitor();

    DataBaseClass::FromTable('Event',"ID='".$CompetitionEvent['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    DataBaseClass::Where("Com.vCompetitors=".$CompetitionEvent['Discipline_Competitors']);
    $commands=[];
    foreach(DataBaseClass::QueryGenerate() as $row){
        $commands[$row['Command_ID']]=$row;
        $commands[$row['Command_ID']]['win']=0;
    }
    
    $CommandsCup=json_decode($CompetitionEvent['Event_CommandsCup'],true);
?>
      
<h3><?= ml('Competition_PsychSheet.Results') ?></h3>
    <table class="table_new" width="80%">
        <thead>
            <tr>
            <td/>
            <?php if($CompetitionEvent['Discipline_CodeScript']=='team_cup'){ ?>
                <td>Team</td>
            <?php } ?>
            <td colspan="<?= $CompetitionEvent['Discipline_Competitors'] ?>"><?= ml('Competition.Name')?></td>
            <td>
                <?= ml('Competition.CitizenOf')?>
            </td>
        </tr> 
    </thead>
    <?php 
    DataBaseClass::Query("
    select count(*) count,sum(wins) wins, Command from(
	select Command1 Command, case when Command1=CommandWin then 1 else 0 end wins from CupCell where Event=410  and Command1
	union all
	select Command2 Command, case when Command2=CommandWin then 1 else 0 end wins from CupCell where Event=410 and Command2
    )t group by Command");
    foreach(DataBaseClass::getRows() as $row){
        $commands[$row['Command']]['wins']=$row['wins'];
        $commands[$row['Command']]['count']=$row['count'];
    }
    foreach($commands  as $c=>$command){
        if($command['wins']==0){
            $commands[$c]['place']=($CommandsCup['Count']/2+1).' - '.sizeof($commands);
        }else{
            if(pow(2,$command['count']+1)>$CommandsCup['Count']){
                if($command['wins']==$command['count']){
                    $commands[$c]['place']=1;
                }else{
                    $commands[$c]['place']=2;    
                }
            }else{
                $commands[$c]['place']=($CommandsCup['Count']/pow(2,$command['wins']+1)+1).' - '.($CommandsCup['Count']/pow(2,$command['wins']));
            }
        }
    } ?>
    
    <?php uasort($commands,'SortCommandCupOrderResult');
    foreach($commands  as $command){ ?>
        <tr>
            <td>
               <?= $command['place'] ?>
            </td>
            <td class="table_new_bold">
                <?= $command['Command_Name'] ?>
            </td>
             <?php   
             DataBaseClass::FromTable("Command","ID=".$command['Command_ID']);
             DataBaseClass::Join_current("CommandCompetitor");
             DataBaseClass::Join_current("Competitor");
             DataBaseClass::OrderClear("Competitor","Name");
             $competitors=DataBaseClass::QueryGenerate();
            for($i=0;$i<$CompetitionEvent['Discipline_Competitors'];$i++){ 
                if(isset($competitors[$i])){ ?>
                    <td>
                        <a href="<?= LinkCompetitor($competitors[$i]['Competitor_ID'],$competitors[$i]['Competitor_WCAID'])?>">
                            <?= Short_Name($competitors[$i]['Competitor_Name']); ?>
                        </a>
                    </td>
                <?php }else{ ?>
                    <td>
                        <i class="fas fa-question"></i>
                    </td>
                <?php }
            } ?>
            <td>
                <?= CountryName($command['Command_vCountry']); ?>
            </td>   
        </tr>
    <?php } ?>
    </table>
