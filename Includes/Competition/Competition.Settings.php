<?php
$Competition=ObjectClass::getObject('PageCompetition');
$CompetitionDelegates=ObjectClass::getObject('PageCompetitionDelegates');

?>

<h1><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>"><?= $Competition['Competition_Name'] ?></a> / Settings</i></h1>
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
<table width='100%'><tr><td>
    <table class="table_info">
        <tr>
            <td>Displaying competitions</td>
            <td>
            <?php if($Competition['Competition_Status']==1){ ?>
                    <i class="fas fa-eye"></i> Competitions are visible
                <?php }elseif($Competition['Competition_Status']==0){ ?>
                    <i class="fas fa-eye-slash"></i> Competitions are hidden
                <?php }elseif($Competition['Competition_Status']==-1){ ?>
                    <i class="fas fa-user-nurse"></i> Competition canceled because of COVID-19
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Registration on the site</td>
            <td>
            <?php if($Competition['Competition_Registration']){ ?>
                    <i class="fas fa-check-circle"></i> Registration is opened
                <?php }else{ ?>
                    <i class="fas fa-times-circle"></i> Registration  is closed
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Registration at the venue</td>
            <td>
            <?php if($Competition['Competition_Onsite']){ ?>
                    <i class="fas fa-check-circle"></i> Registration is allowed
                <?php }else{ ?>
                    <i class="fas fa-times-circle"></i> Registration is prohibited
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <a href="#" ID='mainSettingsShow' onclick="$('#mainSettingsShow').hide();$('#mainSettingsHide').show();$('#mainSettings').show('fast'); return false;" class="local_link">Show settings section</a>
                <a href="#" ID='mainSettingsHide' onclick="$('#mainSettingsShow').show();$('#mainSettingsHide').hide();$('#mainSettings').hide('fast'); return false;" class="local_link" style="display:none">Hide settings section</a>
            </td>
        </tr>
    </table>
</td><td>
    <table class="table_info">
        <tr>
            <td>Inclusion in the main rating</td>
            <td>
                <?php if(!$Competition['Competition_Unofficial']){ ?>
                    <i class="fas fa-signal fa-rotate-90"></i> Results are included in the main rating
                <?php }else{ ?>
                    <i class="fas fa-times-circle"></i> Results are not included in the main rating
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Holding by WCA Delegates</td>
            <td>
                <?php if($Competition['Competition_DelegateWCAOn']){ ?>
                    <i class="fas fa-user-graduate"></i> Access is allowed
                <?php }else{ ?>
                    <i class="fas fa-times-circle"></i> Access denied
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Site with registrations</td>
            <td>
                <?php if($Competition['Competition_Cubingchina']){ ?>
                    <i class="fas fa-globe-asia"></i> CubingChina.com
                <?php }else{ ?>
                    <i class="fas fa-globe"></i> WorldCubeAssociation.org
                <?php } ?>
            </td>
        </tr>
    </table>
</td></tr></table>

<span style="display:none" id="mainSettings">
<form method="POST" action="<?= PageAction('Competition.Edit.Status') ?>">
<input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />    
<table width='100%'><tr><td>
    <table class="table_info">
        <tr>
            <td>Displaying competitions</td>
            <td>
                <select name="Status">   
                    <option value="0" <?= $Competition['Competition_Status']==0?'selected':'' ?>>Competitions are hidden</option>
                    <option value="1" <?= $Competition['Competition_Status']==1?'selected':'' ?>>Competitions are visible</option>
                    <option value="-1" <?= $Competition['Competition_Status']==-1?'selected':'' ?>>Competition canceled because of COVID-19</option>
                </select>    
            </td>    
        </tr>
        <tr>
            <td>Registration on the site</td>
            <td>
                <select name="Registration">   
                    <option value="0" <?= !$Competition['Competition_Registration']?'selected':'' ?>>Registration  is closed</option>
                    <option value="1" <?= $Competition['Competition_Registration']?'selected':'' ?>>Registration is opened</option>
                </select>       
            </td>
        </tr>
        <tr>
            <td>Registration at the venue</td>
            <td>
                <select name="Onsite">   
                    <option value="0" <?= !$Competition['Competition_Onsite']?'selected':'' ?>>Registration is prohibited</option>
                    <option value="1" <?= $Competition['Competition_Onsite']?'selected':'' ?>>Registration is allowed</option>
                </select>       
            </td>
        </tr>
        <tr>
            <td></td>
            <td><button><i class="fas fa-save"> Save</i></button></td>
        </tr>
    </table>
</td><td>
    <?php if(CheckAccess('Competition.Settings.Ext',$Competition['Competition_ID'])){ ?>
    <table class="table_info">
        <tr>
            <td>Inclusion in the main rating <i class="fas fa-crown"></td>
            <td>
                <select name="Unofficial">   
                    <option value="0" <?= !$Competition['Competition_Unofficial']?'selected':'' ?>>Results are included in the main rating</option>
                    <option value="1" <?= $Competition['Competition_Unofficial']?'selected':'' ?>>Results are not included in the main rating</option>
                </select>       
            </td>
        </tr>
        <tr>
            <td>Holding by WCA Delegates <i class="fas fa-crown"></td>
            <td>
                <select name="DelegateWCAOn">   
                    <option value="0" <?= !$Competition['Competition_DelegateWCAOn']?'selected':'' ?>>Access denied</option>
                    <option value="1" <?= $Competition['Competition_DelegateWCAOn']?'selected':'' ?>>Access is allowed</option>
                </select>       
            </td>
        </tr>
        <tr>
            <td>Site with registrations <i class="fas fa-crown"></td>
            <td>
                <select name="Cubingchina">   
                    <option value="0" <?= !$Competition['Competition_Cubingchina']?'selected':'' ?>>WorldCubeAssociation.org</option>
                    <option value="1" <?= $Competition['Competition_Cubingchina']?'selected':'' ?>>CubingChina.com</option>                    
                </select>   
            </td>
        </tr>
    </table>
    <?php } ?>
</td></tr></table>
</form>
</span>
<h3>Competitions details</h3>
<table width='100%'><tr><td>
    <table class="table_info">
    <?php foreach($CompetitionDelegates as $d=>$delegate){?>
        <tr>
            <td><?php if(!$d){ ?>SEE Delegates<?php } ?></td>
            <td><a href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>"><?= short_Name($delegate['Delegate_Name']) ?></a></td>
        </tr>
    <?php } ?>
    <?php if(CheckAccess('Competition.Settings.Ext',$Competition['Competition_ID'])){ ?>
        <tr>
            <td>Appointment of delegates  <i class="fas fa-crown"></td>
            <td>
                <a href="#" ID='EditDelegatesShow' onclick="$('#EditDelegatesShow').hide();$('#EditDelegatesHide').show();$('#EditDelegates').show('fast'); return false;" class="local_link">Show appointment section</a>
                <a href="#" ID='EditDelegatesHide' onclick="$('#EditDelegatesShow').show();$('#EditDelegatesHide').hide();$('#EditDelegates').hide('fast'); return false;" class="local_link" style="display:none">Hide appointment section</a>
            </td>
        </tr>
    <?php } ?>
    <?php if(!$Competition['Competition_Cubingchina']){ ?>
        <tr><td>Сheck registrations on WCA</td>
            <td>
                <form method='POST' action='<?= PageAction('Competition.Competitors.Check')?>'>
                    <input hidden name='ID' value='<?= $Competition['Competition_ID'] ?>'>
                    <button><i class="fas fa-user-check"></i> Check</button>
                    <?= $Competition['Competition_CheckDateTime'] ?>
                </form>
            </td>
    <?php } ?>

        <tr><td>Autoload registrations from <?= $Competition['Competition_Cubingchina']?'Cubingchina':'WCA'?></td>
            <td>
                <form method='POST' action='<?= PageAction('Competition.Competitors.Load')?>'>
                   <input hidden name='ID' value='<?= $Competition['Competition_ID'] ?>'>
                   <button><i class="fas fa-sync-alt"></i> Reload</button>
                    <?= $Competition['Competition_LoadDateTime'] ?>
           </form>
            </td>
        </tr>
        <tr>
            <td>Competitors</td>
            <td><i class="far fa-list-alt"></i> <a target="_blank" href="<?= PageAction('Competition.Competitors.Print')?>/<?= $Competition['Competition_ID'] ?>">Print the list of competitors</a></td>
        </tr>
        <tr>
            <td>Scrambles</td>
            <td>
                <?php if(sizeof($scrambles)==sizeof($events)){ ?>
                    <i class="fas fa-download"></i> <a href="<?= PageIndex()?>ScramblesZip/<?= $Competition['Competition_ID'] ?>">Download zip with all scrambles</a>
                <?php }else{ ?>
                    <i class="fas fa-exclamation-triangle"></i> Not all scrambles are ready
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Pedestals</td>
            <td><i class="fas fa-medal"></i> <a target="_blank" href="<?= PageAction('Competition.Events.Results.Print')?>/<?= $Competition['Competition_ID'] ?>">Print pedestals</a></td>
        </tr>
        <tr>
            <td>Results</td>
            <td><i class="fas fa-file-export"></i> <a target="_blank" href="<?= PageIndex()?>api/v0/competitions/<?= $Competition['Competition_WCA'] ?>/results">Export results as json</a></td>
        </tr>
    </table>
</td><td>
    <table class="table_info">
        <tr>
            <td>Reload data from WCA</td>
            <td>
                <form method="POST" action="<?= PageAction('Competition.Reload') ?>">
                    <input name="WCA" type="hidden" value="<?=  $Competition['Competition_WCA'] ?>" />
                    <button><i class="fas fa-sync-alt"></i> Reload</button>
                </form>
            </td>
        </tr>
        <tr>
            <td>Competition ID</td>
            <td><a target='_blank' href="https://www.worldcubeassociation.org/competitions/<?= $Competition['Competition_WCA'] ?>"><?=  $Competition['Competition_WCA'] ?> <i class="fas fa-external-link-alt"></i></a></td>
        </tr>
        <tr>
            <td>Name</td>
            <td><?=  $Competition['Competition_Name'] ?></td>
        </tr>
        <tr>
            <td>Country</td>
            <td><?=  CountryName($Competition['Competition_Country']) ?></td>
        </tr>
        <tr>
            <td>City</td>
            <td><?=  $Competition['Competition_City'] ?></td>
        </tr>
        <tr>
            <td>Date</td>
            <td><?= date_range($Competition['Competition_StartDate'],$Competition['Competition_EndDate']) ?></td>
        </tr>
        <tr>
            <td>Web site</td>
            <td><a target="_blank" href='<?= $Competition['Competition_WebSite']?>'>Go to the site <i class="fas fa-external-link-alt"></i></a></td>
        </tr>
        <?php foreach(explode(",",$Competition['Competition_DelegateWCA']) as $d=>$delegate){
            $delegate=trim($delegate);
            DataBaseClassWCA::Query("Select * from Persons where id='$delegate'");
            $person=DataBaseClassWCA::getRow();
            $personName=$delegate;
            if(isset($person['name'])){
               $personName= Short_Name($person['name']);
            } ?>
        <tr>
            <td><?php if(!$d){ ?>WCA Delegates <i class="fas fa-external-link-alt"></i><?php } ?></td>
            <td><a target="_blank" href="https://www.worldcubeassociation.org/persons/<?= $delegate ?>"><?= $personName ?></a></td>
        </tr>
        <?php } ?>
<?php if(CheckAccess('Competition.Settings.Ext')){ ?>
     <tr>
        <td>Delete competititon</i></td>
        <td>
        <?php DataBaseClass::FromTable('Event',"Competition='".$Competition['Competition_ID']."'");
        if (sizeof(DataBaseClass::QueryGenerate())==0){ ?>
            <form method="POST" action="<?= PageAction("Competition.Delete") ?>"   onsubmit="return confirm('Confirm the deletion.')">
                <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
                <button class="delete"><i class="far fa-trash-alt"></i> Delete</button>
            </form>
    <?php }else{ ?>
             <i class="fas fa-info-circle"></i> can't be deleted because events exist
    <?php } ?>
        </td>
    </tr>
<?php  } ?>
    </table>
</td></tr></table>


<?php if(CheckAccess('Competition.Settings.Ext',$Competition['Competition_ID'])){ ?>
<span style="display:none;" ID="EditDelegates">
<h3>SEE Delegates <i class="fas fa-crown"></i></h3>
<form method="POST" action="<?= PageAction('Competition.Edit.Delegates')?>">
<input name="Competition" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
<table class="table_new">
    <thead>
    <tr>
        <td>Delegate</td>
    </tr>
    </thead>
    <tbody>
        <?php
        $CompetitionDelegates_from_table = DataBaseClass::SelectTableRows('Delegate');
        foreach($CompetitionDelegates as $i=>$delegate){?>
        <tr><td>
            <select name="Delegates[<?= $i ?>]" style="width:160px">
                <option value="">-</option>
            <?php foreach($CompetitionDelegates_from_table as $delegate_from_table){ ?>
                <option <?= $delegate['Delegate_ID']==$delegate_from_table['Delegate_ID']?"selected":"" ?> value="<?= $delegate_from_table['Delegate_ID'] ?>">
                    <?= $delegate_from_table['Delegate_Status']=='Archive'?'- ':'' ?>
                    <?= Short_Name($delegate_from_table['Delegate_Name']) ?>
                </option>
            <?php } ?>
            </select>
        </td></tr>
         <?php } ?>
        <tr><td>
        <select name="Delegates['+']" style="width:160px">
            <option value="">+</option>
            <?php foreach($CompetitionDelegates_from_table as $delegate_from_table){ ?>
                <option value="<?= $delegate_from_table['Delegate_ID'] ?>">
                    <?= $delegate_from_table['Delegate_Status']=='Archive'?'- ':'' ?>
                    <?= Short_Name($delegate_from_table['Delegate_Name']) ?>
                </option>
            <?php } ?>
        </select>
        </td></tr>
    </tbody>
</table>
<button><i class="fas fa-save"></i> Save</button>
</form>
</span>
<?php } ?>

<a name="CompetitionEvent.Action"></a>
<h3>Extra events</h3>
<table width="100%"><tr><td>
 <table class="table_info">
        <?php if($Competition['Competition_DelegateWCAOn']){ ?>
        <tr>
            <td>Restriction</td>
            <td>
                <i class="fas fa-hand-paper"></i> A WCA Delegate can organize a maximum of 3 simple Extra Event in one round
            </td>
        </tr>
    <?php } ?>
</table>
</td><td>
<?php if(sizeof($events)){ ?>
<table class="table_info">
    <tr>
        <td>Legend</td>
        <td/>
    </tr>
    <tr>
        <td><i class="fas fa-random"></i></td>
        <td>Generate scrambles</td>
    </td>
    <tr>
        <td><i class="fas fa-print"></i></td>
        <td>Print scrambles</td>
    </td>
    <tr>
        <td><i class="fas fa-download"></i></td>
        <td>Download scrambles</td>
    </td>
    <tr>
        <td><i class="fas fa-hand-pointer"></i></td>
        <td>Click on an event to show its setting</td>
    </tr>
</table>
<?php } ?>
</td></tr></table>
<?php 

if(strtotime($Competition['Competition_StartDate'])>=strtotime('now') or $Competition['Competition_Technical']){
    
    if(!$Competition['Competition_DelegateWCAOn'] or sizeof($events)<3){?>
<form method="POST" action="<?= PageAction('CompetitionEvents.Add') ?>">
    <input name="Competition" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
    <select required="" style="width:400px" Name="Events[]" data-placeholder="Select extra events" class="chosen-select" multiple>
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
    <button><i class="far fa-plus-square"></i> Add extra events</button>
</form>
<?php } ?>
<?php }else{ ?>
<i class="fas fa-hand-paper"></i>
You can't change an event in past competitions
<?php } ?>
<?php $error=GetMessage('CompetitionEvents.Add.Error');
if($error){ ?>
<p><?= svg_red() ?> <?=$error ?></p>
<?php } ?>
<?php if(sizeof($events)){ ?>
<table class="table_new">
    <thead>
        <tr>
            <td/>
            <td>Event</td>
            <td>Round</td>
            <td>Format</td>
            <td class="table_new_right">Groups</td>
            <td class="table_new_right">Cutoff</td>
            <td class="table_new_right">Limit</td>
            <td class="table_new_right">Teams</td>
            <td class="table_new_right">no on WCA</td>
            <td class="table_new_left">Scrambles</td>
            <td class="table_new_right">Results</td>

        </tr>
    </thead>
    <tbody>
        <?php foreach($events as $event){
            $commands=count(DataBaseClass::SelectTableRows('Command',"Event='".$event['Event_ID']."' and vCompetitors=".$event['Discipline_Competitors']));
            DataBaseClass::FromTable('Command',"Event='".$event['Event_ID']."'");
            DataBaseClass::Join_current('Attempt');
            DataBaseClass::Where_current('Attempt=1');
            $attemps=count(DataBaseClass::QueryGenerate());

            DataBaseClass::FromTable("Command","Event=".$event['Event_ID']);
            DataBaseClass::Join_current("CommandCompetitor");
            DataBaseClass::Where_current("CheckStatus=0");
            $losts=sizeof(DataBaseClass::QueryGenerate());?>
        <tr>
            <td><?= ImageEvent($event['Discipline_CodeScript'],1)?></td>
            <td id="Event_edit_td_<?= $event['Discipline_CodeScript']?>_<?= $event['Event_Round']?>"
                class="Event_edit_td table_new_bold">
                <a href="#" class="local_link" onclick="
                    var key='<?= $event['Discipline_CodeScript']?>_<?= $event['Event_Round']?>';
                    
                    var selected=$('#Event_edit_td_' + key).hasClass('list_select');
                    $('.Event_edit').hide('fast');
                    $('.Event_edit_td a').removeClass('list_select');
                    if(!selected){
                        $('#Event_edit_' + key).show('fast');
                        $(this).addClass('list_select');
                    }
                    return false;"><?= $event['Discipline_Name']; ?></a>
            </td>
            <td><?= str_replace(": ","",$event['Event_vRound']); ?></td>
            <td><?= $event['Format_Result'] ?> of <?= $event['Format_Attemption'] ?></td>
            <td class="table_new_right"><?= $event['Event_Groups'] ?></td>
            <td class="table_new_right">
                <?php if($event['Event_CutoffMinute'] or $event['Event_CutoffSecond']){ ?>
                    <?= sprintf("%02d:%02d",$event['Event_CutoffMinute'],$event['Event_CutoffSecond']);  ?>
                <?php }else{ ?>
                    <i class="fas fa-ellipsis-h"></i>
                <?php } ?>
            </td>
            <td class="table_new_right">
                <?= $event['Event_Cumulative']?'<i class="fas fa-plus"></i>':''?>
                <?= sprintf("%02d:%02d",$event['Event_LimitMinute'],$event['Event_LimitSecond']); ?>
            </td>
            <td class="table_new_right"><?= $commands ?> / <?= $event['Event_Competitors']==500?'<i class="fas fa-ellipsis-h"></i>':$event['Event_Competitors']?></td>
            <td class="table_new_right"><?= $losts?$losts:''; ?></td>
            <td class="table_new_left">
                <?php if($LinkScrambes=GetLinkScrambes($event)){ ?>
                    <a target="_blank" href="<?= $LinkScrambes ?>/<?= $event['Event_ID'] ?>"><i title="Generate scrambles" class="fas fa-random"></i></a>
                <?php } ?>
                <?php if(isset($scrambles[$event['Event_ID']])){ ?>
                    <?= $scrambles[$event['Event_ID']]['Timestamp'] ?>
                    <a  target="_blank" href="<?= PageIndex()?>Scramble/<?= $event['Event_ID'] ?>">
                            <i title="Print scrambles" class="fas fa-print"></i>
                    </a>
                    <a  target="_blank" href="<?= PageIndex()?>Scramble/<?= $event['Event_ID'] ?>/Download">
                            <i title="Download scrambles" class="fas fa-download"></i>
                    </a>
                <?php } ?>

            </td>
            <td class="table_new_right"><?= $attemps?$attemps:''; ?></td>
            <td>
                <?php if($event['Event_ScramblePublic']){ ?>
                    <i class="fas fa-upload"></i>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<?php foreach($events as $event){ ?>
<span class="Event_edit" style="display: none" id="Event_edit_<?= $event['Discipline_CodeScript']?>_<?= $event['Event_Round']?>">
 <h3>Settings <?= $event['Discipline_Name']?> <?= $event['Event_vRound']?></h3>

    <?php $commands=count(DataBaseClass::SelectTableRows('Command',"Event='".$event['Event_ID']."' and vCompetitors=".$event['Discipline_Competitors']));
    DataBaseClass::FromTable('Command',"Event='".$event['Event_ID']."'");
    DataBaseClass::Join_current('Attempt');
    $attemps=count(DataBaseClass::QueryGenerate());?>
 <table width="100%"><tr><td>
 <table class='table_info'>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Edit') ?>">
    <input name="key" type="hidden" value="<?= $event['Discipline_CodeScript']?>_<?= $event['Event_Round']?>" />
    <input name="ID" type="hidden" value="<?= $event['Event_ID'] ?>" />
    <tr>
        <td><i class="fas fa-cog"></i></td>
        <td>
            <a href="<?= PageIndex() ?>Competition/<?= $Competition['Competition_WCA'] ?>/<?= $event['Discipline_Code'] ?>/<?= $event['Event_Round'] ?>/Settings">Competition event settings</a>
        </td>
    </tr>
     <tr>
         <td>Format</td>
         <td>
             <?php if(!$attemps){
            DataBaseClass::FromTable("DisciplineFormat","Discipline=".$event['Discipline_ID']);
            DataBaseClass::Join_current("Format");
            $formats=DataBaseClass::QueryGenerate();?>
            <select style="width: 120px" name="Format">
            <?php foreach($formats as $format){ ?>
                <option <?= $format['DisciplineFormat_ID']==$event['DisciplineFormat_ID']?'selected':'' ?> value="<?=$format['DisciplineFormat_ID'] ?>"><?= $format['Format_Result']?> of <?= $format['Format_Attemption']?></option>
            <?php } ?>
            </select>
            <?php }else{ ?>
                    <input hidden name="Format" value="<?= $event['DisciplineFormat_ID'] ?>">
                    <input disabled value="<?= $event['Format_Result']?> of <?= $event['Format_Attemption']?>"> <i class="fas fa-info-circle"></i> can't be changed because results exist
            <?php } ?>
         </td>
     </tr>
     <tr>
         <td>Groups</td>
         <td>
             <?php for($i=1;$i<=6;$i++){ ?>
                <?= $i ?><input type="radio" name="Groups" value="<?= $i?>" <?= $i==$event['Event_Groups']?'checked':'' ?> />&nbsp;
             <?php } ?>
         </td>
    </tr>
    <tr>
         <td>Cutoff</td>
         <td>
             <input  name="CutoffMinute" required type="number" step="1" min="0" max="60" value="<?=$event['Event_CutoffMinute'] ?>" /> :
             <input  name="CutoffSecond" required type="number" step="1" min="0" max="59" value="<?= $event['Event_CutoffSecond'] ?>" />
         </td>
     </tr>
     <tr>
         <td>Limit</td>
         <td>
            <input name="LimitMinute" required type="number" step="1" min="0" max="60" value="<?= $event['Event_LimitMinute'] ?>" /> :
            <input  name="LimitSecond" required type="number" step="1" min="0" max="59" value="<?= $event['Event_LimitSecond'] ?>" />
         </td>
     </tr>
     <tr>
         <td>Cumulative limit</td>
         <td>
            <input type="checkbox" name="Cumulative" <?= $event['Event_Cumulative']?'checked':'' ?>>
         </td>
     </tr>
     <tr>
         <td> <?= ($event['Discipline_Competitors']>1)?'Teams':'Competitors'; ?></td>
         <td>
            <input name="Competitors" type="number" step="1" min="1" max="500" value="<?= $event['Event_Competitors']; ?>" />
         </td>
     </tr>
     <tr>
         <td>Save event settings</td>
         <td><button><i class="fas fa-save"></i> Save</button></td>
     </tr>
     </form>
     <tr>
        <td>Delete event</td>
        <td>
    <?php if($commands){ ?>
        <i class="fas fa-info-circle"></i> can't be deleted because results exist
    <?php }elseif($event['Event_Round']!=$eventRounds[$event['Discipline_ID']]-1){ ?>
        <i class="fas fa-info-circle"></i> can't be deleted because next round exist
    <?php  }else{ ?>
        <form  method="POST" action="<?= PageAction('CompetitionEvent.Delete')?>" onsubmit="return confirm('Confirm the deletion.')">
            <input name="ID" type="hidden" value="<?= $event['Event_ID'] ?>" />
            <button class="delete"><i class="fas fa-trash-alt"></i> Delete</button>
        </form>
    <?php } ?>
         </td>
    </tr>
</table>
</td><td>
<?= EventBlockLinks($event); ?>

</td></tr></table>
</span>
<?php } ?>

<a name="Competition.Edit.Comment"></a>
<h3>Information for competition</h3>
<table width="100%"><tr><td>
<table class="table_info">
    <form method="POST" action="<?= PageAction('Competition.Edit.Comment'); ?>">
    <input name="ID" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
    <?php
    $comments=json_decode($Competition['Competition_Comment'],true);
    if(!$comments and $Competition['Competition_Comment']!='[]'){
        $comments[getLanguages()[0]]=$Competition['Competition_Comment'];
    }
    foreach(getLanguages() as $language){ ?>
        <tr>
            <td><?= ImageCountry($language,20); ?> <?= CountryName($language,true) ?></td>
            <td><textarea name="Comment[<?= $language ?>]" style="height: 80px;width: 400px"><?= isset($comments[$language])?$comments[$language]:''; ?></textarea></td>
        </tr>
    <?php } ?>
    <tr>
        <td></td>
        <td><button><i class="fas fa-save"></i> Save</button></td>
    </form>
</table>
</td><td>
<table class="table_info">
    <?php foreach(getLanguages() as $language){ ?>
    <tr>
        <td><?= ImageCountry($language,20); ?> <?= CountryName($language,true) ?></td>
        <td>
        <?php if(isset($comments[$language])){ ?>
            <?php Parsedown($comments[$language]) ?>
        <?php }else{ ?>
            <i class="fas fa-ban"></i> No information available
        <?php } ?>
        <td>
    </tr>
    <?php } ?>
</table>
</td></tr></table>

<h3>Publish scrambles into shared access</h3>
<table class='table_info'>
    <tr>
        <td><i class="fas fa-info-circle"></i></td>
        <td>Loading PDF with scrambles whos are used in competition</td>
    </tr>
    <tr>
        <td><i class="fas fa-exclamation-triangle"></i></td>
        <td>Do this only after all attempts by all competitors have been completed</td>
    </tr>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Scramble.Public.Add')?>" enctype="multipart/form-data" >
    <tr>
        <td>Select an event and round</td>
        <td>
            <select name="Event">
                <option selected></option>
            <?php foreach($events as $event){
                if(!$event['Event_ScramblePublic']){?>
                <option value="<?= $event['Event_ID']?>"><?= $event['Discipline_Name']?> <?= $event['Event_vRound']?></option>
            <?php }
            } ?>
            </select>    
        </td>
    </tr>
    <tr>
        <td>Select a PDF file</td>
        <td><input required type="file" accept="application/pdf"  name="scramble"></td>
    </tr>
    <tr>
        <td>Publish scrambles</td>
        <td><button><i class="fas fa-upload"></i> Publish</button></td>
    </tr>
    <tr>
        <td><hr></td>
        <td><hr></td>
    </form>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Scramble.Public.Delete')?>" enctype="multipart/form-data" >
    <tr>
        <td>Select an event and round</td>
        <td>
            <select name="Event">
                <option selected></option>
            <?php foreach($events as $event){
                if($event['Event_ScramblePublic']){?>
                <option value="<?= $event['Event_ID']?>"><?= $event['Discipline_Name']?> <?= $event['Event_vRound']?></option>
            <?php }
            } ?>
            </select>    
        </td>
    </tr>
    <tr>
        <td>Cancel the publication</td>
        <td><button class='delete'><i class="fas fa-trash-alt"></i> Cancel</button></td>
    </tr>
    </form>
</table>    

<h3> History generation and publication srambles</h3>

    <table class='table_new' width='80%'>
        <thead>
            <td>Scrambles</td>
            <td>Status</td>
            <td>Event</td>
            <td>Round</td>
            <td>Date</td>
            <td>Delegate</td>
            <td>Action</td>
        </thead>
        <tbody>
    <?php DataBaseClass::Query(""
            . "Select SP.Action, SP.Timestamp, D.WCA_ID,D.Name, SP.Secret,E.ScrambleSalt,E.ScramblePublic, Discipline.Code, Discipline.CodeScript, Discipline.Name, E.Round,D.Name Delegate from ScramblePdf SP "
            . " join Event E on SP.Event=E.ID and E.Competition=".$Competition['Competition_ID']
            . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat "
            . " join Discipline on Discipline.ID=DF.Discipline "
            . " join Delegate D on D.ID=SP.Delegate  order by SP.Timestamp desc");
    foreach(DataBaseClass::getRows() as $row){ ?>
        <tr>
            <td>
                <?php if(file_exists("Image/Scramble/".$row['Secret'].".pdf")){ ?>
                    <a target="_blank" href="<?= PageIndex()?>Scramble/<?= $row['Secret'] ?>"><?= $row['Secret'] ?></a>
                <?php }else{ ?>
                    <?= $row['Secret'] ?>
                <?php } ?>    
            </td>
            <td>
            <?php if($row['ScrambleSalt']==$row['Secret']){ ?>
                <i class="fas fa-file-image"></i> Actual
            <?php } ?>
            <?php if($row['ScramblePublic']==$row['Secret']){ ?>
                <i class="fas fa-upload"></i> Published
            <?php } ?>
             </td>
            <td><?= ImageEvent($row['CodeScript'],25)?> <?= $row['Name'] ?></td>
            <td class="table_new_center" ><?= $row['Round'] ?></td>
            <td><?= $row['Timestamp'] ?></td>
            <td><a href="<?= LinkDelegate($row['WCA_ID'])?>"><?= $row['Delegate'] ?></a></td>
            <td><?= $row['Action'] ?></td>


    </tr>
    <?php } ?>
    </tbody>
    </table>


<h3>History of registrations of competitors</h3>
<table class="table_info">
    <tr>
        <td>Legend</td>
        <td/>
    </tr>    
    <tr>
        <td><i class="far fa-user"></i></td>
        <td>Competitor</td>
    </td>    
    <tr>
        <td><i class="fas fa-user-tie"></i></td>
        <td>Delegate</td>
    </td>    
    <tr>
        <td><i class="far fa-list-alt"></i></td>
        <td>ScoreTaker</td>
    </td>    
<table>
            <?php DataBaseClass::Query("Select "
                    . " E.Round,D.Code,D.Name, D.CodeScript, LR.Timestamp,LR.Action, LR.Doing,LR.Details "
                    . " from LogsRegistration LR "
                    . " join Event E on E.ID=LR.Event "
                    . " join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
                    . " join Discipline D on D.ID=DF.Discipline"
                    . " where E.Competition=".$Competition['Competition_ID']." "
                    . " order by LR.Timestamp desc");?>
<table class="table_new">
    <thead>
        <td>Date</td><td>Action</td><td>Event</td><td>Round</td><td>Name</td><td>Who did it</td>
    </thead>
    <?php foreach(DataBaseClass::getRows() as $row){ ?>
    <tr>
        <td><?= $row['Timestamp']?></td>
        <td>
            <?= str_replace(
                    ['x','-','*','+','!','C ','D ','S '],
                    ['Delete','Remove','New','Add','Link',
                        '<i class="far fa-user"></i> ',
                        '<i class="far fa-user-tie"></i> ',
                        '<i class="far fa-list-alt"></i> '],
                    $row['Action']) ?>
        </td>
        <td><?= ImageEvent($row['CodeScript'])?> <?= $row['Name'] ?></td>
        <td class="table_new_center"><?= $row['Round'] ?></td>
        <td><?= str_replace([": ",","],[":<br>","<br>"],$row['Details'])?></td>
        <td><?= str_replace(['Competitor: ','Delegate: ','ScoreTaker'],
                ['<i class="far fa-user"></i> ','<i class="fas fa-user-tie"></i> ','<i class="far fa-list-alt"></i> ScoreTaker'],
                $row['Doing'] ) ?></td>
    </tr>
    <?php } ?>
</table>
<script src="<?= PageLocal()?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>