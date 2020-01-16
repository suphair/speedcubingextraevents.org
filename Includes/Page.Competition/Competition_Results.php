<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
?>    
<?php if(CheckAccess('Competition.Event.Settings',$Competition['Competition_ID'])){ ?>
    <img style="vertical-align: middle" width="20px" src="<?= PageIndex()?>Image/Icons/settings.png"> <a  class='Settings' href="<?= LinkEvent($CompetitionEvent['Event_ID'])?>/Settings"><?= ml('CompetitionEvent.Settings') ?></a>
<?php } ?>
<h2>
    <?= ImageEvent($CompetitionEvent['Discipline_CodeScript'],50)?>
    <a href="<?= LinkEvent($CompetitionEvent['Event_ID'],$CompetitionEvent['Event_Round']) ?>"><?= $CompetitionEvent['Discipline_Name'] ?><?= $CompetitionEvent['Event_vRound'] ?></a> 
    / <?= ml('Competition_PsychSheet.Results') ?>
        
</h2>

<?= EventBlockLinks($CompetitionEvent); ?>
    
<?php if($CompetitionEvent['Event_Comment']){?>
    <div class="block_comment">
        <?= Parsedown(ml_json($CompetitionEvent['Event_Comment'])); ?>
    </div>
<?php } ?>
    
<div class="block_comment">
<?php
        if($CompetitionEvent['Discipline_Competitors']>1){ ?>
            Team has <?= $CompetitionEvent['Discipline_Competitors'] ?> competitors &#9642;
        <?php } ?>
    <?= $CompetitionEvent['Format_Result'].' of '.$CompetitionEvent['Format_Attemption']?>
    <?php if($CompetitionEvent['Event_CutoffMinute']+$CompetitionEvent['Event_CutoffSecond']>0){ ?>
        &#9642; Cutoff <?= sprintf("%02d:%02d",$CompetitionEvent['Event_CutoffMinute'],$CompetitionEvent['Event_CutoffSecond'])?>
    <?php } ?>
        &#9642; <?= $CompetitionEvent['Event_Cumulative']?"Cumulative limit":"Limit"; ?> <?= sprintf("%02d:%02d",$CompetitionEvent['Event_LimitMinute'],$CompetitionEvent['Event_LimitSecond'])?>
</div>
    
    <?php if($CompetitionEvent['Event_ScramblePublic']){ ?>
        <div class="block_comment">
            <a href="<?= PageIndex()?>Scramble/<?= $CompetitionEvent['Event_ScramblePublic'] ?>" target="_blank"><img align="top" height="20px"src="<?= PageIndex()?>Image/Icons/scramble.png"> <?= ml('Competition_Results.ScrambleShare')?></a> 
        </div> 
    <?php } ?>
<?php
if(!isset($CompetitionEvent['Format_ExtResult'])){
    $formats=[$CompetitionEvent['Format_Result']];
}else{
    $formats=[$CompetitionEvent['Format_Result'],$CompetitionEvent['Format_ExtResult']];
}


$Country_Continent=[];
DataBaseClass::Query("Select C.ISO2,Continent from Country C where Continent is not null");
foreach(DataBaseClass::getRows() as $row){
    $Country_Continent[$row['ISO2']]=$row['Continent'];
}

$WRecords=[];
$NRecords=[];
$CRecords=[];
if( !$Competition['Competition_Unofficial']){ 
    foreach($formats as $format){
        $format_arr=[$format];
        if(in_array($format,['Mean','Average'])){
            $format_arr=['Mean','Average'];
        }
        DataBaseClass::FromTable('Competition',"EndDate<='".$CompetitionEvent['Competition_EndDate']."'");       
        DataBaseClass::Join_current("Event");
        DataBaseClass::Join_current("DisciplineFormat");
        DataBaseClass::Join_current("Format");
        DataBaseClass::Join("DisciplineFormat","Discipline");
        DataBaseClass::Join("Event","Command");
        DataBaseClass::Join("Command","Attempt");
        DataBaseClass::Join("Command","CommandCompetitor");
        DataBaseClass::Where('Discipline',"ID='".$CompetitionEvent['Discipline_ID']."'");    
        DataBaseClass::Where("A.Special in ('".implode("','",$format_arr)."')");
        DataBaseClass::Where('A.isDNF = 0');
        DataBaseClass::OrderClear('Attempt', 'vOrder');
        DataBaseClass::SelectPre("distinct Com.ID Command, Com.vCountry, A.vOut, A.vOrder,A.Special,C.WCA ");

        foreach(DataBaseClass::QueryGenerate() as $r){
            if(!isset($WRecords[$format])){
                $WRecords[$format]=$r['vOut'];
            }
            if($r['vCountry'] and !isset($NRecords[$r['vCountry']][$format])){
                $NRecords[$r['vCountry']][$format]=$r['vOut'];
            }

            if($r['vCountry'] and !isset($CRecords[$Country_Continent[$r['vCountry']]][$format]) and isset($Country_Continent[$r['vCountry']])){
                $CRecords[$Country_Continent[$r['vCountry']]][$format]=$r['vOut'];
            }
        }
    }
}

    DataBaseClass::FromTable('Event'); 
    DataBaseClass::Where_current("ID='".$CompetitionEvent['Event_ID']."'");
    DataBaseClass::Join_current('Command');
    DataBaseClass::Where_current('Decline=0');
    DataBaseClass::Join_current('CommandCompetitor');
    DataBaseClass::Join_current('Competitor');
    DataBaseClass::OrderSpecial('case when Place>0 then Place else 9999 end ');
    DataBaseClass::Order("Competitor", "Name");
    $commands=[];
    foreach(DataBaseClass::QueryGenerate() as $row){
        if(!isset($commands[$row['Command_ID']])){
            $commands[$row['Command_ID']]=$row;
        }
    }
    
    $commands= array_values($commands);
    
    $Next=false;
    DataBaseClass::Query("Select E.vRound,E.Round, E.Competitors, count(distinct Com.ID) Commands "
            . " from Event E "
            . " join DisciplineFormat DF on E.DisciplineFormat=DF.ID "
            . " left outer join Command Com on Com.Event=E.ID"
            . "  where Round=".($CompetitionEvent['Event_Round']+1)." and Competition=".$CompetitionEvent['Competition_ID']." and Discipline=".$CompetitionEvent['Discipline_ID']." group by E.ID");
    if(DataBaseClass::rowsCount()>0){
        $Next=DataBaseClass::getRow();
    }
    if($Next){
        $commandsWinner=min($Next['Competitors'],floor(sizeof($commands)*0.75));
    }else{
        $commandsWinner=3;
    }
            
            ?>
        <table class="competition_result">
            <tr class="tr_title"> 
                <td><?= ml('Competition_Results.Table.Place') ?></td>
                <td> <img src="<?=  PageIndex() ?>Image/Icons/persons.png" align="top" width="20px"> <?= $CompetitionEvent['Discipline_Competitors']>1?ml('Event.Competitors.Table.Teams'):ml('Event.Competitors.Table.Competitors') ?></td>
                <?php for($i=1;$i<=$CompetitionEvent['Format_Attemption'];$i++) {?>
                <td class="attempt attempt_header_num">
                    <?php if($image=IconAttempt($CompetitionEvent['Discipline_Code'],$i)){ ?>
                        <img src="<?= PageIndex() ?>/<?= $image ?>" width="20px">
                    <?php }else{ ?>
                        <?= $i ?>
                    <?php } ?>
                </td>
                <?php } ?>
                <td class="attempt_result">
                    <?= ml('Competition_Results.Table.'.$CompetitionEvent['Format_Result']) ?>
                </td>
                <?php if($CompetitionEvent['Format_ExtResult']){ ?>
                    <td class="attempt">
                        <?= ml('Competition_Results.Table.'.$CompetitionEvent['Format_ExtResult']) ?>
                    </td>
                <?php } ?>
            </tr> 
            <tbody>
        <?php foreach($commands as $command){ 

        DataBaseClass::Query("select * from `Attempt` A where Command='".$command['Command_ID']."' ");
        $attempt_rows=DataBaseClass::getRows();
        $attempts=array();
        for($i=1;$i<=$CompetitionEvent['Format_Attemption'];$i++) {
            $attempts[$i]="";
        }
        foreach(DataBaseClass::SelectTableRows("Format") as $format){
            $attempts[$format['Format_Result']]="";    
        }


        foreach($attempt_rows as $attempt_row){
            $attempt=trim($attempt_row['vOut']);
            
            if($attempt_row['Except']){
                $attempt="($attempt)";
            }

            if($attempt_row['Attempt']){
               $attempts[$attempt_row['Attempt']]= $attempt;
            }else{
               $attempts[$attempt_row['Special']]= $attempt; 
            }
        }   
            $class=($command['Command_Place']<=$commandsWinner)?"podium":""; ?> 
            <tr class="<?= $class ?>">
                <td class="number">    
                    <?= $command['Command_Place']?$command['Command_Place']:'' ?>
                </td>
                <td class="result_many_rows">
                <?php if($CompetitionEvent['Discipline_CodeScript']=='cup_team'){ ?>
                    <div class="competitor_td">
                        <b><?= $command['Command_Name'] ?></b>  
                    </div>
                <?php } ?>
                <?php 
                 DataBaseClass::Query("select C.* from `Competitor` C "
                         . " join `CommandCompetitor` CC on CC.Competitor=C.ID where CC.Command='".$command['Command_ID']."' order by C.Name");
                 $competitors=DataBaseClass::getRows();   
                 foreach($competitors as $competitor){ ?>
                    <div class="result_many_rows">
                        <a href="<?= LinkCompetitor($competitor['ID'],$competitor['WCAID'])?>">
                            <nobr>
                                <?php $flag="Image/Flags/".strtolower($competitor['Country']).".png";
                                if(file_exists($flag)){ ?>
                                    <img width="20" style="vertical-align: middle" src="<?= PageIndex()?>Image/Flags/<?= strtolower($competitor['Country'])?>.png">
                                <?php } ?>
                                <?= $competitor['Name'] ?>
                            </nobr>       
                        </a>
                    </div>
                    <?php } ?>
                </td>
                <?php for($i=1;$i<=$CompetitionEvent['Format_Attemption'];$i++) {?>
                <td  class="attempt" >
                    <nobr><?= $attempts[$i]; ?></nobr>
                </td>
                <?php } ?>
                <td class="attempt_result">
                    <nobr>
                        <?= $attempts[$CompetitionEvent['Format_Result']]?>
                        <?php if(isset($WRecords[$CompetitionEvent['Format_Result']]) and  $WRecords[$CompetitionEvent['Format_Result']]==$attempts[$CompetitionEvent['Format_Result']]){ ?>
                                <span class="message">WR</span>
                        <?php }elseif($command['Command_vCountry'] and  isset($CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_Result']]) and
                                $CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_Result']]==$attempts[$CompetitionEvent['Format_Result']]){ ?>
                                <span class="message">CR</span>
                        <?php }elseif($command['Command_vCountry'] and isset($NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_Result']]) and  $NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_Result']]==$attempts[$CompetitionEvent['Format_Result']]){ ?>
                                <span class="message">NR</span>
                        <?php } ?>
                    </nobr>
                </td>
                <?php if($CompetitionEvent['Format_ExtResult']){ ?>
                    <td class="attempt">
                        <?php if(!in_array($attempts[$CompetitionEvent['Format_ExtResult']],array('DNF','DNS'))){ ?>
                            <nobr>
                                <?= $attempts[$CompetitionEvent['Format_ExtResult']]; ?>
                                <?php if(isset($WRecords[$CompetitionEvent['Format_ExtResult']]) and  $WRecords[$CompetitionEvent['Format_ExtResult']]==$attempts[$CompetitionEvent['Format_ExtResult']]){ ?>
                                    <span class="message">WR</span>
                                <?php }elseif($command['Command_vCountry'] and
                                        isset($CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_ExtResult']]) 
                                        and $CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_ExtResult']]==$attempts[$CompetitionEvent['Format_ExtResult']]){ ?>
                                    <span class="message">CR</span>
                                <?php }elseif(isset($command['Command_vCountry']) and isset($NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_ExtResult']]) and  $NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_ExtResult']]==$attempts[$CompetitionEvent['Format_ExtResult']]){ ?>
                                    <span class="message">NR</span>
                                <?php } ?>
                            </nobr>
                        <?php }?>
                    </td>
                <?php } ?>
                <?php if($command['Command_Video']){ ?>    
                    <td>
                        <a target=_blank" href="<?=$command['Command_Video'] ?>"><img class="video" src="<?= PageIndex()?>Image/Icons/Video.png"></a>
                    </td>    
                <?php } ?>    
            </tr>

        <?php } ?>
        </tbody>
        </table>

<?= mlb('Competition_Results.Table.Best') ?>
<?= mlb('Competition_Results.Table.Mean') ?>
<?= mlb('Competition_Results.Table.Average') ?>
<?= mlb('Competition_Results.Table.Sum') ?>
<?= mlb('Competition_Results.ScrambleShare')?>