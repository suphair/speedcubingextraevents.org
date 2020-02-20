<!DOCTYPE HTML>
<html>
<?php $request=Request();
if(!isset($request[1])){
    exit();
}
$Secret=$request[1];

DataBaseClass::Query("Select FR.Format FormatResult,DF.ID DisciplineFormat,C.Onsite Competition_Onsite, D.Competitors, C.ID Competition_ID, D.Competitors Discipline_Competitors,D.ID Discipline_ID, E.Round, E.vRound, F.Result, F.ExtResult, F.Attemption, F.Name FormatName, C.Name Competition, D.Name Discipline,C.WCA Competition_WCA, D.Code Discipline_Code,D.CodeScript Discipline_CodeScript, F.ID Format, E.ID, E.CutoffSecond, E.CutoffMinute,E.LimitSecond,E.LimitMinute, E.Cumulative,D.Codes "
        . "from `Competition` C "
        . "join `Event` E on E.Competition=C.ID "
        . "join `DisciplineFormat` DF on DF.ID=E.DisciplineFormat "
        . "join `Discipline` D on D.ID=DF.Discipline "
        . "join `FormatResult` FR on FR.ID=D.FormatResult "
        . "join `Format` F on F.ID=DF.Format "
        . "Where E.Secret='".$Secret."' ");

if(DataBaseClass::rowsCount()==0){
    echo "not found";
    exit();
}
$event=DataBaseClass::getRow();

?><head>
    <title><?= $event['Discipline']?><?= $event['vRound']?></title>
    <link rel="icon" href="../Logo/Logo_Color.png" >
    <link rel="stylesheet" href="../style.css" type="text/css"/>
    <link rel="stylesheet" href="../fontawesome-free-5.12.0-web/css/all.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="../icons-extra-event/css/Extra-Events.css?t=3" type="text/css"/>    
    <link rel="stylesheet" href="../jQuery/chosen_v1/chosen.css" type="text/css"/>
</head>
<body>
<?php
$Next=false;
DataBaseClass::Query("Select E.vRound,E.Round, E.Competitors, count(distinct Com.ID) Commands "
        . " from Event E "
        . " join DisciplineFormat DF on E.DisciplineFormat=DF.ID "
        . " left outer join Command Com on Com.Event=E.ID"
        . "  where Round=".($event['Round']+1)." and Competition=".$event['Competition_ID']." and Discipline=".$event['Discipline_ID']." group by E.ID");
if(DataBaseClass::rowsCount()>0){
    $Next=DataBaseClass::getRow();
}

$isCutoff=($event['CutoffSecond']+$event['CutoffMinute'])>0;
$Attemption=$event['Attemption'];

DataBaseClass::Query("select GROUP_CONCAT(C.Name order by C.Name SEPARATOR ', ') vName, Com.Onsite, Com.Warnings, case when Com.Place>0 then Com.Place else '' end Place, Com.ID, Com.Decline,Com.CardID "
        . " from `Command` Com"
        . " join CommandCompetitor CC on CC.Command=Com.ID join Competitor C on C.ID=CC.Competitor "
        . " where Com.Event='".$event['ID']."' "
        . " group by Com.ID order by case when Com.Place>0 then Com.Place else 999 end, Com.Decline, 1");
$commands=DataBaseClass::getRows(); 
    
foreach($commands as $j=>$command){
    $names=array();
    foreach(explode(", ",$command['vName']) as $name){
        $names[]=trim(explode("(",$name)[0]);
    }
    $commands[$j]['vName']=implode(", ",$names);
}

DataBaseClass::Query("select *"
        . " from `Command` Com"
        . " left outer join Attempt A on A.Command=Com.ID and A.Attempt is not null"
        . " where A.ID is null and Com.Event='".$event['ID']."' and Com.Decline=0");
$notAttempts=sizeof(DataBaseClass::getRows()); 

if($Next){
    $commandsWinner=min($Next['Competitors'],floor(sizeof($commands)*0.75));
}else{
    $commandsWinner=3;
}
$CutoffN=3;
if($Attemption==5){ $CutoffN=3; }
if($Attemption==3){ $CutoffN=2; }
if($Attemption==2){ $CutoffN=2; }
   
$FormatResult=$event['FormatResult']; ?>

<h2><?= $event['Discipline']?><?= $event['vRound']?> / 
<?= $event['Competition'] ?>
<?php if(CheckAccess('Competition.Event.Settings',$event['Competition_ID'])){ ?>
        <?php 
        DataBaseClass::FromTable("Event","Competition=".$event['Competition_ID']);
        DataBaseClass::Join_current("DisciplineFormat");
        DataBaseClass::Join_current("Discipline");
        DataBaseClass::OrderClear("Discipline","Code");
        DataBaseClass::Order("Event","Round");
        foreach(DataBaseClass::QueryGenerate() as $discipline){?>
            <a  href="<?= PageIndex()?>ScoreTaker/<?= $discipline['Event_Secret']?>"><span class="<?= $discipline['Event_ID']==$event['ID']?'select':'' ?>"><?= ImageEvent($discipline['Discipline_CodeScript'],1) ?></span></a>
        <?php } ?>
<?php } ?>
</h2>    
<script src="<?= PageLocal()?>jQuery/ScoreTaker.js?2" type="text/javascript" charset="utf-8"></script>
<script>
    var formatResult='<?= $FormatResult ?>';
    var submitResult;
    var Attemption=<?= $Attemption ?>;
    var isCutoff=<?= $isCutoff?'true':'false' ?>;
    var cutoff_minute=<?=$event['CutoffMinute']?>;
    var cutoff_second=<?=$event['CutoffSecond']?>;
    var limit_minute=<?=$event['LimitMinute']?>;
    var limit_second=<?=$event['LimitSecond']?>;
    var limits=[];
    var cutoffs=[];
    var disciptions=[];
    var CutoffN=<?= $CutoffN ?>;
    var Competitors=<?= $event['Competitors']; ?>;
    </script>    
    <?php if($event['Discipline_Competitors']>1){?>    
Find the Team using ID on a score card, WCA ID or Name of one of the Team members OR choose the Team from the table (click Team member’s Name).
    <?php }else{ ?>
Find the Competitor using ID on a score card, WCA ID or Name OR choose the Competitor from the table (click Competitor’s Name).
    <?php } ?>
        <br>
        <?php if($event['Competition_Onsite']){ ?>
        <?php if($event['Discipline_Competitors']==1){?>
            Create a new Competitor using WCA Registration (you can find it by WCA ID or Name).
            <?php }else{ ?>
            Create a new Team using <?= array('','','two','tree','for')[$event['Discipline_Competitors']] ?> WCA Registrations (you can find them by WCA ID or Name).
            <?php } ?>
        <?php } 
        $message=GetMessage('ResultsSave'); 
        $warning= GetMessage('ResultsSaveWarning');
        if($message){ ?>
            <p><i class="fas fa-check-circle"></i> <?= $message ?></p>
        <?php } 
        if($warning){ ?>
            <p><i class="fas fa-exclamation-circle"></i> <?= $warning ?></p>
        <?php } ?>
<table width="100%">
    <tr>            
        <td width="10%">
            <form method="POST" action="<?= PageAction('ScoreTaker.Add')?>" onkeydown="
            if ((event.which || event.keyCode) === 13) {
                fn = function (elements, start) {
                    for (var i=start; i < elements.length; i++) {
                        var element = elements[i]
                        with (element) if (tagName === 'INPUT' || tagName === 'BUTTON' ) { focus(); break;}
                    } return i;
                }
               var current = event.target || event.srcElement;
               if(current.name==''){ return true; }
                for (var i=0; i < this.elements.length; i++) { if (elements[i] == current) { break; }}
                if (fn(elements,i+1) == elements.length) fn(elements,0)
            } ">    
                <select tabindex="1" ID="Registration" style="width: 300px;" Name="Selected[]" 
                        <?php if($event['Competition_Onsite']){ ?> 
                            data-placeholder="Choose <?= $event['Discipline_Competitors']==1?'Registration':($event['Discipline_Competitors'].' Registrations')?> or <?= $event['Discipline_Competitors']>1?'Team':'Competitor' ?>" 
                        <?php }else{ ?>
                            data-placeholder="Choose <?= $event['Discipline_Competitors']>1?'Team':'Competitor' ?>" 
                        <?php } ?>
                         class="chosen-select chosen-select-<?= $event['Discipline_Competitors'] ?>" multiple
                         onchange="    
                             var competitors=<?= $event['Discipline_Competitors'] ?>;
                             $('.CommandSelect').attr('disabled', false);
                             SelectUpdate();
                             var selected_count=$('#Registration option:selected').length;
                            if(selected_count>0){
                                var id=$('#Registration option:selected').attr('id');
                                if(id.indexOf('Command')>=0){
                                    SetSelectedOption(1);  
                                    chosenSelectCommandID($('#Registration').val());
                                    $('#value1').focus();
                                    $('#amount1').focus();
                                }else{
                                    $('.CommandSelect').attr('disabled', true);
                                    SetSelectedOption(competitors);
                                    SelectUpdate();
                                    if(selected_count===competitors){
                                        chosenSelectCompetitorID();
                                        $('.value_input').attr('disabled', false);
                                        $('.value_amount').attr('disabled', false);
                                        $('#value1').focus();
                                        $('#amount1').focus();
                                    }else{
                                        $('.chosen-search-input').focus();
                                    }
                                }
                            }else{
                                SetSelectedOption(competitors);
                                chosenSelectCommandID();
                                $('.chosen-search-input').focus();
                                $('.value_input').attr('disabled', true);
                            } ">
                    <option value=""></option>
                    <optgroup label="<?= $event['Discipline_Competitors']>1?'Teams':'Competitors' ?>">
                    <?php DataBaseClass::FromTable("Command","Event=".$event['ID']);
                        foreach(DataBaseClass::QueryGenerate() as $command){  
                            DataBaseClass::FromTable("CommandCompetitor","Command=".$command['Command_ID']);
                            DataBaseClass::Join_current("Competitor");
                            DataBaseClass::OrderClear("Competitor", "Name");
                            $competitors=DataBaseClass::QueryGenerate();
                            ?>
                            <option
                                    class="CommandSelect"
                                    ID="CommandIDSelect<?= $command['Command_ID'] ?>" 
                                    value="<?= $command['Command_ID'] ?>">
                                        <?= $command['Command_CardID'] ?> : <?php 
                                        foreach($competitors as $competitor){
                                            ?> <?= Short_Name($competitor['Competitor_Name']) ?> 
                                            <?= Short_Name($competitor['Competitor_WCAID']) ?> $BR$
                                        <?php } ?>
                            </option>                                
                        <?php } ?> 
                    </optgroup>                        
                    <?php if($event['Competition_Onsite']){ ?>
                        <optgroup label="WCA Registrations">
                            <?php DataBaseClass::Query(" Select distinct C.* from Competitor C 
                                        join Registration Reg on Reg.Competitor=C.ID and Reg.Competition=".$event['Competition_ID']."  and C.ID 
                                        not in(select CC.Competitor from CommandCompetitor CC 
                                             join Command Com on CC.Command=Com.ID and Com.Event=".$event['ID']." ) order by C.Name");
                            foreach(DataBaseClass::getRows() as $competitor){ ?>
                                <option style="text-align:left"
                                        class="RegistrationSelect"
                                        ID="CompetitorIDSelect<?= $competitor['ID'] ?>" 
                                        value="<?= $competitor['ID'] ?>">
                                            <?= Short_Name($competitor['Name']) ?> <?= $competitor['WCAID']?(' '.$competitor['WCAID']):'' ?>
                                </option>    
                            <?php } ?>
                        </optgroup> 
                    <?php } ?>           
                </select>  
                <div style=" margin:10px;">
                    <?php if($isCutoff){ ?>
                    <span id="cutoff_pre"></span>
                    <span id="cutoff">
                        <?= " Cutoff  ".$event['CutoffMinute'].":".sprintf("%02d",$event['CutoffSecond']); ?>.00
                    </span> &nbsp; 
                    <?php } ?>
                    <span id="limit_pre"></span>
                    <span id="limit">
                        <?= ($event['Cumulative']?"Cumulative limit ":"Limit ")." ".$event['LimitMinute'].":".sprintf("%02d",$event['LimitSecond']); ?>.00<br>  
                    </span>
                </div>
                <?php for($i=1;$i<=$event['Attemption'];$i++) {?>
                    <?php if($i==$CutoffN){ ?>
                        <?php if($event['CutoffSecond']+$event['CutoffMinute']>0){?>
                            <hr style="height:2px; background: gray;" id="cutoff_hr">
                        <?php } ?>    
                    <?php } ?>     
                            
                <?php if($event['Codes']){ ?>                
                    <font size="6"><span class=" cubing-icon event-<?= explode(",",$event['Codes'])[$i-1]?>"></span></font>
                <?php }else{ ?>
                    <font size="6"><?= $i ?></font>
                <?php } ?>            
   
                <?php if(strpos($FormatResult,'A')!==false){ ?>
                    &nbsp;&nbsp;&nbsp;<input tabindex="<?=  $i*2 ?>" maxlength='2' autocomplete="off" size="2" style="width:60px;  font-family: monospace; font-size: 35px;text-align: right" name="Amount<?= $i ?>" ID="amount<?= $i ?>" class="amount_input" oninput="AmountEnterOne(<?= $i ?>)" onclick="this.select();" onfocus="this.select();">
                <?php } ?>
                    
                <?php if(strpos($FormatResult,'T')!==false){ ?>
                    &nbsp;&nbsp;&nbsp;<input tabindex="<?=1+ $i*2 ?>" maxlength='8' autocomplete="off" disabled size="8" style="width:180px;  font-family: monospace; font-size: 35px;text-align: right" name="Value<?= $i ?>" ID="value<?= $i ?>" class="value_input" oninput="ValueEnterOne(<?= $i ?>)" onclick="this.select();" onfocus="this.select();">
                <?php } ?>
                    
                <br>
                <span ID="description<?= $i ?>_pre" class="description" style="color:red"></span>
                <span ID="description<?= $i ?>" class="description" style="color:red" ></span><br>
                <?php } ?>
                <span style="font-size:30px;"> </span>
                <button  ID="SubmitValue" type='button' disabled style="width:200px;font-size:30px;"
                       onclick="
                                if(submitResult!==''){ 
                                    if(confirm('Wrong results!\n' + submitResult +'Confirm results anyway?')){
                                       form.submit(); 
                                    }else{
                                       return false;
                                    }
                                }else{ 
                                    form.submit();
                                }">Confirm</button>
                <input name="AttempsWarning" ID="AttempsWarning" type="hidden" value="" />
                <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                <input name="Type" ID="Type" type="hidden"  value="" />
            </form>
            <b>DNF</b> - F,D or *<br>
            <b>DNS</b> - S or /            
            <br>
        </td>
        <td width="90%">
    <div style='height: 600px;overflow: scroll;border:1px dotted black'>        
    <table class="table_new" width="100%">    
        <thead>
        <tr> 
            <td>ID</td>            
            <td></td>
            <td><?= $event['Competitors']>1?('Team : '.(html_spellcount($event['Discipline_Competitors'], 'competitor', 'competitors', 'competitors'))):'Competitor'?></td>
            <?php if($event['Codes']){ ?>                
                <?php for($i=0;$i<$event['Attemption'];$i++) {?>
                <td class="table_new_right">             
                    <span class=" cubing-icon event-<?= explode(",",$event['Codes'])[$i]?>"></span>
                </td>
                <?php } ?>
            <?php }else{ ?>
                <?php for($i=1;$i<=$event['Attemption'];$i++) {?>
                <td class="table_new_right">  
                   <?= $i ?>
                 </td>   
                <?php } ?>
            <?php } ?>
            </td>
            <td class="table_new_right">
                <?= $event['Result']?>
            </td>
            <?php if($event['ExtResult']){ ?>
                <td class="table_new_right">
                    <?= $event['ExtResult']?>
                </td>
            <?php } ?>
            <td  class="table_new_right">Place</td>
            <td></td>
        </tr>
        </thead>
        </tbody>
    <script>
       ValuesSave=[];
        AmountsSave=[];
    </script>
<?php foreach($commands as $c=>$command){ 
    
    DataBaseClass::Query("select * from `Attempt` A where Command='".$command['ID']."' ");
    $attempt_rows=DataBaseClass::getRows();
    $attempts=array();
    for($i=1;$i<=$event['Attemption'];$i++) {
        $attempts[$i]="";
    }
    foreach(DataBaseClass::SelectTableRows("Format") as $format){
        $attempts[$format['Format_Result']]="";    
    }
    ?>
     <script>   
    <?php foreach($attempt_rows as $attempt_row){ ?>
        <?php $attempt=$attempt_row['vOut'];
        
        if($attempt_row['Except']){
            $attempt="($attempt)";
        }
        
        if($attempt_row['Attempt']){
           $attempts[$attempt_row['Attempt']]= $attempt;
           
           
    if($attempt_row['IsDNF']){
        $string='DNF';
    }elseif($attempt_row['IsDNS']){
        $string='DNS';
    }else{
        $string=sprintf( "%d%02d%02d", $attempt_row['Minute'],$attempt_row['Second'],$attempt_row['Milisecond']);
    }
           ?>
              ValuesSave['<?= $command['ID'] ?>_<?= $attempt_row['Attempt'] ?>']='<?= $string ?>';
              AmountsSave['<?= $command['ID'] ?>_<?= $attempt_row['Attempt'] ?>']='<?= round($attempt_row['Amount']) ?>';
           <?php
        }else{
           $attempts[$attempt_row['Special']]= $attempt; 
        }
    } ?>
    </script>  

<?php DataBaseClass::FromTable('Command','ID='.$command['ID'] );
DataBaseClass::Join_current('CommandCompetitor');
DataBaseClass::Join_current('Competitor');
DataBaseClass::OrderClear('Competitor','ID');
$competitors=array();
foreach(DataBaseClass::QueryGenerate() as $competitor){
    $competitors[]=$competitor['Competitor_ID'];
}
?>
   
<tr>
            <td class="table_new_bold">
                <span id="rowCardID<?= $command['ID'] ?>" style="<?= $command['Warnings']?"color:red;":"";  ?>">
                    <?= $command['CardID'] ?>
                </span>
            </td>
            <td>
                <?php if($command['Onsite']){ ?>
                    <i class="far fa-building"></i>
                <?php } ?>
            </td>
            <td>
                <span id="Name<?= $command['ID'] ?>">
                    <a href="#" onclick="ClickRow(<?= $command['ID'] ?>)" ><?= $command['vName'] ?></a>
                </span>
            </td>
            <?php for($i=1;$i<=$event['Attemption'];$i++) {?>
            <td class="table_new_right">
                <span  id="Value<?= $command['ID']."_".$i ?>" class="<?= in_array($i,explode(",",$command['Warnings']))?"color_red":"";  ?>"</span>
                        <?= $attempts[$i]; ?>
                </span>
            </td>
            <?php } ?>
            <td  class="table_new_right table_new_bold">
                <?= $attempts[$event['Result']]?>
            </td>
            <?php if($event['ExtResult']){ ?>
                <td  class="table_new_right"> 
                    <?= $attempts[$event['ExtResult']]?>
                </td>
            <?php } ?>
            <td class="table_new_right <?= ($command['Place']<=$commandsWinner and $command['Place']>0)?"podium":""; ?>">
                <b><?= $command['Place'] ?></b>
            </td>
            <td>
                <?php if(!$command['Decline']){ ?>
                <form  method="POST" action="<?= PageAction('ScoreTaker.Decline')?>"  
                    onsubmit="
                        <?php if($command['Onsite']){ ?>
                            return confirm('Confirm delete')
                        <?php }else{ ?>
                            return confirm('Confirm refusal')
                        <?php } ?>
                    ">      
                    <input id="ID<?= $command['ID'] ?>" name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                    <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                    <button class="delete button_row"><i class="fas fa-user-slash"></i></button>                    
                </form>
            <?php }else{ ?>
                <form   method="POST" action="<?= PageAction('ScoreTaker.Accept')?>">
                    <input name="ID" type="hidden" value="<?= $command['ID'] ?>" />
                    <input name="Secret" type="hidden" value="<?= $Secret ?>" />
                    <button  class="button_row"><i class="fas fa-undo-alt"></i></button>                    
                </form>
                <?php }?>
            </td>
        </tr>
<?php } ?>
    </tbody>   
    </table> 
    </div>   
<?php if($notAttempts){ ?>
    <form method="POST" action="<?= PageAction('ScoreTaker.DeclineAll')?>"  onsubmit="return confirm('Confirm: Complete the event')">
        <input name="ID" type="hidden" value="<?= $event['ID'] ?>" />
        <input name="Secret" type="hidden" value="<?= $Secret ?>" />
        <button><i class="fas fa-tasks"></i> Complete the event</button>
    </form>
<?php }else{ ?>
    <?php if($Next and $commandsWinner>3 and !$Next['Commands']){ ?>
        <form method="POST" action="<?= PageAction('ScoreTaker.NewRound')?>"  onsubmit="return confirm('Confirm: Сreate a new round')">
           <input name="ID" type="hidden" value="<?= $event['ID'] ?>" />
           <input name="Secret" type="hidden" value="<?= $Secret ?>" />
           <button><i class="fas fa-users-cog"></i> Сreate a new round</button>
       </form>
        
    <?php } ?>        
        <br><button onclick="window.open('<?= PageAction('CompetitonEvent.Results.Print')?>/<?= $event['ID'] ?>', '_blank');"><i class="fas fa-print"></i> Print results</button>
<?php } ?>
</td></tr></table>
    
    
    <?php if($event['Competition_Onsite'] and CheckAccess('Competition.Event.Settings',$event['Competition_ID'])){ ?>
                <form method="POST" action="<?= PageAction('ScoreTaker.Registartion.Add')?>">
                    <?php $message=GetMessage("ScoreTaker.Registartion.Add");
                    if($message){ ?>
                        <p style='color:green'><?= $message ?></p>
                    <?php } ?>
                    <?= ml ('ScoreTaker.Registartion.Add') ?>
                    <input name="Competition" type="hidden" value="<?= $event['Competition_ID'] ?>" />
                    <input required="" class="WCAID" placeholder="WCA ID" ID="WCAID" autocomplete="off" style="width:100px" name="WCAID" value="" 
                        onkeyup="
                        if($(this).val().indexOf('_')+1==0){
                            if(!$('#WCAIDsearch').val() || $('#WCAIDsearch').val().toUpperCase()!=$('#WCAID').val().toUpperCase()){
                                $('#tst').html('search...'); 
                                $('#tst').load('<?= PageAction('AJAX.ScoreTaker.Check.WCAID') ?>?Competition=<?= $event['Competition_ID'] ?>&WCAID=' + $('#WCAID').val());
                            }
                        }else{
                            $('#tst').html(''); 
                        }" />
                     <span id="tst"></span>
                </form>
    <?php } ?>
    


<script src="<?= PageLocal()?>jQuery/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="<?= PageLocal()?>jQuery/chosen_v1/chosen.jquery.js" type="text/javascript"></script>
<script src="<?= PageLocal()?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= PageLocal()?>jQuery/maskedinput/jquery.maskedinput.js?4" type="text/javascript"></script>
    
<script> 
    PrepareInputs(false);
    $('.chosen-search-input').focus();        
</script>

<script>
    $(function(){
      $(".WCAID").mask("9999aaaa99");
    });
</script>
<body>
</html>