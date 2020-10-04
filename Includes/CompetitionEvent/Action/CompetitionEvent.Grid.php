<!DOCTYPE HTML>
<html>

<?php 
$request=Request();
if(!isset($request[2]) or !is_numeric($request[2])){
    exit();
}
$Event=$request[2];
$availableCupChange=availableCupChange($Event);
$availableCupDistribution=availableCupDistribution($Event);
$availableCupReset=availableCupReset($Event);

DataBaseClass::Query("Select E.vRound, D.Name Discipline, C.Name Competition, C.ID Competition_ID,C.WCA Competition_WCA, D.Code Discipline_Code,D.CodeScript Discipline_CodeScript, C.ID CompetitionID, E.ID EventID, E.Groups EventGroups,E.CommandsCup "
        . " from `Event` E join DisciplineFormat DF on DF.ID=E.DisciplineFormat"
        . " join  Discipline D on D.ID=DF.Discipline"
        . " join `Competition` C on C.ID=E.Competition Where E.ID='". $Event."'");
    
if(DataBaseClass::getAffectedRows()==0){
    exit();
}
    
$data=DataBaseClass::getRow();
$CommandsCup=json_decode($data['CommandsCup'],true);
$Rounds=isset($CommandsCup['Round'])?$CommandsCup['Round']:5;
$MaxTeams=pow(2,$Rounds);
?>
<head>
    <script src="<?= PageIndex() ?>/jQuery/jquery-3.3.1.min.js" type="text/javascript"></script>
    <title><?= $data['Discipline']?><?= $data['vRound']?></title>
    <link rel="stylesheet" href="<?= PageIndex() ?>/style.css" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex() ?>/fontawesome-free-5.12.0-web/css/all.css?t=3" type="text/css"/>
    <link rel="stylesheet" href="<?= PageIndex() ?>/icons-extra-event/css/Extra-Events.css?t=3" type="text/css"/>    
    <link rel="stylesheet" href="<?= PageIndex() ?>/jQuery/chosen_v1/chosen.css" type="text/css"/>
</head>  
<body>
<?php

$Competition=$data['Competition'];
$Discipline=$data['Discipline'];

RequestClass::CheckAccessExit(__FILE__,'Competition.Event.Settings',$data['Competition_ID']); ?>
    <h1><?= $data['Competition'] ?> ▪ <?= $data['Discipline']?><?= $data['vRound']?></h1>
    <h2>Distribution of teams by grid</h2>
    <table width="100%"><tr><td  width="50%">
    <table class='table_info'>
        <form method="POST" action="<?= PageAction('CompetitionEvent.GridEditReset')?>">
        <input hidden name="ID" value="<?= $Event ?>"/>
        <tr>
            <td>Reset distribution</td>
            <td>
                <?php if(!$availableCupChange and $availableCupReset){ ?>
                    <button><i class="fas fa-undo"></i> Reset</td>
                <?php }elseif(!$availableCupReset){?>
                    Results exists
                <?php }else{ ?>    
                    No distribution
                <?php } ?>
            </td>
        </tr>    
        </form>
        <form class="inputCupValue" method="POST" action="<?= PageAction('CompetitionEvent.GridEditRounds')?>">
        <tr>
            <td>
                Number of rounds   
            </td>
            <td>
                <?php if($availableCupChange) { ?>
                <input hidden name="ID" value="<?= $Event ?>"/>
                <select name='Rounds'>
                    <option value='1' <?= $Rounds==1?'selected':'' ?>>1 - up to 2 teams</option>
                    <option value='2' <?= $Rounds==2?'selected':'' ?>>2 - up to 4 teams</option>
                    <option value='3' <?= $Rounds==3?'selected':'' ?>>3 - up to 8 teams</option>
                    <option value='4' <?= $Rounds==4?'selected':'' ?>>4 - up to 16 teams</option>
                    <option value='5' <?= $Rounds==5?'selected':'' ?>>5 - up to 32 teams</option>
                </select>
                <button><i class="fas fa-save"></i> Save</button>
                <?php }else{ ?>
                    <?= $Rounds ?>
                <?php } ?>
            </td>
        </tr>    
        </form>  
        <form class="inputCupValue" method="POST" action="<?= PageAction('CompetitionEvent.GridEditReloadResults')?>">
            <input hidden name="ID" value="<?= $Event ?>"/>
            <tr>
                <td>Reload results</td>    
                <td>
                    <?php if($availableCupChange) { ?>
                        <button><i class="fas fa-cloud-download-alt"></i> load</button>
                    <?php } ?>    
                    <?= GetValue('GridEditReloadResults_'.$Event); ?>
                </td>    
            <tr>
        </form>  
    </table>
    </td><td  width="50%">
    <table class='table_info'>
        <?php if($availableCupChange and $availableCupDistribution) { ?>
            <tr>
                <td>Distribute by</td> 
                <td>
                    <form method="POST" action="<?= PageAction('CompetitionEvent.GridEditDistrube')?>">
                        <input hidden name="ID" value="<?= $Event ?>"/>
                        <input hidden name="Type" value="default"/>
                        <button><i class="fas fa-expand-arrows-alt"></i> default</button>
                    </form>    
                </td>    
            <tr>
            <tr>
                <td></td>    
                <td>
                    <form method="POST" action="<?= PageAction('CompetitionEvent.GridEditDistrube')?>">
                        <input hidden name="ID" value="<?= $Event ?>"/>
                        <input hidden name="Type" value="random"/>
                        <button><i class="fas fa-expand-arrows-alt"></i> random</button>
                    </form>    
                </td>    
            <tr>
            <tr>
                <td></td>    
                <td>
                    <form method="POST" action="<?= PageAction('CompetitionEvent.GridEditDistrube')?>">
                        <input hidden name="ID" value="<?= $Event ?>"/>
                        <input hidden name="Type" value="name"/>
                        <button><i class="fas fa-expand-arrows-alt"></i> name</button>
                    </form>    
                </td>    
            <tr>    
        
        <?php }elseif(!$availableCupDistribution){ ?>
           <tr>
                <td><i class="fas fa-hand-paper"></i></td>
                <td>Too many teams</td>
            </tr>    
        <?php }else{ ?>
            <tr>
                <td><i class="fas fa-hand-paper"></i></td>
                <td>There is a distribution</td>
            </tr>    
        <?php } ?>    
    </table>
    </td></tr></table>  

<table width="100%">
<tr><td width="20%">    
<form ID='InCup' method="POST" action="<?= PageAction('CompetitionEvent.GridEditTeamInCup')?>">        
<input hidden name="ID" value="<?= $Event ?>"/>    
<table class="table_new">
    <thead>
        <tr>
            <td class="table_new_center table_new_bold">#</td>
            <td>Team</td>
            <td class='table_new_right'>Sum</td>
            <td></td>
            <?php if($availableCupChange) { ?>
                <td></td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
    <?php 
    $countInCup=0;
    DataBaseClass::FromTable('Command',"Event='".$Event."'");
    DataBaseClass::OrderClear('Command',"Sum333");
    $commands=[];
    foreach(DataBaseClass::QueryGenerate() as $command){ 
        $commands[$command['Command_ID']]=$command; 
    ?>
    <tr>
        <td class="table_new_center table_new_bold">
            <?= $command['Command_CardID'] ?>
        </td>
        <td>
            <?= $command['Command_Name']?>
        </td>
        <td class="table_new_right">
            <?= getTimeStrFromValue($command['Command_Sum333']);?>
        </td>
        <td class='table_new_center'>
            <?php if($command['Command_inCup']){ 
                $countInCup++; ?>
                <i class="fas fa-running"></i>
            <?php }else{ ?>
                <i class="fas fa-ban"></i>
            <?php } ?>
        </td> 
        <?php if($availableCupChange){ ?>
        <td class='table_new_center'>
            <input name="Command[<?= $command['Command_ID'] ?>]" type='checkbox' <?= $command['Command_inCup']?'checked':'' ?>>
        </td>        
        <?php } ?>
    </tr>    
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <td/>
        <td/>
        <td class='table_new_right'>
        <?php if($availableCupChange){ ?>
            <button><i class='fas fa-save'></i> Save</button>
        <?php } ?>
        </td>   
        <td class='table_new_center'>
            <span class="<?= $MaxTeams<$countInCup?'color_red':'color_green' ?>">
                <?= $countInCup ?> of <?= $MaxTeams ?>
            </span>
        </td>
        <?php if($availableCupChange) { ?>
            <td class='table_new_center' ID='countInCup'>
                <?= $countInCup ?>
            </td>
        <?php } ?>
    </tr>
    </tfoot>
</table>    
</form>    
</td><td width="80%">
<?php DataBaseClass::Query("Select  sum(CV.Point1) Point1,sum(CV.Point2) Point2,CC.*,C1.Name Command1Name,C2.Name Command2Name "
        . " from CupCell CC"
        . " left outer join Command C1 on CC.Command1=C1.ID "
        . " left outer join Command C2 on CC.Command2=C2.ID "
        . " left outer join CupValue CV on CV.CupCell=CC.ID"
        . " where CC.Event=$Event"
        . " group by CC.ID,C1.ID,C2.ID");
$Cells=[];
$cell_rows=DataBaseClass::getRows();
foreach($cell_rows as $cell){
    $Cells[$cell['Round']][$cell['Number']]=$cell;
} ?>  
    
<table class="table_new_border cup_grid" >
    <thead>
        <tr>
            <?php for($round=1;$round<=$Rounds;$round++){ ?>
            <td width=150px" class="table_new_center table_new_bold" >
                Round <?= $round?>
            </td>
            <?php } ?>
            <td style="display: none"></td>        
        </tr>  
    </thead>
    <tbody>
        <?php for($i=1;$i<=$MaxTeams;$i++){  ?>
        <tr>
            <?php for($round=1;$round<=$Rounds;$round++){ ?>
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
                                <?= $commands[$com]['Command_Name']; ?>
                                </td>
                                <td>
                                <?php if($comwin){ ?>
                                    <?php if($comwin==$com){ ?>
                                        <?php if($cell['Status']=='skip'){ ?>
                                            <i class="fas fa-arrow-right"></i>
                                        <?php }else{ ?>
                                            <i class="color_green fas fa-thumbs-up"></i>
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
</td></tr></table>    

<script>
    $('#InCup input').change(function(){
        $('#countInCup').html($('#InCup :checkbox:checked').length);
    });
</script>    
</body>
</html>
<?php exit(); ?>

