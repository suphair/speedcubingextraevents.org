<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$countCommands=ObjectClass::getObject('countCommands');
$Competitor=GetCompetitorData(); ?>

<?php if(CheckAccess('Competition.Event.Settings',$Competition['Competition_ID'])){ ?>
    <img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <a  class='Settings' href="<?= LinkEvent($CompetitionEvent['Event_ID'])?>/Settings"><?= ml('CompetitionEvent.Settings') ?></a>
<?php } ?>
    
<h2>
    <?= ImageEvent($CompetitionEvent['Discipline_CodeScript'],50)?>
    <a href="<?= LinkEvent($CompetitionEvent['Event_ID']) ?>"><?= $CompetitionEvent['Discipline_Name'] ?><?= $CompetitionEvent['Event_vRound'] ?></a>
    <?php if($Competition['Competition_Registration']!=0){ ?>
        / <?= ml('Competition_PsychSheet.Register') ?>
    <?php } ?>
</h2>

<?= EventBlockLinks($CompetitionEvent); ?>
    
    
<?php if(CheckAccess('Competition.Event.Settings',$Competition['Competition_ID'])){ ?>
    <?php $comment=ml_json($CompetitionEvent['Discipline_Comment']);
        if($comment){ ?>
        <hr>
        <div class="border_warning">
            <b><?= ml('Regultions.Comment') ?></b> <?= $comment; ?>
        </div>
    <?php  } ?>    
<?php  } ?>        
    
<?php if(ml_json($CompetitionEvent['Event_Comment'])){?>
    <div class="block_comment">
        <?= Parsedown(ml_json($CompetitionEvent['Event_Comment'])); ?>
    </div>
<?php } ?>
    
<div class="block_comment">
    <?php
    DataBaseClass::FromTable('Event',"ID='".$CompetitionEvent['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    $commands=DataBaseClass::QueryGenerate();
    $isTeamPartly=false;
    if($CompetitionEvent['Discipline_Competitors']>1){
        foreach($commands as $command){
            if($command['Command_vCompetitors']<$CompetitionEvent['Discipline_Competitors']){
                $isTeamPartly=true;
            }
        }
    } 
        $count=$countCommands[$CompetitionEvent['Event_ID']];
        if($CompetitionEvent['Discipline_Competitors']>1){ ?>
            Team has <?= $CompetitionEvent['Discipline_Competitors'] ?> competitors &#9642;
        <?php } ?>
    <?= $CompetitionEvent['Format_Result'].' of '.$CompetitionEvent['Format_Attemption']?>
    <?php if($CompetitionEvent['Event_CutoffMinute']+$CompetitionEvent['Event_CutoffSecond']>0){ ?>
        &#9642; Cutoff <?= sprintf("%02d:%02d",$CompetitionEvent['Event_CutoffMinute'],$CompetitionEvent['Event_CutoffSecond'])?>
    <?php } ?>
        &#9642; <?= $CompetitionEvent['Event_Cumulative']?"Cumulative limit":"Limit"; ?> <?= sprintf("%02d:%02d",$CompetitionEvent['Event_LimitMinute'],$CompetitionEvent['Event_LimitSecond'])?>
</div>
<?php
    DataBaseClass::FromTable('Event',"ID='".$CompetitionEvent['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    if($CompetitionEvent['Event_Competitors']<=$count){
        DataBaseClass::Where("Com.vCompetitors=".$CompetitionEvent['Discipline_Competitors']);
    }
    $commands=DataBaseClass::QueryGenerate();
?>
<br>
<div class="form">
    <?php if($Competition['Competition_Registration']==0){ ?>
        <p class="error"><?= ml('Competition.Registration.False') ?></p>
    <?php } ?>
    <?php if($CompetitionEvent['Event_Round']>1){        
            if($CompetitionEvent['Event_Competitors']!=500){ ?>
                <nobr><?= $CompetitionEvent['Event_Competitors'] ?> <?=$CompetitionEvent['Discipline_Competitors']>1?'teams':'competitors'; ?></nobr>
        <?php }else{ ?>
              75% from <?=$CompetitionEvent['Discipline_Competitors']>1?'teams':'competitors'; ?>
        <?php } ?>  
    <?php } ?>  
    <?php if($Competition['Competition_Registration']!=0 and $CompetitionEvent['Event_Round']==1){ ?>
        <?php if($CompetitionEvent['Event_Competitors']!=500){ ?>            
            <?php if($CompetitionEvent['Event_Competitors']<=$count){ ?>
                <nobr><span class="error"><?= ml('Competition.Registration.Limit') ?></span></nobr> &#9642;
                <nobr><?= ($CompetitionEvent['Discipline_Competitors']>1)?
                    html_spellcount($count,ml('*.one_team'),ml('*.two_teams'),ml('*.many_teams')):
                    html_spellcount($count,ml('*.one_competitor'),ml('*.two_competitors'),ml('*.many_competitors'))?></nobr>
            <?php }else{ ?>
                <nobr><span class="message"><?= ml('Competition.Registration.True') ?></span></nobr> &#9642;
                <nobr><?= $count ?> <?= ml('*.of'); ?> 
                    <?= ($CompetitionEvent['Discipline_Competitors']>1)?
                    html_spellcount($CompetitionEvent['Event_Competitors'],ml('*.one_team'),ml('*.two_teams'),ml('*.many_teams')):
                    html_spellcount($CompetitionEvent['Event_Competitors'],ml('*.one_competitor'),ml('*.two_competitors'),ml('*.many_competitors'))?>
                </nobr>
            <?php } ?>    
        <?php }else{ ?>
                <span class="message"><?= ml('Competition.Registration.Open') ?></span>
        <?php } ?> 
        <?php if($Competitor){
            DataBaseClass::FromTable("Competitor","WID ='".$Competitor->id."'");
            DataBaseClass::Join_current("CommandCompetitor");
            DataBaseClass::Join_current("Command");
            DataBaseClass::Where_current("Event=".$CompetitionEvent['Event_ID']);
            $competitorevent_row=DataBaseClass::QueryGenerate(false);

            $CompetitorEvent=$competitorevent_row['Command_ID']; 
            if($CompetitorEvent){ ?>

                <form method="POST" action="<?= PageAction('CompetitionEvent.SelfRegistration.Delete') ?>" onsubmit="return confirm('Cancel registration \'<?= $Competitor->name ?> / <?= $Competitor->wca_id ?>\'\n on event \'<?= $CompetitionEvent['Discipline_Name'] ?>\'?')"> 
                    <input name="ID" type="hidden" value="<?=  $CompetitionEvent['Event_ID'] ?>" />
                    <span class="message"><?= $Competitor->name ?>: <?= ml('CompetitionEvent.SelfRegistration.Registered') ?></span>
                    <input class="delete" type="submit" value="<?= ml('CompetitionEvent.SelfRegistration.Delete',false) ?>">
                    <?php if($CompetitionEvent['Discipline_Competitors']>$competitorevent_row['Command_vCompetitors'] ){ ?>
                        <br><?= ml('CompetitionEvent.SelfRegistration.Team.Key') ?><b> <?= $competitorevent_row['Command_Secret'] ?></b>
                    <?php } ?>
                    <?php $err=GetMessage("RegistrationDeleteError");
                    if($err){ ?>
                        <br><span class="error"><?= $err?></span>
                    <?php } ?>
                </form>

        <?php }else{ ?>
                <?php if($CompetitionEvent['Event_Competitors']>$count){ ?>
                    <form method="POST" action="<?= PageAction('CompetitionEvent.SelfRegistration.Add') ?>" onsubmit="return confirm('Register \'<?= $Competitor->name ?> / <?= $Competitor->wca_id ?>\'\n on event \'<?= $CompetitionEvent['Discipline_Name'] ?>\'?')"> 
                        <input name="ID" type="hidden" value="<?=  $CompetitionEvent['Event_ID'] ?>" />
                        <?= $Competitor->name ?></span>
                        <?php if($CompetitionEvent['Discipline_Competitors']==1){ ?>
                            <input class="form_enter" type="submit" value="<?= ml('CompetitionEvent.SelfRegistration.Add',false) ?>">
                        <?php }else{ ?>
                            <input class="form_enter" type="submit" value="<?= ml('CompetitionEvent.SelfRegistration.AddTeam',false) ?>">
                        <?php } ?>
                        <?php $err=GetMessage("RegistrationError");
                        if($err){ ?>
                            <br><span class="error"><?= $err?></span>
                        <?php } ?>
                    </form>
                <?php if($isTeamPartly){ ?>
                     <form method="POST" action="<?= PageAction('CompetitionEvent.SelfRegistration.Add') ?>" onsubmit="return confirm('To join the team \'<?= $Competitor->name ?> / <?= $Competitor->wca_id ?>\'\n on event \'<?= $CompetitionEvent['Discipline_Name'] ?>\'?')"> 
                        <input name="ID" type="hidden" value="<?=  $CompetitionEvent['Event_ID'] ?>" />
                        <input type="text" required style="width: 180px;" placeholder="<?= ml('CompetitionEvent.SelfRegistration.Team.Placeholder',false) ?>" name="Secret" >
                        <input type="submit" value="<?= ml('CompetitionEvent.SelfRegistration.Team.Submit',false) ?>">
                        <?php $err=GetMessage("CompetitionRegistrationKey");
                        if($err){ ?>
                            <br><span class="error"><?= $err?></span>
                        <?php } ?>        
                    </form>
                <?php } ?>       
            <?php } ?> 
          <?php }
        }else{
            if($CompetitionEvent['Event_Competitors']>$count){ ?>
                <?php $_SESSION['Refer']=$_SERVER['REQUEST_URI'];  ?>    
                <nobr>&#9642;  <span class="error"><?= ml('Competition_PsychSheet.Competitor.SignIn') ?> <a href="<?= GetUrlWCA(); ?>"><?= ml('Competitor.SignIn') ?></a></span></nobr> 
        <?php }
        }
    }?>
</div>     
<?php  
    $types=array('ExtResult'=>$CompetitionEvent['Format_ExtResult'],'Result'=>$CompetitionEvent['Format_Result']);  
    $commandsData=array();
    foreach($commands as $command){
        foreach($types as $name=>$type){
            $commandsData[$command['Command_ID']][$name]=array(
                'Competition_Name'=>$CompetitionEvent['Competition_Name'],
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
                    'Event'=>$result['Event_ID'],
                    'vOut'=>$result['Attempt_vOut'],
                    'vOrder'=>$result['Attempt_vOrder'],
                    'Sum333'=>$command['Command_Sum333']
                );
            }
        }
    }    
?>
<?php if(!sizeof($commandsData)){ ?>
<?php }else{ ?>
<h3><?= ml('Competition_PsychSheet.Title') ?></h3>
    <table>
        <thead>
            <tr>
            <td/>
            <td/>
            <td class="attempt">
                <?= ml('Competition_PsychSheet.Table.'.$CompetitionEvent['Format_Result']) ?>
            </td>
            <td/>
            <?php if($CompetitionEvent['Format_ExtResult']){ ?>
            <td class="attempt">
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
        <tr class=""
            onmouseover="this.className='competitor_block_select';"
            onmouseout=" this.className='';">
            <td  class="number">
            <?php if((isset($command['Result']['vOut']) and $command['Result']['vOut']) or (isset($command['ExtResult']['vOut']) and $command['ExtResult']['vOut'])){ ?>
                <?= ++$n ?>
            <?php } ?>    
            </td>
            <td>
              <?php if($CompetitionEvent['Discipline_CodeScript']=='cup_team'){ ?>
                <div class="competitor_td">
                    <b><?= $command['Name'] ?></b>  
                </div>
            <?php } ?>
             <?php   
             DataBaseClass::FromTable("Command","ID=".$commandID);
             DataBaseClass::Join_current("CommandCompetitor");
             DataBaseClass::Join_current("Competitor");
             DataBaseClass::OrderClear("Competitor","Name");
             $competitors=DataBaseClass::QueryGenerate();
            for($i=0;$i<$CompetitionEvent['Discipline_Competitors'];$i++){ 
                if(isset($competitors[$i])){
                    $name= Short_Name($competitors[$i]['Competitor_Name']); ?>
                     <div class="result_many_rows"><a class="pos" href="<?= LinkCompetitor($competitors[$i]['Competitor_ID'],$competitors[$i]['Competitor_WCAID'])?>">
                         <nobr>
                            <?php $flag="Image/Flags/".strtolower($competitors[$i]['Competitor_Country']).".png";
                           if(file_exists($flag)){ ?>
                               <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competitors[$i]['Competitor_Country'])?>.png">
                           <?php } ?>
                            <?= $name ?>
                         </nobr>
                     </a></div>
                <?php }else{ ?>
                  <div class="result_many_rows"><?= svg_red(10); ?></span></div>
                <?php }
            } ?>
            </td>
            <?php if(strpos($CompetitionEvent['Discipline_CodeScript'],'cup_')!==false){ ?>
                <td> 
                    <?= getTimeStrFromValue($command['Result']['Sum333']); ?>
                </td>    
            <?php }else{ ?>
            <td  class="attempt">
                <?php if(isset($command['Result']['vOut'])){ ?>
                    <?= $command['Result']['vOut']; ?>    
                <?php } ?>
            </td>
            <td>
                <?php if($command['Result']['Competition_Name']){ ?>
                <a href="<?= LinkEvent($command['Result']['Event']) ?>">
                    <nobr><?= $command['Result']['Competition_Name'] ?></nobr>
                </a>
                <?php } ?>
            </td>
            <?php } ?>
            
            <?php if($CompetitionEvent['Format_ExtResult'] and isset($command['ExtResult']['vOut']) and !in_array($command['ExtResult']['vOut'],array('DNF','DNS'))){ ?>
                <td  class="attempt">
                    <?= $command['ExtResult']['vOut']; ?> 
                </td>
                <td>
                    <?php if($command['ExtResult']['Competition_Name']){ ?>
                    <a href="<?= LinkEvent($command['ExtResult']['Event']) ?>">
                        <nobr><?= $command['ExtResult']['Competition_Name'] ?></nobr>
                    </a>
                    <?php } ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </table>
<?php } ?>

<?= mlb('Competition.Registration.False') ?>
<?= mlb('Competition.Registration.True') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Registered') ?>
<?= mlb('Competition_PsychSheet.Competitor.SignIn') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Delete') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Add') ?>
<?= mlb('CompetitionEvent.SelfRegistration.AddTeam') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Team.Submit') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Team.Placeholder') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Team.Key') ?>
<?= mlb('Competition_PsychSheet.Table.Best') ?>
<?= mlb('Competition_PsychSheet.Table.Mean') ?>
<?= mlb('Competition_PsychSheet.Table.Average') ?>
<?= mlb('Competition_PsychSheet.Table.Sum') ?>
<?= mlb('Competition.Registration.Limit') ?>
<?= mlb('*.one_team') ?>
<?= mlb('*.two_teams') ?>
<?= mlb('*.many_teams') ?>
<?= mlb('*.one_competitor') ?>
<?= mlb('*.two_competitors') ?>
<?= mlb('*.many_competitors') ?>
<?= mlb('*.of'); ?> 
<?= mlb('CompetitionEvent.SelfRegistration.Add.KeyError') ?>
<?= mlb('CompetitionEvent.SelfRegistration.Add.NotFind') ?>