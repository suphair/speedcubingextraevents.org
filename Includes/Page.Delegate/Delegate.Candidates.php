<?php includePage('Navigator'); ?>
<?php if(CheckAccess('Delegate.Candidates.Settings')){ ?>
    <a class="Settings" href="<?= PageIndex()?>Delegate/Candidates/Settings"><img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?></a>
<?php } ?>
<h1><?= ml('Delegate.Candidates.Title') ?></h1>
<span id="ExpandAll" onclick="$('#CollapseAll').show(); $('#ExpandAll').hide(); $('.Request_details').show()" style="border-bottom: 1px dotted var(--blue); cursor: pointer">Expand all</span>
<span hidden id="CollapseAll" onclick="$('#CollapseAll').hide(); $('#ExpandAll').show(); $('.Request_details').hide()" style="border-bottom: 1px dotted var(--blue); cursor: pointer">Collapse all</span>
    <hr>
    <?php 
            DataBaseClass::Query("Select RC.Competitor, RCV.Status,RCV.Delegate,D.Name,RCV.Reason  from RequestCandidate RC "
             . " join RequestCandidateVote RCV on RCV.Competitor=RC.Competitor"
             . " join Delegate D on D.ID=RCV.Delegate "); 
            $RequestCandidateVote=[];
            $RequestCandidateVoteReason=[];
            $RequestCandidateVoteReasons=[];
            foreach(DataBaseClass::getRows() as $row){
                $RequestCandidateVote[$row['Competitor']][$row['Delegate']]=$row['Status'];
                $RequestCandidateVoteReason[$row['Competitor']][$row['Delegate']]=$row['Reason'];
                $RequestCandidateVoteReasons[$row['Competitor']][]   =$row;
            } 
            DataBaseClass::Query("Select * from Delegate where status='Senior' order by Name");
            $Seniors=DataBaseClass::getRows(); 

        $Delegate= CashDelegate();
          DataBaseClass::FromTable("RequestCandidate");
          DataBaseClass::Join_current("Competitor");
          DataBaseClass::OrderClear("RequestCandidate","Status desc");
          DataBaseClass::Order("RequestCandidate","ID desc");
          $RequestCandidates=DataBaseClass::QueryGenerate();
          DataBaseClass::Join("RequestCandidate","RequestCandidateField");
          DataBaseClass::Order("RequestCandidateField","ID");
          $RequestCandidateFields=DataBaseClass::QueryGenerate();
          if(!sizeof($RequestCandidates)){ ?>
             <h2>Empty</h2>
          <?php }
          foreach($RequestCandidates as $RequestCandidate)if($RequestCandidate['RequestCandidate_Status']==0){ ?>
             <h3>
                <?php 
                $vs=[];
                foreach($Seniors as $senior){
                    if(isset($RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$senior['ID']])){ 
                    $vote=$RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$senior['ID']]; 
                    $reason=$RequestCandidateVoteReason[$RequestCandidate['RequestCandidate_Competitor']][$senior['ID']];
                    ?>
                        <?php if($vote==1){ 
                            $v=1; ?>
                            <?php if(CheckAccess('Delegate.Candidate.Vote')){ ?><span class="message"><?= substr($senior['Name'],0,1) ?><?= $reason?'*':'&nbsp;' ?></span><?php } ?>
                        <?php } ?> 
                        <?php if($vote==-1){ 
                            $v=-1; ?>
                            <?php if(CheckAccess('Delegate.Candidate.Vote')){ ?><span class="error"><?= substr($senior['Name'],0,1) ?><?= $reason?'*':'&nbsp;' ?></span><?php } ?>
                        <?php } ?> 
                        <?php if($vote==0){ 
                            $v=0; ?>
                            <?php if(CheckAccess('Delegate.Candidate.Vote')){ ?><span style="color:var(--light_gray)"><?= substr($senior['Name'],0,1) ?><?= $reason?'*':'&nbsp;' ?></span><?php } ?>
                        <?php } ?> 
                        
                    <?php 
                    $vs[]=$v;
                    } ?>
                <?php } ?>
                <?php $vs_unique=array_unique($vs);
                $result=0;
                if(sizeof($vs)==sizeof($Seniors) and in_array(1,$vs_unique) and !in_array(-1,$vs_unique)){
                    $result=1;
                }
                if(sizeof($vs)==sizeof($Seniors) and  in_array(-1,$vs_unique) and !in_array(1,$vs_unique)){
                    $result=-1;
                }
                
                ?>
                 <?= ImageCountry($RequestCandidate['Competitor_Country'], 30)?>  
                 <?php if($RequestCandidate['Competitor_Avatar']){?>
                    <img src="<?= $RequestCandidate['Competitor_Avatar'] ?>" valign=top height=30px>
                 <?php } ?>
                     <span class="<?= $result==1?'message':''  ?>
                                  <?= $result==-1?'error':''  ?>" 
                           onclick="
             if($('#<?= $RequestCandidate['Competitor_ID'] ?>').is(':hidden')){
                 $('#<?= $RequestCandidate['Competitor_ID'] ?>').show();
             }else{
                 $('#<?= $RequestCandidate['Competitor_ID'] ?>').hide();
             }
                                    " style=" border-bottom: 1px dotted var(--blue); cursor: pointer">   
                  <?= $RequestCandidate['Competitor_Name'] ?>
                 </span>
                 ▪ <?= CountryName($RequestCandidate['Competitor_Country']) ?> 
                 ▪ <?= date_range(date('Y-m-d',strtotime($RequestCandidate['RequestCandidate_Datetime']))); ?>
             </h3>
        <div hidden class="Request_details" id="<?= $RequestCandidate['Competitor_ID'] ?>">  
           <h3>
              <?= $RequestCandidate['Competitor_WCAID'] ?> <a href="https://www.worldcubeassociation.org/persons/<?= $RequestCandidate['Competitor_WCAID'] ?>"><?= ml('Delegate.Candidate.Competitor.WCA') ?></a> &#9642; 
               <a href="<?= PageIndex()."Competitor/".$RequestCandidate['Competitor_WCAID'] ?>"><?= ml('Delegate.Candidate.Competitor') ?></a>
          </h3>
            <?php if($RequestCandidate['Competitor_Avatar']){?>
                    <img src="<?= $RequestCandidate['Competitor_Avatar'] ?>" valign=top>
            <?php } ?>
          <table>
            <?php foreach($RequestCandidateFields as $RequestCandidateField){
                if($RequestCandidateField['RequestCandidateField_RequestCandidate']==$RequestCandidate['RequestCandidate_ID']){ ?>
                    <tr>
                        <td width="400"><?= $RequestCandidateField['RequestCandidateField_Field'] ?></td>
                        <td><?= $RequestCandidateField['RequestCandidateField_Value'] ?></td>
                    <tr>
                <?php }
            } ?>
            
            <?php DataBaseClass::FromTable("Competitor","ID=".$RequestCandidate['Competitor_ID']);
                  DataBaseClass::Join_current("CommandCompetitor");
                  DataBaseClass::Join_current("Command");
                  DataBaseClass::Join_current("Event");
                  DataBaseClass::Join_current("Competition");
                  DataBaseClass::Join("Event","DisciplineFormat");
                  DataBaseClass::Join_current("Discipline");
                  DataBaseClass::OrderClear("Competition","StartDate");
                  $events=DataBaseClass::QueryGenerate();
                  $competitions=array();
                  $disciplines=array();        
                  foreach($events as $event){
                      $competitions[$event['Competition_Name']]=$event['Competition_Name'];
                      $disciplines[$event['Discipline_Name']]=$event['Discipline_Name'];
                  } ?>
                <tr><td>Competitions with SEE</td><td><?= sizeof($competitions) ?></td></tr>
                <tr><td>Extra Events</td><td><?= sizeof($disciplines) ?></td></tr>
                  
                                    
                  
             </table>
        <?php if(CheckAccess('Delegate.Candidate.Vote')){ ?>
                <form method="POST" action="<?= PageAction('Delegate.Candidate.Vote') ?>">
                    <?php $vote=0;
                    if(isset($RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$Delegate['Delegate_ID']])){
                        $vote=$RequestCandidateVote[$RequestCandidate['RequestCandidate_Competitor']][$Delegate['Delegate_ID']];
                        $reason=$RequestCandidateVoteReason[$RequestCandidate['RequestCandidate_Competitor']][$Delegate['Delegate_ID']];
                        
                    } ?>
                     <?= $Delegate['Delegate_Name']?>:
                    <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID']?>'>    
                    <input type="radio" <?= $vote==1?'checked':'' ?> name="Status" value="1"><?= ml('Delegate.Candidate.Vote.Accept') ?>
                    <input type="radio" <?= $vote==0?'checked':'' ?> name="Status" value="0"><?= ml('Delegate.Candidate.Vote.None') ?>
                    <input type="radio" <?= $vote==-1?'checked':'' ?> name="Status" value="-1"><?= ml('Delegate.Candidate.Vote.Decline') ?>
                    <input type="input" value="<?= $reason ?>" name="Reason">
                    <input type='submit' value='<?= ml('Delegate.Candidate.Vote',false) ?>'>
                </form>                 
                <div class="block_comment">    
                <?php 
                $reasons=[];
                if(isset($RequestCandidateVoteReasons[$RequestCandidate['RequestCandidate_Competitor']])){
                    foreach($RequestCandidateVoteReasons[$RequestCandidate['RequestCandidate_Competitor']] as $row){
                          $str='';
                          $str.= $row['Name'];
                          if($row['Status']==-1){
                            $str.= " <span class='error'>[".ml('Delegate.Candidate.Vote.Decline')."]</span>";
                          } 
                          if($row['Status']==0){
                            $str.= " <span>[".ml('Delegate.Candidate.Vote.None')."]</span>";
                          } 
                          if($row['Status']==1){
                            $str.= " <span class='message'>[".ml('Delegate.Candidate.Vote.Accept')."]</span>";
                          } 
                          if($row['Reason']){
                            $str.= ': '.$row['Reason'];
                          }
                          $reasons[]=$str;
                    }
                } ?>
                <?= implode("<br>",$reasons) ?>
                </div>    
        <?php } ?>                    

         <?php if(CheckAccess('Delegate.Candidate.Decline') and $result==-1 ){ ?>
             <form method="POST" action="<?= PageAction('Delegate.Candidate.Decline') ?>" onsubmit="return confirm('Внимание:Подтвердите отказ для <?= $RequestCandidate['Competitor_Name'] ?>.')">
                <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID']?>'>    
                <input class='delete' type='submit' value='<?= ml('Delegate.Candidate.Decline',false) ?>'>
            </form>
        <?php ?>
         <?php }  ?>        
         <?php if(CheckAccess('Delegate.Candidate.Accept') and $result==1 ){ ?>                   
            <form method="POST" action="<?= PageAction('Delegate.Candidate.Accept') ?>" onsubmit="return confirm('Внимание:Подтвердите принятие <?= $RequestCandidate['Competitor_Name'] ?>.')">
                <input type='hidden' name="RequestCandidate" value='<?= $RequestCandidate['RequestCandidate_ID']?>'>    
                <input type='submit' value='<?= ml('Delegate.Candidate.Accept',false) ?>'>
            </form>
        <?php }  ?>
        </div>            
<?php }  ?>  
<?php if(CheckAccess('Delegate.Candidate.Vote')){ ?>
    <hr class="hr_round">
    <h1><?= ml('Delegate.Candidates.Rejected') ?></h1>
    <table>
        <tr class="tr_title">
            <td><?= ml('Delegate.Candidates.Competitor') ?></td>
            <td><?= ml('Delegate.Candidates.Country') ?></td>
            <td>WCAID</td>
            <td><?= ml('Delegate.Candidates.BlockedUntil') ?></td>
            <td><?= ml('Delegate.Candidates.Reasons') ?></td>
        </tr>
    <?php foreach($RequestCandidates as $RequestCandidate)if($RequestCandidate['RequestCandidate_Status']==-1){ ?>
        <tr>
            <td>
                <?= $RequestCandidate['Competitor_Name'] ?>
            </td>
            <td>
                <?= ImageCountry($RequestCandidate['Competitor_Country'], 20)?>  
                <?= CountryName($RequestCandidate['Competitor_Country']) ?> 
            </td>
            <td>
                <a href="https://www.worldcubeassociation.org/persons/<?= $RequestCandidate['Competitor_WCAID'] ?>"><?= $RequestCandidate['Competitor_WCAID'] ?></a>
            </td>
            <td>
                <?= date_range(date('Y-m-d',strtotime($RequestCandidate['RequestCandidate_Datetime']." +1 year "))) ?>
            </td>    
            <td  class="border-left-solid">
                <?php
                $reasons=[];
                if(isset($RequestCandidateVoteReasons[$RequestCandidate['RequestCandidate_Competitor']])){
                    foreach($RequestCandidateVoteReasons[$RequestCandidate['RequestCandidate_Competitor']] as $row){
                      if($row['Reason'] and $row['Status']=-1){
                        $reasons[]=  substr($row['Name'],0,1).': '.$row['Reason'];
                      }
                    }
                } ?>
                <?= implode("; ",$reasons) ?>
            </td>
        </tr>
    <?php } ?>            
    </table>   
<?php } ?>
<?= mlb('Delegate.Candidate.Decline') ?>
<?= mlb('Delegate.Candidate.Accept') ?>
<?= mlb('Delegate.Candidate.Vote') ?>