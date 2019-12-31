<?php 
$CompetitionEvents=ObjectClass::getObject('PageCompetitionEvents');
$Competitor=GetCompetitorData(); 
?>

<div class="line discipline_line">
    <?php $rounds_out=array();
    $countCommands=array();
    $classes=[];
    foreach($CompetitionEvents as $competition_event){
        $event_id=$competition_event['Event_ID']; ?>
        <nobr>

            <?php
            
            DataBaseClass::Query("Select coalesce(sum(case Com.Decline when 0 then 1 else 0 end),0) Commands, count(A.ID)+0 Attempts"
                    . " from Command Com "
                    . " join Event E on E.ID=Com.Event "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
                    . " join Discipline D on D.ID=DF.Discipline "
                    . " left outer join Attempt A on A.Attempt=1 and A.Command=Com.ID"
                    . " where Event=$event_id and Com.vCompetitors=D.Competitors");
                $data=DataBaseClass::getRow();
                $countCommands[$event_id]=$data['Commands'];
                $countAttempts=$data['Attempts'];
            
            $class="";    
                if($Competition['Competition_Registration']==1){
                        $class="RegisterOpen";
                }
                
                if($countCommands[$event_id]>=$competition_event['Event_Competitors'] and $Competition['Competition_Registration']==1 ){
                    $class="RegisterLimit";
                }
                
                DataBaseClass::FromTable("Command","Event=".$competition_event['Event_ID']);
                DataBaseClass::Join_current("CommandCompetitor");
                DataBaseClass::Join_current("Competitor");
                
                if($Competitor){
                    DataBaseClass::Where("Competitor","WID='".$Competitor->id."'");

                    if(DataBaseClass::QueryGenerate(false)['Command_ID']){
                        $class="CompetitionRegister";
                    }
                } 
                            
                if($countAttempts or strtotime($Competition['Competition_StartDate'])<=time() or $competition_event['Event_Round']>1){
                    $class="";
                }
                
                $classes[$competition_event['Event_ID']]=$class;
                ?>
            
                <?= ImageDiscipline($competition_event['Discipline_CodeScript'],30,$competition_event['Discipline_Name']) ?>  
                <a class="<?= $competition_event['Event_ID']==$CompetitionEvent['Event_ID']?"list_select":""?> "  href="<?= LinkEvent($competition_event['Event_ID'],$competition_event['Event_Round']) ?>">
                    <?= $competition_event['Discipline_Name'] ?><?= $competition_event['Event_vRound'] ?>
                </a>
                <span class="badge">
                    <?php if($class=='CompetitionRegister'){ ?>
                        <?= svg_blue(10); ?>
                    <?php } ?>
                    <?php if($class=='RegisterLimit'){ ?>
                        <?= svg_red(10); ?>
                    <?php } ?>
                    <?php if($class=='RegisterOpen'){ ?>
                        <?= svg_green(10); ?>
                    <?php } ?>
                    <?php if($attempts_exists and $countAttempts!=$countCommands[$event_id]){ ?>
                        <?= $countAttempts ?> / <?= $countCommands[$event_id] ?>
                    <?php }else{ ?>
                        <?php if(!$attempts_exists and $countCommands[$event_id]<$competition_event['Event_Competitors'] and $Competition['Competition_Registration']==1){ ?>
                            <?= $countCommands[$event_id] ?> / <?= $competition_event['Event_Competitors']==500?'*':$competition_event['Event_Competitors'] ?>
                        <?php }else{ ?>
                            <?= $countCommands[$event_id] ?>
                        <?php } ?>
                    <?php } ?>
                </span>
        </nobr>
    <?php } ?>
</div>
      <hr class="hr_round"> 
<?php 
 ObjectClass::setObjects('countCommands', $countCommands);
?>      
      
      
      
<?php if ($attempts_exists){
            IncludePage('Competition_Results');
        }else{
            IncludePage('Competition_PsychSheet');
    } ?>     
