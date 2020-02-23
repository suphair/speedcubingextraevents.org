<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
?>    
   
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
    
    $commands= array_values($commands); ?>
        <table class="table_new" width="80%">
            <thead>
                <tr> 
                    <td>#</td>                    
                    <td colspan="<?= $CompetitionEvent['Discipline_Competitors'] ?>"><?=ml('Competition.Name'); ?></td>
                    <td class="table_new_right"><?= ml('Competition_Results.Table.'.$CompetitionEvent['Format_Result']) ?></td>
                    <td/>
                    <?php if($CompetitionEvent['Format_ExtResult']){ ?>
                        <td class="table_new_right"><?= ml('Competition_Results.Table.'.$CompetitionEvent['Format_ExtResult']) ?></td>
                        <td/>
                    <?php } ?>
                        <td>
                            <?=ml('Competition.CitizenOf'); ?>
                        </td>
                        <?php if($CompetitionEvent['Discipline_Codes']){ ?>                
                            <?php for($i=0;$i<$CompetitionEvent['Format_Attemption'];$i++) {?>
                            <td class="table_new_center">             
                                <span class=" cubing-icon event-<?= explode(",",$CompetitionEvent['Discipline_Codes'])[$i]?>"></span>
                            </td>
                            <?php } ?>
                        <?php }else{ ?>
                            <td class="table_new_center" colspan="<?=$CompetitionEvent['Format_Attemption'] ?>">
                                <?=ml('Competition.Solves'); ?>
                            </td>
                        <?php } ?>
                </tr> 
            </thead>
            <tbody>
        <?php foreach($commands as $command){ 

        DataBaseClass::Query("select * from `Attempt` A where Command='".$command['Command_ID']."' ");
        $attempt_rows=DataBaseClass::getRows();
        $attempts=[];
        $attempts_in=[];
        for($i=1;$i<=$CompetitionEvent['Format_Attemption'];$i++) {
            $attempts[$i]="";
            $attempts_in[$i]="";
        }
        foreach(DataBaseClass::SelectTableRows("Format") as $format){
            $attempts[$format['Format_Result']]="";    
            $attempts_in[$format['Format_Result']]="";    
        }


        foreach($attempt_rows as $attempt_row){
            $attempt=trim($attempt_row['vOut']);

            $attempt_in=$attempt;   
            if($attempt_row['Except']){
                $attempt="<span class='table_new_except table_new_attempt'>$attempt</span>";
            }else{
                $attempt="<span class='table_new_attempt'>$attempt</span>";
            }

            if($attempt_row['Attempt']){
               $attempts_in[$attempt_row['Attempt']]= $attempt_in ;
               $attempts[$attempt_row['Attempt']]= $attempt;
            }else{
               $attempts_in[$attempt_row['Special']]= $attempt_in ;
               $attempts[$attempt_row['Special']]= $attempt; 
            }
        }   ?> 
            <tr>
                <td>
                    <?= $command['Command_Place']?$command['Command_Place']:'' ?>
                </td>
                
                    <?php if($CompetitionEvent['Discipline_CodeScript']=='cup_team'){ ?>
                        <td>
                            <b><?= $command['Command_Name'] ?></b><br>
                        </td>        
                    <?php } ?>
                    <?php DataBaseClass::Query("select C.* from `Competitor` C "
                         . " join `CommandCompetitor` CC on CC.Competitor=C.ID where CC.Command='".$command['Command_ID']."' order by C.Name");
                    $competitors=DataBaseClass::getRows();   
                    foreach($competitors as $competitor){ ?>
                        <td>
                            <a href="<?= LinkCompetitor($competitor['ID'],$competitor['WCAID'])?>">    
                                <?= $competitor['Name'] ?>
                            </a>
                        </td>    
                   <?php } ?>
                <td class="table_new_attempt table_new_bold">
                    <?= $attempts[$CompetitionEvent['Format_Result']]?>
                </td>
                <td> 
                    <span class="table_new_PB">
                        <?php if(isset($WRecords[$CompetitionEvent['Format_Result']]) and  $WRecords[$CompetitionEvent['Format_Result']]==$attempts_in[$CompetitionEvent['Format_Result']]){ ?>
                                WR
                        <?php }elseif($command['Command_vCountry'] and  isset($CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_Result']]) and
                                $CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_Result']]==$attempts_in[$CompetitionEvent['Format_Result']]){ ?>
                                CR
                        <?php }elseif($command['Command_vCountry'] and isset($NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_Result']]) and  $NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_Result']]==$attempts_in[$CompetitionEvent['Format_Result']]){ ?>
                                NR
                        <?php } ?>
                    </span>
                </td>
                <?php if($CompetitionEvent['Format_ExtResult']){ ?>
                    <td class="table_new_attempt table_new_bold">
                        <?php if(!in_array($attempts[$CompetitionEvent['Format_ExtResult']],array('DNF','DNS'))){ ?>
                            <?= $attempts[$CompetitionEvent['Format_ExtResult']]; ?>
                        <?php }?>        
                    </td>
                    <td class="table_new_PB">            
                        <?php if(isset($WRecords[$CompetitionEvent['Format_ExtResult']]) and  $WRecords[$CompetitionEvent['Format_ExtResult']]==$attempts_in[$CompetitionEvent['Format_ExtResult']]){ ?>
                                WR
                        <?php }elseif($command['Command_vCountry'] and
                                isset($CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_ExtResult']]) 
                                and $CRecords[$Country_Continent[$command['Command_vCountry']]][$CompetitionEvent['Format_ExtResult']]==$attempts_in[$CompetitionEvent['Format_ExtResult']]){ ?>
                                CR
                        <?php }elseif(isset($command['Command_vCountry']) and isset($NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_ExtResult']]) and  $NRecords[$command['Command_vCountry']][$CompetitionEvent['Format_ExtResult']]==$attempts_in[$CompetitionEvent['Format_ExtResult']]){ ?>
                                NR
                        <?php } ?>
                    </td>
                <?php } ?>
                <td>
                    <?php if($command['Command_vCountry']){ ?>
                        <?= CountryName($command['Command_vCountry']); ?>
                    <?php }else{ ?>
                        Multi-country
                    <?php } ?>
                </td>
                <?php for($i=1;$i<=$CompetitionEvent['Format_Attemption'];$i++) {?>
                <td  class="table_new_attempt" >
                    <nobr><?= $attempts[$i]; ?></nobr>
                </td>
                <?php } ?>
                <?php if($command['Command_Video']){ ?>    
                    <td>
                        <a target=_blank" href="<?=$command['Command_Video'] ?>"><i class="fas fa-video"></i></a>
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