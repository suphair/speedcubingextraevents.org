<script src="<?= PageLocal()?>jQuery/maskedinput/jquery.maskedinput.js?4" type="text/javascript"></script>
<?php
$Competition=ObjectClass::getObject('PageCompetition');
$CompetitionEvents=ObjectClass::getObject('PageCompetitionEvents');
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$CompetitionDelegates=ObjectClass::getObject('PageCompetitionDelegates');
?>
<h1><a href="<?= LinkEvent($CompetitionEvent['Event_ID'])?>"><?= $Competition['Competition_Name'] ?></a></h1>
<h2><?= $CompetitionEvent['Discipline_Name'] ?><?= $CompetitionEvent['Event_vRound'] ?> / Competition event settings</h2>
        

<table class="table_info">
    <?php if(CheckAccess('Competition.Settings',$Competition['Competition_ID'])){ ?>
        <tr>
            <td><i class="fas fa-cog"></i></td>
            <td><a href="<?= LinkCompetition($Competition['Competition_WCA'])?>/Settings">Competition settings</a> - for settings limits and formats events</td>    
        </tr>
    <?php }else{ ?>
        <tr>
            <td><i class="fas fa-info-circle"></i></td>
            <td>Contact senior delegates to settings limits and formats</td>    
        </tr>
    <?php } ?>    
</table>    
            
            
<table width="100%"><tr><td>
    <table class="table_info">
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
                        <a class="<?= $competition_event['Event_ID']==$CompetitionEvent['Event_ID']?"list_select":""?>"  href="<?= LinkEvent($competition_event['Event_ID'],$competition_event['Event_Round']) ?>/Settings"><?= $competition_event['Discipline_Name'] ?><?= $competition_event['Event_vRound'] ?></a>
                    </td>
                </tr>
            <?php } ?> 
    </table>            
</td><td>        
    <table class="table_info"> 
        <tr>
            <td>Preparation</td><td/>
        </tr>    
        <?php if($CompetitionEvent['Discipline_CodeScript']!='team_cup'){ ?>
        <tr>
            <td><i class="fas fa-expand-arrows-alt"></i></td>
            <td><a target="_blank" href="<?= PageAction('CompetitionEvent.Groups' )?>/<?= $CompetitionEvent['Event_ID'] ?>">Distribution of competitors by groups</a><br></td>
        </tr>
        <tr>
            <td><i class="fas fa-list"></i></td>
            <td><a target="_blank" href="<?= PageAction('CompetitionEvent.Competitors.Print')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print the list of competitors</a><br></td>
        </tr>
        <?php }else{ ?>
        <tr>
            <td><i class="fas fa-expand-arrows-alt"></i></td>
            <td><a target="_blank" href="<?= PageAction('CompetitionEvent.Grid' )?>/<?= $CompetitionEvent['Event_ID'] ?>">Distribution of teams by grid</a><br></td>
        </tr>
        <tr>
            <td><i class="fas fa-list"></i></td>
            <td><a target="_blank" href="<?= PageAction('CompetitionEvent.CupCompetitors.Print')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print the list of competitors</a><br></td>
        </tr>
        <?php } ?>
        <tr>
            <td>Competitors cards</td><td/>
        </tr>
        <tr>
            <td><i class="fas fa-print"></i></td>
            <td><a target="_blank" href="<?= PageAction('CompetitonEvent.ScoreCards')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print competitors cards</a></td>
        </tr>    
        <tr>
            <td><i class="fas fa-download"></i></td>
            <td><a target="_blank" href="<?= PageAction('CompetitonEvent.ScoreCards')?>/<?= $CompetitionEvent['Event_ID'] ?>/Download">Download competitors cards</a></td>
        </tr> 
        <tr>
            <td>Scrambles</td><td/>
        </tr>   
    <?php $file="Image/Scramble/".$CompetitionEvent['Event_ScrambleSalt'].".pdf";         
    if($CompetitionEvent['Event_ScrambleSalt'] and file_exists($file)){ ?>
        <tr>
            <td><i class="fas fa-print"></i></td>
            <td><a target="_blank"  href="<?= PageIndex()?>Scramble/<?= $CompetitionEvent['Event_ID'] ?>">Print scrambles</font></a></td>
        </tr>    
        <tr>
            <td><i class="fas fa-download"></i></td>
            <td><a target="_blank"  href="<?= PageIndex()?>Scramble/<?= $CompetitionEvent['Event_ID'] ?>/Download">Download scrambles</font></a></td>
        </tr>
    <?php }else{ ?>
        <tr>
            <td><i class="fas fa-exclamation-triangle"></i></td>
            <td>Scrambles not created</td>
        </tr>
         <tr>
        <?php if(CheckAccess('Competition.Settings',$Competition['Competition_ID']) and $LinkScrambes=GetLinkScrambes($CompetitionEvent)){ ?>
            <td><i class="fas fa-random"></i></td>
            <td>
                <a target="_blank" href="<?= $LinkScrambes ?>/<?= $CompetitionEvent['Event_ID'] ?>">Generate scrambles</a>
            </td>
        <?php }else{ ?>
            <td><i class="fas fa-user-tie"></i></td>
            <td>
                Contact senior delegates
            </td>
        <?php } ?>
    <?php } ?>     
        <tr>
            <td>Results</td><td/> 
        </tr>
        <tr>
            <td><i class="fas fa-edit"></i></td>
            <td>
                Enter the results  <a target="_blank" href="<?= PageIndex()?>ScoreTaker/<?= $CompetitionEvent['Event_Secret'] ?>">[link <?= $CompetitionEvent['Event_Secret'] ?>]</a>
            </td>
        </tr>
        <tr>
            <td/>
            <td>
                <form  method="POST" action="<?= PageAction('ScoreTaker.Regenerate')?> " >
                    <input hidden value="<?= $CompetitionEvent['Event_ID'] ?>" name="ID">
                    <button>Update the link</button> if you do not enter the results yourself
                </form>
            </td>
        </tr>
        <?php if($CompetitionEvent['Discipline_CodeScript']!='team_cup'){ ?>
        <tr>
            <td><i class="fas fa-print"></i></td>
            <td>
                <a target="_blank" href="<?= PageAction('CompetitonEvent.Results.Print')?>/<?= $CompetitionEvent['Event_ID'] ?>">Print the results</a>
            </td>
        </tr>          
        <?php } ?>
    </table>
</td>
</tr></table>



<table class="table_info">
    <tr>
        <td>Information for Event</td>
        <td/>
    </tr>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Edit.Comment'); ?>">
        <input name="ID" type="hidden" value="<?= $CompetitionEvent['Event_ID'] ?>" />
        <?php 
        $comments=json_decode($CompetitionEvent['Event_Comment'],true);
        if(!$comments and $CompetitionEvent['Event_Comment']!='[]'){
            $comments[getLanguages()[0]]=$CompetitionEvent['Event_Comment'];
        }
        foreach(getLanguages() as $language){ ?>
        <tr>
            <td>
            <b><?= ImageCountry($language,20); ?> <?= CountryName($language,true) ?></b><br>
            </td>
            <td>
            <textarea name="Comment[<?= $language ?>]" style="height: 80px;width: 400px"><?= isset($comments[$language])?$comments[$language]:''; ?></textarea><br>
            </td>
        </tr>    
        <?php } ?>
        <tr>
            <td></td>
            <td><button><i class="far fa-save"></i> Save</button></td>
        </tr>
    </form> 
    
</table>    

<?php 
DataBaseClass::Query("select Com.ID Command,C.Country, C.ID,C.Name,GROUP_CONCAT(A.vOut order by A.Attempt SEPARATOR ' - ') Attempts "
        . " from Command Com"
        . " join CommandCompetitor CC on CC.Command=Com.ID "
        . " join Competitor C on CC.Competitor=C.ID " 
        . " join Attempt A on A.Command=Com.ID and A.Attempt is not null"
        . " where Com.Event=".$CompetitionEvent['Event_ID']." and C.WCAID='' and C.WID is null"
        . " group by Command,C.Country,C.ID,C.Name "
        . " order by C.Name");


$competitors=DataBaseClass::getRows();

if(sizeof($competitors)){ ?>
    <h3><?= svg_red() ?> Persons without wca_id and user_id</h3>
        <table class="table_new">
            <thead>
            <tr>
                <td>Name</td>
                <td>Country</td>
                <td class="table_new_center">Solves</td>
                <td>Enter WCA ID</td>
            </tr>    
            </thead>
            <tbody>
        <?php foreach($competitors as $row){ ?>
            <tr>
                <td>
                    <?= $row['Name'] ?>
                 </td>
                 <td>
                    <?= CountryName($row['Country']); ?>
                 </td>
                <td class="table_new_center">
                    <?= $row['Attempts'] ?>
                </td>
                <form  method="POST" action="<?= PageAction('CompetitionEvent.Registration.WCAID')?> " >
                <td>
                    <input name="Competitor" type="hidden" value="<?= $row['ID'] ?>" />
                    <input name="Competition" type="hidden" value="<?= $Competition['Competition_ID'] ?>" />
                    <input required="" class="WCAID" placeholder="WCA ID" ID="WCAID<?= $row['ID'] ?>" autocomplete="off" style="width:80px" name="WCAID" value="" 
                        onkeyup="
                        if($(this).val().indexOf('_')+1==0){
                            if($('#WCAIDsearch<?= $row['ID'] ?>').val()!=$('#WCAID<?= $row['ID'] ?>').val()){
                                $('#tst<?= $row['ID'] ?>').html('search...'); 
                                $('#tst<?= $row['ID'] ?>').load('<?= PageAction('AJAX.Check.WCAID') ?>?Competitor=<?= $row['ID'] ?>&WCAID=' + $('#WCAID<?= $row['ID'] ?>').val());
                            }
                        }else{
                            $('#tst<?= $row['ID'] ?>').html(''); 
                        }" />
                </td>   
                <td>
                     <span id="tst<?= $row['ID'] ?>"></span>
                </td>
                </form>
            </tr>    
        <?php } ?>
            </tbody>
        </table>    
<script>
    $(function(){
      $(".WCAID").mask("9999aaaa99");
    });
</script>    
<?php } ?>
            
<h3>Registrations</h3>

<table class="table_info">
    <form method='POST' action='<?= PageAction('CompetitionEvent.Competitors.Load')?>'>
    <tr>
        <td>Reloaded date</td>
        <td><?= $Competition['Competition_LoadDateTime'] ?></td>    
    </tr>
    <tr>    
        <td></td>
        <td>
            <input hidden name='ID' value='<?= $Competition['Competition_ID'] ?>'>
            <button><i class="fas fa-sync-alt"></i> Reload</button>
        </td>
    </tr>
    </form>
    <form method="POST" action="<?= PageAction('CompetitionEvent.Registration.Add')?>">
        <input name="ID" type="hidden" value="<?= $CompetitionEvent['Event_ID'] ?>" />
    <?php   $CompetitorsEventAdd=GetMessage("CompetitorsEventAdd");
        if(!$CompetitorsEventAdd)$CompetitorsEventAdd=array();
        DataBaseClass::FromTable("Registration","Competition=".$CompetitionEvent['Competition_ID']);
        DataBaseClass::Join_current("Competitor");
        DataBaseClass::OrderClear("Competitor", "Name");
        $registrations=DataBaseClass::QueryGenerate(); ?>
    <tr>
        <td>Add registration<a name="CompetitorEventAdd"></a></td>
        <td>You can only add competitors with <?= $Competition['Competition_Cubingchina']?'CubingChina':'WCA'?> registration</td>    
    </tr>
    <tr>
        <td></td>
        <td>
            <select style="width: 600px" Name="Competitors[]" data-placeholder="Choose <?= html_spellcount($CompetitionEvent['Discipline_Competitors'], 'competitor', 'competitors', 'competitors')?>" class="chosen-select chosen-select-<?= $CompetitionEvent['Discipline_Competitors'] ?>" multiple>
                <option value=""></option>
                <?php foreach($registrations as $competitor){ ?>
                    <option <?= in_array($competitor['Competitor_ID'],$CompetitorsEventAdd)?'selected':'' ?> value="<?= $competitor['Competitor_ID'] ?>"><?= $competitor['Competitor_WCAID'] ?> <?= $competitor['Competitor_Name'] ?></option>    
                <?php } ?>
            </select>            
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <p><?= GetMessage("CompetitorEventAddMessage") ?><?= GetMessage("CompetitorEventAddError") ?></p>
            <button><i class="fas fa-users-cog"></i> Register</button>
        </td>
    </tr>
    </form>
</table>  

<?php
DataBaseClass::Query("select GROUP_CONCAT(C.Name order by C.Name SEPARATOR ', ') vName, Decline, count(A.ID) Attempt,"
        . "CardID,`Group`,Decline,Com.ID,Video,Com. Name,Com.Sum333  "
        . " from Command Com"
        . " join CommandCompetitor CC on CC.Command=Com.ID "
        . " join Competitor C on CC.Competitor=C.ID " 
        . " left outer join Attempt A on A.Command=Com.ID"
        . " where Com.Event=".$CompetitionEvent['Event_ID'].""
        . " group by Com.ID order by "
        .($CompetitionEvent['Discipline_CodeScript']!='team_cup'?" Com.Sum333,":"")
        . " 1"
        );


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

<h3><?= svg_red() ?> Declined <?= $CompetitionEvent['Discipline_Competitors']>1?'teams':'competitors' ?></h3>
<table class="table_new">
    <tbody>
        <?php foreach($deleter_names as $deleter_name){ ?>
        <tr><td><?= $deleter_name ?></td></tr>
        <?php } ?>
    </tbody>
</table>


    <form method="POST" action="<?= PageAction('CompetitonEvent.Registration.DeleteDeclined')?>" onsubmit="return confirm('Attention: Confirm Delete <?= sizeof($deleter_names)?> declined <?= $CompetitionEvent['Discipline_Competitors']>1?'teams':'competitors' ?> !')">
       <input name="ID" type="hidden" value="<?= $CompetitionEvent['Event_ID'] ?>" />
       <button class="delete"><i class="fas fa-user-slash"></i> Delete</button>
    </form>
<?php } ?>

<h3>Competitors</h3> 
<table class="table_new" width="80%">
    <thead>
    <tr>
        <td>ID</td>
        <td class="table_new_center">Group</td>
        <td></td>
        <td>Status</td>
        <td>Name</td>
    </tr>
    </thead>
    <tbody> 
<?php
    $n=1;
    
    ?>    
    <?php foreach($commands as $command){ 
        DataBaseClass::FromTable('CommandCompetitor',"Command=".$command['ID']);
        DataBaseClass::Join_current('Competitor');
        DataBaseClass::OrderClear('Competitor', 'Name');
        $names=array();
        $competitors=DataBaseClass::QueryGenerate();?>   
     <tr>  
         <td class="table_new_bold">
                 <?= $command['CardID']; ?>
         </td>
         <td class="table_new_center">
                <?= Group_Name($command['Group']) ?>
         </td>
         
            <?php if($command['Decline']){ ?>
               <td class="table_new_right"><i class="fas fa-user-minus"></i></td><td>Decline</td>
           <?php }elseif($command['Attempt']){ ?>
               <td class="table_new_right"><i class="fas fa-check-double"></i></td><td>Result</td>
            <?php }elseif(sizeof($competitors)!=$CompetitionEvent['Discipline_Competitors']){  ?>
               <td class="table_new_right"><i class="fas fa-hourglass-half"></i></td><td>Wait teammates</td>
            <?php }else{ ?>
               <td class="table_new_right"><i class="fas fa-check"></i></td><td>Register</td>
            <?php }  ?>
            <td>
            <?php 
            $WCAIDs=array();
            $Countries=array();
            for($i=0;$i<$CompetitionEvent['Discipline_Competitors'];$i++){ ?> 
                <p>
                <?php if(isset($competitors[$i])){
                        $competitor=$competitors[$i];
                        $names[]=$competitor['Competitor_Name']; ?>
                        <a target="_blank" href="<?= LinkCompetitor($competitor['Competitor_ID'])?>"><?= $competitor['Competitor_Name'] ?></a>
                        <?php if(!$competitor['CommandCompetitor_CheckStatus']){ ?>
                            <?= svg_red()?> no registration
                        <?php } ?>
                <?php }else{ ?>
                    <i class="far fa-question-circle"></i>
                <?php } ?>    
                </p>    
            <?php } ?>
            </td>    
        <td>
            <?php if(!$command['Attempt']){ ?>
            <form name="CompetitorRowChange" id="CompetitorRowDelete_<?= $command['ID']?>" method="POST" 
                  action="<?= PageAction('CompetitionEvent.Registration.Delete')?> " 
                  onsubmit="return confirm('Attention: Confirm deletion <?= implode(", ",$names)?>?')">
                <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                <button class="delete"><i class="fas fa-user-slash"></i> Cancel</button>
            </form>
            <?php }else{ ?>
                <form  method="POST" action="<?= PageAction('CompetitionEvent.Registration.Video')?> " >
                    <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                    <input  placeholder="Enter link on video" name="Video" value="<?= $command['Video'] ?>">
                    <button><i class="fas fa-video"></i> Save video</button>    
                </form>
            <?php } ?>
        </td>
        <?php if($CompetitionEvent['Discipline_CodeScript']=='team_cup'){ ?>
            <td>
                <form action="<?= PageAction('CompetitionEvent.Registration.CommandName')?> "  method="POST">
                    <input name="CommandName" value="<?= $command['Name'] ?>"/>
                    <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                    <button><i class="fas fa-users"></i> Set team name</button> 
                </form>
                <?= getTimeStrFromValue($command['Sum333']); ?>
            </td>    
        <?php } ?>
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

<?php DataBaseClass::Query("Select Timestamp,Action, Doing,Details from LogsRegistration where Event=".$CompetitionEvent['Event_ID']." order by Timestamp desc" );?>
<table class="table_new" width='80%'>
    <thead>
        <td>Date</td><td>Action</td><td>Name</td><td>Who did it</td>
    </thead>
    <tbody>
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
        <td><?= str_replace([": ",","],[":<br>","<br>"],$row['Details'])?></td>
        <td><?= str_replace(['Competitor: ','Delegate: ','ScoreTaker'],
                ['<i class="far fa-user"></i> ','<i class="far fa-user-tie"></i> ','<i class="far fa-list-alt"></i> ScoreTaker'],
                $row['Doing'] ) ?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>

</div>

<script>
  $(".chosen-select-1").chosen({max_selected_options: 1});
  $(".chosen-select-2").chosen({max_selected_options: 2});
  $(".chosen-select-3").chosen({max_selected_options: 3});
  $(".chosen-select-4").chosen({max_selected_options: 4});
</script>
<script src="<?= PageLocal()?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>

<?= mlb('*.Set'); ?>
<?= mlb('*.Link'); ?>
<?= mlb('*.Check'); ?>
<?= mlb('Event.Competitors.Table.Teams'); ?>
<?= mlb('Event.Competitors.Table.Competitors'); ?>