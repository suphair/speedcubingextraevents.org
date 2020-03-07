<?php
$Competition=ObjectClass::getObject('PageCompetition'); 
$CompetitionEvent=ObjectClass::getObject('PageCompetitionEvent');
$Competitor=getCompetitor();

    DataBaseClass::FromTable('Event',"ID='".$CompetitionEvent['Event_ID']."'");
    DataBaseClass::Join_current('Command');    
    DataBaseClass::Where("Com.vCompetitors=".$CompetitionEvent['Discipline_Competitors']);
    $commands=[];
    foreach(DataBaseClass::QueryGenerate() as $row){
        $commands[$row['Command_ID']]=$row;
        $commands[$row['Command_ID']]['win']=0;
    }
    
    $CommandsCup=json_decode($CompetitionEvent['Event_CommandsCup'],true);
    $Count=$CommandsCup['Count'];
    $Round=$CommandsCup['Round'];
?>
      
<h3>Results</h3>
    <table class="table_new">
        <thead>
            <tr>
            <td></td>
            <td class="table_new_center" >Place</td>
            <?php if($CompetitionEvent['Discipline_CodeScript']=='team_cup'){ ?>
                <td>Team</td>
            <?php } ?>
            <td colspan="<?= $CompetitionEvent['Discipline_Competitors'] ?>"><?= ml('Competition.Name')?></td>
            <td>
                <?= ml('Competition.CitizenOf')?>
            </td>
        </tr> 
    </thead>
    <?php 
    DataBaseClass::Query("
    select count(*) count,sum(wins) wins,sum(loses) loses, Command from(
	select Command1 Command, case when Command2=CommandWin and Command2 then 1 else 0 end loses, case when Command1=CommandWin then 1 else 0 end wins from CupCell where Event='".$CompetitionEvent['Event_ID']."'  and Command1
	union all
	select Command2 Command, case when Command1=CommandWin and Command1 then 1 else 0 end loses, case when Command2=CommandWin then 1 else 0 end wins from CupCell where Event='".$CompetitionEvent['Event_ID']."' and Command2
    )t group by Command");
    foreach(DataBaseClass::getRows() as $row){
        $commands[$row['Command']]['wins']=$row['wins'];
        $commands[$row['Command']]['loses']=$row['loses'];
        $commands[$row['Command']]['count']=$row['count'];
    }
    foreach($commands  as $c=>$command){
        if($command['wins']==0){
            $commands[$c]['place']=($Count/2+1).' - '.sizeof($commands);
            if(($Count/2+1)==sizeof($commands)){
                $commands[$c]['place']=sizeof($commands);    
            }
        }else{
            if($command['loses']==0){
                $commands[$c]['place']='1 - '.($Count/pow(2,$command['wins']));
            }else{
                $commands[$c]['place']=($Count/pow(2,$command['wins']+1)+1).' - '.($Count/pow(2,$command['wins']));
            }
        }
        if($commands[$c]['place']=='1 - 1'){
            $commands[$c]['place']='1';
        }
        if($commands[$c]['place']=='2 - 2'){
            $commands[$c]['place']='2';
        }
    } ?>
    
    <?php uasort($commands,'SortCommandCupOrderResult');
    foreach($commands  as $command){ ?>
        <tr>
            <td class="table_new_center" width="20px">    
               <?php if($command['place']==='1'){?>
                    <i class="color_gold fas fa-trophy"></i>
               <?php }elseif($command['loses']==0){ ?>
                    <i class="fas fa-running"></i>
                <?php }else{ ?>
                    <i class="color_red far fa-times"></i>
                <?php } ?>    
            </td>
            <td class="table_new_center" >
               <?= $command['place'] ?>
            </td>
            <td class="table_new_bold">
                <a class="Command_<?= $command['Command_ID']  ?>"><?= $command['Command_Name'] ?></a>
            </td>
             <?php   
             DataBaseClass::FromTable("Command","ID=".$command['Command_ID']);
             DataBaseClass::Join_current("CommandCompetitor");
             DataBaseClass::Join_current("Competitor");
             DataBaseClass::OrderClear("Competitor","Name");
             $competitors=DataBaseClass::QueryGenerate();
            for($i=0;$i<$CompetitionEvent['Discipline_Competitors'];$i++){ 
                if(isset($competitors[$i])){ ?>
                    <td>
                        <a href="<?= LinkCompetitor($competitors[$i]['Competitor_ID'],$competitors[$i]['Competitor_WCAID'])?>">
                            <?= Short_Name($competitors[$i]['Competitor_Name']); ?>
                        </a>
                    </td>
                <?php }else{ ?>
                    <td>
                        <i class="fas fa-question"></i>
                    </td>
                <?php }
            } ?>
            <td>
                <?php if($command['Command_vCountry']){ ?>
                    <?= CountryName($command['Command_vCountry']); ?>
                <?php } else{ ?>
                    Multi-country
                <?php } ?>
            </td>   
        </tr>
    <?php } ?>
    </table>
<h3>Attempts</h3>
 <table class="table_new">
    <thead>
        <tr>
            <td class="table_new_center" colspan="3">Solves</td>
            <td class="table_new_right">Sum</td>
            <td class="table_new_right">Team</td>
            <td class="table_new_right"></td>
            <td></td>
            <td></td>
            <td class="table_new_left">Team</td>
            <td class="table_new_right">Sum</td>
            <td class="table_new_center" colspan="3">Solves</td>
            
            
        </tr>    
    </thead>
    <tbody>
    <?php DataBaseClass::Query(" "
        . " select "
            . " group_concat(CV.Sum1) Sum1,"
            . " group_concat(CV.Value1_1) Value1_1,"
            . " group_concat(CV.Value2_1) Value2_1,"
            . " group_concat(CV.Value3_1) Value3_1,"
            . " group_concat(CV.Value1_2) Value1_2,"
            . " group_concat(CV.Value2_2) Value2_2,"
            . " group_concat(CV.Value3_2) Value3_2,"
            . " group_concat(CV.Point1) CV_Point1,"
            . " group_concat(CV.Sum2) Sum2,"
            . " group_concat(CV.Point2) CV_Point2,"
            . "  CC.*, C1.Name Name1, C2.Name Name2, count(CV.Point1) Point1, count(CV.Point2) Point2 from CupCell CC "
        . " join Command C1 on CC.Command1=C1.ID "
        . " join Command C2 on CC.Command2=C2.ID "
        . " left outer join CupValue CV on CV.CupCell=CC.ID "
        . " where CC.Event=".$CompetitionEvent['Event_ID']." "
        . " group by CC.ID, C1.Name, C2.Name"
        . " order by Round desc, Number");
    foreach(DataBaseClass::getRows() as $row){ 
        $Point1=explode(',',$row['CV_Point1']);
        $Point2=explode(',',$row['CV_Point2']);
        $Value1_1=explode(',',$row['Value1_1']);
        $Value2_1=explode(',',$row['Value2_1']);
        $Value3_1=explode(',',$row['Value3_1']);
        $Value1_2=explode(',',$row['Value1_2']);
        $Value2_2=explode(',',$row['Value2_2']);
        $Value3_2=explode(',',$row['Value3_2']);
        ?>
        <tr>
            <td class="table_new_right">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><?= getTimeStrFromValue($Value1_1[$i]); ?></p>
                <?php } ?>
            </td>
            <td class="table_new_right">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><?= getTimeStrFromValue($Value2_1[$i]); ?></p>
                <?php } ?>
            </td>
            <td class="table_new_right">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><?= getTimeStrFromValue($Value3_1[$i]); ?></p>
                <?php } ?>
            </td>
            <td class="table_new_right table_new_bold">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><span class="<?= $Point1[$i]?'color_green':'' ?>">
                        <?= getTimeStrFromValue($sum); ?>
                    </span></p>
                <?php } ?>
            </td>
            <td class="table_new_right table_new_bold">
                <a class="Command_<?= $row['Command1']  ?>"><?= $row['Name1']?></a>
            </td>
            <td class="table_new_right">
                <?php if($row['Status']!='run'){ ?>
                    <?php if($row['CommandWin']==$row['Command1']){ ?>
                        <?php if($row['Round']==$Round){ ?>
                            <i class="color_gold fas fa-trophy"></i>
                        <?php }else{ ?>
                            <i class="color_green fas fa-thumbs-up"></i>
                        <?php } ?>
                    <?php }else{ ?>
                        <i class="color_red far fa-times"></i>
                    <?php }?>
                <?php }else{ ?>
                    <i class="fas fa-running"></i>
                <?php } ?>
            </td>
            <td class="table_new_center">
                <?php if($row['Round']==$Round){ ?>
                    Final
                <?php }elseif($row['Round']==$Round-1){ ?>
                    Semifinal
                <?php }else{ ?>
                    1 / <?= pow(2,$Round-$row['Round'])?>
                <?php } ?>
            </td>
            <td>
                <?php if($row['Status']!='run'){ ?>
                    <?php if($row['CommandWin']==$row['Command2']){ ?>
                        <i class="color_green fas fa-thumbs-up"></i>
                    <?php }else{ ?>
                        <i class="color_red far fa-times"></i>
                    <?php }?>
                <?php }else{ ?>
                    <i class="fas fa-running"></i>
                <?php } ?>
            </td>
            <td class="table_new_left  table_new_bold">
                <a class="Command_<?= $row['Command2']  ?>"><?= $row['Name2']?></a>
            </td>
            <td class="table_new_right table_new_bold">
                <?php foreach(explode(',',$row['Sum2']) as $i=>$sum){?>
                    <p><span class="<?= $Point2[$i]?'color_green':'' ?>">
                        <?= getTimeStrFromValue($sum); ?>
                    </span></p>
                <?php } ?>
            </td>
            <td class="table_new_right">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><?= getTimeStrFromValue($Value1_2[$i]); ?></p>
                <?php } ?>
            </td>
            <td class="table_new_right">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><?= getTimeStrFromValue($Value2_2[$i]); ?></p>
                <?php } ?>
            </td>
            <td class="table_new_right">
                <?php foreach(explode(',',$row['Sum1']) as $i=>$sum){?>
                    <p><?= getTimeStrFromValue($Value3_2[$i]); ?></p>
                <?php } ?>
            </td>
        </tr>    
    <?php } ?>
</tbody>
</table>
<h3>Grid</h3>
<?php DataBaseClass::Query("Select  sum(CV.Point1) Point1,sum(CV.Point2) Point2,CC.*,C1.Name Command1Name,C2.Name Command2Name "
        . " from CupCell CC"
        . " left outer join Command C1 on CC.Command1=C1.ID "
        . " left outer join Command C2 on CC.Command2=C2.ID "
        . " left outer join CupValue CV on CV.CupCell=CC.ID"
        . " where CC.Event=".$CompetitionEvent['Event_ID'].""
        . " group by CC.ID,C1.ID,C2.ID");
$Cells=[];
$cell_rows=DataBaseClass::getRows();
foreach($cell_rows as $cell){
    $Cells[$cell['Round']][$cell['Number']]=$cell;
} ?>  
    
<table align="center" class="table_new_border cup_grid" >
    <thead>
        <tr>
            <?php for($round=1;$round<=$Round;$round++){ ?>
            <td width=150px" class="table_new_center table_new_bold" >
                <?php if($round==$Round){ ?>
                    Final
                <?php }elseif($round==$Round-1){ ?>
                    Semifinal
                <?php }else{ ?>
                    1 / <?= pow(2,$Round-$round)?>
                <?php } ?>
            </td>
            <?php } ?>
            <td style="display: none"></td>        
        </tr>  
    </thead>
    <tbody>
        <?php for($i=1;$i<=$Count;$i++){  ?>
        <tr>
            <?php for($round=1;$round<=$Round;$round++){ ?>
                <?php if($i%(pow(2,$round))-1==0){ 
                    if(isset($Cells[$round][$i/pow(2,$round)+1])){
                        $cell=$Cells[$round][$i/pow(2,$round)+1];
                    }else{
                        $cell=false;
                    }
                    $comwin=$cell['CommandWin'];?>
                        <td rowspan="<?= pow(2,$round) ?>">
                        <table class="table_new_background_white" width="100%">
                            <?php foreach([1,2] as $cn){ ?>
                            <?php if($com=$cell['Command'.$cn]){ ?>
                            <tr>
                                <td width="20px">
                                    <i><?= $commands[$com]['Command_CardID'] ?></i>
                                </td>
                                <td  width="100px">
                                <b>
                                    <a class="Command_<?= $commands[$com]['Command_ID']; ?>"><?= $commands[$com]['Command_Name']; ?></a>
                                    
                                </b>
                                </td>
                                <td>
                                <?php if($comwin){ ?>
                                    <?php if($comwin==$com){ ?>
                                        <?php if($cell['Status']=='skip'){ ?>
                                            <i class="fas fa-arrow-right"></i>
                                        <?php }elseif($round!=$Round){ ?>
                                            <i class="color_green fas fa-thumbs-up"></i>
                                        <?php }else{ ?>    
                                            <i class="color_gold fas fa-trophy"></i>
                                        <?php } ?>    
                                    <?php }else{ ?>
                                        <i class="color_red far fa-times"></i>
                                    <?php 
                                        }
                                    }elseif($cell['Status']=='wait'){ ?>
                                        <i class="fas fa-hourglass-start"></i>
                                    <?php }elseif($cell['Status']=='run'){ ?>
                                        <i class="fas fa-running"></i>
                                    <?php } ?>         
                                </td>        
                            </tr>
                            <?php }else{ ?>
                                <tr>
                                    <td width="20px"></td>
                                    <td width="100px"></td>
                                    <td>
                                        <?php if($cell['Status']=='skip'){ ?>
                                            <i class="fas fa-ban"></i>
                                        <?php }elseif($cell['Status']=='blank'){ ?>
                                            &nbsp;
                                        <?php }else{ ?>
                                            <i class="far fa-question-circle"></i>
                                        <?php } ?>
                                    </td>        
                                </tr>
                            <?php } ?>
                            
                        <?php } ?> 
                        </table>
                    </td>
                <?php } ?>
            <?php } ?>
            <td style="display: none"></td>        
        </tr>
        <?php } ?>
    </tbody>    
</table>
<script>
    $('[class ^= Command]').addClass("result_cup_team");
    
    $('[class ^= Command]').on("mouseover",function(){
        $(':exact('+$(this).html()+')').addClass('result_cup_team_hover');
    });
    
    $('[class ^= Command]').on("click",function(){
        $(this).attr("class").split(' ').forEach(
            (element) => {
                var command_id=element.replace('Command_','');
                if(command_id!==element){
                    team_select(command_id);
                }
            })
    });
    
    $('[class ^= Command]').on("mouseout",function(){
        $('[class ^= Command]').removeClass("result_cup_team_hover");
    });
    
    
    $.expr[":"].exact = $.expr.createPseudo(function(arg) {
        return function(element) {
            return $(element).text() === arg.trim();    
        };
    });
    
    function team_select(command_id){
        location.hash = '#' + command_id;
        $('[class ^= Command]').removeClass("result_cup_team_select");
        $('.Command_' + command_id).addClass("result_cup_team_select");
    }
    
    if(location.hash){
        team_select(location.hash.replace('#',''));
    }
</script>