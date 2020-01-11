<?php
$Competition=ObjectClass::getObject('PageCompetition');
$CompetitionDelegates=ObjectClass::getObject('PageCompetitionDelegates');
?>

<h1><img style="vertical-align: middle" width="40px" src="<?= PageIndex()?>Image/Icons/settings.png"> <?= ml('*.Settings') ?>: <a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a></h1>
<?php 
#$eventRounds
DataBaseClass::Query(" select count(distinct E.ID) Rounds,D.ID Discipline_ID from "
        . " Discipline D "
        . " left outer join DisciplineFormat DF on D.ID=DF.Discipline"
        . " left outer join Event E on E.DisciplineFormat=DF.ID and E.Competition=".$Competition['Competition_ID']
        . " group by D.ID");

foreach(DataBaseClass::getRows() as $event){
    $eventRounds[$event['Discipline_ID']]=$event['Rounds']+1;
} ?>


<?php if(!$Competition['Competition_Cubingchina']){ ?>
<form method='POST' action='<?= PageAction('Competition.Competitors.Check')?>'>
    <?= ml('Competition.Competitors.Check') ?>
    <input hidden name='ID' value='<?= $Competition['Competition_ID'] ?>'>
    <input class="form_row" type='submit' value='<?= ml('*.Check',false) ?>'><?= mlb('*.Check')?>
    <?= $Competition['Competition_CheckDateTime'] ?>
</form>
<?php } ?>
<form method='POST' action='<?= PageAction('Competition.Competitors.Load')?>'>
     <?= ml('Competition.Competitors.Load',$Competition['Competition_Cubingchina']?'Cubingchina':'WCA' ) ?>
    <?php if(CheckAccess('Competition.Settings.Ext',$Competition['Competition_ID'])){ ?>
        <input hidden name='ID' value='<?= $Competition['Competition_ID'] ?>'>
        <span class="badge"><input class="form_row" type='submit' value='<?= ml('*.Reload',false) ?>'><?= mlb('*.Reload')?> Ext</span>   
    <?php } ?>
    <?= $Competition['Competition_LoadDateTime'] ?>
 </form>       
<a target="_blank" href="<?= PageAction('Competition.Competitors.Print')?>/<?= $Competition['Competition_ID'] ?>"><?= ml('Competition.Settings.Competitors.Print') ?></a>

<?php   
    DataBaseClass::FromTable('Event',"Competition='".$Competition['Competition_ID']."'");
    DataBaseClass::Join_current('DisciplineFormat');
    DataBaseClass::Join_current('Discipline');
    DataBaseClass::Join('DisciplineFormat','Format'); 
    DataBaseClass::OrderClear('Discipline','Name');
    DataBaseClass::Order('Event','Round');
    $events=DataBaseClass::QueryGenerate();
    
$scrambles=[];
foreach($events as $event){       
    if(file_exists("Image/Scramble/".$event['Event_ScrambleSalt'].".pdf")){ 
        DataBaseClass::Query("Select * from ScramblePdf where Secret='".$event['Event_ScrambleSalt']."' and Event=".$event['Event_ID']);
        $ScramblePdf=DataBaseClass::getRow();
        if(isset($ScramblePdf['Timestamp'])){
            $scrambles[$event['Event_ID']]=$ScramblePdf;
        } 
    }
} ?>
    
<br>
<?php if(sizeof($scrambles)==sizeof($events)){ ?>                        
    <?= svg_green(10)?><a href="<?= PageIndex()?>ScramblesZip/<?= $Competition['Competition_ID'] ?>"><?= ml('Competition.Settings.Scrambles.Print') ?></a>
<?php }else{ ?>
    <?= svg_red(10)?>Not all scrambles are ready (click on <img src='<?= PageIndex()?>Image/Icons/scramble.png' width='15px'> for generate scrambles)
<?php } ?>
<br><a target="_blank" href="<?= PageAction('Competition.Events.Results.Print')?>/<?= $Competition['Competition_ID'] ?>"><?= ml('Competition.Settings.Results.Print') ?></a>
<br><a target="_blank" href="<?= PageIndex()?>api/v0/competitions/<?= $Competition['Competition_WCA'] ?>/results"><?= ml('Competition.Settings.Results.Export') ?></a><br>
     
<?php if($Competition['Competition_DelegateWCAOn']){ ?>
    <div class="block_comment error">
    A WCA Delegate can organize a maximum of 3 simple Extra Event in one round
    </div><br>
<?php } ?>
                        
<?php foreach($events as $event){   
    $commands=count(DataBaseClass::SelectTableRows('Command',"Event='".$event['Event_ID']."' and vCompetitors=".$event['Discipline_Competitors']));
    DataBaseClass::FromTable('Command',"Event='".$event['Event_ID']."'");
    DataBaseClass::Join_current('Attempt');
    $attemps=count(DataBaseClass::QueryGenerate());
    ?>
    <div class="form">
        <form method="POST" action="<?= PageAction('CompetitionEvent.Edit') ?>">
        <input name="ID" type="hidden" value="<?= $event['Event_ID'] ?>" />
        
        <table>
            <tr>
                <td style='border:0px'>
                    <?= ImageEvent($event['Discipline_CodeScript'],40)?>
                </td>
                <td style='border:0px'>
                    <b><?= $event['Discipline_Name']; ?></b>
                    <br>
        <?php if(file_exists('Scramble/'.$event['Discipline_CodeScript'].'.php')){ ?>
            <?php if(file_exists('Functions/Generate_'.$event['Discipline_CodeScript'].'.php')){ ?>
                <a target="_blank" href="<?= PageAction('CompetitionEvent.Scramble.Generate') ?>/<?= $event['Event_ID'] ?>"><img  class="img_a" src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'></a>
            <?php }else{ ?>
                <?php if(!$event['Discipline_GlueScrambles'] and file_exists('Includes/Action.CompetitionEvent/CompetitionEvent.Scramble.'.$event['Discipline_Code'].'.php') ){ ?>
                   <a target="_blank" href="<?= PageAction('CompetitionEvent.Scramble.'.$event['Discipline_Code'])?>/<?= $event['Event_ID'] ?>"><img  class="img_a" src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'></a>
                <?php }else{ ?>
                       <a target="_blank" href="<?= PageAction('CompetitionEvent.Scramble.Page') ?>/<?= $event['Event_ID'] ?>"><img  class="img_a" src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'></a>
                <?php } ?>        
            <?php } ?>
        <?php }else{ ?>    
            <?php if($event['Discipline_GlueScrambles'] and $event['Discipline_TNoodles']){ ?>
                <a target="_blank" href="<?= PageAction('CompetititionEvent.GlueScrambles.TNoodles')?>/<?= $event['Event_ID'] ?>"><img  class="img_a" src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'></a>
            <?php } ?>
            <?php if($event['Discipline_GlueScrambles'] and $event['Discipline_TNoodle']){ ?>
                <a target="_blank" href="<?= PageAction('CompetititionEvent.GlueScrambles.TNoodle')?>/<?= $event['Event_ID'] ?>"><img  class="img_a" src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'></a>
            <?php } ?>
        <?php } ?>   
        <?php if(isset($scrambles[$event['Event_ID']])){ ?>
                    <a  target="_blank" href="<?= PageIndex()?>Scramble/<?= $event['Event_ID'] ?>">
                        <img class="img_a" src='<?= PageIndex()?>Image/Icons/print.png' width='30px'>
                    </a>
                <font size="2"><?= $scrambles[$event['Event_ID']]['Timestamp'] ?></font>
        <?php } ?>
        <?php if($event['Event_vRound']){ ?>
            <br>
            <?= str_replace(": ","",$event['Event_vRound']); ?><br>
        <?php } ?>
                </td>
            </tr>
        </table>
        <hr>
        <nobr>
            <?= $event['Format_Result'] ?> of <?= $event['Format_Attemption'] ?>
            <?php if($event['Discipline_Competitors']>1){ ?>&#9642; Team has <?= $event['Discipline_Competitors'] ?> competitors</nobr><?php } ?>
        </nobr>
        <br>
        <nobr>    
            <?= html_spellcount($event['Event_Groups'],'group','groups','groups'); ?> &#9642;
            <input class="small_input" ID="Groups" size=1  name="Groups" required type="number" step="1" min="1" max="6" value="<?= $event['Event_Groups'] ?>" /> 
        </nobr>
        <br>
        <nobr>
            Cutoff - 
            <?php if($event['Event_CutoffMinute'] or $event['Event_CutoffSecond']){ ?>
                 <?= sprintf("%02d:%02d",$event['Event_CutoffMinute'],$event['Event_CutoffSecond']);  ?>
           <?php }else{ ?>
                no
           <?php } ?>
            &#9642;
            <input class="small_input" ID="CutoffMinute" size=2  name="CutoffMinute" required type="number" step="1" min="0" max="60" value="<?=$event['Event_CutoffMinute'] ?>" /> :
            <input class="small_input" ID="CutoffSecond" size=2  name="CutoffSecond" required type="number" step="1" min="0" max="59" value="<?= $event['Event_CutoffSecond'] ?>" />        
        </nobr>
        <br>
        <nobr>
            Limit - <?= sprintf("%02d:%02d",$event['Event_LimitMinute'],$event['Event_LimitSecond']); ?>   
            &#9642; 
            <input class="small_input" ID="LimitMinute" size=2 name="LimitMinute" required type="number" step="1" min="0" max="60" value="<?= $event['Event_LimitMinute'] ?>" /> :
            <input class="small_input" ID="LimitSecond" size=2  name="LimitSecond" required type="number" step="1" min="0" max="59" value="<?= $event['Event_LimitSecond'] ?>" />        
        </nobr>
        <br>
        Cumulative limit <?= $event['Event_Cumulative']?'+':'' ?> <input type="checkbox" name="Cumulative" <?= $event['Event_Cumulative']?'checked':'' ?>><br>
        <nobr>
            <?= $commands ?> of <?= $event['Event_Competitors']?> <?= ($event['Discipline_Competitors']>1)?'teams':'competitors'; ?>
            &#9642; 
            <input class="small_input" ID="Competitors" size=1  name="Competitors" required type="number" step="1" min="1" max="500" value="<?= $event['Event_Competitors']; ?>" /> 
         </nobr>
        <br>
        <?php if(!$attemps){ 
            DataBaseClass::FromTable("DisciplineFormat","Discipline=".$event['Discipline_ID']);
            DataBaseClass::Join_current("Format");
            $formats=DataBaseClass::QueryGenerate();?>
            Format <select style="width: 120px" name="Format">
                <?php foreach($formats as $format){ ?>
                    <option <?= $format['DisciplineFormat_ID']==$event['DisciplineFormat_ID']?'selected':'' ?> value="<?=$format['DisciplineFormat_ID'] ?>"><?= $format['Format_Result']?> of <?= $format['Format_Attemption']?></option>
                <?php } ?>
            </select>
                <br>
        <?php }else{ ?>
                <input hidden name="Format" value="<?= $event['DisciplineFormat_ID'] ?>">
                <p><?= $event['Format_Result']?> of <?= $event['Format_Attemption']?></p>
        <?php } ?>
        <?php  DataBaseClass::FromTable("Command","Event=".$event['Event_ID']);
            DataBaseClass::Join_current("CommandCompetitor");
            DataBaseClass::Where_current("CheckStatus=0");
            if(sizeof(DataBaseClass::QueryGenerate())){ ?>
                <nobr>
                  <span class="error"><?= html_spellcount(sizeof(DataBaseClass::QueryGenerate()),'registration','registrations','registrations') ?> are not on the WCA</span>
                </nobr>  
                <br>
            <?php } ?>
            <input type="submit" value="<?= ml('*.Save',false) ?>"><?= mlb('*.Save') ?>
        </form>
        <?php if(!$commands and $event['Event_Round']==$eventRounds[$event['Discipline_ID']]-1){ ?>
        <form  method="POST" action="<?= PageAction('CompetitionEvent.Delete')?>" onsubmit="return confirm('Attention: Confirm delete <?= $event['Discipline_Name']; ?>.')">  
                <input class="delete"  type="submit" value="<?= ml('*.Delete',false) ?>"><?= mlb('*.Delete') ?>
                <input name="ID" type="hidden" value="<?= $event['Event_ID'] ?>" />
        </form>
        <?php } ?>    
    </div>
<?php } ?>
<?php if(!$Competition['Competition_DelegateWCAOn'] or sizeof($events)<3){?>
    <div class="form">
        <b><?= ml('Competition.Settings.EventsAdd.Title')?></b>
        <hr>
        <form method="POST" action="<?= PageAction('CompetitionEvents.Add') ?>">
            <input name="Competition" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
            <?= mlb('Competition.Settings.EventsAdd.Placeholder') ?>
            <select required="" style="width:400px" Name="Events[]" data-placeholder="<?= ml('Competition.Settings.EventsAdd.Placeholder',false) ?>" class="chosen-select" multiple>
            <option value=""></option>
            <?php DataBaseClass::FromTable("Discipline","Status='Active'");
            if($Competition['Competition_DelegateWCAOn']){
                DataBaseClass::Where_current("Simple=1");
            }
            DataBaseClass::Join_current("DisciplineFormat");
            DataBaseClass::Join_current("Format");
            DataBaseClass::Select("Distinct D.*");
            DataBaseClass::OrderClear("Discipline","Name");
            foreach(DataBaseClass::QueryGenerate(true,true) as $event){ 
                if(!$Competition['Competition_DelegateWCAOn'] or $eventRounds[$event['ID']]==1){ 
                    if($eventRounds[$event['ID']]<=4){?>
                        <option value='<?= json_encode([$event['ID'],$eventRounds[$event['ID']]]) ?>'>
                            <?= $event['Name'] ?><?php 
                            if($eventRounds[$event['ID']]>1){ ?>
                                &#9642 <?= $eventRounds[$event['ID']]?> round
                            <?php } ?> 
                        </option>
                    <?php } ?>    
                <?php } ?>    
            <?php } ?>
        </select>
            <br><input type="submit" value="<?= ml('*.Add',false) ?>"><?= mlb('*.Add') ?>
            <br>
        </form>
    </div>
<?php } ?>
<br>
<div class="form">
    <b><?= ml('Competition.Settings.Comment.Title') ?></b>
    <form method="POST" action="<?= PageAction('Competition.Edit.Comment'); ?>">
        <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
        <?php 
        $comments=json_decode($Competition['Competition_Comment'],true);
        if(!$comments and $Competition['Competition_Comment']!='[]'){
            $comments[getLanguages()[0]]=$Competition['Competition_Comment'];
        }
        foreach(getLanguages() as $language){ ?>
            <b><?= ImageCountry($language,20); ?> <?= CountryName($language,true) ?></b><br>
            <textarea name="Comment[<?= $language ?>]" style="height: 80px;width: 400px"><?= isset($comments[$language])?$comments[$language]:''; ?></textarea><br>
        <?php } ?>
        <input type="submit" name="submit" value="<?= ml('*.Save',false) ?>">
        <?= mlb('*.Save') ?>
    </form> 
</div>
<div class="form">
    <form method="POST" action="<?= PageAction('Competition.Reload') ?>">
    <input name="WCA" type="hidden" value="<?=  $Competition['Competition_WCA'] ?>" />
WCA &#9642; <a href="https://www.worldcubeassociation.org/competitions/<?= $Competition['Competition_WCA'] ?>"><?=  $Competition['Competition_WCA'] ?></a><br>
Delegate WCA &#9642; 
<span class='<?= $Competition['Competition_DelegateWCAOn']?'message':'' ?>' ><?=  $Competition['Competition_DelegateWCA'] ?></span><br>
Name &#9642; <?=  $Competition['Competition_Name'] ?><br>
City &#9642; <?=  $Competition['Competition_City'] ?><br>
Country &#9642; <?=  $Competition['Competition_Country'] ?> (<?=  CountryName($Competition['Competition_Country']) ?>)<br>
StartDate &#9642; <?=  $Competition['Competition_StartDate'] ?><br>
EndDate &#9642; <?=  $Competition['Competition_EndDate'] ?><br>
WebSite &#9642; <a href="<?=  $Competition['Competition_WebSite'] ?>"><?=  $Competition['Competition_WebSite'] ?></a>
<br><input type="submit" name="submit" value="<?= ml('*.Reload',false) ?>"><?= mlb('*.Reload') ?>
    </form>
    <?php DataBaseClass::FromTable('Event',"Competition='".$Competition['Competition_ID']."'");
        if (sizeof(DataBaseClass::QueryGenerate())==0 and CheckAccess('Competition.Settings.Ext')){ ?>
            <form method="POST" action="<?= PageAction("Competition.Delete") ?>"   onsubmit="return confirm('Attention: Confirm the delete.')">
                <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
                <span class="badge">Ext</span><input class="delete"  type="submit" value="<?= ml('*.Delete',false) ?>">
            </form>
    <?php  } ?>
</div>
    <div class="form">
            <form method="POST" action="<?= PageAction('Competition.Edit.Status') ?>">
                <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
                <?php if($Competition['Competition_Status']){ ?>
                    <?= svg_green(10) ?>
                <?php }else{ ?>
                    <?= svg_red(10) ?>
                <?php } ?>
                <label for="Competition.Status"><?= $Competition['Competition_Status']?ml('Competition.Status.Show.True'):ml('Competition.Status.Show.False')   ?></label><input id="Competition.Status" name="Status" <?= $Competition['Competition_Status']?'checked':'' ?> type="checkbox"><br>
                <?php if($Competition['Competition_Registration']){ ?>
                    <?= svg_green(10) ?>
                <?php }else{ ?>
                    <?= svg_red(10) ?>
                <?php } ?>
                <label for="Competition.Registration"><?= $Competition['Competition_Registration']?ml('Competition.Registration.True'):ml('Competition.Registration.False')   ?></label><input id="Competition.Registration" name="Registration" <?= $Competition['Competition_Registration']?'checked':'' ?> type="checkbox"><br>
                <?php if($Competition['Competition_Onsite']){ ?>
                    <?= svg_green(10) ?>
                <?php }else{ ?>
                    <?= svg_red(10) ?>
                <?php } ?>
                <label for="Competition.Onsite"><?= $Competition['Competition_Onsite']?ml('Competition.Onsite.True'):ml('Competition.Onsite.False')   ?></label><input id="Competition.Onsite" name="Onsite" <?= $Competition['Competition_Onsite']?'checked':'' ?> type="checkbox"><br>
                <?php if(CheckAccess('Competition.Settings.Ext',$Competition['Competition_ID'])){ ?>
                
                <hr><span class="badge">Ext</span><br>
                    <?php if(!$Competition['Competition_Unofficial']){ ?>
                        <?= svg_green(10) ?>
                    <?php }else{ ?>
                        <?= svg_red(10) ?>
                    <?php } ?>
                    <label for="Competition.Unofficial"><?= $Competition['Competition_Unofficial']?ml('Competition.Unofficial.True'):ml('Competition.Unofficial.False')   ?></label><input id="Competition.Unofficial" name="Unofficial" <?= !$Competition['Competition_Unofficial']?'checked':'' ?> type="checkbox"><br>
                    <?php if($Competition['Competition_DelegateWCAOn']){ ?>
                        <?= svg_green(10) ?>
                    <?php }else{ ?>
                        <?= svg_red(10) ?>
                    <?php } ?>
                    <label for="Competition.DelegateWCAOn"><?= $Competition['Competition_DelegateWCAOn']?ml('Competition.DelegateWCAOn.False'):ml('Competition.DelegateWCAOn.True')   ?></label><input id="Competition.DelegateWCAOn" name="DelegateWCAOn" <?= $Competition['Competition_DelegateWCAOn']?'checked':'' ?> type="checkbox"><br>
                    <label for="Competition.Cubingchina"><?= $Competition['Competition_Cubingchina']?ml('Competition.Cubingchina.True'):ml('Competition.Cubingchina.False')   ?></label><input id="Competition.Cubingchina" name="Cubingchina" <?= $Competition['Competition_Cubingchina']?'checked':'' ?> type="checkbox"><br>
                <?php } ?>
                <input type="submit" name="submit" value="<?= ml('*.Save',false) ?>"><?= mlb('*.Save') ?>
            </form> 
    </div> 
 
<?php if(CheckAccess('Competition.Settings.Ext',$Competition['Competition_ID'])){ ?>
<div class="form">
    <span class="badge">Ext</span><br>
    <b><?= ml('Competition.Edit.Delegates.Title') ?></b><br>
    <input name="Competition" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
    <?php foreach($CompetitionDelegates as $delegate){?>
            <?= Short_Name($delegate['Delegate_Name']) ?><br>
    <?php } ?>
    <form method="POST" action="<?= PageAction('Competition.Edit.Delegates')?>">
        <input name="Competition" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
        <?php 
        $CompetitionDelegates_from_table = DataBaseClass::SelectTableRows('Delegate');
        foreach($CompetitionDelegates as $i=>$delegate){?>
            <select name="Delegates[<?= $i ?>]" style="width:160px">
                <option value="">-</option>
            <?php foreach($CompetitionDelegates_from_table as $delegate_from_table){ ?>
                <option <?= $delegate['Delegate_ID']==$delegate_from_table['Delegate_ID']?"selected":"" ?> value="<?= $delegate_from_table['Delegate_ID'] ?>">
                    <?= $delegate_from_table['Delegate_Status']=='Archive'?'- ':'' ?>
                    <?= Short_Name($delegate_from_table['Delegate_Name']) ?>
                </option>
            <?php } ?>
            </select>
            <br>
         <?php } ?>
        <select name="Delegates['+']" style="width:160px">
            <option value="">+</option>
            <?php foreach($CompetitionDelegates_from_table as $delegate_from_table){ ?>
                <option value="<?= $delegate_from_table['Delegate_ID'] ?>">
                    <?= $delegate_from_table['Delegate_Status']=='Archive'?'- ':'' ?>
                    <?= Short_Name($delegate_from_table['Delegate_Name']) ?>
                </option>            
            <?php } ?>
        </select>    
        <br>
        <input type="submit" name="submit" value="<?= ml('*.Save',false) ?>"><?= mlb('*.Save') ?>
    </form>  
</div> 
<?php } ?>

<div class="form">
    <?= ImageCompetition($Competition['Competition_WCA']) ?> 
    <form name="LoadCompetitionImage" enctype="multipart/form-data" method="POST" action="<?= PageIndex()."Actions/CompetitionEditImage" ?>">           
            <div class="fileinputs">
                <input type="file" name="uploadfile" class="file"  onchange="document.forms['LoadCompetitionImage'].submit();"/>
                <div class="fakefile" id="fkf">
                    <button class="form_change">Load.jpg</button>
                </div>
            </div>  
            <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
        </form> 
</div>
<div class="form">
    <span class="error"><img src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'> Publish scrambles to all competitors</span>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Scramble.Public.Add')?>" enctype="multipart/form-data" >
        .pdf <input required type="file" accept="application/pdf"  name="scramble">
        <select style="width:240px" name="Event">
            <option selected></option>
            <?php foreach($events as $event){
                if(!$event['Event_ScramblePublic']){?>
                <option value="<?= $event['Event_ID']?>"><?= $event['Discipline_Name']?> <?= $event['Event_vRound']?></option>
            <?php }
            }?>
        </select>
        <input type="submit" value="Publication">
    </form>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Scramble.Public.Delete')?>" >
        <select style="width:240px" name="Event">
            <option selected></option>
            <?php foreach($events as $event){
                if($event['Event_ScramblePublic']){?>
                <option value="<?= $event['Event_ID']?>"><?= $event['Discipline_Name']?> <?= $event['Event_vRound']?></option>
            <?php }
            }?>
        </select>
        <input type="submit" class="delete" value="Cancel publication">
    </form>
</div>
<div class="block_comment">
    <b><img src='<?= PageIndex()?>Image/Icons/scramble.png' width='30px'> History generations/publications srambles</b>
    <table>
    <?php DataBaseClass::Query(""
            . "Select SP.Action, SP.Timestamp, D.Name, SP.Secret,E.ScrambleSalt,E.ScramblePublic, Discipline.Code, Discipline.CodeScript, Discipline.Name, E.vRound,D.Name Delegate from ScramblePdf SP "
            . " join Event E on SP.Event=E.ID and E.Competition=".$Competition['Competition_ID']
            . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat "
            . " join Discipline on Discipline.ID=DF.Discipline "
            . " join Delegate D on D.ID=SP.Delegate  order by SP.Timestamp desc");
    foreach(DataBaseClass::getRows() as $row){ ?>
        <tr>
            <td>
                <?php if(file_exists("Image/Scramble/".$row['Secret'].".pdf")){ ?>
                    <td>
                        <a target="_blank" href="<?= PageIndex()?>Scramble/<?= $row['Secret'] ?>"><?= $row['Secret'] ?></a>
                    </td>
                    <td>
                    <?php if($row['ScrambleSalt']==$row['Secret']){ ?>
                        <?= svg_blue(10); ?>Last
                    <?php } ?>
                    <?php if($row['ScramblePublic']==$row['Secret']){ ?>
                        <?= svg_green(10); ?>Public 
                    <?php } ?>
                     </td>   
                <?php }else{ ?>
                    <td>
                        <?= $row['Secret'] ?>
                    </td>
                    <td>
                     <?php if($row['ScrambleSalt']==$row['Secret']){ ?>
                        <?= svg_red(10); ?>Last
                    <?php } ?>
                    <?php if($row['ScramblePublic']==$row['Secret']){ ?>
                        <?= svg_red(10); ?>Public 
                    <?php } ?>
                     </td>      
                <?php } ?>
            </td>
            <td><?= ImageEvent($row['CodeScript'],25)?> <?= $row['Name'] ?></td>
            <td><?= $row['vRound'] ?></td>
            <td class="border-right-dotted border-left-dotted"><?= $row['Timestamp'] ?></td>
            <td class="border-right-solid"><?= $row['Delegate'] ?></td>
            <td><?= $row['Action'] ?></td>                
                

    </tr>
    <?php } ?>
    </table>
    
</div>

<br>
<div class="block_comment">
        <b><img src='<?= PageIndex()?>Image/Icons/persons.png' width='30px'> History of registrations of competitors</b><br>
            <?php DataBaseClass::Query("Select "
                    . " D.Code,D.CodeScript, LR.Timestamp,LR.Action, LR.Doing,LR.Details "
                    . " from LogsRegistration LR "
                    . " join Event E on E.ID=LR.Event "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
                    . " join Discipline D on D.ID=DF.Discipline"
                    . " where E.Competition=".$Competition['Competition_ID']." "
                    . " order by LR.Timestamp desc");?>
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
                <td class="border-left-dotted border-right-dotted">
                            <?= ImageEvent($row['CodeScript'],20)?>
                            <?= $row['Details']?>
                </td>
                <td><?= $row['Doing']?></td>
            </tr>
            <?php } ?>
        </table>
        <span class="badge">C</span> - Competitor <span class="badge">D</span> - Delegate <span class="badge">S</span> - ScoreTaker
    </div>
<?= mlb('*.Delete') ?>
<?= mlb('Competition.Status.Show.True') ?>
<?= mlb('Competition.Status.Show.False') ?>
<?= mlb('Competition.Registration.Open') ?>
<?= mlb('Competition.Registration.Close') ?>
<?= mlb('Competition.Onsite.Open') ?>
<?= mlb('Competition.Onsite.Close') ?>
<?= mlb('Competition.Unofficial.True') ?>
<?= mlb('Competition.Unofficial.False') ?>
<?= mlb('Competition.DelegateWCAOn.True') ?>
<?= mlb('Competition.DelegateWCAOn.False') ?>
<?= mlb('Competition.Cubingchina.True') ?>
<?= mlb('Competition.Cubingchina.False') ?>