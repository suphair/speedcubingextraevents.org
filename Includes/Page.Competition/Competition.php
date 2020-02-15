<?php 
$Competition=ObjectClass::getObject('PageCompetition');
$CompetitionEvents=ObjectClass::getObject('PageCompetitionEvents');
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$CompetitionDelegates=ObjectClass::getObject('PageCompetitionDelegates');
$Competitor=GetCompetitorData(); 

if(!$CompetitionEvent and isset($CompetitionEvents[0])){
    ObjectClass::setObjects('PageCompetitionEvent',$CompetitionEvents[0]);
    $CompetitionEvent=$CompetitionEvents[0];        
}

DataBaseClass::Query("select Cn.ID, count( distinct C.ID) count,count(A.ID) Attempts, now()>=Cm.StartDate Start from `Competition` Cn  
join `Event` E on E.Competition=Cn.ID
join `Competition` Cm on Cm.ID=E.Competition
left outer join `Command` Com on Com.Event=E.ID and Com.Decline!=1
left outer join `CommandCompetitor` CC on CC.Command=Com.ID 
left outer join `Competitor` C on CC.Competitor=C.ID
left outer join Attempt A on A.Command=Com.ID
where E.Competition='".$Competition['Competition_ID']."'
group by Cn.ID");
$data=DataBaseClass::getRow();

$count_competitors=$data['count']+0;
$attempts_exists=($data['Attempts']>0 or $data['Start']);
    
?>
 
    <h1>
        <?= ImageCountry($Competition['Competition_Country']) ?></span>
        <a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a>
    </h1>    
        
        
        
<?php 
    $delegatesWCA_out=[];
    $delegatesSEE_out=[];
    if($Competition['Competition_DelegateWCAOn']){
        $CompetitionDelegatesWCA=explode(',',$Competition['Competition_DelegateWCA']);
        foreach($CompetitionDelegatesWCA as $d=>$delegate){
            if(trim($delegate)){
                DataBaseClass::FromTable('Competitor',"WCAID='".trim($delegate)."'");
                $row=DataBaseClass::QueryGenerate(false);
                if(isset($row['Competitor_Name'])){
                    $delegatesWCA_out[]="<a href='mailto:".$row['Competitor_Email']."'><i class='far fa-envelope'></i> ".$row['Competitor_Name']."</a>";   
                }
            }
        }
    }else{
        foreach($CompetitionDelegates as $delegate){ 
            ob_start(); 
            if($delegate['Delegate_Status']!='Archive'){
                ?><a href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>"><?= Short_Name($delegate['Delegate_Name'])?></a><?php 
            }else{
                ?><?= Short_Name($delegate['Delegate_Name'])?><?php
            }        
            $delegatesSEE_out[]= ob_get_contents();
            ob_end_clean();
       }
    }  ?>  
    <table width="100%"><tr><td>            
    <table class="table_info" >
        <tr>    
            <td><?= ml('Competition.Date') ?></td>
            <td><?=  date_range($Competition['Competition_StartDate'],$Competition['Competition_EndDate']); ?></td>
        </tr>   
        <tr>    
            <td><?= ml('Competition.City') ?></td>
            <td><?= CountryName($Competition['Competition_Country']); ?>, <?= $Competition['Competition_City'] ?></td>
        </tr>   
        <tr>
            <td><?= ml('Competition.WCApage') ?></td>
            <td><a target="_blank" href="https://www.worldcubeassociation.org/competitions/<?= $Competition['Competition_WCA'] ?>"><?= $Competition['Competition_Name'] ?> <i class="fas fa-external-link-alt"></i></a></td>
        </tr>
        <?php if($delegatesSEE_out){ ?>
        <tr>
            <td><?= sizeof($delegatesSEE_out)>1?ml('Competiion.Delegates'):ml('Competiion.Delegate') ?></td>
            <td><p><?= implode("</p><p>",$delegatesSEE_out) ?></p></td>
        </tr>
        <?php } ?>
        <?php if($delegatesWCA_out){ ?>
        <tr>
            <td><?= sizeof($delegatesWCA_out)>1?ml('Competiion.DelegatesWCA'):ml('Competiion.DelegateWCA') ?></td>
            <td><p><?= implode("</p><p>",$delegatesWCA_out) ?></p></td>
        </tr>
        <?php } ?>
        <?php if(strtotime($Competition['Competition_EndDate'])>=strtotime(date('Y-m-d'))){ ?>
        <tr>    
            <?php if(!$attempts_exists){ $reg_out=true;?>
            <td><?= ml('Competition.Registration') ?></td>
            <td>
                <?php if($Competition['Competition_Registration']){ ?>
                    <i class="fas fa-check-circle"></i> <?= ml('Competition.Registration.True') ?>
                <?php }else{ ?>
                    <i class="fas fa-times-circle"></i> <?= ml('Competition.Registration.False') ?>
                <?php } ?>
            </td>        
            <?php } ?>
        </tr>
            <?php if($Competition['Competition_Onsite']){ ?>
        <tr>
            <td></td>
            <td><i class="fas fa-check-circle"></i> <?= ml('Competition.Onsite.True') ?></td>
        </tr>        
           <?php } ?>
        <?php } ?>   
        <?php if(strtotime($Competition['Competition_StartDate'])<=strtotime(date('Y-m-d')) and $Competition['Competition_Unofficial'] ){ ?>
        <tr>
            <td><?= ml('Competition.Results') ?></td>
            <td>
            <?= svg_red(); ?> <?= $Competition['Competition_DelegateWCAOn']?ml('Competition.Unofficial.True'):ml('Competition.Unofficial.TrueTemp') ?>
            </td>
        </tr>    
        <?php } ?>
        <tr>
            <td><?= ml('Competition.ExtraEvents') ?></td>
            <td>
                <?php if(!sizeof($CompetitionEvents)){ ?>
                    <i class="fas fa-ban"></i>
                <?php } ?>
            </td>
        </tr>    
            
            <?php $countCommands=array();
            foreach($CompetitionEvents as $competition_event){  ?>
               <tr>
                   <td><?= ImageEvent($competition_event['Discipline_CodeScript'],1.3,$competition_event['Discipline_Name']) ?></td>
                   <td>
                <a class="<?= $competition_event['Event_ID']==$CompetitionEvent['Event_ID']?"list_select":""?>"  href="<?= LinkEvent($competition_event['Event_ID'],$competition_event['Event_Round']) ?>"><?= $competition_event['Discipline_Name'] ?><?= $competition_event['Event_vRound'] ?></a>
                    </td>
                </td>
            <?php } ?>        
         </tr>
    </table>  
    </td>
    <td> 
        <table class="table_info" >
        <?php if(CheckAccess('Competition.Settings',$Competition['Competition_ID'])){ ?>
        <tr>
            <td><i class="fas fa-cog"></i></td>
            <td><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Settings">Competition settings</a></td>    
        </tr>
        <?php } ?> 
        <?php if(strtotime($Competition['Competition_StartDate'])<=strtotime(date('Y-m-d'))){
            if(CheckAccess('Competition.Report.Create',$Competition['Competition_ID'])){ ?>    
        <tr>
            <td><i class="far fa-file-alt"></i></td>
            <td><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Report">Reports</a></td>
        </tr>        
            <?php }elseif(CheckAccess('Competition.Report',$Competition['Competition_ID']) and sizeof(DataBaseClass::SelectTableRows("CompetitionReport","Competition=".$Competition['Competition_ID']))){ ?>
        <tr>
            <td><i class="far fa-file-alt"></i></td>
            <td><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Report">Reports</a></td>
        </tr>                
            <?php } 
        } ?>
        
        <?php if(!$Competition['Competition_Status']){?>
        <tr>    
            <td><i class="fas fa-eye-slash"></i></td>
            <td><?=  ml('Competition.Status.Show.False') ?></td>
        </tr>   
        <?php } ?>
 
        <?php if($comment=Parsedown(ml_json($Competition['Competition_Comment']),false)){ ?>
             <tr>
                <td><?= ml('Competition.Information') ?></td>
                <td><?= $comment ?></td>
            </tr>    
        <?php } ?>
        <tr>
            <td><?= ml('Competition.Competitors')?></td>
            <td><?= $count_competitors ?></td>
        </tr>
        </table>    
    </td></tr></table>     

<?php if(sizeof($CompetitionEvents)){ ?>
<h1>
    <?= ImageEvent($CompetitionEvent['Discipline_CodeScript'])?>
    <?= $CompetitionEvent['Discipline_Name'] ?><?= $CompetitionEvent['Event_vRound'] ?>
    <?php if($attempts_exists){ ?>
        / <?= ml('Competition_PsychSheet.Results') ?>
    <?php }else{  ?>
        <?php if($Competition['Competition_Registration']!=0){ ?>
           / <?= ml('Competition_PsychSheet.Register') ?>
       <?php } ?>
    <?php } ?>     
</h1>

<?php
DataBaseClass::Query("Select coalesce(sum(case Com.Decline when 0 then 1 else 0 end),0) Commands, count(A.ID)+0 Attempts"
         . " from Command Com "
         . " join Event E on E.ID=Com.Event "
         . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
         . " join Discipline D on D.ID=DF.Discipline "
         . " left outer join Attempt A on A.Attempt=1 and A.Command=Com.ID"
         . " where Event={$CompetitionEvent['Event_ID']} and Com.vCompetitors=D.Competitors");
$data=DataBaseClass::getRow();
$countCommands=$data['Commands'];
ObjectClass::setObjects('CompetitionEventCommands',$countCommands);
?>              
<table width="100%"><tr><td>     
    <table class="table_info">
        
        <?php if(CheckAccess('Competition.Event.Settings',$Competition['Competition_ID'])){ ?>
    <?php $comment=ml_json($CompetitionEvent['Discipline_Comment']);
        if($comment){ ?>
        <tr>
            <td>For delegates</td>
            <td> <i class="fas fa-exclamation-circle"></i> <?= $comment; ?></td>
        </tr>
    <?php  } ?>    
<?php  } ?>  
        
<?php if(CheckAccess('Competition.Event.Settings',$Competition['Competition_ID'])){ ?>
        <tr>
            <td><i class="fas fa-cog"></i></td>
            <td><a href="<?= LinkEvent($CompetitionEvent['Event_ID'])?>/Settings">Competition event settings</a></td>
        </tr>        
<?php } ?>     
<?php if($CompetitionEvent['Event_Competitors']!=500){ ?>        
        <tr>
            <td>
                <?php if($CompetitionEvent['Discipline_Competitors']>1){?>
                    <?= ml('Competition.LimitTeam') ?>                
                <?php }else{ ?>
                    <?= ml('Competition.LimitCompetitors') ?>
                <?php } ?>             
            </td>
            <td><?= $CompetitionEvent['Event_Competitors'] ?></td>
        </tr>    
<?php } ?>        
                                    
    <?php 
    $registrartionLimit=false; 
    $isRegisterOpen=false;
    if(!$attempts_exists and $Competition['Competition_Registration']!=0 and $CompetitionEvent['Event_Round']==1){
        if($CompetitionEvent['Event_Competitors']<=$countCommands){
            $registrartionLimit=true;
        }else{
            $isRegisterOpen=true;
        }
    } 
    
    $isTeamPartly=false;
    if($isRegisterOpen  and $CompetitionEvent['Discipline_Competitors']>1){
        DataBaseClass::FromTable('Event',"ID='".$CompetitionEvent['Event_ID']."'");
        DataBaseClass::Join_current('Command');    
        $commands=DataBaseClass::QueryGenerate();
        foreach($commands as $command){
            if($command['Command_vCompetitors']<$CompetitionEvent['Discipline_Competitors']){
                $isTeamPartly=true;
            }
        }
    }
    
    ?> 
      
      
<?php if($CompetitionEvent['Event_Round']>1 and $CompetitionEvent['Event_Competitors']==500){ ?>
        <tr>
            <td>
                <?php if($CompetitionEvent['Discipline_Competitors']>1){?>
                    <?= ml('Competition.LimitTeam') ?>                
                <?php }else{ ?>
                    <?= ml('Competition.LimitCompetitors') ?>
                <?php } ?>             
            </td>    
            <td>
                <?= ml('Competition.NextRound',75) ?>
            </td>
      </tr>
<?php } ?>        
        
<tr>
    <td>
        <?php if($CompetitionEvent['Discipline_Competitors']>1){?>
            <?= ml('Competition.RegisteredTeam') ?>                
        <?php }else{ ?>
            <?= ml('Competition.RegisteredCompetitors') ?>
        <?php } ?>          
    </td>
    <td><?= $countCommands?>
        <?php if($registrartionLimit){ ?>
            <i class="fas fa-hand-paper"></i> <?= ml('Competition.LimitRegistration') ?>
        <?php } ?>
    </td>
</tr>         
        
        
<?php if($CompetitionEvent['Discipline_Competitors']>1){ ?>
        <tr>
            <td><?= ml('Competition.TeamEvent') ?></td>
            <td><?= $CompetitionEvent['Discipline_Competitors'] ?> <?= ml('Competition.CompetitorsTeam') ?></td>
        </tr>    
<?php } ?> 
        <tr>
            <td><?= ml('Competition.Format') ?></td>
            <td>
                <?php if($CompetitionEvent['Format_Result']=='Average'){ ?>
                    <?= ml('Competition.Average') ?> <?= $CompetitionEvent['Format_Attemption'] ?> 
                <?php } ?>
                <?php if($CompetitionEvent['Format_Result']=='Mean'){ ?>
                    <?= ml('Competition.Mean') ?> <?= $CompetitionEvent['Format_Attemption'] ?> 
                <?php } ?>
                <?php if($CompetitionEvent['Format_Result']=='Best'){ ?>
                    <?= ml('Competition.Best') ?> <?= $CompetitionEvent['Format_Attemption'] ?> 
                <?php } ?>
                <?php if($CompetitionEvent['Format_Result']=='Sum'){ ?>
                    <?= ml('Competition.Sum') ?> <?= $CompetitionEvent['Format_Attemption'] ?> 
                <?php } ?>
            </td>
        </tr>         
<?php if($CompetitionEvent['Event_CutoffMinute']+$CompetitionEvent['Event_CutoffSecond']>0){ ?>
        <tr>
            <td><?= ml('Competition.Cutoff') ?></td>
            <td>
                <?= $CompetitionEvent['Event_CutoffMinute'] ?> <?= ml('Competition.Minutes') ?> 
                <?= $CompetitionEvent['Event_CutoffSecond'] ?> <?= ml('Competition.Seconds') ?>
            </td>
        </tr>         
<?php } ?>
        <tr>
            <td><?= ml('Competition.Limit') ?></td>
            <td>
                <?= $CompetitionEvent['Event_LimitMinute'] ?> <?= ml('Competition.Minutes') ?> 
                <?= $CompetitionEvent['Event_LimitSecond'] ?> <?= ml('Competition.Seconds') ?>
            </td>
        </tr>   
<?php if($comment=Parsedown(ml_json($CompetitionEvent['Event_Comment']),false)){?>
        <tr>
            <td><?= ml('Competition.Information') ?></td>
            <td><?= $comment ?></td>
        </tr>
<?php } ?>
<?php if($CompetitionEvent['Event_ScramblePublic']){ ?>
        <tr>
            <td><i class="fas fa-random"></i></td>
            <td><a href="<?= PageIndex()?>Scramble/<?= $CompetitionEvent['Event_ScramblePublic'] ?>" target="_blank"><?= ml('Competition_Results.ScrambleShare')?></a> </td>
        </tr>    
<?php } ?>


<?php if($isRegisterOpen){ ?>        
    <tr>
        <td> <?= ml('Competition.Person') ?></td>
       <td>
           <?php if(!$Competitor){ ?>
                <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?> 
                <a href="<?= GetUrlWCA(); ?>"><i class="fas fa-sign-in-alt"></i> <?= ml('Competitor.SignIn')?></a>
           <?php }else{ ?>
                <?= Short_Name($Competitor->name); ?>
           <?php } ?>
       </td>    
    </tr>         
<?php } ?>        

<?php if($Competitor){ ?>        
<?php 
    DataBaseClass::FromTable("Competitor","WID ='".$Competitor->id."'");
    DataBaseClass::Join_current("CommandCompetitor");
    DataBaseClass::Join_current("Command");
    DataBaseClass::Where_current("Event=".$CompetitionEvent['Event_ID']);
    $competitorevent_row=DataBaseClass::QueryGenerate(false);
    $RegisterID=$competitorevent_row['Command_ID']; 
    $RegisterCompetitiorTeam=$competitorevent_row['Command_vCompetitors'];
?>        
<?php if($isTeamPartly and !$RegisterID){ ?>
     <tr>
        <td><?= ml('CompetitionEvent.SelfRegistration.Team.Submit',false) ?></td>
        <td>
        <form method="POST" action="<?= PageAction('CompetitionEvent.SelfRegistration.Add') ?>"> 
           <input name="ID" type="hidden" value="<?=  $CompetitionEvent['Event_ID'] ?>" />
           <i class="fas fa-lock"></i> <input type="text" required style="width: 100px;" placeholder="<?= ml('CompetitionEvent.SelfRegistration.Team.Placeholder',false) ?>" name="Secret" >
           <button><i class="fas fa-sm fa-user-plus"></i> join</button>

           <?php $err=GetMessage("RegistrationError");
           if($err){ ?>
               <p><?= svg_red() ?> <?= $err?></p>
           <?php } ?>
           <?php $err=GetMessage("CompetitionRegistrationKey");
           if($err){ ?>
               <p><?= svg_red() ?> <?= $err?></p>
           <?php } ?>        
        </form>      
       </td>        
    </tr>
</script>
<?php } ?>                

<?php if($isRegisterOpen and !$RegisterID){ ?>
    <form method="POST" action="<?= PageAction('CompetitionEvent.SelfRegistration.Add') ?>"> 
    <input name="ID" type="hidden" value="<?=  $CompetitionEvent['Event_ID'] ?>" />
    <tr>
        <td><?= ml('Competition.Registration') ?></td>
        <td>
        <?php if($CompetitionEvent['Discipline_Competitors']==1){ ?>
            <button><i class="fas fa-user-check"></i> <?= ml('Competition.Register'); ?></button>
        <?php }else{ ?>
            <button><i class="fas fa-sm fa-user-friends"></i> <?= ml('Competition.Create'); ?></button>
        <?php } ?>    
        </td>    
    </tr>    
    </form>
<?php } ?>
<?php if($RegisterID){ ?>
    <?php if($CompetitionEvent['Discipline_Competitors']!=$RegisterCompetitiorTeam){?>
        <?php if($isRegisterOpen){ ?>
        <tr>
            <td><?= ml('Competition.Registration') ?></td>
            <td>
                    <i class="fas fa-hourglass-half"></i> <?= ml('Competition.WaitTeam') ?>
                <?php for($i=$CompetitionEvent['Discipline_Competitors']-$RegisterCompetitiorTeam;$i<$CompetitionEvent['Discipline_Competitors'];$i++){ ?>    
                    <i class="fas fa-user"></i>
                <?php } ?>    
                <?php for($i=0;$i<$CompetitionEvent['Discipline_Competitors']-$RegisterCompetitiorTeam;$i++){ ?>
                    <i class="far fa-user"></i>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= ml('CompetitionEvent.SelfRegistration.Team.Key') ?>
            </td>
            <td>
                <?= ml('Competition.PassTeammate') ?><br>
                <i class="fas fa-key"></i> <?= $competitorevent_row['Command_Secret'] ?>
            </td>
        </tr>    
        <?php }else{ ?>
            <tr>
                <td><?= ml('Competition.Registration') ?></td>
                <td><?= svg_red() ?> <?= ml('Competition.TeamIncomplete') ?></td>
            </tr>
        <?php } ?>
    <?php }else{ ?>
         <tr>
            <td><?= ml('Competition.Registration') ?></td>
            <td>
                   <?= svg_green() ?> <?= ml('Competition.Complete') ?>
            </td>
        </tr>
    <?php } ?>        
<?php } ?>

<?php if($isRegisterOpen and $RegisterID){ ?>
        
        <tr>
            <td><?= ml('Competition.CancelRegistration') ?></td>
            <td>
                <form method="POST" action="<?= PageAction('CompetitionEvent.SelfRegistration.Delete') ?>" onsubmit="return confirm('Confirm: Cancel registration')"> 
                    <input name="ID" type="hidden" value="<?=  $CompetitionEvent['Event_ID'] ?>" />
                    <button class="delete"><i class="fas fa-user-minus fa-flip-horizontal"></i> <?= ml('Competition.Cancel') ?></button>
                </form>
            </td>
        </tr>
<?php } ?>
<?php } ?>
    </table>    
</td><td>    
    <?= EventBlockLinks($CompetitionEvent); ?>
</td></tr></table> 

<?php if ($attempts_exists){
            IncludePage('Competition_Results');
    }else{
            IncludePage('Competition_PsychSheet');
    } ?>                        
                
<?php } ?>                