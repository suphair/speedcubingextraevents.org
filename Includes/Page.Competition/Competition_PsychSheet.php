<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$Competitor=GetCompetitorData();
$countCommands=ObjectClass::getObject('CompetitionEventCommands'); 

    DataBaseClass::FromTable('Event',"ID='".$CompetitionEvent['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    if($CompetitionEvent['Event_Competitors']<=$countCommands){
        DataBaseClass::Where("Com.vCompetitors=".$CompetitionEvent['Discipline_Competitors']);
    }
    $commands=DataBaseClass::QueryGenerate();
?>
   
<?php  
    $types=array('ExtResult'=>$CompetitionEvent['Format_ExtResult'],'Result'=>$CompetitionEvent['Format_Result']);  
    $commandsData=array();
    foreach($commands as $command){
        foreach($types as $name=>$type){
            $commandsData[$command['Command_ID']][$name]=array(
                'Competition_Name'=>$CompetitionEvent['Competition_Name'],
                'Command_vCountry'=>'',
                'Event'=>$CompetitionEvent['Event_ID'],
                'Out'=>''
            ); 
            DataBaseClass::Query("Select Com.Name from Competitor C join CommandCompetitor CC on CC.Competitor=C.ID"
                    . " join Command Com on Com.ID=CC.Command "
                    . " where Com.ID=".$command['Command_ID']
                    . " order by C.Name Limit 1");
            $commandsData[$command['Command_ID']]['Name']=DataBaseClass::getRow()['Name'];
            if(true or $command['Command_vCompetitors']==$CompetitionEvent['Discipline_Competitors']){
                DataBaseClass::Query("Select * from CommandCompetitor CC where CC.Command=".$command['Command_ID']);
                
                $sql='Select Com.ID from Command Com ';
                foreach(DataBaseClass::getRows() as $competitor){
                    $Competitor_ID=$competitor['Competitor'];
                    $sql.=' join CommandCompetitor CC'.$Competitor_ID.' on CC'.$Competitor_ID.'.Command=Com.ID';
                    $sql.=' join Competitor C'.$Competitor_ID.' on C'.$Competitor_ID.'.ID=CC'.$Competitor_ID.'.Competitor and C'.$Competitor_ID.'.ID='.$Competitor_ID;
                
                }
                DataBaseClass::Query($sql);
                $command_ids=array();
                foreach(DataBaseClass::getRows() as $com){
                    $command_ids[]=$com['ID'];
                }

                $type_arr=[$type];
                if($type=='Mean' or $type=='Average'){
                    $type_arr=['Mean','Average'];    
                }
                DataBaseClass::FromTable('Command',"ID in('".implode("','",$command_ids)."')");
                DataBaseClass::Join_current('Event');
                DataBaseClass::Join_current('DisciplineFormat');
                DataBaseClass::Where_current("Discipline='".$CompetitionEvent['Discipline_ID']."'");
                DataBaseClass::Join('Event','Competition');
                DataBaseClass::Join('Command','Attempt');
                DataBaseClass::Where_current("Special in ('".implode("','",$type_arr)."')");
                
                if(strpos($CompetitionEvent['Discipline_CodeScript'],'cup_')===false){
                    DataBaseClass::OrderClear('Attempt', 'vOrder') ;
                }else{
                    DataBaseClass::OrderClear('Command', 'Sum333') ;
                }
                DataBaseClass::Limit('1');
                $result=DataBaseClass::QueryGenerate(false);
                $commandsData[$command['Command_ID']][$name]=array(
                    'Competition_Name'=>$result['Competition_Name'],
                    'Command_vCountry'=>$command['Command_vCountry']?$command['Command_vCountry']:'Multi-country',
                    'Event'=>$result['Event_ID'],
                    'vOut'=>$result['Attempt_vOut'],
                    'vOrder'=>$result['Attempt_vOrder'],
                    'Sum333'=>$command['Command_Sum333']
                );
            }
        }
    }    
?>
<h3><?= ml('Competition_PsychSheet.Title') ?></h3>
    <table class="table_new" width="80%">
        <thead>
            <tr>
            <td/>
            <?php if($CompetitionEvent['Discipline_CodeScript']=='cup_team'){ ?>
                <td>Team name</td>
            <?php } ?>
            <td colspan="<?= $CompetitionEvent['Discipline_Competitors'] ?>"><?= ml('Competition.Name')?></td>
            <td>
                <?= ml('Competition.CitizenOf')?>
            </td>
            <td>
                <?= ml('Competition_PsychSheet.Table.'.$CompetitionEvent['Format_Result']) ?>
            </td>
            <td/>
            <?php if($CompetitionEvent['Format_ExtResult']){ ?>
            <td>
                <?= ml('Competition_PsychSheet.Table.'.$CompetitionEvent['Format_ExtResult']) ?>
            </td>
            <?php } ?>
            <td/>
        </tr> 
    </thead>
    <?php 
    $n=0;
   
    uasort($commandsData,'SortCommandOrder');
    
    foreach($commandsData  as $commandID=>$command){ ?>
        <tr>
            <td>
            <?php if((isset($command['Result']['vOut']) and $command['Result']['vOut']) or (isset($command['ExtResult']['vOut']) and $command['ExtResult']['vOut'])){ ?>
                <?= ++$n ?>
            <?php } ?>    
            </td>
            <?php if($CompetitionEvent['Discipline_CodeScript']=='cup_team'){ ?>
                <td>
                    <?= $command['Name'] ?>
                </td>
            <?php } ?>
             <?php   
             DataBaseClass::FromTable("Command","ID=".$commandID);
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
                <?= CountryName($command['Result']['Command_vCountry']); ?>
            </td>   
            <?php if(strpos($CompetitionEvent['Discipline_CodeScript'],'cup_')!==false){ ?>  
                <td> 
                    <?= getTimeStrFromValue($command['Result']['Sum333']); ?>
                </td>    
            <?php }else{ ?>
            <td>
                <?php if(isset($command['Result']['vOut'])){ ?>
                    <?= $command['Result']['vOut']; ?>    
                <?php } ?>
            </td>
            <td>
                <?php if($command['Result']['Competition_Name']){ ?>
                <a href="<?= LinkEvent($command['Result']['Event']) ?>">
                    <?= $command['Result']['Competition_Name'] ?>
                </a>
                <?php } ?>
            </td>
            <?php } ?>
            
            <?php if($CompetitionEvent['Format_ExtResult'] and isset($command['ExtResult']['vOut']) and !in_array($command['ExtResult']['vOut'],array('DNF','DNS'))){ ?>
                <td>
                    <?= $command['ExtResult']['vOut']; ?> 
                </td>
                <td>
                    <?php if($command['ExtResult']['Competition_Name']){ ?>
                    <a href="<?= LinkEvent($command['ExtResult']['Event']) ?>">
                        <?= $command['ExtResult']['Competition_Name'] ?>
                    </a>
                    <?php } ?>
                </td>
            <?php }else{ ?>
                <td/><td/>
            <?php } ?>
        </tr>
    <?php } ?>
    </table>

<?php if(!sizeof($commandsData)){ ?>
    <i class="fas fa-user-slash"></i> <?= ml('Compettion.NoCommand');    ?>
<?php } ?>