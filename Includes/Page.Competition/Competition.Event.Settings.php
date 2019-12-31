
<?php
$Competition=ObjectClass::getObject('PageCompetition');
$CompetitionEvents=ObjectClass::getObject('PageCompetitionEvents');
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$CompetitionDelegates=ObjectClass::getObject('PageCompetitionDelegates');
?>
<h1><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a></h1>
<div class="line discipline_line">
    <?php foreach($CompetitionEvents as $competition_event){?>
        <nobr>
            <?= ImageDiscipline($competition_event['Discipline_CodeScript'],30) ?>
            <?php if($competition_event['Event_ID']==$CompetitionEvent['Event_ID']){ ?>
                <span class="list_select"><?= $competition_event['Discipline_Name'] ?><?= $competition_event['Event_vRound'] ?></span>
            <?php }else{ ?>
                <a class="<?= $competition_event['Event_ID']==$Event?"list_select":""?>"  href="<?= LinkEvent($competition_event['Event_ID']) ?>/Settings"><?= $competition_event['Discipline_Name'] ?><?= $competition_event['Event_vRound'] ?></a>
            <?php } ?>
        </nobr>
    <?php } ?>
</div>
<hr class='hr_round'>
<h2>
    
    <img style="vertical-align: middle" width="40px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?>:
    <?= ImageDiscipline($CompetitionEvent['Discipline_CodeScript'],50) ?> 
    <a href="<?= LinkEvent($CompetitionEvent['Event_ID']) ?>"><?= $CompetitionEvent['Discipline_Name'] ?><?= $CompetitionEvent['Event_vRound'] ?></a></h1>
</h2>
        
        <br>
<h3><?= ml('Competition.Event.Setting.Block.Preparation') ?></h3>
<a target="_blank" href="<?= PageAction('CompetitionEvent.Groups' )?>/<?= $CompetitionEvent['Event_ID'] ?>">Distribution of competitors by groups</a><br>
<br>
<h3><?= ml('Competition.Event.Setting.Block.Print') ?></h3>
<a target="_blank" href="<?= PageAction('CompetitionEvent.Competitors.Print')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print the list of competitors</a><br>
<a target="_blank" href="<?= PageAction('CompetitonEvent.ScoreCards')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print competitors cards</a> <br>  
    
<br>
<h3><?= ml('Competition.Event.Setting.Block.Scrambles') ?></h3>
    <?php $file="Image/Scramble/".$CompetitionEvent['Event_ScrambleSalt'].".pdf";         
    if(file_exists($file)){ ?>
            <a target="_blank"  href="<?= PageIndex()?>Scramble/<?= $CompetitionEvent['Event_ID'] ?>">Print scrambles</font></a>
    <?php }else{ ?>
        <?= svg_red(10) ?> Scrambles not created ▪    
        <?php if(CheckAccess('Competition.Settings',$Competition['Competition_ID'])){ ?>
            To create them go to the page <a href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Settings"><?= ml('Competition.Settings') ?></a>
        <?php }else{ ?>
            Senior delgates can create them
        <?php } ?>
    <?php } ?>
<br><br>
<h3><?= ml('Competition.Event.Setting.Block.Results') ?></h3>
   <a href="<?= PageAction('ScoreTaker.Regenerate')?>/<?= $CompetitionEvent['Event_ID'] ?>">Update the link to enter results</a> <nobr>&#9642; If you do not enter the results yourself</nobr><br>   
   <a target="_blank" href="<?= PageIndex()?>ScoreTaker/<?= $CompetitionEvent['Event_Secret'] ?>">To enter results</a> <nobr>&#9642; <?= $CompetitionEvent['Event_Secret'] ?></nobr>
   <span class="message"><?= GetMessage("EventGenerateScoreTakerMessage") ?></span><br>     
   <a target="_blank" href="<?= PageAction('CompetitonEvent.Results.Print')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print the results</a><br>    

   
<div class="form">
    <b><?= ml('Competition.Event.Settings.Comment.Title') ?></b>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Edit.Comment'); ?>">
        <input name="ID" type="hidden" value="<?= $CompetitionEvent['Event_ID'] ?>" />
        <?php 
        $comments=json_decode($CompetitionEvent['Event_Comment'],true);
        if(!$comments){
            $comments[getLanguages()[0]]=$CompetitionEvent['Event_Comment'];
        }
        foreach(getLanguages() as $language){ ?>
            <b><?= ImageCountry($language,20); ?> <?= CountryName($language,true) ?></b><br>
            <textarea name="Comment[<?= $language ?>]" style="height: 80px;width: 400px"><?= isset($comments[$language])?$comments[$language]:''; ?></textarea><br>
        <?php } ?>
        <input type="submit" name="submit" value="<?= ml('*.Save',false) ?>">
        <?= mlb('*.Save') ?>
    </form> 
</div>
<br>

<a name="CompetitorEventAdd"></a>
<div class="form">
    <form method="POST" action="<?= PageAction('CompetitionEvent.Registration.Add')?>">
        <?= ml('CompetitionEvent.Registration.Add') ?><br>
        <input name="ID" type="hidden" value="<?= $CompetitionEvent['Event_ID'] ?>" />
        <select style="width: 600px" Name="Competitors[]" data-placeholder="Choose <?= html_spellcount($CompetitionEvent['Discipline_Competitors'], 'competitor', 'competitors', 'competitors')?>" class="chosen-select chosen-select-<?= $CompetitionEvent['Discipline_Competitors'] ?>" multiple>
            <option value=""></option>
            <?php 
                $CompetitorsEventAdd=GetMessage("CompetitorsEventAdd");
                if(!$CompetitorsEventAdd)$CompetitorsEventAdd=array();
                    DataBaseClass::FromTable("Registration","Competition=".$CompetitionEvent['Competition_ID']);
                    DataBaseClass::Join_current("Competitor");
                    DataBaseClass::OrderClear("Competitor", "Name");
            foreach(DataBaseClass::QueryGenerate() as $competitor){ ?>
                <option <?= in_array($competitor['Competitor_ID'],$CompetitorsEventAdd)?'selected':'' ?> value="<?= $competitor['Competitor_ID'] ?>"><?= $competitor['Competitor_WCAID'] ?> <?= $competitor['Competitor_Name'] ?></option>    
            <?php } ?>
        </select>
        <input class="form_enter" type="submit" value="<?=ml('*.Register',false) ?>"><?=mlb('*.Register') ?>
        <p>
            <span class="message"><?= GetMessage("CompetitorEventAddMessage") ?></font>
            <span class="error"><?= GetMessage("CompetitorEventAddError") ?></font> 
        </p>
    </form>
</div>
<?php
DataBaseClass::Query("select GROUP_CONCAT(C.Name order by C.Name SEPARATOR ', ') vName, Decline, count(A.ID) Attempt,"
        . "CardID,`Group`,Decline,Com.ID,Video  "
        . " from Command Com"
        . " join CommandCompetitor CC on CC.Command=Com.ID "
        . " join Competitor C on CC.Competitor=C.ID " 
        . " left outer join Attempt A on A.Command=Com.ID"
        . " where Com.Event=".$CompetitionEvent['Event_ID'].""
        . " group by Com.ID "
        . " order by 1");


$commands=DataBaseClass::getRows();
$deleter=true;
$deleter_names=array();
foreach($commands as $row){
    if(!$row['Attempt'] and !$row['Decline'] ){
        $deleter=false;  
    }else{
       if($row['Decline']){ 
            $deleter_names[]=$row['vName'];
       }
    }
}
if($deleter and sizeof($deleter_names)>0){?>
<br>
<div class="form">
    <form method="POST" action="<?= PageAction('CompetitonEvent.Registration.DeleteDeclined')?>" onsubmit="return confirm('Attention: Confirm Delete <?= sizeof($deleter_names)?> declined <?= $CompetitionEvent['Discipline_Competitors']>1?'teams':'competitors' ?> !')">
       <?= implode("<br>",$deleter_names) ?><br>
       <input name="ID" type="hidden" value="<?= $CompetitionEvent['Event_ID'] ?>" />
       <input class="delete" type="submit" value="Delete declined <?= $CompetitionEvent['Discipline_Competitors']>1?'teams':'competitors' ?>">
    </form>
</div>
<?php } ?>
<h3><?= ml('Event.Competitors.Title'); ?></h3> 
<table class="competitions">
    <tr class="tr_title">
        <td><?= ml('Event.Competitors.Table.ID'); ?></td>
        <td><?= ml('Event.Competitors.Table.Group'); ?></td>
        <td><?= ml('Event.Competitors.Table.Status'); ?></td>
        <td><?= $CompetitionEvent['Discipline_Competitors']>1?ml('Event.Competitors.Table.Teams'):ml('Event.Competitors.Table.Competitors') ?></td>
        <td/>
        <td><?= ml('Event.Competitors.Table.Video'); ?></td>
    </tr>
    <tbody> 
<?php
    $n=1;
    
    ?>    
    <?php foreach($commands as $command){ ?>   
     <tr>  
         <td>
                 <?= $command['CardID']; ?>
         </td>
         <td>
                <?= Group_Name($command['Group']) ?>
         </td>
         <td>
             <?php if($command['Decline']){ ?>
                <span class="error">Decline</span>
             <?php } ?>
            <?php if($command['Attempt']){ ?>
                <span class="message">Result</span>
             <?php } ?>
         </td>
         <td>  
            <?php 
            $WCAIDs=array();
            $Countries=array();

            DataBaseClass::FromTable('CommandCompetitor',"Command=".$command['ID']);
            DataBaseClass::Join_current('Competitor');
            DataBaseClass::OrderClear('Competitor', 'Name');
            $names=array();
            $competitors=DataBaseClass::QueryGenerate();
            for($i=0;$i<$CompetitionEvent['Discipline_Competitors'];$i++){ ?> 
                <div class="competitor_td">
                    <nobr>
                        <?php if(isset($competitors[$i])){
                                $competitor=$competitors[$i];
                                $names[]=$competitor['Competitor_Name']; ?>
                                <?= $competitor['Competitor_Name'] ?> &#9642; <?= $competitor['Competitor_WCAID']; ?> &#9642; <?= $competitor['Competitor_Country']; ?>
                                <?php if(!$competitor['CommandCompetitor_CheckStatus']){ ?>
                                    &#9642; <span class="error">no on WCA</span>
                                <?php } ?>
                        <?php }else{ ?>
                            <?= svg_red(10); ?>
                        <?php } ?>    
                    </nobr>
                </div>
            <?php } ?>
        </td>
       
        <td>
            <?php if(!$command['Attempt']){ ?>
            <form name="CompetitorRowChange" id="CompetitorRowDelete_<?= $command['ID']?>" method="POST" 
                  action="<?= PageAction('CompetitionEvent.Registration.Delete')?> " 
                  onsubmit="return confirm('Attention: Confirm deletion!\nDelete <?= implode(", ",$names)?>?')">
                <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                <input class="form_row delete" type="submit" value="X">
            </form>
            <?php }else{ ?>
                <span id="CompetitorRowDelete_<?= $command['ID']?>"/>
            <?php } ?>
        </td>
        <td>
            <form  method="POST" action="<?= PageAction('CompetitionEvent.Registration.Video')?> " >
                <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                <input  name="Video" value="<?= $command['Video'] ?>">
                <input class="form_row" type="submit" value="<?= ml('*.Save',false); ?>" style="margin:0px; padding:1px 2px;">
            </form>
        </td>
        </tr>
    <?php } ?>
        </tbody>
 </table>

<hr class="hr_round">

    <div class="block_comment">
        <b><img src='<?= PageIndex()?>Image/Icons/persons.png' width='30px'> History of registrations of competitors</b><br>
            <?php DataBaseClass::Query("Select Timestamp,Action, Doing,Details from LogsRegistration where Event=".$CompetitionEvent['Event_ID']." order by Timestamp desc" );?>
        <table>
            <?php foreach(DataBaseClass::getRows() as $row){ ?>
            <tr>
                <td><?= $row['Timestamp']?></td>
                <td><span style="
                    <?php if(substr($row['Action'],2,1)=='x' or substr($row['Action'],2,1)=='-'){ ?>
                          color:var(--red)
                    <?php } ?>
                    <?php if(substr($row['Action'],2,1)=='*' or substr($row['Action'],2,1)=='+' ){ ?>
                          color:var(--green)
                    <?php } ?>
                    ">
                    <?= str_replace(
                            ['x','-','*','+'],
                            ['Del','Rem','New','Add'],$row['Action']) ?>
                    </span>
                </td>
                <td class="border-left-dotted border-right-dotted"><?= $row['Details']?></td>
                <td><?= $row['Doing']?></td>
            </tr>
            <?php } ?>
        </table>
        <span class="badge">C</span> - Competitor <span class="badge">D</span> - Delegate <span class="badge">S</span> - ScoreTaker
    </div>
</div>




<?= mlb('Event.Competitors.Table.Teams'); ?>
<?= mlb('Event.Competitors.Table.Competitors'); ?>